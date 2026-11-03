<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CargoTrace') }} — Global Shipment Visibility & Tracking</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet.js (Map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased">
    <!-- Navbar -->
    <nav class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center gap-8">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow">
                            <i class="ph ph-navigation-arrow"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-white font-bold text-lg tracking-wide leading-none">Cargo<span class="text-blue-400">Trace</span></span>
                            <span class="text-slate-400 text-[10px] tracking-wider uppercase">Shipment Visibility</span>
                        </div>
                    </a>

                    @auth
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('shipments.*') ? 'bg-slate-800 text-blue-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Shipments
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('carriers.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('carriers.*') ? 'bg-slate-800 text-blue-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Carriers
                        </a>
                        <a href="{{ route('locations.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('locations.*') ? 'bg-slate-800 text-blue-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Ports & Hubs
                        </a>
                        <a href="{{ route('audit_logs.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('audit_logs.*') ? 'bg-slate-800 text-blue-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Audit Logs
                        </a>
                        @endif
                    </div>
                    @endauth
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-slate-400 hover:text-white text-sm flex items-center gap-1 mr-2">
                        <i class="ph ph-magnifying-glass"></i> Public Track
                    </a>

                    @auth
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-white text-sm font-medium leading-none">{{ auth()->user()->name }}</span>
                            <span class="text-xs uppercase font-semibold mt-1 px-2 py-0.5 rounded-full inline-block
                                @if(auth()->user()->isAdmin()) bg-purple-900/60 text-purple-300 border border-purple-700
                                @elseif(auth()->user()->isOperator()) bg-emerald-900/60 text-emerald-300 border border-emerald-700
                                @else bg-blue-900/60 text-blue-300 border border-blue-700 @endif">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout" class="w-9 h-9 rounded-lg bg-slate-800 text-slate-300 hover:bg-rose-900/50 hover:text-rose-300 flex items-center justify-center transition">
                                <i class="ph ph-sign-out text-lg"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-lg shadow transition">
                        Sign In
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center gap-2">
            <i class="ph ph-check-circle text-lg text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-lg bg-rose-50 border border-rose-200 flex items-center gap-2">
            <i class="ph ph-warning-circle text-lg text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-lg bg-rose-50 border border-rose-200">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="ph ph-x-circle text-lg text-rose-600"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside ml-2 space-y-1">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                <strong>CargoTrace</strong> — Global Shipment Visibility & Tracking Platform (Web Programming 2 UAS)
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Built with Laravel 11 & Leaflet.js</span>
                <span>•</span>
                <span>REST API Enabled</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
