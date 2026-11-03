<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ShipmentService
{
    /**
     * Strict state machine transitions as mandated by business rules.
     */
    public const ALLOWED_TRANSITIONS = [
        'CREATED' => ['BOOKED', 'CANCELLED'],
        'BOOKED' => ['PICKED_UP', 'CANCELLED'],
        'PICKED_UP' => ['IN_TRANSIT', 'CANCELLED'],
        'IN_TRANSIT' => ['OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'],
        'OUT_FOR_DELIVERY' => ['DELIVERED', 'CANCELLED'],
        'DELIVERED' => [], // Terminal state
        'CANCELLED' => [], // Terminal state
    ];

    protected EtaService $etaService;

    public function __construct(EtaService $etaService)
    {
        $this->etaService = $etaService;
    }

    /**
     * Check if a status transition is valid according to business rules.
     */
    public function canTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }

        $allowed = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        return in_array($newStatus, $allowed, true);
    }

    /**
     * Create a new shipment with generated reference number.
     */
    public function createShipment(array $data, User $actor): Shipment
    {
        if (empty($data['shipment_number'])) {
            $data['shipment_number'] = 'CRG-' . date('Y') . '-' . strtoupper(Str::random(6));
        }

        $data['current_status'] = 'CREATED';

        // Auto evaluate delay if estimated arrival is provided
        if (empty($data['estimated_arrival']) && !empty($data['expected_arrival'])) {
            $data['estimated_arrival'] = $data['expected_arrival'];
        }

        $shipment = Shipment::create($data);

        // Check initial ETA
        $this->etaService->evaluateDelay($shipment);

        // Audit Log
        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'CREATE_SHIPMENT',
            'entity_type' => 'Shipment',
            'entity_id' => $shipment->id,
            'new_values' => [
                'shipment_number' => $shipment->shipment_number,
                'customer_id' => $shipment->customer_id,
                'origin_id' => $shipment->origin_id,
                'destination_id' => $shipment->destination_id,
            ],
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'System',
        ]);

        return $shipment;
    }

    /**
     * Transition shipment status with strict validation.
     */
    public function transitionStatus(Shipment $shipment, string $newStatus, User $actor, ?string $reason = null): Shipment
    {
        if (!$this->canTransition($shipment->current_status, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => ["Invalid status transition from {$shipment->current_status} to {$newStatus}."],
            ]);
        }

        $oldStatus = $shipment->current_status;
        $shipment->current_status = $newStatus;

        if ($newStatus === 'IN_TRANSIT' && empty($shipment->actual_departure)) {
            $shipment->actual_departure = Carbon::now();
        }

        if ($newStatus === 'DELIVERED') {
            $shipment->actual_arrival = Carbon::now();
        }

        $shipment->save();

        // Audit Log
        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'UPDATE_STATUS',
            'entity_type' => 'Shipment',
            'entity_id' => $shipment->id,
            'old_values' => ['current_status' => $oldStatus],
            'new_values' => ['current_status' => $newStatus, 'reason' => $reason],
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'System',
        ]);

        // Notify Customer
        Notification::create([
            'user_id' => $shipment->customer_id,
            'shipment_id' => $shipment->id,
            'title' => "Shipment {$shipment->shipment_number} Status Updated",
            'message' => "Your shipment status is now {$newStatus}" . ($reason ? ": {$reason}" : "."),
            'type' => 'STATUS_CHANGE',
            'is_read' => false,
        ]);

        return $shipment;
    }
}
