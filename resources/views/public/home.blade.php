@extends('layouts.app')

@section('content')
<div class="py-12">
    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold uppercase tracking-wider mb-4">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            Real-Time Logistics Visibility
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
            Track Cargo & Shipments <br><span class="text-blue-600">Across Global Routes</span>
        </h1>
        <p class="text-slate-600 text-lg mb-8">
            End-to-end shipment visibility with deterministic delay rules, chronological timelines, open exceptions tracking, and interactive GIS mapping.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('public.track') }}" method="GET" class="relative max-w-xl mx-auto shadow-xl rounded-2xl overflow-hidden border border-slate-200">
            <div class="flex items-center bg-white p-2">
                <i class="ph ph-magnifying-glass text-2xl text-slate-400 ml-3"></i>
                <input type="text" name="number" placeholder="Enter Shipment Number (e.g. CRG-2026-8801)" 
                       class="w-full px-4 py-3 text-slate-800 placeholder-slate-400 focus:outline-none text-base font-medium" required>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow flex items-center gap-2">
                    <span>Track</span>
                    <i class="ph ph-arrow-right font-bold"></i>
                </button>
            </div>
        </form>

        <!-- Quick Demo Trackers -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-500">
            <span class="font-semibold text-slate-700">Quick Demo Links:</span>
            <a href="{{ route('public.track', ['number' => 'CRG-2026-8801']) }}" class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 font-mono font-medium">
                🚢 CRG-2026-8801 (Customs Hold)
            </a>
            <a href="{{ route('public.track', ['number' => 'CRG-2026-8802']) }}" class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 font-mono font-medium">
                🚢 CRG-2026-8802 (In Transit)
            </a>
            <a href="{{ route('public.track', ['number' => 'CRG-2026-8803']) }}" class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 font-mono font-medium">
                ✈️ CRG-2026-8803 (Delivered Air)
            </a>
        </div>
    </div>

    <!-- Feature Highlights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4">
                <i class="ph ph-path"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-2">Deterministic State Machine</h3>
            <p class="text-slate-600 text-sm leading-relaxed">
                Strict status lifecycle from Created to Delivered. Terminal states prevent invalid regression, validated in backend service layer.
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-4">
                <i class="ph ph-warning"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-2">ETA & Disruption Rules</h3>
            <p class="text-slate-600 text-sm leading-relaxed">
                Automated delay severity classification (Low, Medium, High). Track Port Congestion, Weather delays, and Customs holds.
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4">
                <i class="ph ph-map-pin"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-2">GIS Map & Atmospheric API</h3>
            <p class="text-slate-600 text-sm leading-relaxed">
                Leaflet.js interactive OpenStreetMap route visualization integrated with real-time weather data at current coordinates.
            </p>
        </div>
    </div>
</div>
@endsection
