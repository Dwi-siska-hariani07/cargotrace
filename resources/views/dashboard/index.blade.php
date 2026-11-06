@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Operations Dashboard</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Real-time visibility metrics for <strong class="text-slate-700">{{ auth()->user()->company ?? auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }})
            </p>
        </div>

        @if(!auth()->user()->isCustomer())
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow transition flex items-center gap-2">
                <i class="ph ph-plus-circle text-lg"></i>
                <span>New Shipment</span>
            </a>
        </div>
        @endif
    </div>

    <!-- 5 KPI Stat Cards (Slide 10) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Active</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="ph ph-package text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ $totalShipments }}</div>
            <span class="text-[11px] text-slate-400">Total tracked orders</span>
        </div>

        <!-- In Transit -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">In Transit</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="ph ph-boat text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-indigo-600 mt-2">{{ $inTransitCount }}</div>
            <span class="text-[11px] text-slate-400">On sea / air routes</span>
        </div>

        <!-- Delivered -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Delivered</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="ph ph-check-circle text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 mt-2">{{ $deliveredCount }}</div>
            <span class="text-[11px] text-slate-400">Terminal completed</span>
        </div>

        <!-- Delayed -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Delayed</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="ph ph-clock-countdown text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-600 mt-2">{{ $delayedCount }}</div>
            <span class="text-[11px] text-amber-600 font-semibold">{{ $delayRate }}% delay rate</span>
        </div>

        <!-- Exceptions -->
        <div class="col-span-2 lg:col-span-1 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Exceptions</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="ph ph-warning text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-rose-600 mt-2">{{ $openExceptionsCount }}</div>
            <span class="text-[11px] text-rose-600 font-semibold">{{ $exceptionRate }}% incident rate</span>
        </div>
    </div>

    <!-- Global Map & Chart.js Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Global Interactive Overview Map -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ph ph-globe-hemisphere-east text-blue-600 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Global Active Vessel & Flight Fleet</h3>
                </div>
                <span class="text-[11px] text-slate-400">Live Geo-Plotting</span>
            </div>
            <div id="overviewMap" class="w-full h-80 bg-slate-100"></div>
        </div>

        <!-- Chart.js Breakdown -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Shipments by Lifecycle Status</h3>
                <span class="text-[11px] text-slate-400">Operational distribution</span>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="statusChart" class="max-h-60"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Shipments Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ph ph-list-dashes text-blue-600 text-lg"></i>
                <h3 class="font-bold text-slate-800 text-sm">Active Shipments Log</h3>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs text-blue-600 font-bold hover:underline">
                View All &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-semibold">
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
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                            {{ $s->shipment_number }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 font-medium">
                            {{ $s->customer->name ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            <span class="font-semibold text-slate-700">{{ $s->carrier->name ?? 'N/A' }}</span>
                            <span class="text-[10px] block text-slate-400">{{ $s->transport_type }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            {{ $s->origin->city }} &rarr; {{ $s->destination->city }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                @if($s->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800
                                @elseif($s->current_status === 'IN_TRANSIT') bg-blue-100 text-blue-800
                                @elseif($s->current_status === 'CANCELLED') bg-rose-100 text-rose-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ str_replace('_', ' ', $s->current_status) }}
                            </span>
                            @if($s->is_delayed)
                            <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-200 text-amber-800">
                                DELAYED
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-500">
                            {{ $s->estimated_arrival->format('d M, H:i') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('shipments.show', $s->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 font-bold transition text-xs">
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
        // 1. Chart.js Status Doughnut
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
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                }
            }
        });

        // 2. Leaflet Overview Map
        const mapShipments = @json($mapShipments);
        const map = L.map('overviewMap').setView([10.0, 105.0], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        if (mapShipments.length > 0) {
            const markers = [];
            mapShipments.forEach(s => {
                const marker = L.circleMarker([s.latitude, s.longitude], {
                    color: s.is_delayed ? '#f59e0b' : '#2563eb',
                    fillColor: s.is_delayed ? '#f59e0b' : '#3b82f6',
                    fillOpacity: 0.8,
                    radius: 7
                }).addTo(map)
                .bindPopup(`<b>${s.shipment_number}</b><br>${s.carrier}<br>Status: ${s.current_status}`);
                markers.push([s.latitude, s.longitude]);
            });

            if (markers.length > 1) {
                map.fitBounds(markers, { padding: [40, 40] });
            }
        }
    });
</script>
@endpush

