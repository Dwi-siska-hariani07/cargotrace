@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-ocean-100 text-ocean-900 border border-ocean-200">
                        Shipment Initiation
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Register New Cargo Shipment</h1>
                <p class="text-xs text-slate-500 mt-1">Initialize shipment lifecycle with origin, carrier, schedule, and cargo specifications</p>
            </div>
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                &larr; Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Customer & Carrier -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Customer / Consignee *</label>
                    <select name="customer_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-ocean-500">
                        <option value="">Pilih Akun Customer</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->company ?? $c->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Armada / Carrier Operator *</label>
                    <select name="carrier_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-ocean-500">
                        <option value="">Pilih Operator Logistik</option>
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
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Moda Transportasi *</label>
                    <select name="transport_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-ocean-500">
                        <option value="SEA" {{ old('transport_type') === 'SEA' ? 'selected' : '' }}>🚢 Kapal Kontainer Laut (Sea)</option>
                        <option value="AIR" {{ old('transport_type') === 'AIR' ? 'selected' : '' }}>✈️ Pesawat Kargo Udara (Air)</option>
                        <option value="LAND" {{ old('transport_type') === 'LAND' ? 'selected' : '' }}>🚛 Angkutan Darat (Land)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Pelabuhan / Hub Asal *</label>
                    <select name="origin_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-ocean-500">
                        <option value="">Pilih Asal</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Pelabuhan / Hub Tujuan *</label>
                    <select name="destination_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-ocean-500">
                        <option value="">Pilih Tujuan</option>
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
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Jadwal Rencana Keberangkatan *</label>
                    <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Jadwal Estimasi Kedatangan (ETA) *</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
                </div>
            </div>

            <!-- Section 4: Cargo Specifications -->
            <div class="p-5 bg-ocean-50/50 rounded-2xl border border-ocean-100 space-y-4">
                <span class="text-xs font-bold text-ocean-950 uppercase tracking-wider block flex items-center gap-1.5">
                    <i class="ph ph-package text-ocean-700"></i>
                    <span>Spesifikasi & Manifest Muatan</span>
                </span>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Jenis Komoditas *</label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Consumer Electronics') }}" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-ocean-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Total Berat Kotor (kg) *</label>
                        <input type="number" step="0.01" name="total_weight_kg" value="{{ old('total_weight_kg', '5000') }}" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-ocean-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Volume Kontainer (CBM / m³)</label>
                        <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', '25') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-ocean-500 bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Item Spesifik</label>
                        <input type="text" name="item_name" value="{{ old('item_name', 'Industrial Hardware Boxes') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-ocean-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Jenis Kemasan</label>
                        <input type="text" name="package_type" value="{{ old('package_type', 'Pallets') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-ocean-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Jumlah Unit</label>
                        <input type="number" name="quantity" value="{{ old('quantity', '50') }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-ocean-500 bg-white">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center gap-2">
                    <i class="ph ph-check-bold text-gold-400"></i>
                    <span>Simpan & Mulai Siklus Hidup Kargo</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
