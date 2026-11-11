@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-ocean-100 text-slate-500 hover:text-ocean-700 flex items-center justify-center transition">
                    <i class="ph ph-arrow-left text-lg"></i>
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono flex items-center gap-2">
                    <span>{{ $shipment->shipment_number }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $shipment->shipment_number }}'); alert('Nomor resi/kargo disalin!');" title="Salin Nomor Resi" class="text-xs text-slate-400 hover:text-ocean-600 transition p-1">
                        <i class="ph ph-copy text-lg"></i>
                    </button>
                </h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($shipment->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800 border border-emerald-300
                    @elseif($shipment->current_status === 'IN_TRANSIT') bg-ocean-100 text-ocean-900 border border-ocean-300
                    @elseif($shipment->current_status === 'CANCELLED') bg-rose-100 text-rose-800 border border-rose-300
                    @else bg-gold-100 text-gold-900 border border-gold-300 @endif">
                    {{ str_replace('_', ' ', $shipment->current_status) }}
                </span>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Carrier: <strong class="text-slate-800">{{ $shipment->carrier->name ?? 'N/A' }}</strong> • Komoditas: <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Moda Transportasi:</span>
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-ocean-50 text-ocean-900 border border-ocean-200/80 flex items-center gap-1.5 shadow-2xs">
                @if($shipment->transport_type === 'SEA') 🚢 Kapal Kontainer Laut (Global Sea Freight)
                @elseif($shipment->transport_type === 'AIR') ✈️ Pesawat Kargo Udara (International Air Cargo)
                @else 🚛 Angkutan Darat (Overland Logistics) @endif
            </span>
        </div>
    </div>

    <!-- Delay Notification Banner if Delayed -->
    @if($shipment->is_delayed)
    <div class="p-4 rounded-2xl bg-gold-50 border border-gold-300 text-gold-900 flex items-start gap-3 shadow-xs">
        <i class="ph ph-warning-circle text-2xl text-gold-600 mt-0.5 shrink-0"></i>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-sm">Pemberitahuan Keterlambatan (+{{ $shipment->delay_hours }} Jam)</h3>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-gold-200 text-gold-900 border border-gold-300">
                    Tingkat: {{ $shipment->delay_severity }}
                </span>
            </div>
            <p class="text-xs text-gold-800 mt-1">
                {{ $shipment->delay_reason ?? 'Perkiraan durasi transit melebihi jadwal kedatangan semula.' }}
            </p>
        </div>
    </div>
    @endif

    <!-- LIVE JOURNEY TIME, MID-ROUTE POSITION & ETA COUNTDOWN BOARD (Oceanic Teal & Gold Edition) -->
    <div class="bg-gradient-to-br from-[#042f2e] via-[#093f3d] to-[#042f2e] text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-ocean-700/60 relative overflow-hidden">
        <!-- Subtle nautical wave accent background -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-ocean-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header of Board -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-ocean-800/80 gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-400 animate-ping"></span>
                    <span class="text-xs uppercase tracking-widest text-gold-400 font-bold">Live Global Tracking Terminal</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight mt-1 flex items-center gap-2">
                    <i class="ph ph-compass text-gold-400"></i>
                    <span>Papan Pantau Perjalanan Kargo & Waktu Aktif Dunia</span>
                </h2>
            </div>
            <div class="flex items-center gap-3 bg-white/10 px-4 py-2.5 rounded-2xl border border-white/15 backdrop-blur-md shadow-inner">
                <i class="ph ph-clock text-2xl text-gold-400"></i>
                <div>
                    <span class="text-[10px] uppercase font-bold text-ocean-200 block leading-tight">Waktu Kamu Melihat Sekarang:</span>
                    <span id="user-current-clock" class="text-sm font-mono font-bold text-white">--:--:-- WIB</span>
                </div>
            </div>
        </div>

        <!-- 3-Phase Journey Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6 relative z-10">
            <!-- 1. Departure (Titik Awal Berangkat) -->
            <div class="bg-white/5 border border-white/10 hover:border-ocean-400/40 transition rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-ocean-300 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-anchor text-base text-gold-400"></i> 1. TITIK ASAL BERANGKAT</span>
                        <span class="px-2 py-0.5 rounded bg-ocean-500/20 text-ocean-200 border border-ocean-500/30 text-[10px]">DEPARTED</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->origin->name }}</h3>
                    <p class="text-xs text-ocean-200">{{ $shipment->origin->city }}, {{ $shipment->origin->country }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-ocean-200/80">Jadwal Berangkat:</span>
                            <span class="font-mono text-white font-semibold">{{ $shipment->expected_departure->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-ocean-200/80">Jam di Pelabuhan Asal Sekarang:</span>
                            <span class="font-mono text-gold-300 font-bold live-port-clock" data-tz="{{ $shipment->origin->timezone }}">--:--:--</span>
                        </div>
                    </div>
                </div>

                <!-- Weather at origin -->
                @if($originWeather && $originWeather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-black/20 p-2.5 rounded-xl border border-white/5">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-sun text-gold-400 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-ocean-200 block leading-tight">Cuaca di Pelabuhan Asal:</span>
                            <span class="font-semibold text-white">{{ $originWeather['condition'] }}</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-white font-mono">{{ $originWeather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>

            <!-- 2. Mid-Transit Coordinate (Posisi Kapal di Tengah Jalan) -->
            <div class="bg-gradient-to-b from-ocean-900/60 to-ocean-950/80 border-2 border-gold-500/50 rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between shadow-xl">
                <div class="absolute top-0 right-0 w-28 h-28 bg-gold-500/10 rounded-full blur-2xl"></div>
                <div>
                    <div class="flex items-center justify-between text-xs text-gold-400 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-boat text-base"></i> 2. DI PERTENGAHAN JALAN</span>
                        <span class="px-2 py-0.5 rounded bg-gold-500/20 text-gold-300 border border-gold-500/30 text-[10px] animate-pulse">ACTIVE AT SEA</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->current_location_name ?? 'In Transit Ocean Route' }}</h3>
                    <p class="text-xs text-ocean-200">Koordinat: {{ number_format($shipment->current_latitude, 4) }}, {{ number_format($shipment->current_longitude, 4) }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-ocean-200">Jam di Posisi Kapal Saat Ini:</span>
                            <span class="font-mono text-gold-300 text-sm font-bold live-port-clock" data-tz="{{ $shipment->carrier->type === 'AIR' ? 'Asia/Singapore' : 'Asia/Singapore' }}">--:--:--</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ocean-200">Status Operasional:</span>
                            <span class="font-semibold text-white">{{ str_replace('_', ' ', $shipment->current_status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Weather at sea -->
                @if($weather && $weather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-black/30 p-2.5 rounded-xl border border-gold-500/20">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-wind text-ocean-300 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-ocean-200 block leading-tight">Cuaca & Angin Maritim:</span>
                            <span class="font-semibold text-white">{{ $weather['condition'] }} • Angin {{ $weather['wind_speed_kmh'] }} km/h</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-gold-400 font-mono">{{ $weather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>

            <!-- 3. Destination & Live ETA Countdown (Berapa Jam Lagi Sampai?) -->
            <div class="bg-white/5 border border-white/10 hover:border-emerald-400/40 transition rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-emerald-400 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-flag-checkered text-base"></i> 3. TUJUAN KEDATANGAN</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px]">DESTINATION</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->destination->name }}</h3>
                    <p class="text-xs text-ocean-200">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>

                    <!-- LIVE COUNTDOWN BOX -->
                    <div class="mt-4 p-3.5 rounded-2xl bg-gradient-to-r from-emerald-950/70 to-slate-950 border border-emerald-500/40 text-center shadow-inner">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">
                            ⏱️ Estimasi Sampai (Hitung Mundur):
                        </span>
                        <div id="eta-countdown" class="text-base sm:text-lg font-mono font-black text-white mt-1">
                            Menghitung...
                        </div>
                        <span class="text-[10px] text-ocean-300/80 block mt-0.5">
                            Target: {{ $shipment->estimated_arrival->format('d M Y, H:i') }} (Waktu Lokal)
                        </span>
                    </div>

                    <div class="mt-3 pt-2 border-t border-white/10 flex justify-between text-xs">
                        <span class="text-ocean-200/80">Jam di Kota Tujuan Sekarang:</span>
                        <span class="font-mono text-emerald-400 font-bold live-port-clock" data-tz="{{ $shipment->destination->timezone }}">--:--:--</span>
                    </div>
                </div>

                <!-- Weather at destination -->
                @if($destWeather && $destWeather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-black/20 p-2.5 rounded-xl border border-white/5">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-cloud-sun text-emerald-300 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-ocean-200 block leading-tight">Cuaca di Tujuan (Eropa/Global):</span>
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
        <!-- Interactive Leaflet Map with Layer Switcher -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="ph ph-navigation-arrow text-ocean-700 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Peta Navigasi Spasial GIS (Rute Maritim & Udara Dunia)</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold text-ocean-800 bg-ocean-100 px-2 py-0.5 rounded-md border border-ocean-200">
                        Pilihan Lapisan Peta Tersedia (Satelit / Radar / OSM)
                    </span>
                </div>
            </div>
            <div id="shipmentMap" class="w-full h-[450px] bg-slate-900 z-10"></div>
            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500 flex flex-wrap items-center justify-between gap-2">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-gold-500 inline-block"></span> <strong>Kuning Emas:</strong> Posisi Real-time Kapal/Pesawat
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-ocean-600 inline-block border-t border-dashed"></span> <strong>Garis Putus-Putus Teal:</strong> Jalur Pelayaran / Koridor Udara
                </span>
                <span class="text-slate-400">Gunakan tombol kanan atas peta untuk mengatur Lapisan Peta &amp; Label Wilayah</span>
            </div>
        </div>

        <!-- Cargo Manifest Specs -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="ph ph-package text-ocean-700"></i>
                    <span>Spesifikasi & Manifest</span>
                </h4>
                <span class="font-mono text-[10px] text-slate-400">ISO 6346 COMPLIANT</span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Total Berat Kotor:</span>
                    <strong class="text-slate-900 font-mono text-sm">{{ number_format($shipment->total_weight_kg, 2) }} kg</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Volume Muatan (CBM):</span>
                    <strong class="text-slate-900 font-mono text-sm">{{ $shipment->total_volume_cbm ?? 'N/A' }} m³</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Komoditas Utama:</span>
                    <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Armada / Carrier:</span>
                    <strong class="text-ocean-800 font-bold">{{ $shipment->carrier->name }}</strong>
                </div>
            </div>

            @if($shipment->items->count() > 0)
            <div class="pt-3 border-t border-slate-100">
                <span class="text-slate-500 block mb-2 font-semibold">Manifest Rinci:</span>
                <ul class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    @foreach($shipment->items as $item)
                    <li class="bg-slate-50 hover:bg-ocean-50/50 transition p-2.5 rounded-xl border border-slate-100 flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-slate-800 block">{{ $item->item_name }} (x{{ $item->quantity }})</span>
                            <span class="text-[10px] text-slate-400">HS Code: {{ $item->hs_code ?? '8542.31' }}</span>
                        </div>
                        <span class="font-mono text-ocean-900 font-bold">{{ $item->weight_kg }} kg</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Chronological Journey Timeline -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-base mb-6 flex items-center gap-2">
            <i class="ph ph-clock-counter-clockwise text-ocean-700 text-xl"></i>
            <span>Riwayat Perjalanan Kronologis (Checkpoint Log & Waypoints)</span>
        </h3>

        <div class="relative border-l-2 border-ocean-200 ml-4 space-y-6 pb-4">
            @forelse($shipment->trackingEvents as $event)
            <div class="relative pl-6">
                <!-- Dot icon -->
                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 
                    @if($event->is_exception) bg-rose-500 border-rose-200 animate-pulse
                    @elseif($loop->last) bg-gold-500 border-gold-200 ring-4 ring-gold-100
                    @else bg-ocean-600 border-ocean-100 @endif">
                </div>

                <div class="bg-slate-50 hover:bg-white hover:border-ocean-300 transition p-4 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm">{{ $event->location_name }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase tracking-wide
                                @if($event->is_exception) bg-rose-100 text-rose-700 border border-rose-200
                                @else bg-ocean-100 text-ocean-800 border border-ocean-200 @endif">
                                {{ str_replace('_', ' ', $event->event_status) }}
                            </span>
                        </div>
                        <span class="text-xs font-mono text-slate-500">{{ $event->event_time->format('d M Y, H:i') }} UTC</span>
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

        // 4. Leaflet Map with Layer Switcher (ArcGIS Satellite, CartoDB Dark, OpenStreetMap)
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};
        const currentLat = {{ $shipment->current_latitude ?? $shipment->origin->latitude }};
        const currentLng = {{ $shipment->current_longitude ?? $shipment->origin->longitude }};

        // Define Tile Layers
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: '&copy; Esri, Maxar, Earthstar Geographics'
        });

        const labelsOverlay = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            zIndex: 10,
            attribution: '&copy; Esri Boundaries & Places'
        });

        const darkRadarLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; CARTO &copy; OpenStreetMap'
        });

        // Initialize Map with Satellite Layer + Country/Region Labels as default
        const map = L.map('shipmentMap', {
            center: [currentLat, currentLng],
            zoom: 3,
            layers: [satelliteLayer, labelsOverlay]
        });

        // Layer Switcher Control
        const baseMaps = {
            "🛰️ Citra Satelit + Nama Wilayah": satelliteLayer,
            "🌙 Radar Laut Malam (Dark)": darkRadarLayer,
            "🗺️ Peta Navigasi Standar (OSM)": osmLayer
        };
        const overlays = {
            "🏷️ Label Negara, Kota & Batas": labelsOverlay
        };
        L.control.layers(baseMaps, overlays, { position: 'topright' }).addTo(map);

        map.on('baselayerchange', function (e) {
            if (e.layer === osmLayer) {
                if (map.hasLayer(labelsOverlay)) map.removeLayer(labelsOverlay);
            } else {
                if (!map.hasLayer(labelsOverlay)) labelsOverlay.addTo(map);
            }
        });

        // Origin Marker (Departure Port)
        L.marker([originLat, originLng]).addTo(map)
            .bindTooltip('🛫 Asal: {{ $shipment->origin->city }} ({{ $shipment->origin->code }})', {
                permanent: true,
                direction: 'top',
                offset: [0, -32]
            })
            .bindPopup('<b>🛫 Titik Asal:</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        // Destination Marker (Arrival Port)
        L.marker([destLat, destLng]).addTo(map)
            .bindTooltip('🛬 Tujuan: {{ $shipment->destination->city }} ({{ $shipment->destination->code }})', {
                permanent: true,
                direction: 'bottom',
                offset: [0, 6]
            })
            .bindPopup('<b>🛬 Titik Tujuan:</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}');

        // Vessel Current Position Marker with Gold/Teal pulse
        const vesselMarker = L.circleMarker([currentLat, currentLng], {
            color: '#f59e0b',
            fillColor: '{{ $shipment->is_delayed ? "#f59e0b" : "#14b8a6" }}',
            fillOpacity: 0.95,
            weight: 3,
            radius: 10
        }).addTo(map)
        .bindPopup('<b>🚢 Posisi Kapal / Pesawat:</b><br>{{ $shipment->current_location_name ?? "In Transit" }}<br>Status: {{ $shipment->current_status }}')
        .openPopup();

        // Polyline connecting checkpoints and destination
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
            color: '#2dd4bf',
            weight: 4,
            dashArray: '8, 8',
            opacity: 0.85
        }).addTo(map);

        map.fitBounds(L.latLngBounds(pathCoords), { padding: [50, 50] });
    });
</script>
@endpush
