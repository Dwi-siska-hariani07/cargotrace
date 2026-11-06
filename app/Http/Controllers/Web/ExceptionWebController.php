<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExceptionWebController extends Controller
{
    protected ExceptionService $exceptionService;

    public function __construct(ExceptionService $exceptionService)
    {
        $this->exceptionService = $exceptionService;
    }

    public function store(Request $request, $shipmentId)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized action.');
        }

        $shipment = Shipment::findOrFail($shipmentId);

        $validated = $request->validate([
            'exception_type' => 'required|in:CUSTOMS_HOLD,WEATHER_DELAY,PORT_CONGESTION,DAMAGED,MISROUTED,DOCUMENTATION_ISSUE',
            'severity' => 'required|in:LOW,MEDIUM,HIGH',
            'description' => 'required|string',
            'location_name' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'delay_hours_impact' => 'nullable|integer|min:1',
            'detected_at' => 'nullable|date',
        ]);

        try {
            $this->exceptionService->reportException($shipment, $validated, $user);
            return back()->with('success', "Disruption [{$validated['exception_type']}] reported successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error reporting exception: ' . $e->getMessage());
        }
    }

    public function resolve(Request $request, $exceptionId)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized action.');
        }

        $exception = ShipmentException::findOrFail($exceptionId);

        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        try {
            $this->exceptionService->resolveException($exception, $validated, $user);
            return back()->with('success', "Exception [{$exception->exception_type}] marked as RESOLVED.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error resolving exception: ' . $e->getMessage());
        }
    }
}

