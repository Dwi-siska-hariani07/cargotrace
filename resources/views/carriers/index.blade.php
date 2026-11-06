@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Logistics Carriers</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage global shipping lines, airlines and freight partners</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Carriers Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm">Active Carriers</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-semibold">
                            <th class="py-3 px-4">Code</th>
                            <th class="py-3 px-4">Carrier Name</th>
                            <th class="py-3 px-4">Mode</th>
                            <th class="py-3 px-4">Contact</th>
                            <th class="py-3 px-4">Shipments</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($carriers as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $c->code }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $c->name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $c->type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                <div>{{ $c->contact_email ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $c->contact_phone ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">
                                {{ $c->shipments_count }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Carrier Form -->
        @if(auth()->user()->isAdmin())
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Add New Carrier</h3>
            <form method="POST" action="{{ route('carriers.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Carrier Code *</label>
                    <input type="text" name="code" placeholder="e.g. HAPAG" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Carrier Name *</label>
                    <input type="text" name="name" placeholder="e.g. Hapag-Lloyd AG" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Transport Mode *</label>
                    <select name="type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                        <option value="SEA">SEA (Maritime Shipping)</option>
                        <option value="AIR">AIR (Air Freight)</option>
                        <option value="LAND">LAND (Trucking)</option>
                        <option value="MULTIMODAL">MULTIMODAL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" placeholder="operations@carrier.com"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" placeholder="+1 800 000 000"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase rounded-xl transition shadow-xs">
                    Save Carrier
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

