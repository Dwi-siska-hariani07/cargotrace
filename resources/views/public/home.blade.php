@extends('layouts.app')

@section('content')
<div class="py-12">
    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-ocean-100/80 text-ocean-950 text-xs font-bold uppercase tracking-wider mb-5 border border-ocean-200/80 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-gold-500 animate-ping"></span>
            Real-Time Oceanic & Air Cargo Visibility
        </div>
        <h1 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight mb-4 leading-tight">
            Track Shipments & Vessels <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-ocean-700 via-ocean-600 to-gold-600">Across Global Ocean Routes</span>
        </h1>
        <p class="text-slate-600 text-base sm:text-lg mb-8 leading-relaxed">
            Multi-modal tracking system with deterministic delay rules, live Open-Meteo marine weather, ESRI satellite GIS mapping, and active countdown telemetry.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('public.track') }}" method="GET" class="relative max-w-xl mx-auto shadow-xl rounded-2xl overflow-hidden border-2 border-ocean-200/80 focus-within:border-ocean-600 transition">
            <div class="flex items-center bg-white p-2">
                <i class="ph ph-magnifying-glass text-2xl text-ocean-600 ml-3 shrink-0"></i>
                <input type="text" name="number" placeholder="Enter Cargo / Container Number (e.g. CRG-2026-8801)" 
                       class="w-full px-4 py-3 text-slate-800 placeholder-slate-400 focus:outline-none text-sm sm:text-base font-mono font-medium" required>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-ocean-700 to-ocean-800 hover:from-ocean-800 hover:to-ocean-900 text-white font-bold rounded-xl transition shadow flex items-center gap-2 shrink-0">
                    <span>Track</span>
                    <i class="ph ph-arrow-right font-bold text-gold-400"></i>
                </button>
            </div>
        </form>

        <!-- Quick Demo Trackers -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5 text-xs text-slate-500">
            <span class="font-bold text-slate-700">Live Demo Shipments:</span>
            <a href="{{ route('public.track', ['number' => 'CRG-2026-8801']) }}" class="px-3 py-1.5 rounded-xl bg-gold-50 text-gold-900 border border-gold-300 hover:bg-gold-100 font-mono font-bold transition shadow-2xs">
                🚢 CRG-2026-8801 (Customs Hold)
            </a>
            <a href="{{ route('public.track', ['number' => 'CRG-2026-8802']) }}" class="px-3 py-1.5 rounded-xl bg-ocean-50 text-ocean-900 border border-ocean-300 hover:bg-ocean-100 font-mono font-bold transition shadow-2xs">
                🚢 CRG-2026-8802 (In Transit Sea)
            </a>
            <a href="{{ route('public.track', ['number' => 'CRG-2026-8803']) }}" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-300 hover:bg-emerald-100 font-mono font-bold transition shadow-2xs">
                ✈️ CRG-2026-8803 (Delivered Air)
            </a>
        </div>
    </div>

    <!-- Feature Highlights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-2xs hover:border-ocean-300 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-ocean-50 text-ocean-700 flex items-center justify-center text-2xl mb-4 border border-ocean-100">
                <i class="ph ph-path"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-2">Deterministic State Machine</h3>
            <p class="text-slate-600 text-sm leading-relaxed">
                Strict status progression from Created to Delivered. Terminal states prevent invalid regression, enforced strictly in Laravel service layer.
            </p>
        </div>

        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-2xs hover:border-gold-300 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-gold-50 text-gold-600 flex items-center justify-center text-2xl mb-4 border border-gold-100">
                <i class="ph ph-warning"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-2">ETA & Disruption Rules</h3>
            <p class="text-slate-600 text-sm leading-relaxed">
                Automated delay severity classification (Low, Medium, High). Handle Port Congestion, Weather delays, and Customs holds with dynamic recalculation.
            </p>
        </div>

        <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-2xs hover:border-emerald-300 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4 border border-emerald-100">
                <i class="ph ph-globe-hemisphere-west"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-2">Satellite GIS & Real Weather</h3>
            <p class="text-slate-600 text-sm leading-relaxed">
                Interactive Leaflet map with ArcGIS Earth Satellite, Dark Nautical Radar, and OSM layers integrated with real-time Open-Meteo weather.
            </p>
        </div>
    </div>
</div>
@endsection
