@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-ocean-100 text-ocean-900 border border-ocean-200">
                    Administrasi Akses
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Manajemen Pengguna & Hak Akses</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola akun Administrator, Operator Logistik, dan Customer CargoTrace</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Users Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm">Daftar Akun Terdaftar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-400 uppercase font-semibold">
                            <th class="py-3 px-4">Nama & Perusahaan</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Role Saat Ini</th>
                            <th class="py-3 px-4">Ubah Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                        <tr class="hover:bg-ocean-50/30 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $u->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $u->company ?? 'Personal / Internal' }} • {{ $u->shipments_count }} Pengiriman</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    @if($u->role === 'admin') bg-gold-100 text-gold-900 border border-gold-300
                                    @elseif($u->role === 'operator') bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @else bg-ocean-100 text-ocean-900 border border-ocean-300 @endif">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.updateRole', $u->id) }}" class="flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="px-2 py-1 text-[11px] rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                                        <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                        <option value="operator" {{ $u->role === 'operator' ? 'selected' : '' }}>Operator</option>
                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="px-2.5 py-1 bg-ocean-700 hover:bg-ocean-800 text-white rounded-lg text-[10px] font-bold transition">
                                        Simpan
                                    </button>
                                </form>
                                @else
                                <span class="text-[10px] font-semibold text-slate-400 italic">Akun Aktif Anda</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $users->links() }}
            </div>
            @endif
        </div>

        <!-- Create User Form -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm h-fit">
            <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2">
                <i class="ph ph-user-plus text-ocean-700 text-lg"></i>
                <span>Tambah Akun Pengguna Baru</span>
            </h3>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" placeholder="misal: Andi Operator" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Alamat Email *</label>
                    <input type="email" name="email" placeholder="andi@cargotrace.com" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Role / Hak Akses *</label>
                        <select name="role" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-ocean-500">
                            <option value="customer">Customer</option>
                            <option value="operator" selected>Operator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Password Minimal 8 *</label>
                        <input type="password" name="password" placeholder="••••••••" required minlength="8"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Nama Perusahaan</label>
                    <input type="text" name="company" placeholder="PT Logistik Nusantara"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" placeholder="+62 812..."
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-ocean-500">
                </div>
                <button type="submit" class="w-full py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase rounded-xl transition shadow-2xs">
                    Simpan Pengguna Baru
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

