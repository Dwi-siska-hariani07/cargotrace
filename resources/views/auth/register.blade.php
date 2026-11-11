@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-ocean-800 to-[#021d1d] border border-gold-500/40 text-gold-400 flex items-center justify-center text-3xl mx-auto shadow-md mb-3">
                <i class="ph ph-user-plus"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('ui.auth.register_title') }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ __('ui.auth.register_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold uppercase text-slate-600 mb-1">{{ __('ui.auth.full_name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase text-slate-600 mb-1">{{ __('ui.auth.email_label') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="role" class="block text-xs font-bold uppercase text-slate-600 mb-1">{{ __('ui.auth.role_label') }}</label>
                    <select id="role" name="role" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-ocean-500">
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>{{ __('ui.auth.role_customer') }}</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>{{ __('ui.auth.role_operator') }}</option>
                    </select>
                </div>
                <div>
                    <label for="company" class="block text-xs font-bold uppercase text-slate-600 mb-1">{{ __('ui.auth.company_label') }}</label>
                    <input id="company" type="text" name="company" value="{{ old('company') }}" placeholder="PT / Corp"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase text-slate-600 mb-1">{{ __('ui.auth.password_label') }}</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase text-slate-600 mb-1">{{ __('ui.auth.confirm_password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-ocean-500">
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-ocean-700 to-ocean-800 hover:from-ocean-800 hover:to-ocean-900 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <span>{{ __('ui.auth.register_btn') }}</span>
                <i class="ph ph-arrow-right font-bold text-gold-400"></i>
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            {{ __('ui.auth.have_account') }}
            <a href="{{ route('login') }}" class="text-ocean-700 font-bold hover:underline">{{ __('ui.auth.login_link') }}</a>
        </div>
    </div>
</div>
@endsection
