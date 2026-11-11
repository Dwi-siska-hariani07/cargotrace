@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-ocean-100 text-ocean-900 border border-ocean-200">
                    Live Operations Center
                </span>
                <span class="text-xs text-slate-400">• {{ auth()->user()->company ?? auth()->user()->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1 flex items-center gap-2">
                <span>Operations Fleet & Shipment Dashboard</span>
            </h1>
        </div>

        @if(!auth()->user()->isCustomer())
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="ph ph-plus-circle text-lg text-gold-300"></i>
                <span>New Shipment Order</span>
            </a>
        </div>
        @endif
    </div>

    <!-- 5 KPI Stat Cards (Slide 10 Brief Requirements) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs hover:border-ocean-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Active</span>
                <div class="w-8 h-8 rounded-xl bg-ocean-50 text-ocean-700 flex items-center justify-center">
                    <i class="ph ph-package text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ $totalShipments }}</div>
            <span class="text-[11px] text-slate-400">Total tracked orders</span>
        </div>

        <!-- In Transit -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs hover:border-ocean-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">In Transit</span>
                <div class="w-8 h-8 rounded-xl bg-ocean-100 text-ocean-800 flex items-center justify-center">
                    <i class="ph ph-boat text-lg text-ocean-700"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-ocean-800 mt-2">{{ $inTransitCount }}</div>
            <span class="text-[11px] text-ocean-600 font-medium">On maritime & air routes</span>
        </div>

        <!-- Delivered -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs hover:border-emerald-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Delivered</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="ph ph-check-circle text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 mt-2">{{ $deliveredCount }}</div>
            <span class="text-[11px] text-slate-400">Terminal completed</span>
        </div>

        <!-- Delayed -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs hover:border-gold-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Delayed</span>
                <div class="w-8 h-8 rounded-xl bg-gold-50 text-gold-600 flex items-center justify-center">
                    <i class="ph ph-clock-countdown text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gold-600 mt-2">{{ $delayedCount }}</div>
            <span class="text-[11px] text-gold-700 font-semibold">{{ $delayRate }}% delay rate</span>
        </div>

        <!-- Exceptions -->
        <div class="col-span-2 lg:col-span-1 bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs hover:border-rose-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Exceptions</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="ph ph-warning text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-rose-600 mt-2">{{ $openExceptionsCount }}</div>
            <span class="text-[11px] text-rose-600 font-semibold">{{ $exceptionRate }}% incident rate</span>
        </div>
    </div>

    <!-- Global Map & Chart.js Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Global Interactive Overview Map with Layer Switcher -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="ph ph-globe-hemisphere-east text-ocean-700 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Global Fleet & Active Cargo Geo-Plotting</h3>
                </div>
                <span class="text-[11px] text-ocean-800 font-semibold bg-ocean-100 px-2 py-0.5 rounded-md border border-ocean-200">
                    Multi-Layer Satellite & Radar
                </span>
            </div>
            <div id="overviewMap" class="w-full h-84 bg-slate-900 z-10"></div>
            <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Klik armada untuk melihat nomor kargo dan status perjalanannya.</span>
                <span class="text-ocean-700 font-medium">Oceanic GIS Engine</span>
            </div>
        </div>

        <!-- Chart.js Breakdown -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col">
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="ph ph-chart-pie-slice text-ocean-700"></i>
                    <span>Status Lifecycle Distribution</span>
                </h3>
                <span class="text-[11px] text-slate-400">Strict State Machine breakdown</span>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="statusChart" class="max-h-60"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Shipments Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <i class="ph ph-list-dashes text-ocean-700 text-lg"></i>
                <h3 class="font-bold text-slate-800 text-sm">Active Shipments Log</h3>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs text-ocean-700 hover:text-ocean-900 font-bold flex items-center gap-1">
                <span>View All Shipments</span>
                <i class="ph ph-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Shipment #</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Carrier & Mode</th>
                        <th class="py-3 px-4">Route</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">ETA</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentShipments as $s)
                    <tr class="hover:bg-ocean-50/30 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                            {{ $s->shipment_number }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 font-medium">
                            {{ $s->customer->name ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            <span class="font-bold text-ocean-900">{{ $s->carrier->name ?? 'N/A' }}</span>
                            <span class="text-[10px] block text-slate-400">{{ $s->transport_type }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            {{ $s->origin->city }} &rarr; {{ $s->destination->city }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                @if($s->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800 border border-emerald-300
                                @elseif($s->current_status === 'IN_TRANSIT') bg-ocean-100 text-ocean-900 border border-ocean-300
                                @elseif($s->current_status === 'CANCELLED') bg-rose-100 text-rose-800 border border-rose-300
                                @else bg-gold-100 text-gold-900 border border-gold-300 @endif">
                                {{ str_replace('_', ' ', $s->current_status) }}
                            </span>
                            @if($s->is_delayed)
                            <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-gold-200 text-gold-900 border border-gold-300">
                                DELAYED
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-500">
                            {{ $s->estimated_arrival->format('d M, H:i') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('shipments.show', $s->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-ocean-100 text-slate-700 hover:text-ocean-900 font-bold transition text-xs">
                                Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No shipments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart.js Status Doughnut with Oceanic & Maritime Palette
        const statusData = @json($statusCounts);
        const labels = Object.keys(statusData);
        const data = Object.values(statusData);

        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.length ? labels : ['None'],
                datasets: [{
                    data: data.length ? data : [1],
                    backgroundColor: ['#0d9488', '#14b8a6', '#f59e0b', '#10b981', '#f43f5e', '#6366f1'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            }
        });

        // 2. Leaflet Overview Map with Multi-Layer BaseMaps
        const mapShipments = @json($mapShipments);

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

        const map = L.map('overviewMap', {
            center: [15.0, 100.0],
            zoom: 3,
            layers: [satelliteLayer, labelsOverlay]
        });

        const baseMaps = {
            "🛰️ Citra Satelit + Nama Wilayah": satelliteLayer,
            "🌙 Radar Laut Malam (Dark)": darkRadarLayer,
            "🗺️ Peta Standar Maritim (OSM)": osmLayer
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

        if (mapShipments.length > 0) {
            const markers = [];
            mapShipments.forEach(s => {
                const marker = L.circleMarker([s.latitude, s.longitude], {
                    color: s.is_delayed ? '#f59e0b' : '#2dd4bf',
                    fillColor: s.is_delayed ? '#f59e0b' : '#0d9488',
                    fillOpacity: 0.9,
                    weight: 2,
                    radius: 8
                }).addTo(map)
                .bindTooltip(`${s.shipment_number}`, { direction: 'top' })
                .bindPopup(`<b>${s.shipment_number}</b><br>Armada: ${s.carrier}<br>Status: ${s.current_status}`);
                markers.push([s.latitude, s.longitude]);
            });

            if (markers.length > 1) {
                map.fitBounds(markers, { padding: [40, 40] });
            }
        }
    });
</script>
@endpush
