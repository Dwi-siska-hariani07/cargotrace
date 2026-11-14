@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 sm:py-12 px-4 sm:px-6">
    <!-- Elevated Natural Registration Card with Deep Drop-Shadow -->
    <div class="rounded-3xl border border-[#b8d6ce] shadow-[0_25px_60px_-15px_rgba(4,47,46,0.22),0_10px_25px_-5px_rgba(4,47,46,0.12)] overflow-hidden grid grid-cols-1 md:grid-cols-12 transition-all">

        <!-- Left Showcase Column: Maritime Logistics & Trust (Desktop) -->
        <div class="hidden md:flex md:col-span-5 relative bg-[#042f2e] text-white flex-col justify-between p-8 sm:p-9 overflow-hidden">
            <img src="{{ asset('images/banner-port.jpg') }}" alt="CargoTrace Seaport Hub" class="absolute inset-0 w-full h-full object-cover opacity-45">
            <div class="absolute inset-0 bg-gradient-to-b from-[#021d1d]/92 via-[#042f2e]/82 to-[#021d1d]/96"></div>

            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-white/10 border border-gold-400/40 text-gold-400 flex items-center justify-center text-2xl backdrop-blur-xs mb-5 shadow-sm">
                    <i class="ph ph-user-plus"></i>
                </div>
                <span class="inline-block px-3 py-1 rounded-full bg-gold-500/20 border border-gold-400/40 text-gold-300 text-[10px] font-bold uppercase tracking-widest mb-3">
                    Registrasi Resmi &bull; Jaringan Global
                </span>
                <h2 class="text-2xl font-extrabold leading-snug tracking-tight text-white">
                    Akselerasi Visibilitas <span class="text-gold-400">Rantai Pasok Kargo</span>
                </h2>
                <p class="text-xs text-slate-200/90 leading-relaxed mt-3">
                    Daftarkan akun perusahaan atau operasional Anda untuk membuka visibilitas kargo laut & udara, telemetri satelit real-time, dan manajemen dokumen resmi.
                </p>
            </div>

            <div class="relative z-10 space-y-3 border-t border-white/15 pt-6 text-xs text-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg bg-ocean-500/20 border border-ocean-400/30 flex items-center justify-center shrink-0 text-gold-400">
                        <i class="ph ph-globe-hemisphere-east text-sm"></i>
                    </div>
                    <span>Monitoring rute maritim & kargo udara global</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg bg-ocean-500/20 border border-ocean-400/30 flex items-center justify-center shrink-0 text-gold-400">
                        <i class="ph ph-file-text text-sm"></i>
                    </div>
                    <span>Akses unduh Surat Muatan Resmi (Waybill / e-B/L)</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg bg-ocean-500/20 border border-ocean-400/30 flex items-center justify-center shrink-0 text-gold-400">
                        <i class="ph ph-shield-check text-sm"></i>
                    </div>
                    <span>Data terenkripsi korporat & verifikasi bea cukai</span>
                </div>
            </div>

            <div class="relative z-10 pt-4">
                <div class="px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-[11px] text-slate-300 flex items-center gap-2.5 backdrop-blur-xs">
                    <i class="ph ph-seal-check text-gold-400 text-base shrink-0"></i>
                    <span>Terintegrasi dengan 500+ pelabuhan & hub kargo dunia</span>
                </div>
            </div>
        </div>

        <!-- Right Form Column: Natural Calming Organic Tone (No stark white!) -->
        <div class="md:col-span-7 p-6 sm:p-9 lg:p-10 flex flex-col justify-center bg-gradient-to-b from-[#f2f7f5] via-[#ebf4f1] to-[#e4efec]">
            <div class="text-center md:text-left mb-6">
                <div class="md:hidden w-13 h-13 rounded-2xl bg-gradient-to-br from-ocean-800 to-[#021d1d] border border-gold-500/40 text-gold-400 flex items-center justify-center text-2xl mx-auto shadow-md mb-3">
                    <i class="ph ph-user-plus"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('ui.auth.register_title') }}</h1>
                <p class="text-xs text-slate-600 mt-1">{{ __('ui.auth.register_subtitle') }}</p>
            </div>

            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="font-bold flex items-center gap-1.5 mb-1 text-rose-900">
                    <i class="ph ph-warning-circle text-base text-rose-600"></i>
                    <span>Terdapat data yang perlu diperiksa:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ph ph-user text-ocean-700 text-sm"></i>
                        <span>{{ __('ui.auth.full_name') }}</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Masukkan nama lengkap Anda"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#bed8d2] bg-white/90 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-ocean-600 focus:ring-4 focus:ring-ocean-600/15 shadow-xs transition">
                </div>

                <!-- Alamat Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ph ph-envelope-simple text-ocean-700 text-sm"></i>
                        <span>{{ __('ui.auth.email_label') }}</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           placeholder="nama@perusahaan.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#bed8d2] bg-white/90 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-ocean-600 focus:ring-4 focus:ring-ocean-600/15 shadow-xs transition">
                </div>

                <!-- Peran / Role Interactive Selector Cards -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ph ph-identification-badge text-ocean-700 text-sm"></i>
                        <span>{{ __('ui.auth.role_label') }}</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2.5 mb-2">
                        <label class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition select-none bg-white/90 hover:bg-white has-[:checked]:bg-ocean-50 has-[:checked]:border-ocean-600 has-[:checked]:shadow-sm border-[#bed8d2]">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-1.5 font-bold text-xs text-slate-800 has-[:checked]:text-ocean-950">
                                    <i class="ph ph-user text-ocean-700"></i>
                                    <span>{{ __('ui.auth.role_customer') }}</span>
                                </div>
                                <input type="radio" name="role" value="customer" class="text-ocean-700 focus:ring-ocean-500" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }}>
                            </div>
                            <span class="text-[10px] text-slate-500 leading-tight">Pemilik kargo, lacak resi & unduh surat jalan</span>
                        </label>

                        <label class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition select-none bg-white/90 hover:bg-white has-[:checked]:bg-ocean-50 has-[:checked]:border-ocean-600 has-[:checked]:shadow-sm border-[#bed8d2]">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-1.5 font-bold text-xs text-slate-800 has-[:checked]:text-ocean-950">
                                    <i class="ph ph-compass text-ocean-700"></i>
                                    <span>{{ __('ui.auth.role_operator') }}</span>
                                </div>
                                <input type="radio" name="role" value="operator" class="text-ocean-700 focus:ring-ocean-500" {{ old('role') === 'operator' ? 'checked' : '' }}>
                            </div>
                            <span class="text-[10px] text-slate-500 leading-tight">Operasional pelabuhan, kargo & armada</span>
                        </label>
                    </div>
                </div>

                <!-- Nama Perusahaan -->
                <div>
                    <label for="company" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ph ph-buildings text-ocean-700 text-sm"></i>
                        <span>{{ __('ui.auth.company_label') }}</span>
                    </label>
                    <input id="company" type="text" name="company" value="{{ old('company') }}" placeholder="Contoh: PT Sumber Jaya Kargo"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#bed8d2] bg-white/90 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-ocean-600 focus:ring-4 focus:ring-ocean-600/15 shadow-xs transition">
                </div>

                <!-- Password & Confirm Password Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ph ph-lock-key text-ocean-700 text-sm"></i>
                            <span>{{ __('ui.auth.password_label') }}</span>
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required minlength="8"
                                   placeholder="Min. 8 karakter"
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-[#bed8d2] bg-white/90 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-ocean-600 focus:ring-4 focus:ring-ocean-600/15 shadow-xs transition">
                            <button type="button" onclick="togglePasswordVisibility('password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-ocean-700 transition p-1 focus:outline-none"
                                    title="Lihat / Sembunyikan Kata Sandi">
                                <i class="ph ph-eye text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ph ph-shield-check text-ocean-700 text-sm"></i>
                            <span>{{ __('ui.auth.confirm_password') }}</span>
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                                   placeholder="Ulangi kata sandi"
                                   class="w-full px-4 py-2.5 pr-10 rounded-xl border border-[#bed8d2] bg-white/90 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-ocean-600 focus:ring-4 focus:ring-ocean-600/15 shadow-xs transition">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-ocean-700 transition p-1 focus:outline-none"
                                    title="Lihat / Sembunyikan Kata Sandi">
                                <i class="ph ph-eye text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-ocean-800 via-ocean-700 to-[#042f2e] hover:from-ocean-900 hover:to-ocean-800 text-white font-bold text-sm rounded-xl shadow-lg shadow-ocean-900/20 hover:shadow-xl transition-all flex items-center justify-center gap-2 group cursor-pointer">
                        <span>{{ __('ui.auth.register_btn') }}</span>
                        <i class="ph ph-arrow-right font-bold text-gold-400 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-[#bed8d2]/70 text-center text-xs text-slate-600">
                {{ __('ui.auth.have_account') }}
                <a href="{{ route('login') }}" class="text-ocean-800 font-extrabold hover:text-ocean-950 hover:underline inline-flex items-center gap-1">
                    <span>{{ __('ui.auth.login_link') }}</span>
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ph ph-eye-slash text-lg text-ocean-700';
            btn.classList.add('text-ocean-700');
        } else {
            input.type = 'password';
            icon.className = 'ph ph-eye text-lg';
            btn.classList.remove('text-ocean-700');
        }
    }
</script>
@endsection
