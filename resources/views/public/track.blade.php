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
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Carrier: <strong class="text-slate-700">{{ $shipment->carrier->name ?? 'N/A' }}</strong> ({{ $shipment->transport_type }}) • Cargo: <strong class="text-slate-700">{{ $shipment->cargo_type }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Mode:</span>
            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-200 text-slate-700">
                @if($shipment->transport_type === 'SEA') 🚢 Global Sea Freight
                @elseif($shipment->transport_type === 'AIR') ✈️ International Air Cargo
                @else 🚛 Overland Logistics @endif
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
                    
                    <!-- Live Time at Origin -->
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
                    
                    <!-- Live Time at Destination -->
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
        <!-- Interactive Leaflet Map Container -->
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

            <!-- Cargo Spec Card -->
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
                    <span class="text-slate-500">Commodity:</span>
                    <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
                </div>
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
                        <span class="text-xs font-mono text-slate-400">{{ $event->event_time->format('d M Y, H:i') }} UTC</span>
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
        // Ticking live clocks for local port timezones
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

        // Leaflet Global Map
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};
        const currentLat = {{ $shipment->current_latitude ?? $shipment->origin->latitude }};
        const currentLng = {{ $shipment->current_longitude ?? $shipment->origin->longitude }};

        const map = L.map('shipmentMap').setView([currentLat, currentLng], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Custom origin icon
        L.marker([originLat, originLng]).addTo(map)
            .bindPopup('<b>🛫 Origin:</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        // Custom destination icon
        L.marker([destLat, destLng]).addTo(map)
            .bindPopup('<b>🛬 Destination:</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}');

        // Vessel pulsating circle marker
        const currentMarker = L.circleMarker([currentLat, currentLng], {
            color: '{{ $shipment->is_delayed ? "#f59e0b" : "#2563eb" }}',
            fillColor: '{{ $shipment->is_delayed ? "#f59e0b" : "#3b82f6" }}',
            fillOpacity: 0.9,
            radius: 9
        }).addTo(map)
        .bindPopup('<b>🚢 Live Position:</b> {{ $shipment->current_location_name ?? "In Transit" }}<br>Status: {{ $shipment->current_status }}<br>Carrier: {{ $shipment->carrier->name }}')
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
            color: '#2563eb',
            weight: 3.5,
            dashArray: '6, 8',
            opacity: 0.8
        }).addTo(map);

        map.fitBounds(L.latLngBounds(pathCoords), { padding: [50, 50] });
    });
</script>
@endpush
