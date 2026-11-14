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

    public function publicHome(Request $request)
    {
        $mode = $request->get('mode', 'ALL');
        $search = trim((string) $request->get('q', ''));

        $query = Shipment::with(['carrier', 'origin', 'destination'])
            ->select([
                'id',
                'shipment_number',
                'carrier_id',
                'origin_id',
                'destination_id',
                'transport_type',
                'cargo_type',
                'vessel_or_flight',
                'container_or_awb',
                'total_weight_kg',
                'estimated_arrival',
                'current_status',
                'is_delayed',
                'delay_hours',
                'current_location_name',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhere('vessel_or_flight', 'like', "%{$search}%")
                  ->orWhere('container_or_awb', 'like', "%{$search}%");
            });
        }

        if (in_array($mode, ['SEA', 'AIR', 'LAND'], true)) {
            $query->where('transport_type', $mode);
        } elseif ($mode === 'DELAYED') {
            $query->where('is_delayed', true);
        }

        $featuredShipments = $query->orderBy('id')->take(9)->get();

        $searchableShipments = Shipment::with(['origin', 'destination', 'carrier'])
            ->select([
                'id',
                'shipment_number',
                'transport_type',
                'cargo_type',
                'vessel_or_flight',
                'current_status',
                'is_delayed',
                'origin_id',
                'destination_id',
                'carrier_id',
            ])
            ->latest('id')
            ->get();

        $networkStats = [
            'totalShipments' => Shipment::count(),
            'activeTransit' => Shipment::where('current_status', 'IN_TRANSIT')->count(),
            'portsCount' => Location::count(),
            'carriersCount' => Carrier::where('is_active', true)->count(),
        ];

        $quoteLocations = Location::orderBy('country')->orderBy('city')->get();
        $globalPorts = Location::orderBy('id')->take(9)->get();

        return view('public.home', compact(
            'featuredShipments',
            'searchableShipments',
            'networkStats',
            'globalPorts',
            'quoteLocations',
            'mode',
            'search'
        ));
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

        $searchOptions = Shipment::query()
            ->when($user && $user->isCustomer(), fn($q) => $q->where('customer_id', $user->id))
            ->with(['origin', 'destination', 'carrier'])
            ->select(['id', 'shipment_number', 'cargo_type', 'vessel_or_flight', 'current_status', 'transport_type', 'origin_id', 'destination_id', 'carrier_id'])
            ->latest('id')
            ->take(30)
            ->get();

        return view('shipments.index', compact('shipments', 'carriers', 'searchOptions'));
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
            'vessel_or_flight' => 'nullable|string|max:120',
            'container_or_awb' => 'nullable|string|max:100',
            'receiver_name' => 'nullable|string|max:150',
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
                ->with('success', __('ui.flash.shipment_created', ['number' => $shipment->shipment_number]));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', __('ui.flash.shipment_create_err', ['msg' => $e->getMessage()]));
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
        $resolved = $this->resolvePublicShipment($request);
        if (!$resolved instanceof Shipment) {
            return $resolved;
        }
        $shipment = $resolved;

        $weather = null;
        if ($shipment->current_latitude && $shipment->current_longitude) {
            $weather = $this->weatherService->getWeather($shipment->current_latitude, $shipment->current_longitude);
        }

        $originWeather = $this->weatherService->getWeather($shipment->origin->latitude, $shipment->origin->longitude);
        $destWeather = $this->weatherService->getWeather($shipment->destination->latitude, $shipment->destination->longitude);

        return view('public.track', compact('shipment', 'weather', 'originWeather', 'destWeather'));
    }

    /**
     * Render a printable Electronic Bill of Lading / Official Waybill for an authorized customer or staff.
     * Public guests are redirected to login because a Bill of Lading is a legally binding title document.
     */
    public function publicWaybill(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))
                ->with('warning', __('ui.waybill.auth_required'));
        }

        $resolved = $this->resolvePublicShipment($request);
        if (!$resolved instanceof Shipment) {
            return $resolved;
        }

        $user = Auth::user();
        if ($user->isCustomer() && $resolved->customer_id !== $user->id) {
            abort(403, __('ui.waybill.unauthorized_customer'));
        }

        return view('public.waybill', ['shipment' => $resolved]);
    }

    /**
     * Resolve shipment by tracking number for public tracking or printable waybill views.
     */
    private function resolvePublicShipment(Request $request): Shipment|\Illuminate\Http\RedirectResponse
    {
        $number = trim((string) $request->input('number', ''));
        if ($number === '') {
            return redirect()->route('home')->with('error', __('ui.flash.enter_number'));
        }

        $shipment = Shipment::with([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'exceptions',
        ])->where('shipment_number', $number)->first();

        if (!$shipment) {
            return redirect()->route('home')->with('error', __('ui.flash.not_found', ['number' => $number]));
        }

        return $shipment;
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can delete shipments.');
        }

        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', __('ui.flash.shipment_deleted'));
    }
}

