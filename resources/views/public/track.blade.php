@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Bar -->
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
                Carrier: <strong class="text-slate-700">{{ $shipment->carrier->name ?? 'N/A' }}</strong> • Komoditas: <strong class="text-slate-700">{{ $shipment->cargo_type }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Moda Transportasi:</span>
            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-200 text-slate-800 flex items-center gap-1.5 shadow-xs">
                @if($shipment->transport_type === 'SEA') 🚢 Kapal Kontainer Laut (Global Sea Freight)
                @elseif($shipment->transport_type === 'AIR') ✈️ Pesawat Kargo Udara (International Air Cargo)
                @else 🚛 Angkutan Darat (Overland Logistics) @endif
            </span>
        </div>
    </div>

    <!-- Delay Notification Banner if Delayed -->
    @if($shipment->is_delayed)
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3 shadow-sm">
        <i class="ph ph-warning-circle text-2xl text-amber-600 mt-0.5 shrink-0"></i>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-sm">Pemberitahuan Keterlambatan (+{{ $shipment->delay_hours }} Jam)</h3>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-200 text-amber-800">
                    Tingkat: {{ $shipment->delay_severity }}
                </span>
            </div>
            <p class="text-xs text-amber-800 mt-1">
                {{ $shipment->delay_reason ?? 'Perkiraan durasi transit melebihi jadwal kedatangan semula.' }}
            </p>
        </div>
    </div>
    @endif

    <!-- LIVE JOURNEY TIME, MID-ROUTE POSITION & ETA COUNTDOWN BOARD -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-700">
        <!-- Header of Board -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-700/80 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs uppercase tracking-widest text-emerald-400 font-bold">Live Journey Monitoring</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight mt-1">Papan Pantau Perjalanan Kargo & Waktu Aktif Dunia</h2>
            </div>
            <div class="flex items-center gap-3 bg-white/10 px-4 py-2 rounded-2xl border border-white/10 backdrop-blur-md">
                <i class="ph ph-user-circle text-2xl text-blue-400"></i>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block leading-tight">Waktu Kamu Melihat Sekarang:</span>
                    <span id="user-current-clock" class="text-sm font-mono font-bold text-white">--:--:-- WIB</span>
                </div>
            </div>
        </div>

        <!-- 3-Phase Journey Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- 1. Departure (Titik Awal Berangkat) -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-blue-300 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-anchor text-base"></i> 1. TITIK ASAL BERANGKAT</span>
                        <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 text-[10px]">DEPARTED</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->origin->name }}</h3>
                    <p class="text-xs text-slate-300">{{ $shipment->origin->city }}, {{ $shipment->origin->country }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Jadwal Berangkat:</span>
                            <span class="font-mono text-white font-semibold">{{ $shipment->expected_departure->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Jam di Pelabuhan Asal Sekarang:</span>
                            <span class="font-mono text-emerald-400 font-bold live-port-clock" data-tz="{{ $shipment->origin->timezone }}">--:--:--</span>
                        </div>
                    </div>
                </div>

                <!-- Weather at origin -->
                @if($originWeather && $originWeather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-white/5 p-2.5 rounded-xl">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-sun text-amber-400 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-slate-400 block leading-tight">Cuaca di Pelabuhan Asal:</span>
                            <span class="font-semibold text-white">{{ $originWeather['condition'] }}</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-white font-mono">{{ $originWeather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>

            <!-- 2. Mid-Transit Coordinate (Posisi Kapal di Tengah Jalan) -->
            <div class="bg-blue-900/30 border border-blue-500/40 rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between shadow-lg">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                <div>
                    <div class="flex items-center justify-between text-xs text-amber-400 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-boat text-base"></i> 2. DI PERTENGAHAN JALAN</span>
                        <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[10px] animate-pulse">ACTIVE AT SEA</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->current_location_name ?? 'In Transit Sea Route' }}</h3>
                    <p class="text-xs text-blue-200">Koordinat: {{ number_format($shipment->current_latitude, 4) }}, {{ number_format($shipment->current_longitude, 4) }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-300">Jam di Lokasi Kapal Saat Ini:</span>
                            <span class="font-mono text-amber-300 text-sm font-bold live-port-clock" data-tz="{{ $shipment->carrier->type === 'AIR' ? 'Asia/Singapore' : 'Asia/Singapore' }}">--:--:--</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Status Kargo:</span>
                            <span class="font-semibold text-white">{{ str_replace('_', ' ', $shipment->current_status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Weather at sea -->
                @if($weather && $weather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-blue-950/60 p-2.5 rounded-xl border border-blue-500/20">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-wind text-cyan-400 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-blue-200 block leading-tight">Cuaca di Posisi Kapal:</span>
                            <span class="font-semibold text-white">{{ $weather['condition'] }} • Angin {{ $weather['wind_speed_kmh'] }} km/h</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-amber-300 font-mono">{{ $weather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>

            <!-- 3. Destination & Live ETA Countdown (Berapa Jam Lagi Sampai?) -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-emerald-400 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-flag-checkered text-base"></i> 3. TUJUAN KEDATANGAN</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px]">DESTINATION</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->destination->name }}</h3>
                    <p class="text-xs text-slate-300">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>

                    <!-- LIVE COUNTDOWN BOX -->
                    <div class="mt-4 p-3 rounded-xl bg-gradient-to-r from-emerald-950/60 to-slate-900 border border-emerald-500/30 text-center">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">
                            ⏱️ Estimasi Sampai (Hitung Mundur):
                        </span>
                        <div id="eta-countdown" class="text-base sm:text-lg font-mono font-black text-white mt-1">
                            Menghitung...
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">
                            Target: {{ $shipment->estimated_arrival->format('d M Y, H:i') }} (Waktu Lokal)
                        </span>
                    </div>

                    <div class="mt-3 pt-2 border-t border-white/10 flex justify-between text-xs">
                        <span class="text-slate-400">Jam di Kota Tujuan Sekarang:</span>
                        <span class="font-mono text-emerald-400 font-bold live-port-clock" data-tz="{{ $shipment->destination->timezone }}">--:--:--</span>
                    </div>
                </div>

                <!-- Weather at destination -->
                @if($destWeather && $destWeather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-white/5 p-2.5 rounded-xl">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-cloud-fog text-blue-300 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-slate-400 block leading-tight">Cuaca di Tujuan (Eropa/Luar):</span>
                            <span class="font-semibold text-white">{{ $destWeather['condition'] }}</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-white font-mono">{{ $destWeather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Map & Manifest Specs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Leaflet Map -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ph ph-map-trifold text-blue-600 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Peta Navigasi Spasial GIS (Rute Kapal Dunia)</h3>
                </div>
                <span class="text-[11px] text-slate-400 font-mono">Leaflet.js • OpenStreetMap</span>
            </div>
            <div id="shipmentMap" class="w-full h-[400px] bg-slate-100"></div>
        </div>

        <!-- Cargo Manifest Specs -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-xs space-y-4">
            <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Spesifikasi Kargo</h4>
            <div class="flex justify-between">
                <span class="text-slate-500">Total Berat:</span>
                <strong class="text-slate-800 font-mono text-sm">{{ number_format($shipment->total_weight_kg, 2) }} kg</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Volume Kontainer (CBM):</span>
                <strong class="text-slate-800 font-mono text-sm">{{ $shipment->total_volume_cbm ?? 'N/A' }} m³</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Jenis Barang / Komoditas:</span>
                <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
            </div>
            @if($shipment->items->count() > 0)
            <div class="pt-3 border-t border-slate-100">
                <span class="text-slate-500 block mb-2 font-semibold">Manifest Barang:</span>
                <ul class="space-y-1.5">
                    @foreach($shipment->items as $item)
                    <li class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-slate-800 block">{{ $item->item_name }} (x{{ $item->quantity }})</span>
                            <span class="text-[10px] text-slate-400">HS Code: {{ $item->hs_code ?? '8542.31' }}</span>
                        </div>
                        <span class="font-mono text-slate-600 font-bold">{{ $item->weight_kg }} kg</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Chronological Journey Timeline -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-base mb-6 flex items-center gap-2">
            <i class="ph ph-clock-counter-clockwise text-blue-600 text-lg"></i>
            Riwayat Perjalanan Kronologis (Checkpoint Log)
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
                        <span class="text-xs font-mono text-slate-400">{{ $event->event_time->format('d M Y, H:i') }} UTC</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $event->description }}</p>
                </div>
            </div>
            @empty
            <p class="text-slate-400 text-xs pl-4">Belum ada catatan checkpoint perjalanan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. User Local Clock
        function updateUserClock() {
            const now = new Date();
            const el = document.getElementById('user-current-clock');
            if (el) {
                el.textContent = new Intl.DateTimeFormat('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).format(now) + ' WIB';
            }
        }
        updateUserClock();
        setInterval(updateUserClock, 1000);

        // 2. Port & Ship Clocks
        function updatePortClocks() {
            const now = new Date();
            document.querySelectorAll('.live-port-clock').forEach(el => {
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
        updatePortClocks();
        setInterval(updatePortClocks, 1000);

        // 3. Live Countdown to Estimated Arrival (Berapa Jam Lagi Sampai)
        const targetEta = new Date("{{ $shipment->estimated_arrival->toIso8601String() }}").getTime();
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetEta - now;
            const el = document.getElementById('eta-countdown');
            if (!el) return;

            if (distance < 0) {
                el.innerHTML = '<span class="text-emerald-400 font-bold">✅ TELAH TIBA DI TUJUAN</span>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            el.innerHTML = `${days} Hari ${hours} Jam ${minutes} Menit ${seconds} Detik`;
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // 4. Leaflet Map
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

        L.marker([originLat, originLng]).addTo(map)
            .bindPopup('<b>🛫 Asal:</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        L.marker([destLat, destLng]).addTo(map)
            .bindPopup('<b>🛬 Tujuan:</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}');

        L.circleMarker([currentLat, currentLng], {
            color: '{{ $shipment->is_delayed ? "#f59e0b" : "#2563eb" }}',
            fillColor: '{{ $shipment->is_delayed ? "#f59e0b" : "#3b82f6" }}',
            fillOpacity: 0.9,
            radius: 9
        }).addTo(map)
        .bindPopup('<b>🚢 Posisi Kapal di Laut:</b> {{ $shipment->current_location_name ?? "In Transit" }}<br>Status: {{ $shipment->current_status }}')
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
            color: '#2563eb',
            weight: 3.5,
            dashArray: '6, 8',
            opacity: 0.8
        }).addTo(map);

        map.fitBounds(L.latLngBounds(pathCoords), { padding: [50, 50] });
    });
</script>
@endpush
