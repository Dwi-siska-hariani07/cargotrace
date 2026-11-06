<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingWebController extends Controller
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function store(Request $request, $shipmentId)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized action.');
        }

        $shipment = Shipment::findOrFail($shipmentId);

        $validated = $request->validate([
            'event_status' => 'required|string',
            'location_id' => 'nullable|exists:locations,id',
            'location_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'event_time' => 'nullable|date',
            'description' => 'required|string',
            'new_status' => 'nullable|string',
            'new_estimated_arrival' => 'nullable|date',
        ]);

        try {
            $this->trackingService->addEvent($shipment, $validated, $user);
            return back()->with('success', 'Tracking checkpoint logged successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error logging event: ' . $e->getMessage());
        }
    }
}

