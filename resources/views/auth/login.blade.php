@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl mx-auto shadow-md mb-3">
                <i class="ph ph-lock-key"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Sign In to CargoTrace</h2>
            <p class="text-xs text-slate-500 mt-1">Access operational dashboards, tracking events & management</p>
        </div>

        <!-- Quick One-Click Demo Buttons -->
        <div class="mb-6 p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2 text-center">
                🚀 One-Click Demo Credentials
            </span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillCredentials('admin@cargotrace.com', 'password123')"
                        class="px-2 py-1.5 bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 rounded-xl text-xs font-semibold shadow-xs transition text-center">
                    👑 Admin
                </button>
                <button type="button" onclick="fillCredentials('operator@cargotrace.com', 'password123')"
                        class="px-2 py-1.5 bg-white hover:bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold shadow-xs transition text-center">
                    🛠️ Operator
                </button>
                <button type="button" onclick="fillCredentials('customer@cargotrace.com', 'password123')"
                        class="px-2 py-1.5 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 rounded-xl text-xs font-semibold shadow-xs transition text-center">
                    📦 Customer
                </button>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold uppercase text-slate-600 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase text-slate-600 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-blue-600 border-slate-300">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Register here</a>
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
