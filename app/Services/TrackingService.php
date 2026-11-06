<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TrackingService
{
    protected ShipmentService $shipmentService;
    protected EtaService $etaService;

    public function __construct(ShipmentService $shipmentService, EtaService $etaService)
    {
        $this->shipmentService = $shipmentService;
        $this->etaService = $etaService;
    }

    /**
     * Add tracking event and synchronize with shipment coordinates and status.
     */
    public function addEvent(Shipment $shipment, array $data, User $actor): TrackingEvent
    {
        $eventTime = !empty($data['event_time']) 
            ? Carbon::parse($data['event_time']) 
            : Carbon::now();

        // Historical check: ensure event time is not earlier than creation time
        if ($eventTime->lt($shipment->created_at->subMinutes(5))) {
            throw ValidationException::withMessages([
                'event_time' => ['Event time cannot precede the shipment creation timestamp.'],
            ]);
        }

        $event = TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_status' => $data['event_status'] ?? $shipment->current_status,
            'location_id' => $data['location_id'] ?? null,
            'location_name' => $data['location_name'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'event_time' => $eventTime,
            'description' => $data['description'] ?? 'Checkpoint passed.',
            'is_exception' => $data['is_exception'] ?? false,
            'created_by_user_id' => $actor->id,
        ]);

        // Update shipment current location if coordinates provided
        if (!empty($data['latitude']) && !empty($data['longitude'])) {
            $shipment->current_latitude = $data['latitude'];
            $shipment->current_longitude = $data['longitude'];
            $shipment->current_location_name = $data['location_name'];
        }

        // If event specifies a valid status progression, update shipment
        if (!empty($data['new_status']) && $data['new_status'] !== $shipment->current_status) {
            $this->shipmentService->transitionStatus($shipment, $data['new_status'], $actor, $data['description'] ?? null);
        } else {
            $shipment->save();
        }

        // Update ETA if provided
        if (!empty($data['new_estimated_arrival'])) {
            $shipment->estimated_arrival = Carbon::parse($data['new_estimated_arrival']);
            $this->etaService->evaluateDelay($shipment, $data['description'] ?? null);
        }

        // Audit Log
        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'ADD_TRACKING_EVENT',
            'entity_type' => 'TrackingEvent',
            'entity_id' => $event->id,
            'new_values' => [
                'shipment_number' => $shipment->shipment_number,
                'location_name' => $event->location_name,
                'status' => $event->event_status,
            ],
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'System',
        ]);

        return $event;
    }
}

