@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Back & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="ph ph-arrow-left text-xl"></i>
                </a>
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
            <p class="text-slate-500 text-sm mt-1">
                Carrier: <strong class="text-slate-700">{{ $shipment->carrier->name ?? 'N/A' }}</strong> ({{ $shipment->transport_type }}) • Cargo: <strong class="text-slate-700">{{ $shipment->cargo_type }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Mode:</span>
            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-200 text-slate-700">
                @if($shipment->transport_type === 'SEA') 🚢 Sea Freight
                @elseif($shipment->transport_type === 'AIR') ✈️ Air Cargo
                @else 🚛 Land Transport @endif
            </span>
        </div>
    </div>

    <!-- Delay Notification Banner if Delayed -->
    @if($shipment->is_delayed)
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3 shadow-sm">
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

    <!-- Route Cards & Progress Bar -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <!-- Origin -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-sm">
                    <i class="ph ph-anchor"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Origin</span>
                    <h4 class="font-bold text-slate-800 text-base">{{ $shipment->origin->name }}</h4>
                    <p class="text-xs text-slate-500">{{ $shipment->origin->city }}, {{ $shipment->origin->country }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Departed: {{ $shipment->expected_departure->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <!-- Current Transit Position -->
            <div class="text-center px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-xs text-slate-400 uppercase font-semibold">Current Location</span>
                <p class="font-bold text-slate-800 text-sm mt-0.5">
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
                <span class="text-[11px] text-slate-500">
                    Status: <strong class="text-blue-600">{{ str_replace('_', ' ', $shipment->current_status) }}</strong>
                </span>
            </div>

            <!-- Destination -->
            <div class="flex items-center gap-4 md:justify-end">
                <div class="md:text-right">
                    <span class="text-xs text-slate-400 uppercase font-semibold">Destination</span>
                    <h4 class="font-bold text-slate-800 text-base">{{ $shipment->destination->name }}</h4>
                    <p class="text-xs text-slate-500">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">
                        ETA: <strong class="{{ $shipment->is_delayed ? 'text-amber-600 font-bold' : 'text-slate-600' }}">{{ $shipment->estimated_arrival->format('d M Y, H:i') }}</strong>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-sm">
                    <i class="ph ph-flag-checkered"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & Weather Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Leaflet Map Container -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ph ph-map-trifold text-blue-600 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Interactive Route & Location Map</h3>
                </div>
                <span class="text-[11px] text-slate-400">Leaflet.js • OpenStreetMap</span>
            </div>
            <div id="shipmentMap" class="w-full h-96 bg-slate-100"></div>
        </div>

        <!-- Weather & Details Widget -->
        <div class="space-y-6">
            <!-- Weather Card -->
            <div class="bg-gradient-to-br from-blue-900 to-slate-900 text-white p-6 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs uppercase tracking-wider text-blue-300 font-semibold flex items-center gap-1.5">
                        <i class="ph ph-cloud-sun text-base"></i> Atmospheric Conditions
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-blue-800/80 text-blue-200">Live API</span>
                </div>

                @if($weather && $weather['available'])
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-extrabold">{{ $weather['temperature_c'] }}°C</div>
                        <div class="text-sm text-blue-200 font-medium mt-0.5">{{ $weather['condition'] }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-800/50 flex items-center justify-center text-2xl text-blue-300">
                        <i class="ph ph-wind"></i>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-blue-800/60 grid grid-cols-2 gap-2 text-xs text-blue-200">
                    <div>Wind: <strong class="text-white">{{ $weather['wind_speed_kmh'] }} km/h</strong></div>
                    <div>Humidity: <strong class="text-white">{{ $weather['humidity_pct'] }}%</strong></div>
                </div>
                <div class="text-[10px] text-blue-400/80 mt-2">
                    Source: {{ $weather['source'] }}
                </div>
                @else
                <div class="text-sm text-blue-200">
                    Weather data unavailable for the current coordinates or station offline.
                </div>
                @endif
            </div>

            <!-- Shipment Cargo Details -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-xs space-y-3">
                <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Cargo Metrics</h4>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Weight:</span>
                    <strong class="text-slate-800">{{ number_format($shipment->total_weight_kg, 2) }} kg</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Volume (CBM):</span>
                    <strong class="text-slate-800">{{ $shipment->total_volume_cbm ?? 'N/A' }} m³</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Cargo Description:</span>
                    <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
                </div>
                @if($shipment->items->count() > 0)
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 block mb-1">Manifest Items:</span>
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

    <!-- Timeline of Tracking Events -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-lg mb-6 flex items-center gap-2">
            <i class="ph ph-clock-counter-clockwise text-blue-600"></i>
            Chronological Journey Timeline
        </h3>

        <div class="relative border-l-2 border-slate-200 ml-4 space-y-8 pb-4">
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
        // Prepare Leaflet map
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

        // Origin marker
        L.marker([originLat, originLng]).addTo(map)
            .bindPopup('<b>Origin:</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}');

        // Destination marker
        L.marker([destLat, destLng]).addTo(map)
            .bindPopup('<b>Destination:</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}');

        // Current Location marker (Custom Circle Highlight)
        const currentMarker = L.circleMarker([currentLat, currentLng], {
            color: '{{ $shipment->is_delayed ? "#f59e0b" : "#2563eb" }}',
            fillColor: '{{ $shipment->is_delayed ? "#f59e0b" : "#3b82f6" }}',
            fillOpacity: 0.9,
            radius: 8
        }).addTo(map)
        .bindPopup('<b>Current Position:</b> {{ $shipment->current_location_name ?? "In Transit" }}<br>Status: {{ $shipment->current_status }}')
        .openPopup();

        // Connect path with Polyline
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

        // Fit map bounds
        const bounds = L.latLngBounds(pathCoords);
        map.fitBounds(bounds, { padding: [50, 50] });
    });
</script>
@endpush

