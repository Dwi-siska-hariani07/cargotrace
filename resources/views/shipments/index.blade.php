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

        @if(!auth()->user()->isCustomer())
        <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition flex items-center gap-2">
            <i class="ph ph-plus-circle text-lg text-gold-300"></i>
            <span>{{ __('ui.shipments.new_btn') }}</span>
        </a>
        @endif
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.filter_search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.shipments.filter_search_ph') }}"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">{{ __('ui.shipments.filter_status') }}</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                    <option value="">{{ __('ui.shipments.all_statuses') }}</option>
                    <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>CREATED</option>
                    <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>BOOKED</option>
                    <option value="PICKED_UP" {{ request('status') === 'PICKED_UP' ? 'selected' : '' }}>PICKED UP</option>
                    <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>IN TRANSIT</option>
                    <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
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
                                {{ str_replace('_', ' ', $s->current_status) }}
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
@endsection
