@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-ocean-800 to-[#021d1d] border border-gold-500/40 text-gold-400 flex items-center justify-center text-3xl mx-auto shadow-md mb-3">
                <i class="ph ph-compass"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke CargoTrace</h2>
            <p class="text-xs text-slate-500 mt-1">Platform Pemantauan Kargo & Logistik Maritim Global</p>
        </div>

        <!-- Quick One-Click Demo Buttons -->
        <div class="mb-6 p-4 bg-ocean-50/50 rounded-2xl border border-ocean-100">
            <span class="text-[11px] font-extrabold text-ocean-950 uppercase tracking-wider block mb-2.5 text-center">
                ⚡ Akses Cepat Demo (Klik untuk Isi Akun)
            </span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillCredentials('admin@cargotrace.com', 'password123')"
                        class="px-2 py-2 bg-white hover:bg-gold-50 text-gold-900 border border-gold-300 rounded-xl text-xs font-bold shadow-2xs transition text-center">
                    👑 Admin
                </button>
                <button type="button" onclick="fillCredentials('operator@cargotrace.com', 'password123')"
                        class="px-2 py-2 bg-white hover:bg-emerald-50 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold shadow-2xs transition text-center">
                    🛠️ Operator
                </button>
                <button type="button" onclick="fillCredentials('customer@cargotrace.com', 'password123')"
                        class="px-2 py-2 bg-white hover:bg-ocean-100 text-ocean-900 border border-ocean-300 rounded-xl text-xs font-bold shadow-2xs transition text-center">
                    📦 Customer
                </button>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold uppercase text-slate-600 mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500 transition">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase text-slate-600 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500 transition">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-ocean-700 border-slate-300 focus:ring-ocean-500">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-ocean-700 to-ocean-800 hover:from-ocean-800 hover:to-ocean-900 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <i class="ph ph-arrow-right font-bold text-gold-400"></i>
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="text-ocean-700 font-bold hover:underline">Daftar Akun Baru</a>
        </div>
    </div>
</div>

<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
