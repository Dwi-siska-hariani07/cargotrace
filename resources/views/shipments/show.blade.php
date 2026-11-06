@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight font-mono">
                        {{ $shipment->shipment_number }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($shipment->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($shipment->current_status === 'IN_TRANSIT') bg-blue-100 text-blue-800 border border-blue-300
                        @elseif($shipment->current_status === 'CANCELLED') bg-rose-100 text-rose-800 border border-rose-300
                        @else bg-amber-100 text-amber-800 border border-amber-300 @endif">
                        {{ str_replace('_', ' ', $shipment->current_status) }}
                    </span>
                </div>
                <p class="text-slate-500 text-xs mt-1">
                    Customer: <strong class="text-slate-700">{{ $shipment->customer->name ?? 'N/A' }}</strong> ({{ $shipment->customer->company }}) • Carrier: <strong class="text-slate-700">{{ $shipment->carrier->name }}</strong>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('public.track', ['number' => $shipment->shipment_number]) }}" target="_blank"
               class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i class="ph ph-arrow-square-out text-sm"></i> Public View
            </a>
        </div>
    </div>

    <!-- Delay Notification Banner if Delayed -->
    @if($shipment->is_delayed)
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3 shadow-sm">
        <i class="ph ph-warning-circle text-2xl text-amber-600 mt-0.5"></i>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-sm">Shipment Delayed (+{{ $shipment->delay_hours }} Hours)</h3>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-200 text-amber-800">
                    Severity: {{ $shipment->delay_severity }}
                </span>
            </div>
            <p class="text-xs text-amber-800 mt-1">
                {{ $shipment->delay_reason ?? 'Estimated transit duration exceeds scheduled arrival time.' }}
            </p>
        </div>
    </div>
    @endif

    <!-- World Route Bar with Multi-Timezone & Live Weather Badges -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <!-- Origin Hub -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-sm shrink-0">
                    <i class="ph ph-anchor"></i>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Origin Hub</span>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">{{ $shipment->origin->name }}</h4>
                    <p class="text-xs text-slate-500">{{ $shipment->origin->city }}, {{ $shipment->origin->country }}</p>
                    
                    <!-- Live Time & Weather at Origin -->
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-slate-700 flex items-center gap-1">
                            <i class="ph ph-clock text-xs text-slate-400"></i>
                            <span class="live-tz-clock" data-tz="{{ $shipment->origin->timezone }}">--:--:--</span>
                            <span class="text-[9px] text-slate-400">{{ $shipment->origin->timezone }}</span>
                        </span>
                        @if($originWeather && $originWeather['available'])
                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold flex items-center gap-1">
                            <i class="ph ph-sun text-xs text-amber-500"></i>
                            {{ $originWeather['temperature_c'] }}°C ({{ $originWeather['condition'] }})
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current Transit Position -->
            <div class="text-center px-4 py-3 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Current Ocean / Air Coordinate</span>
                <p class="font-bold text-slate-900 text-sm mt-0.5">
                    {{ $shipment->current_location_name ?? 'In Transit' }}
                </p>
                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden my-2">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500
                        @if($shipment->current_status === 'DELIVERED') w-full bg-emerald-500
                        @elseif($shipment->current_status === 'IN_TRANSIT') w-2/3
                        @elseif($shipment->current_status === 'PICKED_UP') w-1/3
                        @else w-1/6 @endif">
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>{{ str_replace('_', ' ', $shipment->current_status) }}</span>
                    @if($weather && $weather['available'])
                    <span class="text-blue-600 font-semibold">{{ $weather['temperature_c'] }}°C • Wind {{ $weather['wind_speed_kmh'] }} km/h</span>
                    @endif
                </div>
            </div>

            <!-- Destination Hub -->
            <div class="flex items-start gap-4 md:justify-end">
                <div class="md:text-right">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Destination Hub</span>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">{{ $shipment->destination->name }}</h4>
                    <p class="text-xs text-slate-500">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>
                    
                    <!-- Live Time & Weather at Destination -->
                    <div class="mt-2 flex flex-wrap items-center md:justify-end gap-2 text-[11px]">
                        @if($destWeather && $destWeather['available'])
                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold flex items-center gap-1">
                            <i class="ph ph-cloud text-xs text-blue-500"></i>
                            {{ $destWeather['temperature_c'] }}°C ({{ $destWeather['condition'] }})
                        </span>
                        @endif
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-slate-700 flex items-center gap-1">
                            <i class="ph ph-clock text-xs text-slate-400"></i>
                            <span class="live-tz-clock" data-tz="{{ $shipment->destination->timezone }}">--:--:--</span>
                            <span class="text-[9px] text-slate-400">{{ $shipment->destination->timezone }}</span>
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-sm shrink-0">
                    <i class="ph ph-flag-checkered"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & 3-Hub Atmospheric Weather Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Leaflet Map -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ph ph-map-trifold text-blue-600 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Global Maritime GIS Tracking Map</h3>
                </div>
                <span class="text-[11px] text-slate-400 font-mono">Real-Time Coordinates Plotting</span>
            </div>
            <div id="shipmentMap" class="w-full h-[420px] bg-slate-100"></div>
        </div>

        <!-- Atmospheric Weather Comparison Matrix -->
        <div class="space-y-4">
            <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white p-5 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-3 border-b border-blue-900/60 pb-2">
                    <span class="text-xs uppercase tracking-wider text-blue-300 font-bold flex items-center gap-1.5">
                        <i class="ph ph-broadcast text-emerald-400 animate-pulse text-base"></i> Live Multi-Port Weather (API)
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-blue-800 text-blue-200 font-mono">Open-Meteo</span>
                </div>

                <!-- 1. Origin Weather -->
                <div class="mb-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="flex items-center justify-between text-xs text-blue-200 mb-1">
                        <span class="font-bold text-white">🛫 {{ $shipment->origin->city }}</span>
                        <span>Origin</span>
                    </div>
                    @if($originWeather && $originWeather['available'])
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-bold">{{ $originWeather['temperature_c'] }}°C</span>
                        <span class="text-xs text-blue-300">{{ $originWeather['condition'] }}</span>
                        <span class="text-[11px] text-slate-400">Wind: {{ $originWeather['wind_speed_kmh'] }} km/h</span>
                    </div>
                    @else
                    <span class="text-xs text-slate-400">Weather data offline</span>
                    @endif
                </div>

                <!-- 2. Mid-Transit Coordinate Weather -->
                <div class="mb-3 p-3 rounded-xl bg-blue-600/20 border border-blue-500/30">
                    <div class="flex items-center justify-between text-xs text-blue-200 mb-1">
                        <span class="font-bold text-white">🚢 At Sea Position</span>
                        <span class="text-blue-300">Mid-Voyage</span>
                    </div>
                    @if($weather && $weather['available'])
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-bold text-amber-300">{{ $weather['temperature_c'] }}°C</span>
                        <span class="text-xs text-white">{{ $weather['condition'] }}</span>
                        <span class="text-[11px] text-blue-200">Wind: {{ $weather['wind_speed_kmh'] }} km/h</span>
                    </div>
                    @else
                    <span class="text-xs text-slate-400">Vessel weather offline</span>
                    @endif
                </div>

                <!-- 3. Destination Weather -->
                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="flex items-center justify-between text-xs text-blue-200 mb-1">
                        <span class="font-bold text-white">🛬 {{ $shipment->destination->city }}</span>
                        <span>Destination</span>
                    </div>
                    @if($destWeather && $destWeather['available'])
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-bold">{{ $destWeather['temperature_c'] }}°C</span>
                        <span class="text-xs text-blue-300">{{ $destWeather['condition'] }}</span>
                        <span class="text-[11px] text-slate-400">Wind: {{ $destWeather['wind_speed_kmh'] }} km/h</span>
                    </div>
                    @else
                    <span class="text-xs text-slate-400">Weather data offline</span>
                    @endif
                </div>
            </div>

            <!-- Cargo Spec -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-xs space-y-3">
                <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Manifest Details</h4>
                <div class="flex justify-between">
                    <span class="text-slate-500">Weight:</span>
                    <strong class="text-slate-800 font-mono">{{ number_format($shipment->total_weight_kg, 2) }} kg</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Volume:</span>
                    <strong class="text-slate-800 font-mono">{{ $shipment->total_volume_cbm ?? 'N/A' }} m³</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Cargo Type:</span>
                    <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
                </div>
                @if($shipment->items->count() > 0)
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 block mb-1">Item Manifest:</span>
                    <ul class="space-y-1">
                        @foreach($shipment->items as $item)
                        <li class="bg-slate-50 p-2 rounded border border-slate-100 flex justify-between">
                            <span>{{ $item->item_name }} (x{{ $item->quantity }})</span>
                            <span class="font-mono text-slate-500">{{ $item->weight_kg }} kg</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Exceptions / Incident Reports -->
    @if($shipment->exceptions->count() > 0)
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
            <i class="ph ph-warning-octagon text-rose-600 text-lg"></i>
            Operational Disruptions & Exceptions Log
        </h3>
        <div class="space-y-3">
            @foreach($shipment->exceptions as $ex)
            <div class="p-4 rounded-xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
                @if($ex->status === 'RESOLVED') bg-slate-50 border-slate-200 text-slate-600
                @else bg-rose-50 border-rose-200 text-rose-900 @endif">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono font-bold text-sm">{{ $ex->exception_type }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                            @if($ex->severity === 'HIGH') bg-rose-200 text-rose-800
                            @elseif($ex->severity === 'MEDIUM') bg-amber-200 text-amber-800
                            @else bg-blue-200 text-blue-800 @endif">
                            Severity: {{ $ex->severity }}
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                            @if($ex->status === 'RESOLVED') bg-emerald-200 text-emerald-800
                            @else bg-rose-200 text-rose-800 @endif">
                            {{ $ex->status }}
                        </span>
                    </div>
                    <p class="text-xs mt-1">{{ $ex->description }}</p>
                    @if($ex->resolution_notes)
                    <p class="text-xs text-emerald-700 font-medium mt-1">
                        Resolved: {{ $ex->resolution_notes }} (by {{ $ex->resolver->name ?? 'Staff' }})
                    </p>
                    @endif
                </div>

                @if(!auth()->user()->isCustomer() && $ex->status !== 'RESOLVED')
                <div>
                    <form method="POST" action="{{ route('exceptions.resolve', $ex->id) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="resolution_notes" placeholder="Resolution notes..." required
                               class="px-3 py-1.5 text-xs rounded-lg border border-slate-300">
                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                            Resolve
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Operator Operational Actions Section (Slide 05 & 07) -->
    @if(!auth()->user()->isCustomer() && $shipment->current_status !== 'DELIVERED' && $shipment->current_status !== 'CANCELLED')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form 1: Add Tracking Event / Update Status -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm mb-4 flex items-center gap-2">
                <i class="ph ph-map-pin-line text-blue-600 text-lg"></i>
                Log Tracking Checkpoint
            </h3>

            <form method="POST" action="{{ route('tracking.store', $shipment->id) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Checkpoint Location Name *</label>
                        <input type="text" name="location_name" placeholder="e.g. Malacca Strait Anchorage" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Preset Hub (Optional)</label>
                        <select name="location_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="">Custom Coordinates</option>
                            @foreach($allLocations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Latitude</label>
                        <input type="number" step="0.0000001" name="latitude" placeholder="e.g. 1.3521"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" placeholder="e.g. 103.8198"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Event Type / Milestone</label>
                        <select name="event_status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="CHECKPOINT">WAYPOINT_CHECKPOINT</option>
                            <option value="BERTH_ARRIVAL">PORT_ARRIVAL</option>
                            <option value="CUSTOMS_CLEARED">CUSTOMS_CLEARED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Advance Status (State Machine)</label>
                        <select name="new_status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="">Keep ({{ $shipment->current_status }})</option>
                            <option value="BOOKED">BOOKED</option>
                            <option value="PICKED_UP">PICKED_UP</option>
                            <option value="IN_TRANSIT">IN_TRANSIT</option>
                            <option value="OUT_FOR_DELIVERY">OUT_FOR_DELIVERY</option>
                            <option value="DELIVERED">DELIVERED (Terminal)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Description / Log Note *</label>
                    <textarea name="description" rows="2" placeholder="Vessel cleared checkpoint with normal cruising speed." required
                              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition">
                    Save Checkpoint
                </button>
            </form>
        </div>

        <!-- Form 2: Report Disruption / Exception -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm mb-4 flex items-center gap-2">
                <i class="ph ph-warning-diamond text-amber-600 text-lg"></i>
                Report Disruption / Exception (Rule Trigger)
            </h3>

            <form method="POST" action="{{ route('exceptions.store', $shipment->id) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Exception Category *</label>
                        <select name="exception_type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="PORT_CONGESTION">PORT_CONGESTION</option>
                            <option value="WEATHER_DELAY">WEATHER_DELAY</option>
                            <option value="CUSTOMS_HOLD">CUSTOMS_HOLD</option>
                            <option value="DAMAGED">DAMAGED</option>
                            <option value="MISROUTED">MISROUTED</option>
                            <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Severity *</label>
                        <select name="severity" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="LOW">LOW (0 - 6 hrs)</option>
                            <option value="MEDIUM" selected>MEDIUM (6 - 24 hrs)</option>
                            <option value="HIGH">HIGH (> 24 hrs)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Delay Impact (Hours)</label>
                    <input type="number" name="delay_hours_impact" value="12" min="1"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Description / Disruption Detail *</label>
                    <textarea name="description" rows="2" placeholder="e.g. Typhoon swell forced vessel to anchor outside harbor channel." required
                              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition">
                    Report Disruption & Recalculate ETA
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Chronological Timeline -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-base mb-6 flex items-center gap-2">
            <i class="ph ph-clock-counter-clockwise text-blue-600"></i>
            Chronological Journey Timeline (Events Log)
        </h3>

        <div class="relative border-l-2 border-slate-200 ml-4 space-y-6 pb-4">
            @forelse($shipment->trackingEvents as $event)
            <div class="relative pl-6">
                <!-- Dot icon -->
                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 
                    @if($event->is_exception) bg-rose-500 border-rose-200 animate-pulse
                    @elseif($loop->last) bg-blue-600 border-blue-200
                    @else bg-slate-400 border-slate-100 @endif">
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm">{{ $event->location_name }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                                @if($event->is_exception) bg-rose-100 text-rose-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ str_replace('_', ' ', $event->event_status) }}
                            </span>
                        </div>
                        <span class="text-xs font-mono text-slate-400">{{ $event->event_time->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $event->description }}</p>
                    <div class="mt-2 text-[10px] text-slate-400">
                        Recorded by: {{ $event->creator->name ?? 'Operator' }}
                    </div>
                </div>
            </div>
            @empty
            <p class="text-slate-400 text-xs pl-4">No tracking checkpoints logged yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateTzClocks() {
            const now = new Date();
            document.querySelectorAll('.live-tz-clock').forEach(el => {
                const tz = el.getAttribute('data-tz');
                if (tz) {
                    try {
                        el.textContent = new Intl.DateTimeFormat('en-GB', {
                            timeZone: tz,
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        }).format(now);
                    } catch (e) {
                        el.textContent = '--:--:--';
                    }
                }
            });
        }
        updateTzClocks();
        setInterval(updateTzClocks, 1000);

        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};
        const currentLat = {{ $shipment->current_latitude ?? $shipment->origin->latitude }};
        const currentLng = {{ $shipment->current_longitude ?? $shipment->origin->longitude }};

        const map = L.map('shipmentMap').setView([currentLat, currentLng], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([originLat, originLng]).addTo(map)
            .bindPopup('<b>Origin:</b> {{ $shipment->origin->name }}');

        L.marker([destLat, destLng]).addTo(map)
            .bindPopup('<b>Destination:</b> {{ $shipment->destination->name }}');

        L.circleMarker([currentLat, currentLng], {
            color: '{{ $shipment->is_delayed ? "#f59e0b" : "#2563eb" }}',
            fillColor: '{{ $shipment->is_delayed ? "#f59e0b" : "#3b82f6" }}',
            fillOpacity: 0.9,
            radius: 8
        }).addTo(map)
        .bindPopup('<b>Current Position:</b> {{ $shipment->current_location_name ?? "In Transit" }}<br>Status: {{ $shipment->current_status }}')
        .openPopup();

        const pathCoords = [
            [originLat, originLng],
            @foreach($shipment->trackingEvents as $ev)
                @if($ev->latitude && $ev->longitude)
                    [{{ $ev->latitude }}, {{ $ev->longitude }}],
                @endif
            @endforeach
            [destLat, destLng]
        ];

        L.polyline(pathCoords, {
            color: '#3b82f6',
            weight: 3,
            dashArray: '6, 8',
            opacity: 0.7
        }).addTo(map);

        map.fitBounds(L.latLngBounds(pathCoords), { padding: [50, 50] });
    });
</script>
@endpush

