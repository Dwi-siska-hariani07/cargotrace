<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class ExceptionService
{
    protected EtaService $etaService;

    public function __construct(EtaService $etaService)
    {
        $this->etaService = $etaService;
    }

    /**
     * Report a new exception for a shipment.
     */
    public function reportException(Shipment $shipment, array $data, User $actor): ShipmentException
    {
        $detectedAt = !empty($data['detected_at']) 
            ? Carbon::parse($data['detected_at']) 
            : Carbon::now();

        $exception = ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => $data['exception_type'],
            'severity' => $data['severity'] ?? 'MEDIUM',
            'status' => 'OPEN',
            'description' => $data['description'],
            'detected_at' => $detectedAt,
            'reported_by_user_id' => $actor->id,
        ]);

        // Create tracking event reflecting this disruption
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_status' => $data['exception_type'],
            'location_id' => $data['location_id'] ?? null,
            'location_name' => $data['location_name'] ?? ($shipment->current_location_name ?? 'In Transit Route'),
            'latitude' => $data['latitude'] ?? $shipment->current_latitude,
            'longitude' => $data['longitude'] ?? $shipment->current_longitude,
            'event_time' => $detectedAt,
            'description' => "[EXCEPTION - {$data['severity']}] {$data['description']}",
            'is_exception' => true,
            'created_by_user_id' => $actor->id,
        ]);

        // If extra delay hours caused by exception are provided, adjust estimated arrival
        if (!empty($data['delay_hours_impact'])) {
            $shipment->estimated_arrival = Carbon::parse($shipment->estimated_arrival)->addHours((int)$data['delay_hours_impact']);
            $this->etaService->evaluateDelay($shipment, "Exception: {$data['exception_type']} - {$data['description']}");
        }

        // Notify Customer
        Notification::create([
            'user_id' => $shipment->customer_id,
            'shipment_id' => $shipment->id,
            'title' => "Incident Report on {$shipment->shipment_number}: {$data['exception_type']}",
            'message' => "Severity: {$data['severity']}. {$data['description']}",
            'type' => 'EXCEPTION_ALERT',
            'is_read' => false,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'REPORT_EXCEPTION',
            'entity_type' => 'ShipmentException',
            'entity_id' => $exception->id,
            'new_values' => [
                'shipment_number' => $shipment->shipment_number,
                'type' => $exception->exception_type,
                'severity' => $exception->severity,
            ],
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'System',
        ]);

        return $exception;
    }

    /**
     * Resolve an exception.
     */
    public function resolveException(ShipmentException $exception, array $data, User $actor): ShipmentException
    {
        $exception->status = 'RESOLVED';
        $exception->resolved_at = Carbon::now();
        $exception->resolved_by_user_id = $actor->id;
        $exception->resolution_notes = $data['resolution_notes'] ?? 'Exception cleared by operator.';
        $exception->save();

        // Add tracking event for resolution
        $shipment = $exception->shipment;
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_status' => 'EXCEPTION_RESOLVED',
            'location_name' => $shipment->current_location_name ?? 'In Transit Route',
            'latitude' => $shipment->current_latitude,
            'longitude' => $shipment->current_longitude,
            'event_time' => Carbon::now(),
            'description' => "Resolved [{$exception->exception_type}]: {$exception->resolution_notes}",
            'is_exception' => false,
            'created_by_user_id' => $actor->id,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'RESOLVE_EXCEPTION',
            'entity_type' => 'ShipmentException',
            'entity_id' => $exception->id,
            'old_values' => ['status' => 'OPEN'],
            'new_values' => ['status' => 'RESOLVED', 'notes' => $exception->resolution_notes],
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'System',
        ]);

        // Notify Customer
        Notification::create([
            'user_id' => $shipment->customer_id,
            'shipment_id' => $shipment->id,
            'title' => "Incident Resolved on {$shipment->shipment_number}",
            'message' => "The {$exception->exception_type} issue has been resolved. {$exception->resolution_notes}",
            'type' => 'EXCEPTION_RESOLVED',
            'is_read' => false,
        ]);

        return $exception;
    }
}
