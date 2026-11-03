<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\Request;

class ExceptionApiController extends Controller
{
    protected ExceptionService $exceptionService;

    public function __construct(ExceptionService $exceptionService)
    {
        $this->exceptionService = $exceptionService;
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

        $exceptions = $shipment->exceptions()->with(['reporter:id,name', 'resolver:id,name'])->get();

        return response()->json([
            'success' => true,
            'data' => $exceptions,
        ]);
    }

    public function store(Request $request, $id)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

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

        $exception = $this->exceptionService->reportException($shipment, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Exception logged successfully.',
            'data' => $exception,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $exception = ShipmentException::find($id);
        if (!$exception) {
            return response()->json(['success' => false, 'message' => 'Exception not found.'], 404);
        }

        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $resolved = $this->exceptionService->resolveException($exception, $validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Exception resolved successfully.',
            'data' => $resolved,
        ]);
    }
}
