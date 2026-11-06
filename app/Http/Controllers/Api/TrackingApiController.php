<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\Request;

class TrackingApiController extends Controller
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function index(Request $request, $id)
    {
        $user = $request->user();
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $events = $shipment->trackingEvents()->with('location')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shipment_number' => $shipment->shipment_number,
                'current_status' => $shipment->current_status,
                'is_delayed' => $shipment->is_delayed,
                'delay_hours' => $shipment->delay_hours,
                'delay_severity' => $shipment->delay_severity,
                'current_location' => [
                    'name' => $shipment->current_location_name,
                    'latitude' => $shipment->current_latitude,
                    'longitude' => $shipment->current_longitude,
                ],
                'events' => $events,
            ],
        ]);
    }

    public function store(Request $request, $id)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Customers cannot add tracking checkpoints.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $validated = $request->validate([
            'event_status' => 'required|string',
            'location_id' => 'nullable|exists:locations,id',
            'location_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'event_time' => 'nullable|date',
            'description' => 'required|string',
            'is_exception' => 'nullable|boolean',
            'new_status' => 'nullable|string',
            'new_estimated_arrival' => 'nullable|date',
        ]);

        $event = $this->trackingService->addEvent($shipment, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Tracking event recorded successfully.',
            'data' => $event,
        ], 201);
    }
}

