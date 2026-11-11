<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#e8f1f0]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CargoTrace') }} — {{ __('ui.meta.title') }}</title>
    
    <!-- Custom SVG Favicon: Oceanic Compass / Anchor -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><circle cx='16' cy='16' r='14' fill='%23042f2e'/><path d='M16 6 L19 14 L27 16 L19 18 L16 26 L13 18 L5 16 L13 14 Z' fill='%23f59e0b'/><circle cx='16' cy='16' r='3' fill='%230f766e'/></svg>">

    <!-- Google Fonts: Poppins & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    },
                    colors: {
                        ocean: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        },
                        gold: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#042f2e',
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
<body class="min-h-screen flex flex-col font-sans text-slate-800 antialiased bg-[#e8f1f0]" style="background: linear-gradient(135deg, #e6f0ef 0%, #ecf4f3 50%, #f4f7f6 100%);">
    <!-- Navbar: Oceanic Teal & Nautical Gold Theme -->
    <nav class="bg-[#042f2e] border-b border-ocean-800/70 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand & Main Nav -->
                <div class="flex items-center gap-8">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-ocean-800 to-[#021d1d] border border-gold-500/40 flex items-center justify-center text-gold-400 font-bold text-xl shadow-inner group-hover:border-gold-400 transition">
                            <i class="ph ph-compass text-2xl text-gold-400"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-white font-extrabold text-lg tracking-wide leading-none">Cargo<span class="text-gold-400">Trace</span></span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-gold-500/20 text-gold-300 border border-gold-500/30">OCEANIC</span>
                            </div>
                            <span class="text-ocean-300 text-[10px] tracking-wider uppercase font-medium">{{ __('ui.nav.tagline') }}</span>
                        </div>
                    </a>

                    @auth
                    <div class="hidden md:flex items-center gap-1.5">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60 shadow-sm' : 'text-ocean-100/80 hover:bg-ocean-900/60 hover:text-white' }}">
                            {{ __('ui.nav.dashboard') }}
                        </a>
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('shipments.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60 shadow-sm' : 'text-ocean-100/80 hover:bg-ocean-900/60 hover:text-white' }}">
                            {{ __('ui.nav.shipments') }}
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('carriers.index') }}" class="px-3 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('carriers.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60 shadow-sm' : 'text-ocean-100/80 hover:bg-ocean-900/60 hover:text-white' }}">
                            {{ __('ui.nav.carriers') }}
                        </a>
                        <a href="{{ route('locations.index') }}" class="px-3 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('locations.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60 shadow-sm' : 'text-ocean-100/80 hover:bg-ocean-900/60 hover:text-white' }}">
                            {{ __('ui.nav.locations') }}
                        </a>
                        <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('users.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60 shadow-sm' : 'text-ocean-100/80 hover:bg-ocean-900/60 hover:text-white' }}">
                            {{ __('ui.nav.users') }}
                        </a>
                        <a href="{{ route('audit_logs.index') }}" class="px-3 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('audit_logs.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60 shadow-sm' : 'text-ocean-100/80 hover:bg-ocean-900/60 hover:text-white' }}">
                            {{ __('ui.nav.audit_logs') }}
                        </a>
                        @endif
                    </div>
                    @endauth
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2 sm:gap-3.5">
                    <!-- Bilingual Language Switcher (ID / EN) -->
                    <div class="inline-flex items-center p-0.5 rounded-xl bg-ocean-900/90 border border-ocean-700/80 text-[11px] font-bold shadow-xs" role="group" aria-label="Language Switcher">
                        <a href="{{ route('locale.switch', 'id') }}"
                           class="px-2 py-1 rounded-lg transition {{ app()->getLocale() === 'id' ? 'bg-gold-500 text-slate-950 shadow-2xs' : 'text-ocean-200 hover:text-white' }}">
                            🇮🇩 ID
                        </a>
                        <a href="{{ route('locale.switch', 'en') }}"
                           class="px-2 py-1 rounded-lg transition {{ app()->getLocale() === 'en' ? 'bg-gold-500 text-slate-950 shadow-2xs' : 'text-ocean-200 hover:text-white' }}">
                            🇬🇧 EN
                        </a>
                    </div>

                    <a href="{{ route('home') }}" class="text-ocean-200 hover:text-gold-300 text-sm font-medium flex items-center gap-1.5 transition">
                        <i class="ph ph-magnifying-glass text-gold-400"></i> <span class="hidden sm:inline">{{ __('ui.nav.public_track') }}</span>
                    </a>

                    @auth
                    @php
                        $unreadNotifCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" title="{{ __('ui.nav.notification_center') }}" class="relative w-9 h-9 rounded-xl bg-ocean-900 border {{ request()->routeIs('notifications.*') ? 'border-gold-400 text-gold-400' : 'border-ocean-700 text-ocean-200 hover:text-gold-300' }} flex items-center justify-center transition shadow-xs">
                        <i class="ph ph-bell text-lg"></i>
                        @if($unreadNotifCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-gold-500 text-slate-950 font-extrabold text-[10px] flex items-center justify-center shadow ring-2 ring-[#042f2e]">
                            {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
                        </span>
                        @endif
                    </a>

                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-white text-sm font-medium leading-none">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] uppercase font-bold mt-1 px-2 py-0.5 rounded-full inline-block
                                @if(auth()->user()->isAdmin()) bg-gold-500/20 text-gold-300 border border-gold-500/40
                                @elseif(auth()->user()->isOperator()) bg-emerald-500/20 text-emerald-300 border border-emerald-500/40
                                @else bg-ocean-500/20 text-ocean-300 border border-ocean-500/40 @endif">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="{{ __('ui.nav.logout') }}" class="w-9 h-9 rounded-xl bg-ocean-900 border border-ocean-700 text-ocean-200 hover:bg-rose-950/60 hover:border-rose-700 hover:text-rose-300 flex items-center justify-center transition shadow-xs">
                                <i class="ph ph-sign-out text-lg"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-slate-950 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="ph ph-sign-in font-bold"></i>
                        <span>{{ __('ui.nav.sign_in') }}</span>
                    </a>
                    @endauth
                </div>
            </div>

            @auth
            <!-- Mobile Scrollable Navigation -->
            <div class="md:hidden flex items-center gap-1.5 pb-3 pt-1 overflow-x-auto no-scrollbar border-t border-ocean-800/60">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.dashboard') }}
                </a>
                <a href="{{ route('shipments.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('shipments.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.shipments') }}
                </a>
                <a href="{{ route('notifications.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('notifications.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.notifications') }}
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('carriers.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('carriers.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.carriers') }}
                </a>
                <a href="{{ route('locations.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('locations.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.locations') }}
                </a>
                <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('users.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.users') }}
                </a>
                <a href="{{ route('audit_logs.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ request()->routeIs('audit_logs.*') ? 'bg-ocean-900 text-gold-400 border border-ocean-700/60' : 'text-ocean-100/80' }}">
                    {{ __('ui.nav.audit_logs') }}
                </a>
                @endif
            </div>
            @endauth
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-900 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 shadow-xs">
            <i class="ph ph-check-circle text-xl text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-4 text-sm text-rose-900 rounded-2xl bg-rose-50 border border-rose-200 flex items-center gap-3 shadow-xs">
            <i class="ph ph-warning-circle text-xl text-rose-600 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 mb-4 text-sm text-rose-900 rounded-2xl bg-rose-50 border border-rose-200 shadow-xs">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="ph ph-x-circle text-xl text-rose-600"></i>
                <span>{{ __('ui.nav.validation_errors') }}</span>
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
    <footer class="bg-[#042f2e] text-ocean-200 border-t border-ocean-800/80 pt-10 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8 border-b border-ocean-800/70">
                <div>
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-ocean-900 border border-gold-500/40 flex items-center justify-center text-gold-400">
                            <i class="ph ph-compass text-lg"></i>
                        </div>
                        <span class="text-white font-extrabold text-base tracking-wide">Cargo<span class="text-gold-400">Trace</span></span>
                    </div>
                    <p class="text-xs text-ocean-200/80 leading-relaxed max-w-sm">
                        {{ __('ui.footer.desc') }}
                    </p>
                </div>

                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">{{ __('ui.footer.corridors_title') }}</h4>
                    <div class="grid grid-cols-2 gap-1.5 text-xs text-ocean-200/80">
                        <span>🇮🇩 Jakarta (IDJKT)</span>
                        <span>🇳🇱 Rotterdam (NLRTM)</span>
                        <span>🇸🇬 Singapore (SGSIN)</span>
                        <span>🇦🇺 Sydney (AUSYD)</span>
                        <span>🇦🇪 Dubai (AEDXB)</span>
                        <span>🇺🇸 Los Angeles (USLAX)</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">{{ __('ui.footer.quick_access_title') }}</h4>
                    <ul class="space-y-1.5 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-gold-300 transition flex items-center gap-1.5"><i class="ph ph-magnifying-glass"></i> {{ __('ui.footer.link_track') }}</a></li>
                        <li><a href="{{ route('public.track', ['number' => 'CRG-2026-8802']) }}" class="hover:text-gold-300 transition flex items-center gap-1.5"><i class="ph ph-boat"></i> {{ __('ui.footer.link_vessel') }}</a></li>
                        @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gold-300 transition flex items-center gap-1.5"><i class="ph ph-squares-four"></i> {{ __('ui.footer.link_dashboard') }}</a></li>
                        @else
                        <li><a href="{{ route('login') }}" class="hover:text-gold-300 transition flex items-center gap-1.5"><i class="ph ph-sign-in"></i> {{ __('ui.footer.link_login') }}</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="pt-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-ocean-300/70">
                <span>&copy; {{ date('Y') }} <strong>CargoTrace Global Logistics</strong>. {{ __('ui.footer.rights') }}</span>
                <span>{{ __('ui.footer.tech_bar') }}</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
