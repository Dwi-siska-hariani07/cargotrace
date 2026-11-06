<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\ShipmentService;
use App\Services\EtaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentApiController extends Controller
{
    protected ShipmentService $shipmentService;
    protected EtaService $etaService;

    public function __construct(ShipmentService $shipmentService, EtaService $etaService)
    {
        $this->shipmentService = $shipmentService;
        $this->etaService = $etaService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Shipment::with(['customer:id,name,email,company', 'carrier', 'origin', 'destination', 'latestTrackingEvent']);

        // Authorization rule: Customer only sees their own shipments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }

        if ($request->filled('is_delayed')) {
            $query->where('is_delayed', filter_var($request->is_delayed, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) ($request->get('per_page', 10)), 50);
        $shipments = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Customers cannot create operational shipments.'], 403);
        }

        $validated = $request->validate([
            'shipment_number' => 'nullable|string|unique:shipments,shipment_number',
            'customer_id' => 'required|exists:users,id',
            'carrier_id' => 'required|exists:carriers,id',
            'origin_id' => 'required|exists:locations,id',
            'destination_id' => 'required|exists:locations,id|different:origin_id',
            'transport_type' => 'required|in:SEA,AIR,LAND',
            'cargo_type' => 'required|string|max:255',
            'total_weight_kg' => 'required|numeric|min:0.1',
            'total_volume_cbm' => 'nullable|numeric|min:0.01',
            'expected_departure' => 'required|date',
            'expected_arrival' => 'required|date|after_or_equal:expected_departure',
            'estimated_arrival' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.package_type' => 'nullable|string',
            'items.*.weight_kg' => 'required|numeric|min:0.1',
            'items.*.volume_cbm' => 'nullable|numeric',
            'items.*.hs_code' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $shipment = $this->shipmentService->createShipment($validated, $user);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $shipment->items()->create($itemData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully.',
                'data' => $shipment->load(['customer', 'carrier', 'origin', 'destination', 'items']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $shipment = Shipment::with([
            'customer:id,name,email,company,phone',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'exceptions.reporter:id,name',
            'exceptions.resolver:id,name'
        ])->find($id);

        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        // Authorization check: Customer cannot view other customers' shipments
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to this shipment.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $validated = $request->validate([
            'carrier_id' => 'sometimes|exists:carriers,id',
            'cargo_type' => 'sometimes|string',
            'total_weight_kg' => 'sometimes|numeric|min:0.1',
            'total_volume_cbm' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'current_status' => 'sometimes|in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,OUT_FOR_DELIVERY,DELIVERED,CANCELLED',
            'status_reason' => 'nullable|string',
            'estimated_arrival' => 'sometimes|date',
        ]);

        if (!empty($validated['current_status']) && $validated['current_status'] !== $shipment->current_status) {
            $this->shipmentService->transitionStatus($shipment, $validated['current_status'], $user, $validated['status_reason'] ?? null);
        }

        if (!empty($validated['estimated_arrival'])) {
            $shipment->estimated_arrival = $validated['estimated_arrival'];
            $this->etaService->evaluateDelay($shipment);
        }

        $shipment->update(array_diff_key($validated, array_flip(['current_status', 'status_reason', 'estimated_arrival'])));

        return response()->json([
            'success' => true,
            'message' => 'Shipment updated successfully.',
            'data' => $shipment->fresh(),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Only administrators can delete shipments.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $shipment->delete(); // Soft delete as required

        return response()->json([
            'success' => true,
            'message' => 'Shipment soft-deleted successfully.',
        ]);
    }
}

