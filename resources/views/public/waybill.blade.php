<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shipment->shipment_number }} — {{ __('ui.waybill.doc_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                        ocean: { 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 700: '#0f766e', 800: '#115e59', 900: '#134e4a', 950: '#042f2e' },
                        gold: { 100: '#fef3c7', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <style>
        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .waybill-sheet {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen font-sans text-slate-800 antialiased py-6 px-4 bg-[#e8f1f0]" style="background: linear-gradient(135deg, #e6f0ef 0%, #ecf4f3 50%, #f4f7f6 100%);">

    <!-- Top Action Toolbar (Hidden When Printing) -->
    <div class="no-print max-w-4xl mx-auto mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('public.track', ['number' => $shipment->shipment_number]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-ocean-50 text-ocean-900 border border-ocean-200 text-xs font-bold shadow-sm transition">
            <span>{{ __('ui.waybill.back_track') }}</span>
        </a>

        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#042f2e] hover:bg-ocean-900 text-white text-xs font-bold shadow-md transition">
            <i class="ph ph-printer text-base text-gold-400"></i>
            <span>{{ __('ui.waybill.print_now') }}</span>
        </button>
    </div>

    <!-- A4 Printable Waybill / Electronic Bill of Lading -->
    <article class="waybill-sheet max-w-4xl mx-auto bg-white rounded-3xl border border-ocean-200/80 shadow-xl overflow-hidden">
        <!-- Corporate Letterhead -->
        <header class="bg-[#042f2e] text-white p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b-4 border-gold-500">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-900 border border-gold-500/50 flex items-center justify-center text-gold-400">
                        <i class="ph ph-compass text-2xl"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight">Cargo<span class="text-gold-400">Trace</span></span>
                        <span class="block text-[10px] uppercase tracking-widest text-ocean-200 font-semibold">Global Logistics &amp; Marine Registry</span>
                    </div>
                </div>
                <p class="text-[11px] text-ocean-200/85 max-w-md leading-relaxed">
                    {{ __('ui.footer.hq_address') }}<br>
                    Dispatch 24/7: +62 21 5099 8800 • ops@cargotrace.co.id
                </p>
            </div>

            <div class="flex items-center gap-4 bg-white/10 p-3.5 rounded-2xl border border-white/15">
                <div id="waybill-qr" class="bg-white p-1.5 rounded-xl w-20 h-20 flex items-center justify-center shrink-0"></div>
                <div class="text-left sm:text-right">
                    <span class="text-[10px] uppercase tracking-widest text-gold-400 font-bold block">{{ __('ui.waybill.bill_no') }}</span>
                    <span class="font-mono text-lg font-extrabold text-white block">{{ $shipment->shipment_number }}</span>
                    <span class="text-[10px] text-ocean-200 block mt-0.5">{{ __('ui.waybill.scan_verify') }}</span>
                </div>
            </div>
        </header>

        <!-- Document Banner -->
        <div class="px-6 sm:px-8 py-4 bg-ocean-50/70 border-b border-ocean-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-sm sm:text-base font-extrabold text-ocean-950 tracking-wide">{{ __('ui.waybill.doc_title') }}</h1>
                <p class="text-[11px] text-slate-500">{{ __('ui.waybill.doc_sub') }}</p>
            </div>
            <div class="text-left sm:text-right text-[11px]">
                <span class="text-slate-400 block">{{ __('ui.waybill.issued_at') }}</span>
                <span class="font-mono font-bold text-slate-800">{{ ($shipment->created_at ?? now())->format('d M Y, H:i') }} UTC</span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6 text-xs">
            <!-- Parties & Equipment Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-ocean-700 block">{{ __('ui.waybill.shipper') }}</span>
                    <p class="font-bold text-sm text-slate-900">{{ $shipment->customer->company ?? $shipment->customer->name ?? 'CargoTrace Shipper' }}</p>
                    <p class="text-slate-600">{{ $shipment->customer->name ?? '' }} • {{ $shipment->customer->email ?? '' }}</p>
                    <p class="text-slate-500 font-mono text-[11px]">{{ $shipment->customer->phone ?? '+62 21 5099 8800' }}</p>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-ocean-700 block">{{ __('ui.waybill.consignee') }}</span>
                    <p class="font-bold text-sm text-slate-900">{{ $shipment->receiver_name ?: ($shipment->destination->city . ' Import Terminal') }}</p>
                    <p class="text-slate-600">{{ $shipment->destination->name }}</p>
                    <p class="text-slate-500">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>
                </div>
            </div>

            <!-- Route & Carrier Metadata -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div class="p-3.5 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.waybill.carrier_vessel') }}</span>
                    <strong class="text-slate-900 block mt-0.5">{{ $shipment->carrier->name ?? 'N/A' }}</strong>
                    <span class="text-[11px] text-ocean-800 font-medium block mt-0.5">{{ $shipment->vessel_or_flight ?: $shipment->transport_type }}</span>
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.waybill.container_ref') }}</span>
                    <strong class="font-mono text-slate-900 block mt-0.5">{{ $shipment->container_or_awb ?: $shipment->shipment_number }}</strong>
                    <span class="text-[11px] text-slate-500 block mt-0.5">{{ $shipment->transport_type }} • ISO 6346</span>
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.waybill.origin_port') }}</span>
                    <strong class="text-slate-900 block mt-0.5">{{ $shipment->origin->name }}</strong>
                    <span class="font-mono text-[11px] text-ocean-700">{{ $shipment->origin->code }} ({{ $shipment->origin->city }})</span>
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.waybill.dest_port') }}</span>
                    <strong class="text-slate-900 block mt-0.5">{{ $shipment->destination->name }}</strong>
                    <span class="font-mono text-[11px] text-ocean-700">{{ $shipment->destination->code }} ({{ $shipment->destination->city }})</span>
                </div>
            </div>

            <!-- Itemized Manifest Table -->
            <div class="space-y-2">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">{{ __('ui.waybill.manifest_table') }}</h2>
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/90 border-b border-slate-200 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                                <th class="py-3 px-4">{{ __('ui.waybill.item_desc') }}</th>
                                <th class="py-3 px-4">{{ __('ui.waybill.hs_code') }}</th>
                                <th class="py-3 px-4">{{ __('ui.waybill.qty_pkg') }}</th>
                                <th class="py-3 px-4 text-right">{{ __('ui.waybill.weight_vol') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($shipment->items as $item)
                            <tr>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $item->item_name }}</td>
                                <td class="py-3 px-4 font-mono text-slate-600">{{ $item->hs_code ?? '8542.31.00' }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $item->quantity }} &times; {{ $item->package_type }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    {{ number_format((float) $item->weight_kg, 2) }} kg
                                    @if($item->volume_cbm)
                                        <span class="text-slate-400 font-normal">/ {{ $item->volume_cbm }} m³</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $shipment->cargo_type }}</td>
                                <td class="py-3 px-4 font-mono text-slate-600">GENERAL CARGO</td>
                                <td class="py-3 px-4 text-slate-700">1 Lot</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    {{ number_format((float) $shipment->total_weight_kg, 2) }} kg
                                    @if($shipment->total_volume_cbm)
                                        <span class="text-slate-400 font-normal">/ {{ $shipment->total_volume_cbm }} m³</span>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-ocean-50/60 border-t border-ocean-200 font-bold text-ocean-950">
                                <td colspan="2" class="py-3 px-4">{{ __('ui.waybill.status_box') }}: {{ $shipment->statusLabel() }}</td>
                                <td class="py-3 px-4">ETA: {{ $shipment->estimated_arrival->format('d M Y, H:i') }}</td>
                                <td class="py-3 px-4 text-right font-mono">
                                    {{ number_format((float) $shipment->total_weight_kg, 2) }} kg / {{ $shipment->total_volume_cbm ?? '-' }} m³
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes & Compliance -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600">
                <strong class="text-slate-800 block mb-1 uppercase tracking-wider text-[10px]">{{ __('ui.waybill.notes_label') }}</strong>
                <p>{{ $shipment->notes ?: 'Subject to CargoTrace Global Standard Conditions of Carriage, Incoterms 2020 & ISO 6346 container verification.' }}</p>
            </div>

            <!-- Authorization & Signature Boxes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <div class="p-4 rounded-2xl border border-dashed border-ocean-300 bg-ocean-50/30 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.waybill.signature_carrier') }}</span>
                        <p class="font-bold text-ocean-950 mt-2">CargoTrace Operations Desk</p>
                        <p class="font-mono text-[10px] text-ocean-700">AUTH-ID: CT-{{ str_pad((string) $shipment->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="w-16 h-16 rounded-full border-2 border-ocean-700 text-ocean-800 flex flex-col items-center justify-center text-center rotate-[-10deg] shrink-0 bg-white">
                        <span class="text-[8px] font-extrabold tracking-widest uppercase">CARGOTRACE</span>
                        <span class="text-[9px] font-black text-gold-600">VERIFIED</span>
                        <span class="text-[7px] font-mono">{{ date('Y') }}</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/40 flex flex-col justify-between">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.waybill.signature_receiver') }}</span>
                    @if($shipment->current_status === 'DELIVERED')
                        <div class="mt-2 flex items-center justify-between gap-2">
                            <div>
                                <p class="font-bold text-emerald-800">{{ $shipment->receiver_name ?: 'Authorized Consignee' }}</p>
                                <p class="font-mono text-[10px] text-emerald-700">{{ ($shipment->actual_arrival ?? $shipment->estimated_arrival)->format('d M Y, H:i') }} UTC</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300 font-mono text-[10px] font-extrabold">
                                e-POD SIGNED
                            </span>
                        </div>
                    @else
                        <div class="mt-6 border-t border-slate-300 pt-1 text-[10px] text-slate-400">
                            {{ $shipment->receiver_name ?: 'Signature & Company Stamp upon Delivery' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </article>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('waybill-qr');
            if (!container) return;
            const verifyUrl = @json(route('public.track', ['number' => $shipment->shipment_number]));
            if (typeof qrcode === 'function') {
                const qr = qrcode(0, 'M');
                qr.addData(verifyUrl);
                qr.make();
                container.innerHTML = qr.createSvgTag({ cellSize: 2, margin: 0, scalable: true });
                const svg = container.querySelector('svg');
                if (svg) {
                    svg.setAttribute('class', 'w-full h-full');
                }
            } else {
                container.innerHTML = '<span class="font-mono text-[9px] font-bold text-slate-700 text-center">' + @json($shipment->shipment_number) + '</span>';
            }
        });
    </script>
</body>
</html>
