@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Register New Cargo Shipment</h1>
                <p class="text-xs text-slate-500 mt-1">Initialize shipment lifecycle with origin, carrier and schedule</p>
            </div>
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                &larr; Back to List
            </a>
        </div>

        <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Customer & Carrier -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Customer / Consignee *</label>
                    <select name="customer_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">Select Customer Account</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->company ?? $c->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Carrier Operator *</label>
                    <select name="carrier_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">Select Logistics Carrier</option>
                        @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                            {{ $carrier->name }} ({{ $carrier->type }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Route & Transport Mode -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Transport Mode *</label>
                    <select name="transport_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="SEA" {{ old('transport_type') === 'SEA' ? 'selected' : '' }}>🚢 Sea Freight (Container)</option>
                        <option value="AIR" {{ old('transport_type') === 'AIR' ? 'selected' : '' }}>✈️ Air Freight (Cargo)</option>
                        <option value="LAND" {{ old('transport_type') === 'LAND' ? 'selected' : '' }}>🚛 Overland Trucking</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Origin Port/Hub *</label>
                    <select name="origin_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">Select Origin</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Destination Port/Hub *</label>
                    <select name="destination_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">Select Destination</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('destination_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 3: Schedule -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Expected Departure Date & Time *</label>
                    <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Expected Scheduled Arrival *</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <!-- Section 4: Cargo Specifications -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-4">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Cargo Specifications</span>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cargo Type / Commodity *</label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Consumer Electronics') }}" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Total Weight (kg) *</label>
                        <input type="number" step="0.01" name="total_weight_kg" value="{{ old('total_weight_kg', '5000') }}" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Volume (CBM / m³)</label>
                        <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', '25') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Primary Item Name</label>
                        <input type="text" name="item_name" value="{{ old('item_name', 'Industrial Hardware Boxes') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Package Type</label>
                        <input type="text" name="package_type" value="{{ old('package_type', 'Pallets') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity', '50') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition">
                    Save & Initiate Lifecycle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
