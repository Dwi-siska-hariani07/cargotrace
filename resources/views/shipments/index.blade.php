@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-ocean-100 text-ocean-900 border border-ocean-200">
                    {{ __('ui.shipments.badge') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ __('ui.shipments.title') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('ui.shipments.subtitle') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('shipments.export.csv', request()->query()) }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl border border-slate-200 shadow-2xs transition flex items-center gap-2">
                <i class="ph ph-download-simple text-base text-ocean-700"></i>
                <span>{{ __('ui.shipments.export_csv') }}</span>
            </a>
            @if(!auth()->user()->isCustomer())
            <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="ph ph-plus-circle text-lg text-gold-300"></i>
                <span>{{ __('ui.shipments.new_btn') }}</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('shipments.index') }}" autocomplete="off" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div class="relative" id="shipmentSearchWrapper">
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.filter_search') }}</label>
                <div class="relative">
                    <input type="text" id="shipmentSearchInput" name="search" value="{{ request('search') }}" autocomplete="off"
                           placeholder="{{ __('ui.shipments.filter_search_ph') }}"
                           class="w-full pl-3 pr-8 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500 font-mono">
                    <button type="button" id="toggleShipmentSearchDropdown" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-ocean-700 transition p-1" title="{{ __('ui.shipments.dropdown_click_to_select') }}">
                        <i class="ph ph-caret-down text-xs transition-transform duration-200" id="shipmentSearchCaret"></i>
                    </button>
                </div>

                @if(isset($searchOptions) && $searchOptions->count() > 0)
                <div id="shipmentSearchDropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 max-h-64 overflow-y-auto divide-y divide-slate-100 text-slate-800 text-xs">
                    <div class="p-2.5 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between text-[11px] text-slate-500 sticky top-0 backdrop-blur-xs z-10">
                        <span class="font-bold uppercase tracking-wider flex items-center gap-1 text-ocean-950">
                            <i class="ph ph-package text-ocean-700"></i>
                            <span>{{ __('ui.shipments.dropdown_registered', ['count' => $searchOptions->count()]) }}</span>
                        </span>
                        <span class="text-[10px] text-slate-400">{{ __('ui.shipments.dropdown_click_to_select') }}</span>
                    </div>
                    <div id="shipmentSearchOptionsList">
                        @foreach($searchOptions as $opt)
                        <div class="shipment-opt-item p-2.5 hover:bg-ocean-50/70 transition cursor-pointer flex items-center justify-between gap-2"
                             onclick="selectShipmentSearch('{{ $opt->shipment_number }}')"
                             data-search="{{ strtolower($opt->shipment_number . ' ' . $opt->cargo_type . ' ' . ($opt->vessel_or_flight ?? '') . ' ' . ($opt->carrier->name ?? '')) }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0
                                    @if($opt->transport_type === 'SEA') bg-ocean-100 text-ocean-800
                                    @elseif($opt->transport_type === 'AIR') bg-amber-100 text-amber-900
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    @if($opt->transport_type === 'SEA') <i class="ph ph-boat text-xs"></i>
                                    @elseif($opt->transport_type === 'AIR') <i class="ph ph-airplane-tilt text-xs"></i>
                                    @else <i class="ph ph-truck text-xs"></i> @endif
                                </span>
                                <div class="min-w-0">
                                    <div class="font-mono font-bold text-slate-900 text-xs truncate">{{ $opt->shipment_number }}</div>
                                    <div class="text-[10px] text-slate-500 truncate">{{ $opt->cargo_type }} @if($opt->vessel_or_flight) • {{ $opt->vessel_or_flight }} @endif</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider shrink-0
                                @if($opt->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800
                                @elseif($opt->current_status === 'IN_TRANSIT') bg-ocean-100 text-ocean-900
                                @else bg-gold-100 text-gold-900 @endif">
                                {{ $opt->statusLabel() }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <div id="shipmentSearchNoResults" class="hidden p-4 text-center text-xs text-slate-400">
                        {{ __('ui.shipments.dropdown_no_match') }}
                    </div>
                </div>
                @endif
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.filter_status') }}</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                    <option value="">{{ __('ui.shipments.all_statuses') }}</option>
                    @foreach(['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ __('ui.status.' . $st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.filter_carrier') }}</label>
                <select name="carrier_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                    <option value="">{{ __('ui.shipments.all_carriers') }}</option>
                    @foreach($carriers as $c)
                    <option value="{{ $c->id }}" {{ request('carrier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-ocean-800 hover:bg-ocean-900 text-white rounded-xl text-xs font-bold transition shadow-2xs">
                    {{ __('ui.shipments.apply_btn') }}
                </button>
                <a href="{{ route('shipments.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                    {{ __('ui.shipments.reset_btn') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-400 uppercase font-semibold">
                        <th class="py-3.5 px-4">{{ __('ui.shipments.col_number') }}</th>
                        <th class="py-3.5 px-4">{{ __('ui.shipments.col_customer') }}</th>
                        <th class="py-3.5 px-4">{{ __('ui.shipments.col_carrier') }}</th>
                        <th class="py-3.5 px-4">{{ __('ui.shipments.col_route') }}</th>
                        <th class="py-3.5 px-4">{{ __('ui.shipments.col_status') }}</th>
                        <th class="py-3.5 px-4">{{ __('ui.shipments.col_eta') }}</th>
                        <th class="py-3.5 px-4 text-right">{{ __('ui.shipments.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shipments as $s)
                    <tr class="hover:bg-ocean-50/40 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                            {{ $s->shipment_number }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            <span class="font-medium text-slate-800 block">{{ $s->customer->name ?? 'N/A' }}</span>
                            <span class="text-[10px] text-slate-400">{{ $s->customer->company ?? '' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            <span class="font-bold text-ocean-900">{{ $s->carrier->name ?? 'N/A' }}</span>
                            <span class="text-[10px] block text-slate-400">{{ $s->transport_type }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            <div class="font-medium text-slate-800">{{ $s->origin->name }}</div>
                            <div class="text-slate-400 text-[10px]">&rarr; {{ $s->destination->name }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                @if($s->current_status === 'DELIVERED') bg-emerald-100 text-emerald-800 border border-emerald-300
                                @elseif($s->current_status === 'IN_TRANSIT') bg-ocean-100 text-ocean-900 border border-ocean-300
                                @elseif($s->current_status === 'CANCELLED') bg-rose-100 text-rose-800 border border-rose-300
                                @else bg-gold-100 text-gold-900 border border-gold-300 @endif">
                                {{ $s->statusLabel() }}
                            </span>
                            @if($s->is_delayed)
                            <span class="block mt-1 text-[9px] font-bold text-gold-900 bg-gold-100 border border-gold-300 px-1.5 py-0.5 rounded w-max">
                                {{ __('ui.shipments.delayed_hours', ['hours' => $s->delay_hours]) }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-500">
                            {{ $s->estimated_arrival->format('d M Y, H:i') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('shipments.show', $s->id) }}" class="px-3 py-1.5 rounded-xl bg-ocean-50 text-ocean-800 hover:bg-ocean-100 border border-ocean-200/60 font-bold transition text-xs">
                                    {{ __('ui.shipments.detail_btn') }}
                                </a>
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('shipments.destroy', $s->id) }}" data-confirm="{{ __('ui.shipments.delete_confirm') }}" onsubmit="return confirm(this.getAttribute('data-confirm'));" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Delete">
                                        <i class="ph ph-trash text-base"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">{{ __('ui.shipments.empty') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInp = document.getElementById('shipmentSearchInput');
    const dropdown = document.getElementById('shipmentSearchDropdown');
    const toggleBtn = document.getElementById('toggleShipmentSearchDropdown');
    const caret = document.getElementById('shipmentSearchCaret');
    const list = document.getElementById('shipmentSearchOptionsList');
    const noResults = document.getElementById('shipmentSearchNoResults');
    const wrapper = document.getElementById('shipmentSearchWrapper');

    function openDropdown() {
        if (!dropdown) return;
        dropdown.classList.remove('hidden');
        caret?.classList.add('rotate-180');
    }

    function closeDropdown() {
        if (!dropdown) return;
        dropdown.classList.add('hidden');
        caret?.classList.remove('rotate-180');
    }

    function filterOptions(query) {
        const items = list?.querySelectorAll('.shipment-opt-item');
        if (!items) return;
        const q = (query || '').trim().toLowerCase();
        let visibleCount = 0;

        items.forEach(item => {
            const data = item.getAttribute('data-search') || '';
            if (q === '' || data.includes(q)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        if (visibleCount === 0) {
            noResults?.classList.remove('hidden');
        } else {
            noResults?.classList.add('hidden');
        }
    }

    searchInp?.addEventListener('focus', () => {
        openDropdown();
        filterOptions(searchInp.value);
    });

    searchInp?.addEventListener('input', (e) => {
        openDropdown();
        filterOptions(e.target.value);
    });

    toggleBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (dropdown?.classList.contains('hidden')) {
            searchInp?.focus();
            openDropdown();
            filterOptions(searchInp.value);
        } else {
            closeDropdown();
        }
    });

    window.selectShipmentSearch = function(num) {
        if (searchInp) {
            searchInp.value = num;
            closeDropdown();
            searchInp.form?.submit();
        }
    };

    document.addEventListener('click', (e) => {
        if (wrapper && !wrapper.contains(e.target)) {
            closeDropdown();
        }
    });
});
</script>
@endsection
