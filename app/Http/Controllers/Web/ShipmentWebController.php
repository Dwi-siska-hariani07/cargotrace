<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\User;
use App\Services\ShipmentService;
use App\Services\EtaService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentWebController extends Controller
{
    protected ShipmentService $shipmentService;
    protected EtaService $etaService;
    protected WeatherService $weatherService;

    public function __construct(
        ShipmentService $shipmentService, 
        EtaService $etaService,
        WeatherService $weatherService
    ) {
        $this->shipmentService = $shipmentService;
        $this->etaService = $etaService;
        $this->weatherService = $weatherService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination']);

        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Search & Filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('shipment_number', 'like', "%{$s}%")
                  ->orWhere('cargo_type', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }

        if ($request->filled('delayed')) {
            $query->where('is_delayed', $request->delayed === '1');
        }

        $shipments = $query->latest()->paginate(10)->withQueryString();
        $carriers = Carrier::where('is_active', true)->get();

        return view('shipments.index', compact('shipments', 'carriers'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized action.');
        }

        $customers = User::where('role', 'customer')->get();
        $carriers = Carrier::where('is_active', true)->get();
        $locations = Location::all();

        return view('shipments.create', compact('customers', 'carriers', 'locations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized action.');
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
            'item_name' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'package_type' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $originLoc = Location::find($validated['origin_id']);
            $validated['current_latitude'] = $originLoc->latitude;
            $validated['current_longitude'] = $originLoc->longitude;
            $validated['current_location_name'] = $originLoc->name;

            $shipment = $this->shipmentService->createShipment($validated, $user);

            // Add single item if filled
            if (!empty($validated['item_name'])) {
                $shipment->items()->create([
                    'item_name' => $validated['item_name'],
                    'quantity' => $validated['quantity'] ?? 1,
                    'package_type' => $validated['package_type'] ?? 'Standard Package',
                    'weight_kg' => $validated['total_weight_kg'],
                    'volume_cbm' => $validated['total_volume_cbm'],
                ]);
            }

            // Create initial CREATED tracking event
            $shipment->trackingEvents()->create([
                'event_status' => 'CREATED',
                'location_id' => $originLoc->id,
                'location_name' => $originLoc->name,
                'latitude' => $originLoc->latitude,
                'longitude' => $originLoc->longitude,
                'event_time' => now(),
                'description' => 'Shipment registered and queued for booking.',
                'created_by_user_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('shipments.show', $shipment->id)
                ->with('success', "Shipment {$shipment->shipment_number} created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating shipment: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $shipment = Shipment::with([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'exceptions.reporter',
            'exceptions.resolver',
        ])->findOrFail($id);

        if ($user && $user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Unauthorized access to this shipment.');
        }

        // Live Weather at current coordinates and ports
        $weather = null;
        if ($shipment->current_latitude && $shipment->current_longitude) {
            $weather = $this->weatherService->getWeather($shipment->current_latitude, $shipment->current_longitude);
        }

        $originWeather = $this->weatherService->getWeather($shipment->origin->latitude, $shipment->origin->longitude);
        $destWeather = $this->weatherService->getWeather($shipment->destination->latitude, $shipment->destination->longitude);

        $allLocations = Location::all();

        return view('shipments.show', compact('shipment', 'weather', 'originWeather', 'destWeather', 'allLocations'));
    }

    public function publicTrack(Request $request)
    {
        $number = trim($request->input('number', ''));
        if (empty($number)) {
            return redirect()->route('home')->with('error', 'Please enter a valid shipment tracking number.');
        }

        $shipment = Shipment::with([
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'exceptions',
        ])->where('shipment_number', $number)->first();

        if (!$shipment) {
            return redirect()->route('home')->with('error', "No shipment found matching tracking number '{$number}'.");
        }

        $weather = null;
        if ($shipment->current_latitude && $shipment->current_longitude) {
            $weather = $this->weatherService->getWeather($shipment->current_latitude, $shipment->current_longitude);
        }

        $originWeather = $this->weatherService->getWeather($shipment->origin->latitude, $shipment->origin->longitude);
        $destWeather = $this->weatherService->getWeather($shipment->destination->latitude, $shipment->destination->longitude);

        return view('public.track', compact('shipment', 'weather', 'originWeather', 'destWeather'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can delete shipments.');
        }

        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', 'Shipment soft-deleted successfully.');
    }
}

