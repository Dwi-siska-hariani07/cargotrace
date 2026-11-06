@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl mx-auto shadow-md mb-3">
                <i class="ph ph-user-plus"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Create an Account</h2>
            <p class="text-xs text-slate-500 mt-1">Join the CargoTrace visibility network</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold uppercase text-slate-600 mb-1">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase text-slate-600 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="role" class="block text-xs font-bold uppercase text-slate-600 mb-1">Role</label>
                    <select id="role" name="role" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                    </select>
                </div>
                <div>
                    <label for="company" class="block text-xs font-bold uppercase text-slate-600 mb-1">Company</label>
                    <input id="company" type="text" name="company" value="{{ old('company') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase text-slate-600 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase text-slate-600 mb-1">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                Register Account
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Sign In</a>
        </div>
    </div>
</div>
@endsection

