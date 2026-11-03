@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ports & Hub Locations</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Geographical coordinates for origin, destination and waypoint check-ins</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Locations Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm">Registered Waypoints & Terminals</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-semibold">
                            <th class="py-3 px-4">Code</th>
                            <th class="py-3 px-4">Port / Hub Name</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">City & Country</th>
                            <th class="py-3 px-4">GIS Coordinates</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($locations as $loc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $loc->code }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $loc->name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $loc->type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $loc->city }}, {{ $loc->country }}
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                {{ number_format($loc->latitude, 4) }}, {{ number_format($loc->longitude, 4) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Location Form -->
        @if(auth()->user()->isAdmin())
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Add Port / Terminal Hub</h3>
            <form method="POST" action="{{ route('locations.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Code *</label>
                        <input type="text" name="code" placeholder="e.g. SGSIN" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Type *</label>
                        <select name="type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                            <option value="SEAPORT">SEAPORT</option>
                            <option value="AIRPORT">AIRPORT</option>
                            <option value="WAREHOUSE">WAREHOUSE</option>
                            <option value="CUSTOMS_CHECKPOINT">CUSTOMS</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Terminal / Port Name *</label>
                    <input type="text" name="name" placeholder="Port of Tanjung Priok" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">City *</label>
                        <input type="text" name="city" placeholder="Jakarta" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Country *</label>
                        <input type="text" name="country" placeholder="Indonesia" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Latitude *</label>
                        <input type="number" step="0.0000001" name="latitude" placeholder="-6.1038" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Longitude *</label>
                        <input type="number" step="0.0000001" name="longitude" placeholder="106.8821" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase rounded-xl transition shadow-xs">
                    Save Location
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
