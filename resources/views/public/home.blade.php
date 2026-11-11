@extends('layouts.app')

@section('content')
<div class="space-y-10 pb-6">

    <!-- 1. Hero Visual Carousel & Integrated Shipment Tracker -->
    <section class="relative rounded-3xl overflow-hidden shadow-2xl border border-ocean-900/20 bg-[#042f2e]">
        <div id="hero-carousel" class="relative min-h-[480px] sm:min-h-[520px] w-full flex flex-col justify-between overflow-hidden">
            <!-- Background Photography Slides -->
            <div class="absolute inset-0 z-0">
                <div class="hero-slide absolute inset-0 transition-opacity duration-500 ease-in-out opacity-100">
                    <img src="{{ asset('images/banner-ocean.jpg') }}" alt="Global Ocean Container Ship" class="w-full h-full object-cover object-center">
                </div>
                <div class="hero-slide absolute inset-0 transition-opacity duration-500 ease-in-out opacity-0 pointer-events-none">
                    <img src="{{ asset('images/banner-aircargo.jpg') }}" alt="Priority Air Cargo Freighter" class="w-full h-full object-cover object-center">
                </div>
                <div class="hero-slide absolute inset-0 transition-opacity duration-500 ease-in-out opacity-0 pointer-events-none">
                    <img src="{{ asset('images/banner-port.jpg') }}" alt="International Seaport Terminal" class="w-full h-full object-cover object-center">
                </div>
            </div>

            <!-- Carousel Controls (Top Right) -->
            <div class="relative z-20 flex justify-end pt-5 px-5 sm:pt-6 sm:px-6">
                <div class="flex items-center gap-2">
                    <button type="button" id="hero-prev" aria-label="{{ __('ui.home.prev_slide') }}"
                            class="w-10 h-10 rounded-full bg-black/35 hover:bg-black/60 text-white border border-white/20 flex items-center justify-center backdrop-blur-xs transition">
                        <i class="ph ph-caret-left text-lg"></i>
                    </button>
                    <button type="button" id="hero-next" aria-label="{{ __('ui.home.next_slide') }}"
                            class="w-10 h-10 rounded-full bg-black/35 hover:bg-black/60 text-white border border-white/20 flex items-center justify-center backdrop-blur-xs transition">
                        <i class="ph ph-caret-right text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Stacked Responsive Slide Headlines -->
            <div class="relative z-10 px-6 sm:px-12 py-4 grid grid-cols-1 grid-rows-1 max-w-3xl drop-shadow-[0_2px_10px_rgba(2,29,29,0.75)]">
                <div class="hero-caption col-start-1 row-start-1 transition-opacity duration-300 ease-out opacity-100 visible flex flex-col justify-center">
                    <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-gold-500/20 border border-gold-400/40 text-gold-300 text-[11px] font-bold uppercase tracking-widest mb-4 backdrop-blur-xs">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        {{ __('ui.home.slide1_badge') }}
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight tracking-tight mb-3">
                        {{ __('ui.home.slide1_title_pre') }} <span class="text-gold-400">{{ __('ui.home.slide1_title_hl') }}</span>
                    </h1>
                    <p class="text-slate-200 text-sm sm:text-base leading-relaxed max-w-2xl">
                        {{ __('ui.home.slide1_desc') }}
                    </p>
                </div>

                <div class="hero-caption col-start-1 row-start-1 opacity-0 invisible pointer-events-none flex flex-col justify-center">
                    <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-ocean-400/20 border border-ocean-300/40 text-ocean-100 text-[11px] font-bold uppercase tracking-widest mb-4 backdrop-blur-xs">
                        <i class="ph ph-airplane-tilt text-sm text-gold-400"></i>
                        {{ __('ui.home.slide2_badge') }}
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight tracking-tight mb-3">
                        {{ __('ui.home.slide2_title_pre') }} <span class="text-gold-400">{{ __('ui.home.slide2_title_hl') }}</span>
                    </h2>
                    <p class="text-slate-200 text-sm sm:text-base leading-relaxed max-w-2xl">
                        {{ __('ui.home.slide2_desc') }}
                    </p>
                </div>

                <div class="hero-caption col-start-1 row-start-1 opacity-0 invisible pointer-events-none flex flex-col justify-center">
                    <span class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-[11px] font-bold uppercase tracking-widest mb-4 backdrop-blur-xs">
                        <i class="ph ph-anchor text-sm text-gold-400"></i>
                        {{ __('ui.home.slide3_badge') }}
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight tracking-tight mb-3">
                        {{ __('ui.home.slide3_title_pre') }} <span class="text-gold-400">{{ __('ui.home.slide3_title_hl') }}</span>
                    </h2>
                    <p class="text-slate-200 text-sm sm:text-base leading-relaxed max-w-2xl">
                        {{ __('ui.home.slide3_desc') }}
                    </p>
                </div>
            </div>

            <!-- Tracking Search Bar & Quick Chips Docked at Bottom of Hero -->
            <div class="relative z-20 p-5 sm:p-8 bg-gradient-to-t from-[#021d1d] via-[#021d1d]/85 to-transparent">
                <div class="max-w-4xl">
                    <form action="{{ route('public.track') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 bg-white p-2 rounded-2xl shadow-2xl border border-gold-400/30">
                        <div class="flex items-center flex-1 px-3 py-1">
                            <i class="ph ph-magnifying-glass text-xl text-ocean-700 mr-3 shrink-0"></i>
                            <input type="text" name="number" placeholder="{{ __('ui.home.track_placeholder') }}"
                                   class="w-full py-2 text-slate-800 placeholder-slate-400 focus:outline-none text-sm sm:text-base font-mono font-medium bg-transparent" required>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-ocean-700 to-ocean-900 hover:from-ocean-800 hover:to-[#021d1d] text-white font-bold rounded-xl transition shadow-md flex items-center justify-center gap-2 shrink-0 text-sm">
                            <span>{{ __('ui.home.track_button') }}</span>
                            <i class="ph ph-compass text-lg text-gold-400"></i>
                        </button>
                    </form>

                    <div class="mt-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="text-slate-300 font-semibold">{{ __('ui.home.sample_label') }}</span>
                            <a href="{{ route('public.track', ['number' => 'CRG-2026-8801']) }}"
                               class="px-2.5 py-1 rounded-lg bg-gold-500/20 hover:bg-gold-500/35 text-gold-200 border border-gold-400/40 font-mono font-semibold transition">
                                🚢 CRG-2026-8801 ({{ __('ui.home.sample_hold') }})
                            </a>
                            <a href="{{ route('public.track', ['number' => 'CRG-2026-8802']) }}"
                               class="px-2.5 py-1 rounded-lg bg-ocean-500/25 hover:bg-ocean-500/45 text-ocean-100 border border-ocean-300/40 font-mono font-semibold transition">
                                🚢 CRG-2026-8802 ({{ __('ui.home.sample_transit') }})
                            </a>
                            <a href="{{ route('public.track', ['number' => 'CRG-2026-8803']) }}"
                               class="px-2.5 py-1 rounded-lg bg-emerald-500/25 hover:bg-emerald-500/45 text-emerald-100 border border-emerald-300/40 font-mono font-semibold transition">
                                ✈️ CRG-2026-8803 ({{ __('ui.home.sample_delivered') }})
                            </a>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button type="button" class="hero-dot w-7 h-2 rounded-full bg-gold-400 transition-all" data-slide="0" aria-label="Slide 1"></button>
                            <button type="button" class="hero-dot w-2.5 h-2 rounded-full bg-white/40 hover:bg-white/70 transition-all" data-slide="1" aria-label="Slide 2"></button>
                            <button type="button" class="hero-dot w-2.5 h-2 rounded-full bg-white/40 hover:bg-white/70 transition-all" data-slide="2" aria-label="Slide 3"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Global Operational Corridor Strip (Metrics & Portal Access) -->
    <section class="rounded-2xl bg-gradient-to-r from-ocean-900 via-[#042f2e] to-ocean-800 text-white p-5 sm:p-6 shadow-lg border border-ocean-700/60">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-8 flex-1">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-gold-400 shrink-0">
                        <i class="ph ph-package text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-ocean-200 font-semibold">{{ __('ui.home.stat_total') }}</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-white leading-tight">{{ $networkStats['totalShipments'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-emerald-400 shrink-0">
                        <i class="ph ph-boat text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-ocean-200 font-semibold">{{ __('ui.home.stat_transit') }}</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-white leading-tight">{{ $networkStats['activeTransit'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-sky-300 shrink-0">
                        <i class="ph ph-globe-hemisphere-east text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-ocean-200 font-semibold">{{ __('ui.home.stat_ports') }}</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-white leading-tight">{{ $networkStats['portsCount'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-gold-300 shrink-0">
                        <i class="ph ph-buildings text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-ocean-200 font-semibold">{{ __('ui.home.stat_carriers') }}</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-white leading-tight">{{ $networkStats['carriersCount'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 lg:border-l lg:border-white/15 lg:pl-6 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-[#021d1d] font-bold text-xs sm:text-sm transition shadow flex items-center gap-2">
                        <i class="ph ph-squares-four text-lg"></i>
                        <span>{{ __('ui.home.cta_dashboard') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-400 text-[#021d1d] font-bold text-xs sm:text-sm transition shadow flex items-center gap-2">
                        <i class="ph ph-sign-in text-lg"></i>
                        <span>{{ __('ui.home.cta_login') }}</span>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- 3. Filter Pills & Featured Live Shipments Showcase Grid -->
    <section id="shipments" class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-ocean-700 mb-1">
                    <i class="ph ph-broadcast text-base text-gold-600"></i>
                    {{ __('ui.home.feed_kicker') }}
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ __('ui.home.feed_title') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ __('ui.home.feed_subtitle') }}
                </p>
            </div>

            <form method="GET" action="{{ route('home') }}#shipments" class="flex items-center gap-2 w-full md:w-auto">
                @if(!empty($mode) && $mode !== 'ALL')
                    <input type="hidden" name="mode" value="{{ $mode }}">
                @endif
                <div class="relative flex-1 md:w-72">
                    <i class="ph ph-magnifying-glass text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('ui.home.filter_placeholder') }}"
                           class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:border-ocean-600 shadow-2xs">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-ocean-800 hover:bg-ocean-900 text-white text-xs sm:text-sm font-bold transition shadow-2xs">
                    {{ __('ui.home.filter_btn') }}
                </button>
            </form>
        </div>

        <!-- Horizontal Quick Filter Pills -->
        <div class="flex flex-wrap items-center gap-2.5">
            @php
                $pills = [
                    'ALL' => ['label' => __('ui.home.pill_all'), 'icon' => 'ph-globe'],
                    'SEA' => ['label' => __('ui.home.pill_sea'), 'icon' => 'ph-boat'],
                    'AIR' => ['label' => __('ui.home.pill_air'), 'icon' => 'ph-airplane-tilt'],
                    'DELAYED' => ['label' => __('ui.home.pill_delayed'), 'icon' => 'ph-warning-circle'],
                ];
            @endphp
            @foreach($pills as $key => $pill)
                @php $isActive = ($mode ?? 'ALL') === $key; @endphp
                <a href="{{ route('home', array_filter(['mode' => $key === 'ALL' ? null : $key, 'q' => $search !== '' ? $search : null])) }}#shipments"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold border transition shadow-2xs
                   {{ $isActive
                        ? 'bg-[#042f2e] text-gold-300 border-[#042f2e]'
                        : 'bg-white text-slate-700 border-slate-200 hover:border-ocean-400 hover:text-ocean-800' }}">
                    <i class="ph {{ $pill['icon'] }} text-base"></i>
                    <span>{{ $pill['label'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- Shipments Photographic Cards Grid -->
        @if($featuredShipments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredShipments as $shipment)
                    @php
                        $thumb = match (true) {
                            $shipment->is_delayed => asset('images/card-customs.jpg'),
                            $shipment->transport_type === 'AIR' => asset('images/card-air.jpg'),
                            default => asset('images/card-sea.jpg'),
                        };
                        $modeLabel = match ($shipment->transport_type) {
                            'AIR' => __('ui.home.mode_air'),
                            'LAND' => __('ui.home.mode_land'),
                            default => __('ui.home.mode_sea'),
                        };
                        $modeIcon = match ($shipment->transport_type) {
                            'AIR' => 'ph-airplane-tilt',
                            'LAND' => 'ph-truck',
                            default => 'ph-boat',
                        };
                    @endphp
                    <article class="group bg-white rounded-3xl border border-ocean-200/75 shadow-sm shadow-ocean-950/5 hover:shadow-xl hover:border-ocean-400 transition duration-300 overflow-hidden flex flex-col">
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-900">
                            <img src="{{ $thumb }}" alt="{{ $shipment->shipment_number }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#021d1d]/85 via-black/15 to-transparent"></div>

                            <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#042f2e]/85 text-white text-[11px] font-bold tracking-wide backdrop-blur-xs border border-white/15">
                                    <i class="ph {{ $modeIcon }} text-gold-400"></i>
                                    {{ $modeLabel }}
                                </span>

                                @if($shipment->is_delayed)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-500 text-slate-950 text-[11px] font-extrabold shadow">
                                        <i class="ph ph-warning"></i>
                                        {{ __('ui.home.delay_badge', ['hours' => $shipment->delay_hours]) }}
                                    </span>
                                @elseif($shipment->current_status === 'DELIVERED')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500 text-white text-[11px] font-bold shadow">
                                        <i class="ph ph-check-circle"></i>
                                        {{ __('ui.home.delivered_badge') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/95 text-ocean-900 text-[11px] font-bold shadow">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ str_replace('_', ' ', $shipment->current_status) }}
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-3.5 left-4 right-4 flex items-end justify-between gap-2 text-white">
                                <div>
                                    <p class="text-[11px] text-gold-300 font-semibold truncate">
                                        {{ $shipment->carrier->name ?? 'Global Maritime Line' }}
                                    </p>
                                    <h3 class="font-mono font-bold text-base sm:text-lg tracking-tight leading-tight">
                                        {{ $shipment->shipment_number }}
                                    </h3>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-xs border border-white/20 shrink-0">
                                    {{ number_format((float) ($shipment->total_weight_kg ?? 0), 0, ',', '.') }} kg
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2 bg-slate-50 rounded-2xl p-3 border border-slate-100">
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.home.origin_label') }}</span>
                                        <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">
                                            {{ $shipment->origin->city ?? $shipment->origin->name ?? __('ui.home.origin_label') }}
                                        </p>
                                        <span class="text-[11px] font-mono text-ocean-700 font-semibold">{{ $shipment->origin->code ?? '' }}</span>
                                    </div>
                                    <div class="flex flex-col items-center px-2 shrink-0 text-ocean-600">
                                        <i class="ph ph-arrow-right text-lg font-bold"></i>
                                    </div>
                                    <div class="min-w-0 text-right">
                                        <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.home.dest_label') }}</span>
                                        <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">
                                            {{ $shipment->destination->city ?? $shipment->destination->name ?? __('ui.home.dest_label') }}
                                        </p>
                                        <span class="text-[11px] font-mono text-ocean-700 font-semibold">{{ $shipment->destination->code ?? '' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-2 text-xs">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-slate-500 flex items-center gap-1.5">
                                            <i class="ph ph-cube text-ocean-600"></i> {{ __('ui.home.cargo_label') }}
                                        </span>
                                        <span class="font-semibold text-slate-800 text-right truncate max-w-[60%]">
                                            {{ $shipment->cargo_type ?? 'General Cargo' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-slate-500 flex items-center gap-1.5">
                                            <i class="ph ph-map-pin text-gold-600"></i> {{ __('ui.home.location_label') }}
                                        </span>
                                        <span class="font-semibold text-slate-800 text-right truncate max-w-[60%]">
                                            {{ $shipment->current_location_name ?? __('ui.home.in_voyage') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-slate-500 flex items-center gap-1.5">
                                            <i class="ph ph-calendar-check text-emerald-600"></i> {{ __('ui.home.eta_label') }}
                                        </span>
                                        <span class="font-semibold text-slate-800">
                                            {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('d M Y, H:i') : __('ui.home.pending_schedule') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('public.track', ['number' => $shipment->shipment_number]) }}"
                               class="w-full py-2.5 px-4 rounded-xl bg-ocean-800 hover:bg-[#042f2e] text-white font-bold text-xs sm:text-sm transition shadow-sm flex items-center justify-center gap-2">
                                <span>{{ __('ui.home.track_satellite_btn') }}</span>
                                <i class="ph ph-arrow-right text-gold-400"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center space-y-3 shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-ocean-50 text-ocean-700 flex items-center justify-center text-3xl mx-auto">
                    <i class="ph ph-compass"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">{{ __('ui.home.empty_title') }}</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">
                    {{ __('ui.home.empty_desc') }}
                </p>
                <div class="pt-2">
                    <a href="{{ route('home') }}#shipments" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-ocean-700 hover:bg-ocean-800 text-white text-xs sm:text-sm font-bold transition">
                        <i class="ph ph-arrow-counter-clockwise"></i>
                        <span>{{ __('ui.home.empty_reset') }}</span>
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- 4. Commercial Freight Divisions Showcase (Photographic Cards) -->
    <section class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gold-600">{{ __('ui.home.services_kicker') }}</span>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ __('ui.home.services_title') }}</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">{{ __('ui.home.services_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl border border-ocean-200/75 overflow-hidden shadow-sm shadow-ocean-950/5 hover:shadow-lg hover:border-ocean-300 transition group">
                <div class="h-44 overflow-hidden relative">
                    <img src="{{ asset('images/card-sea.jpg') }}" alt="Ocean Container Freight" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021d1d]/70 to-transparent"></div>
                    <span class="absolute bottom-3 left-4 inline-flex items-center gap-1.5 text-white text-xs font-bold">
                        <i class="ph ph-anchor text-gold-400 text-base"></i> {{ __('ui.home.srv1_badge') }}
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-2">{{ __('ui.home.srv1_title') }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ __('ui.home.srv1_desc') }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-ocean-200/75 overflow-hidden shadow-sm shadow-ocean-950/5 hover:shadow-lg hover:border-ocean-300 transition group">
                <div class="h-44 overflow-hidden relative">
                    <img src="{{ asset('images/card-air.jpg') }}" alt="Air Cargo Operations" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021d1d]/70 to-transparent"></div>
                    <span class="absolute bottom-3 left-4 inline-flex items-center gap-1.5 text-white text-xs font-bold">
                        <i class="ph ph-airplane-takeoff text-gold-400 text-base"></i> {{ __('ui.home.srv2_badge') }}
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-2">{{ __('ui.home.srv2_title') }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ __('ui.home.srv2_desc') }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-ocean-200/75 overflow-hidden shadow-sm shadow-ocean-950/5 hover:shadow-lg hover:border-ocean-300 transition group">
                <div class="h-44 overflow-hidden relative">
                    <img src="{{ asset('images/card-customs.jpg') }}" alt="Customs Clearance Hub" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021d1d]/70 to-transparent"></div>
                    <span class="absolute bottom-3 left-4 inline-flex items-center gap-1.5 text-white text-xs font-bold">
                        <i class="ph ph-clipboard-text text-gold-400 text-base"></i> {{ __('ui.home.srv3_badge') }}
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-2">{{ __('ui.home.srv3_title') }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ __('ui.home.srv3_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Connected International Port & Airport Hubs -->
    @if(isset($globalPorts) && $globalPorts->count() > 0)
    <section class="bg-white rounded-3xl border border-ocean-200/75 p-6 sm:p-8 shadow-sm shadow-ocean-950/5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-ocean-700">{{ __('ui.home.ports_kicker') }}</span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-0.5">{{ __('ui.home.ports_title') }}</h2>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ __('ui.home.ports_badge') }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($globalPorts as $port)
                <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 hover:border-ocean-300 transition">
                    <div class="w-11 h-11 rounded-xl bg-ocean-100 text-ocean-800 flex items-center justify-center text-xl shrink-0">
                        <i class="ph {{ str_contains(strtoupper((string) $port->type), 'AIR') ? 'ph-airplane-in-flight' : 'ph-anchor' }}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-extrabold text-ocean-800 bg-white px-2 py-0.5 rounded border border-slate-200">{{ $port->code }}</span>
                            <span class="text-[11px] font-medium text-slate-400 truncate">{{ $port->country }}</span>
                        </div>
                        <p class="text-sm font-bold text-slate-900 truncate mt-1">{{ $port->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $port->city }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('#hero-carousel .hero-slide');
    const captions = document.querySelectorAll('#hero-carousel .hero-caption');
    const dots = document.querySelectorAll('#hero-carousel .hero-dot');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');

    if (!slides.length) return;

    let current = 0;
    let timer = null;

    function goToSlide(index) {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            const active = i === current;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
        });

        captions.forEach((caption, i) => {
            const active = i === current;
            caption.classList.toggle('transition-opacity', active);
            caption.classList.toggle('duration-300', active);
            caption.classList.toggle('ease-out', active);
            caption.classList.toggle('opacity-100', active);
            caption.classList.toggle('visible', active);
            caption.classList.toggle('opacity-0', !active);
            caption.classList.toggle('invisible', !active);
            caption.classList.toggle('pointer-events-none', !active);
        });

        dots.forEach((dot, i) => {
            const active = i === current;
            dot.classList.toggle('w-7', active);
            dot.classList.toggle('bg-gold-400', active);
            dot.classList.toggle('w-2.5', !active);
            dot.classList.toggle('bg-white/40', !active);
        });
    }

    function restartAuto() {
        if (timer) clearInterval(timer);
        timer = setInterval(() => goToSlide(current + 1), 6000);
    }

    prevBtn?.addEventListener('click', () => {
        goToSlide(current - 1);
        restartAuto();
    });

    nextBtn?.addEventListener('click', () => {
        goToSlide(current + 1);
        restartAuto();
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const idx = Number(dot.getAttribute('data-slide') || 0);
            goToSlide(idx);
            restartAuto();
        });
    });

    restartAuto();
});
</script>
@endsection
