@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-ocean-100 text-slate-500 hover:text-ocean-700 flex items-center justify-center transition">
                <i class="ph ph-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono flex items-center gap-2">
                        <span>{{ $shipment->shipment_number }}</span>
                        <button type="button" onclick="copyTrackingNum(this)" data-num="{{ $shipment->shipment_number }}" data-msg="{{ __('ui.track.copy_alert') }}" title="{{ __('ui.track.copy_title') }}" class="text-xs text-slate-400 hover:text-ocean-600 transition p-1">
                            <i class="ph ph-copy text-lg"></i>
                        </button>
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($shipment->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($shipment->current_status === 'IN_TRANSIT') bg-ocean-100 text-ocean-900 border border-ocean-300
                        @elseif($shipment->current_status === 'CANCELLED') bg-rose-100 text-rose-800 border border-rose-300
                        @else bg-gold-100 text-gold-900 border border-gold-300 @endif">
                        {{ $shipment->statusLabel() }}
                    </span>
                </div>
                <p class="text-slate-500 text-xs mt-1">
                    {{ __('ui.shipments.col_customer') }}: <strong class="text-slate-800">{{ $shipment->customer->name ?? 'N/A' }}</strong> ({{ $shipment->customer->company }}) • {{ __('ui.track.carrier_label') }} <strong class="text-ocean-800 font-bold">{{ $shipment->carrier->name }}</strong>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('public.waybill', ['number' => $shipment->shipment_number]) }}" target="_blank"
               class="px-3.5 py-2 bg-[#042f2e] hover:bg-ocean-900 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="ph ph-printer text-base text-gold-400"></i>
                <span>{{ __('ui.track.print_waybill_btn') }}</span>
            </a>
            <a href="{{ route('public.track', ['number' => $shipment->shipment_number]) }}" target="_blank"
               class="px-3.5 py-2 bg-gradient-to-r from-ocean-50 to-ocean-100 hover:from-ocean-100 hover:to-ocean-200 text-ocean-900 border border-ocean-200/80 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="ph ph-arrow-square-out text-base text-ocean-700"></i>
                <span>{{ __('ui.shipments.public_view_btn') }}</span>
            </a>
        </div>
    </div>

    <!-- Delay Notification Banner if Delayed -->
    @if($shipment->is_delayed)
    <div class="p-4 rounded-2xl bg-gold-50 border border-gold-300 text-gold-900 flex items-start gap-3 shadow-xs">
        <i class="ph ph-warning-circle text-2xl text-gold-600 mt-0.5 shrink-0"></i>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-sm">{{ __('ui.shipments.show_delay_title', ['hours' => $shipment->delay_hours]) }}</h3>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-gold-200 text-gold-900 border border-gold-300">
                    {{ __('ui.track.severity_label') }} {{ $shipment->delay_severity }}
                </span>
            </div>
            <p class="text-xs text-gold-800 mt-1">
                {{ $shipment->delay_reason ?? __('ui.track.delay_default') }}
            </p>
        </div>
    </div>
    @endif

    @include('partials.shipment_progress', ['shipment' => $shipment])

    <!-- LIVE JOURNEY TIME, MID-ROUTE POSITION & ETA COUNTDOWN BOARD -->
    <div class="bg-gradient-to-br from-[#042f2e] via-[#093f3d] to-[#042f2e] text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-ocean-700/60 relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-ocean-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header of Board -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-ocean-800/80 gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-400 animate-ping"></span>
                    <span class="text-xs uppercase tracking-widest text-gold-400 font-bold">{{ __('ui.shipments.telemetry_badge') }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight mt-1 flex items-center gap-2">
                    <i class="ph ph-compass text-gold-400"></i>
                    <span>{{ __('ui.track.board_title') }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-3 bg-white/10 px-4 py-2.5 rounded-2xl border border-white/15 backdrop-blur-md shadow-inner">
                <i class="ph ph-clock text-2xl text-gold-400"></i>
                <div>
                    <span class="text-[10px] uppercase font-bold text-ocean-200 block leading-tight">{{ __('ui.track.your_time') }}</span>
                    <span id="user-current-clock" class="text-sm font-mono font-bold text-white">--:--:-- WIB</span>
                </div>
            </div>
        </div>

        <!-- 3-Phase Journey Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6 relative z-10">
            <!-- 1. Departure -->
            <div class="bg-white/5 border border-white/10 hover:border-ocean-400/40 transition rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-ocean-300 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-anchor text-base text-gold-400"></i> {{ __('ui.track.phase1_title') }}</span>
                        <span class="px-2 py-0.5 rounded bg-ocean-500/20 text-ocean-200 border border-ocean-500/30 text-[10px]">{{ __('ui.track.phase1_badge') }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->origin->name }}</h3>
                    <p class="text-xs text-ocean-200">{{ $shipment->origin->city }}, {{ $shipment->origin->country }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-ocean-200/80">{{ __('ui.track.scheduled_dep') }}</span>
                            <span class="font-mono text-white font-semibold">{{ $shipment->expected_departure->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-ocean-200/80">{{ __('ui.track.origin_clock') }}</span>
                            <span class="font-mono text-gold-300 font-bold live-port-clock" data-tz="{{ $shipment->origin->timezone }}">--:--:--</span>
                        </div>
                    </div>
                </div>

                @if($originWeather && $originWeather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-black/20 p-2.5 rounded-xl border border-white/5">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-sun text-gold-400 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-ocean-200 block leading-tight">{{ __('ui.track.origin_weather') }}</span>
                            <span class="font-semibold text-white">{{ $originWeather['condition'] }}</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-white font-mono">{{ $originWeather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>

            <!-- 2. Mid-Transit Coordinate -->
            <div class="bg-gradient-to-b from-ocean-900/60 to-ocean-950/80 border-2 border-gold-500/50 rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between shadow-xl">
                <div class="absolute top-0 right-0 w-28 h-28 bg-gold-500/10 rounded-full blur-2xl"></div>
                <div>
                    <div class="flex items-center justify-between text-xs text-gold-400 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-boat text-base"></i> {{ __('ui.track.phase2_title') }}</span>
                        <span class="px-2 py-0.5 rounded bg-gold-500/20 text-gold-300 border border-gold-500/30 text-[10px] animate-pulse">{{ __('ui.track.phase2_badge') }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->current_location_name ?? 'In Transit Ocean Route' }}</h3>
                    <p class="text-xs text-ocean-200">{{ __('ui.track.coords') }} {{ number_format($shipment->current_latitude, 4) }}, {{ number_format($shipment->current_longitude, 4) }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-ocean-200">{{ __('ui.track.vessel_clock') }}</span>
                            <span class="font-mono text-gold-300 text-sm font-bold live-port-clock" data-tz="{{ $shipment->carrier->type === 'AIR' ? 'Asia/Singapore' : 'Asia/Singapore' }}">--:--:--</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ocean-200">{{ __('ui.track.op_status') }}</span>
                            <span class="font-semibold text-white">{{ $shipment->statusLabel() }}</span>
                        </div>
                    </div>
                </div>

                @if($weather && $weather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-black/30 p-2.5 rounded-xl border border-gold-500/20">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-wind text-ocean-300 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-ocean-200 block leading-tight">{{ __('ui.track.sea_weather') }}</span>
                            <span class="font-semibold text-white">{{ $weather['condition'] }} • {{ __('ui.track.wind_speed', ['speed' => $weather['wind_speed_kmh']]) }}</span>
                        </div>
                    </div>
                    <span class="text-base font-bold text-gold-400 font-mono">{{ $weather['temperature_c'] }}°C</span>
                </div>
                @endif
            </div>

            <!-- 3. Destination & Live ETA Countdown -->
            <div class="bg-white/5 border border-white/10 hover:border-emerald-400/40 transition rounded-2xl p-5 backdrop-blur-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-emerald-400 font-bold mb-2">
                        <span class="flex items-center gap-1.5"><i class="ph ph-flag-checkered text-base"></i> {{ __('ui.track.phase3_title') }}</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px]">{{ __('ui.track.phase3_badge') }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ $shipment->destination->name }}</h3>
                    <p class="text-xs text-ocean-200">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>

                    <div class="mt-4 p-3.5 rounded-2xl bg-gradient-to-r from-emerald-950/70 to-slate-950 border border-emerald-500/40 text-center shadow-inner">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">
                            {{ __('ui.track.eta_countdown_title') }}
                        </span>
                        <div id="eta-countdown" class="text-base sm:text-lg font-mono font-black text-white mt-1">
                            {{ __('ui.countdown.calculating') }}
                        </div>
                        <span class="text-[10px] text-ocean-300/80 block mt-0.5">
                            {{ __('ui.track.target_eta') }} {{ $shipment->estimated_arrival->format('d M Y, H:i') }} ({{ __('ui.countdown.local_time') }})
                        </span>
                    </div>

                    <div class="mt-3 pt-2 border-t border-white/10 flex justify-between text-xs">
                        <span class="text-ocean-200/80">{{ __('ui.track.dest_clock') }}</span>
                        <span class="font-mono text-emerald-400 font-bold live-port-clock" data-tz="{{ $shipment->destination->timezone }}">--:--:--</span>
                    </div>
                </div>

                @if($destWeather && $destWeather['available'])
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs bg-black/20 p-2.5 rounded-xl border border-white/5">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-cloud-sun text-emerald-300 text-lg"></i>
                        <div>
                            <span class="text-[10px] text-ocean-200 block leading-tight">{{ __('ui.track.dest_weather') }}</span>
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
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="ph ph-navigation-arrow text-ocean-700 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm">{{ __('ui.track.map_title') }}</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold text-ocean-800 bg-ocean-100 px-2 py-0.5 rounded-md border border-ocean-200">
                        {{ __('ui.shipments.map_active_badge') }}
                    </span>
                </div>
            </div>
            <div id="shipmentMap" class="w-full h-[450px] bg-slate-900 z-10"></div>
            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500 flex flex-wrap items-center justify-between gap-2">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-gold-500 inline-block"></span> <strong>{{ __('ui.map.legend_gold') }}</strong> {{ __('ui.map.legend_gold_desc') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-0.5 bg-ocean-600 inline-block border-t border-dashed"></span> <strong>{{ __('ui.map.legend_teal') }}</strong> {{ __('ui.map.legend_teal_desc') }}
                </span>
                <span class="text-slate-400">{{ __('ui.shipments.map_hint_short') }}</span>
            </div>
        </div>

        <!-- Cargo Manifest Specs -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="ph ph-package text-ocean-700"></i>
                    <span>{{ __('ui.shipments.specs_header') }}</span>
                </h4>
                <span class="font-mono text-[10px] text-slate-400">ISO 6346 COMPLIANT</span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">{{ __('ui.shipments.specs_weight') }}</span>
                    <strong class="text-slate-900 font-mono text-sm">{{ number_format($shipment->total_weight_kg, 2) }} kg</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">{{ __('ui.shipments.specs_volume') }}</span>
                    <strong class="text-slate-900 font-mono text-sm">{{ $shipment->total_volume_cbm ?? 'N/A' }} m³</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">{{ __('ui.shipments.specs_commodity') }}</span>
                    <strong class="text-slate-800">{{ $shipment->cargo_type }}</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">{{ __('ui.track.carrier_col') }}</span>
                    <strong class="text-ocean-800 font-bold">{{ $shipment->carrier->name }}</strong>
                </div>
            </div>

            @if($shipment->items->count() > 0)
            <div class="pt-3 border-t border-slate-100">
                <span class="text-slate-500 block mb-2 font-semibold">{{ __('ui.shipments.specs_items') }}</span>
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

    <!-- Active Exceptions / Incident Reports -->
    @if($shipment->exceptions->count() > 0)
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
            <i class="ph ph-warning-octagon text-rose-600 text-xl"></i>
            <span>{{ __('ui.shipments.disruptions_title') }}</span>
        </h3>
        <div class="space-y-3">
            @foreach($shipment->exceptions as $ex)
            <div class="p-4 rounded-2xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
                @if($ex->status === 'RESOLVED') bg-slate-50 border-slate-200 text-slate-600
                @else bg-rose-50 border-rose-200 text-rose-900 @endif">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono font-bold text-sm">{{ $ex->exception_type }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                            @if($ex->severity === 'HIGH') bg-rose-200 text-rose-800 border border-rose-300
                            @elseif($ex->severity === 'MEDIUM') bg-gold-200 text-gold-800 border border-gold-300
                            @else bg-ocean-200 text-ocean-800 border border-ocean-300 @endif">
                            {{ __('ui.track.severity_label') }} {{ $ex->severity }}
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                            @if($ex->status === 'RESOLVED') bg-emerald-200 text-emerald-800 border border-emerald-300
                            @else bg-rose-200 text-rose-800 border border-rose-300 @endif">
                            {{ $ex->status }}
                        </span>
                    </div>
                    <p class="text-xs mt-1">{{ $ex->description }}</p>
                    @if($ex->resolution_notes)
                    <p class="text-xs text-emerald-700 font-medium mt-1">
                        {{ __('ui.shipments.resolved_by', ['notes' => $ex->resolution_notes, 'name' => $ex->resolver->name ?? 'Staff']) }}
                    </p>
                    @endif
                </div>

                @if(!auth()->user()->isCustomer() && $ex->status !== 'RESOLVED')
                <div>
                    <form method="POST" action="{{ route('exceptions.resolve', $ex->id) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="resolution_notes" placeholder="{{ __('ui.shipments.resolution_ph') }}" required
                               class="px-3 py-1.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:border-ocean-500">
                        <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                            {{ __('ui.shipments.resolve_btn') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Operator Operational Actions Section -->
    @if(!auth()->user()->isCustomer() && $shipment->current_status !== 'DELIVERED' && $shipment->current_status !== 'CANCELLED')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form 1: Add Tracking Event -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm mb-3 flex items-center gap-2">
                <i class="ph ph-map-pin-line text-ocean-700 text-lg"></i>
                <span>{{ __('ui.shipments.checkpoint_title') }}</span>
            </h3>

            <!-- Quick Ocean/Route Simulation Presets -->
            <div class="mb-4 p-3 rounded-2xl bg-ocean-50/70 border border-ocean-200/70">
                <span class="block text-[10px] font-extrabold uppercase text-ocean-900 mb-2">
                    {{ __('ui.shipments.preset_title') }}
                </span>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="fillWaypoint('Selat Malaka Waypoint', 2.4820, 101.2150, 'Vessel transiting the Malacca Strait corridor westbound.')"
                            class="px-2.5 py-1 bg-white hover:bg-ocean-100 text-ocean-900 border border-ocean-200 rounded-lg text-[10px] font-bold transition shadow-2xs">
                        ⚓ Selat Malaka
                    </button>
                    <button type="button" onclick="fillWaypoint('Samudra Hindia (Indian Ocean)', 6.1500, 78.4000, 'Vessel cruising steadily across the Indian Ocean south of Sri Lanka.')"
                            class="px-2.5 py-1 bg-white hover:bg-ocean-100 text-ocean-900 border border-ocean-200 rounded-lg text-[10px] font-bold transition shadow-2xs">
                        🌊 Indian Ocean
                    </button>
                    <button type="button" onclick="fillWaypoint('Terusan Suez (Suez Canal)', 30.5852, 32.2654, 'Vessel joining Suez Canal convoy toward the Mediterranean Sea.')"
                            class="px-2.5 py-1 bg-white hover:bg-ocean-100 text-ocean-900 border border-ocean-200 rounded-lg text-[10px] font-bold transition shadow-2xs">
                        🚢 Suez Canal
                    </button>
                    <button type="button" onclick="fillWaypoint('Selat Gibraltar (Mediterranean)', 35.9800, -5.5000, 'Vessel clearing the Strait of Gibraltar into the North Atlantic.')"
                            class="px-2.5 py-1 bg-white hover:bg-ocean-100 text-ocean-900 border border-ocean-200 rounded-lg text-[10px] font-bold transition shadow-2xs">
                        🧭 Gibraltar
                    </button>
                    <button type="button" onclick="fillWaypoint('Laut Timor (Timor Sea)', -11.2500, 127.8000, 'Vessel crossing Timor Sea waters bound for Australia.')"
                            class="px-2.5 py-1 bg-white hover:bg-ocean-100 text-ocean-900 border border-ocean-200 rounded-lg text-[10px] font-bold transition shadow-2xs">
                        🌊 Timor Sea
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('tracking.store', $shipment->id) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_name') }}</label>
                        <input id="cp-location-name" type="text" name="location_name" placeholder="{{ __('ui.shipments.cp_name_ph') }}" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_hub') }}</label>
                        <select id="cp-location-id" name="location_id" onchange="syncSelectedHub(this)" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                            <option value="">{{ __('ui.shipments.cp_hub_free') }}</option>
                            @foreach($allLocations as $loc)
                            <option value="{{ $loc->id }}" data-name="{{ $loc->name }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}">{{ $loc->name }} ({{ $loc->city }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_lat') }}</label>
                        <input id="cp-lat" type="number" step="0.0000001" name="latitude" placeholder="e.g. 1.3521"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_lng') }}</label>
                        <input id="cp-lng" type="number" step="0.0000001" name="longitude" placeholder="e.g. 103.8198"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_milestone') }}</label>
                        <select name="event_status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                            <option value="CHECKPOINT">WAYPOINT_CHECKPOINT</option>
                            <option value="BERTH_ARRIVAL">PORT_ARRIVAL</option>
                            <option value="CUSTOMS_CLEARED">CUSTOMS_CLEARED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_status') }}</label>
                        <select name="new_status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                            <option value="">{{ __('ui.shipments.cp_keep', ['status' => $shipment->current_status]) }}</option>
                            <option value="BOOKED">BOOKED</option>
                            <option value="PICKED_UP">PICKED_UP</option>
                            <option value="IN_TRANSIT">IN_TRANSIT</option>
                            <option value="OUT_FOR_DELIVERY">OUT_FOR_DELIVERY</option>
                            <option value="DELIVERED">DELIVERED</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.cp_desc') }}</label>
                    <textarea id="cp-desc" name="description" rows="2" placeholder="{{ __('ui.shipments.cp_desc_ph') }}" required
                              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition">
                    {{ __('ui.shipments.cp_submit') }}
                </button>
            </form>
        </div>

        <!-- Form 2: Report Disruption / Exception -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm mb-4 flex items-center gap-2">
                <i class="ph ph-warning-diamond text-gold-600 text-lg"></i>
                <span>{{ __('ui.shipments.exception_title') }}</span>
            </h3>

            <form method="POST" action="{{ route('exceptions.store', $shipment->id) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.ex_category') }}</label>
                        <select name="exception_type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                            <option value="PORT_CONGESTION">PORT_CONGESTION</option>
                            <option value="WEATHER_DELAY">WEATHER_DELAY</option>
                            <option value="CUSTOMS_HOLD">CUSTOMS_HOLD</option>
                            <option value="DAMAGED">DAMAGED</option>
                            <option value="MISROUTED">MISROUTED</option>
                            <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.ex_severity') }}</label>
                        <select name="severity" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                            <option value="LOW">LOW (0 - 6h)</option>
                            <option value="MEDIUM" selected>MEDIUM (6 - 24h)</option>
                            <option value="HIGH">HIGH (> 24h)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.ex_impact') }}</label>
                    <input type="number" name="delay_hours_impact" value="12" min="1"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.ex_detail') }}</label>
                    <textarea name="description" rows="2" placeholder="{{ __('ui.shipments.ex_detail_ph') }}" required
                              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-gold-600 hover:bg-gold-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition">
                    {{ __('ui.shipments.ex_submit') }}
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Chronological Timeline -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 text-base mb-6 flex items-center gap-2">
            <i class="ph ph-clock-counter-clockwise text-ocean-700 text-xl"></i>
            <span>{{ __('ui.track.timeline_title') }}</span>
        </h3>

        <div class="relative border-l-2 border-ocean-200 ml-4 space-y-6 pb-4">
            @forelse($shipment->trackingEvents as $event)
            <div class="relative pl-6">
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
            <p class="text-slate-400 text-xs pl-4">{{ __('ui.track.timeline_empty') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyTrackingNum(btn) {
        navigator.clipboard.writeText(btn.getAttribute('data-num'));
        alert(btn.getAttribute('data-msg'));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const uiUnits = @json(__('ui.countdown'));
        const uiMap = @json(__('ui.map'));

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

        // 3. Live Countdown to Estimated Arrival
        const targetEta = new Date("{{ $shipment->estimated_arrival->toIso8601String() }}").getTime();
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetEta - now;
            const el = document.getElementById('eta-countdown');
            if (!el) return;

            if (distance < 0) {
                el.innerHTML = `<span class="text-emerald-400 font-bold">✅ ${uiUnits.arrived}</span>`;
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            el.innerHTML = `${days} ${uiUnits.days} ${hours} ${uiUnits.hours} ${minutes} ${uiUnits.minutes} ${seconds} ${uiUnits.seconds}`;
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // 4. Leaflet Map with Layer Switcher
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};
        const currentLat = {{ $shipment->current_latitude ?? $shipment->origin->latitude }};
        const currentLng = {{ $shipment->current_longitude ?? $shipment->origin->longitude }};

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

        const map = L.map('shipmentMap', {
            center: [currentLat, currentLng],
            zoom: 4,
            layers: [satelliteLayer, labelsOverlay]
        });

        const baseMaps = {
            [uiMap.layer_satellite]: satelliteLayer,
            [uiMap.layer_dark]: darkRadarLayer,
            [uiMap.layer_osm]: osmLayer
        };
        const overlays = {
            [uiMap.overlay_labels]: labelsOverlay
        };
        L.control.layers(baseMaps, overlays, { position: 'topright' }).addTo(map);

        map.on('baselayerchange', function (e) {
            if (e.layer === osmLayer) {
                if (map.hasLayer(labelsOverlay)) map.removeLayer(labelsOverlay);
            } else {
                if (!map.hasLayer(labelsOverlay)) labelsOverlay.addTo(map);
            }
        });

        L.marker([originLat, originLng]).addTo(map)
            .bindTooltip(`${uiMap.origin_prefix} {{ $shipment->origin->city }} ({{ $shipment->origin->code }})`, {
                permanent: true,
                direction: 'top',
                offset: [0, -32]
            })
            .bindPopup(`<b>${uiMap.popup_origin}</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}`);

        L.marker([destLat, destLng]).addTo(map)
            .bindTooltip(`${uiMap.dest_prefix} {{ $shipment->destination->city }} ({{ $shipment->destination->code }})`, {
                permanent: true,
                direction: 'bottom',
                offset: [0, 6]
            })
            .bindPopup(`<b>${uiMap.popup_dest}</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}`);

        L.circleMarker([currentLat, currentLng], {
            color: '#f59e0b',
            fillColor: '{{ $shipment->is_delayed ? "#f59e0b" : "#14b8a6" }}',
            fillOpacity: 0.95,
            weight: 3,
            radius: 9
        }).addTo(map)
        .bindPopup(`<b>${uiMap.popup_vessel}</b><br>{{ $shipment->current_location_name ?? "In Transit" }}<br>Status: {{ $shipment->current_status }}`)
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
            color: '#2dd4bf',
            weight: 3.5,
            dashArray: '8, 8',
            opacity: 0.85
        }).addTo(map);

        map.fitBounds(L.latLngBounds(pathCoords), { padding: [50, 50] });
    });

    function fillWaypoint(name, lat, lng, desc) {
        const hubSelect = document.getElementById('cp-location-id');
        if (hubSelect) hubSelect.value = '';
        document.getElementById('cp-location-name').value = name;
        document.getElementById('cp-lat').value = lat;
        document.getElementById('cp-lng').value = lng;
        document.getElementById('cp-desc').value = desc;
    }

    function syncSelectedHub(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        if (!option || !option.value) return;
        document.getElementById('cp-location-name').value = option.getAttribute('data-name') || '';
        document.getElementById('cp-lat').value = option.getAttribute('data-lat') || '';
        document.getElementById('cp-lng').value = option.getAttribute('data-lng') || '';
        if (!document.getElementById('cp-desc').value) {
            document.getElementById('cp-desc').value = 'Arrived at hub ' + (option.getAttribute('data-name') || '') + '.';
        }
    }
</script>
@endpush
