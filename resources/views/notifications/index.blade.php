@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-ocean-100 text-ocean-900 border border-ocean-200">
                    Pusat Pemberitahuan
                </span>
                @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-gold-500 text-slate-950">
                    {{ $unreadCount }} Baru
                </span>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Notifikasi & Peringatan Pengiriman</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau perubahan status kargo, jadwal kedatangan, dan laporan gangguan operasional</p>
        </div>

        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="px-4 py-2.5 bg-ocean-700 hover:bg-ocean-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="ph ph-checks text-base text-gold-300"></i>
                <span>Tandai Semua Dibaca</span>
            </button>
        </form>
        @endif
    </div>

    <!-- Notification List -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
        <div class="p-5 rounded-3xl border transition flex flex-col sm:flex-row sm:items-center justify-between gap-4
            {{ $notif->is_read ? 'bg-white border-slate-200 opacity-85' : 'bg-ocean-50/60 border-ocean-300 shadow-sm' }}">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl shrink-0
                    @if($notif->type === 'EXCEPTION_ALERT') bg-rose-100 text-rose-600 border border-rose-200
                    @else bg-ocean-100 text-ocean-700 border border-ocean-200 @endif">
                    @if($notif->type === 'EXCEPTION_ALERT')
                        <i class="ph ph-warning-octagon"></i>
                    @else
                        <i class="ph ph-bell-ringing"></i>
                    @endif
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                            @if($notif->type === 'EXCEPTION_ALERT') bg-rose-100 text-rose-800 border border-rose-200
                            @else bg-ocean-100 text-ocean-900 border border-ocean-200 @endif">
                            {{ str_replace('_', ' ', $notif->type ?? 'UPDATE') }}
                        </span>
                        @if(!$notif->is_read)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-gold-200 text-gold-950 border border-gold-300">
                            BELUM DIBACA
                        </span>
                        @endif
                        <span class="text-[11px] font-mono text-slate-400">
                            {{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('d M Y, H:i') }})
                        </span>
                    </div>

                    <h3 class="font-bold text-slate-900 text-sm">{{ $notif->title }}</h3>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $notif->message }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                @if($notif->shipment)
                <a href="{{ route('shipments.show', $notif->shipment->id) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-ocean-100 text-ocean-800 border border-ocean-200 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="ph ph-map-pin-line"></i>
                    <span>Lihat Kapal</span>
                </a>
                @endif

                @if(!$notif->is_read)
                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-ocean-700 hover:bg-ocean-800 text-white text-xs font-bold transition">
                        Tandai Dibaca
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
            <div class="w-14 h-14 rounded-2xl bg-ocean-50 text-ocean-600 flex items-center justify-center text-3xl mx-auto mb-3">
                <i class="ph ph-bell-slash"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Notifikasi</h3>
            <p class="text-xs text-slate-500 mt-1">Pemberitahuan otomatis akan muncul saat ada perubahan status pengiriman atau laporan gangguan.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

