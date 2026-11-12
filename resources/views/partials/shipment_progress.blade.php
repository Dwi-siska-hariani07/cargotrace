@php
    $currentStep = $shipment->progressStep();
    $milestones = [
        1 => ['label' => __('ui.track.step1'), 'icon' => 'ph-clipboard-text'],
        2 => ['label' => __('ui.track.step2'), 'icon' => 'ph-package'],
        3 => ['label' => __('ui.track.step3'), 'icon' => $shipment->transport_type === 'AIR' ? 'ph-airplane-tilt' : ($shipment->transport_type === 'LAND' ? 'ph-truck' : 'ph-boat')],
        4 => ['label' => __('ui.track.step4'), 'icon' => 'ph-shield-check'],
        5 => ['label' => __('ui.track.step5'), 'icon' => 'ph-seal-check'],
    ];
@endphp

<div class="bg-white rounded-3xl border border-ocean-200/75 shadow-sm shadow-ocean-950/5 p-5 sm:p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <i class="ph ph-git-commit text-lg text-ocean-700"></i>
            <span>{{ __('ui.track.step_title') }}</span>
        </h3>
        <span class="text-xs font-semibold text-ocean-800 bg-ocean-50 px-3 py-1 rounded-full border border-ocean-200/80 self-start sm:self-auto">
            {{ $shipment->statusLabel() }}
        </span>
    </div>

    @if($currentStep === 0)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2.5">
            <i class="ph ph-prohibit text-lg text-rose-600"></i>
            <span>{{ __('ui.track.step_cancelled') }}</span>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-2 relative">
            @foreach($milestones as $stepNum => $step)
                @php
                    $isDone = $currentStep > $stepNum || ($currentStep === 5 && $stepNum === 5);
                    $isCurrent = $currentStep === $stepNum && $currentStep < 5;
                    $isDelayedCurrent = $isCurrent && $shipment->is_delayed;
                @endphp
                <div class="relative flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 p-2.5 sm:p-2 rounded-2xl
                    {{ $isCurrent ? ($isDelayedCurrent ? 'bg-amber-50/80 border border-amber-200' : 'bg-ocean-50/70 border border-ocean-200') : '' }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-base font-bold transition
                        @if($isDone)
                            bg-emerald-600 text-white shadow-sm
                        @elseif($isDelayedCurrent)
                            bg-amber-500 text-slate-950 ring-4 ring-amber-200
                        @elseif($isCurrent)
                            bg-[#042f2e] text-gold-300 ring-4 ring-ocean-200
                        @else
                            bg-slate-100 text-slate-400 border border-slate-200
                        @endif">
                        <i class="ph {{ $isDone ? 'ph-check' : $step['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-mono font-bold uppercase tracking-wider
                            {{ $isDone ? 'text-emerald-700' : ($isCurrent ? 'text-ocean-900' : 'text-slate-400') }}">
                            0{{ $stepNum }}
                        </span>
                        <span class="block text-xs font-bold leading-snug
                            {{ $isDone || $isCurrent ? 'text-slate-900' : 'text-slate-400' }}">
                            {{ $step['label'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Structured Fleet, Container/AWB & Receiver Metadata -->
    <div class="pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
        <div class="bg-slate-50/90 rounded-2xl p-3.5 border border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-ocean-100 text-ocean-800 flex items-center justify-center shrink-0">
                <i class="ph {{ $shipment->transport_type === 'AIR' ? 'ph-airplane-takeoff' : ($shipment->transport_type === 'LAND' ? 'ph-truck' : 'ph-anchor') }} text-lg"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.track.vessel_flight_label') }}</span>
                <span class="font-bold text-slate-900 block truncate">{{ $shipment->vessel_or_flight ?: ($shipment->carrier->name ?? 'Scheduled Carrier') }}</span>
            </div>
        </div>

        <div class="bg-slate-50/90 rounded-2xl p-3.5 border border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gold-100 text-gold-800 flex items-center justify-center shrink-0">
                <i class="ph ph-barcode text-lg"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.track.container_awb_label') }}</span>
                <span class="font-mono font-bold text-slate-900 block truncate">{{ $shipment->container_or_awb ?: $shipment->shipment_number }}</span>
            </div>
        </div>

        <div class="bg-slate-50/90 rounded-2xl p-3.5 border border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <i class="ph ph-buildings text-lg"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">{{ __('ui.track.receiver_label') }}</span>
                <span class="font-bold text-slate-900 block truncate">{{ $shipment->receiver_name ?: ($shipment->destination->city . ', ' . $shipment->destination->country) }}</span>
            </div>
        </div>
    </div>

    @if($shipment->current_status === 'DELIVERED')
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <div class="flex items-center gap-2.5 text-emerald-900">
                <i class="ph ph-seal-check text-xl text-emerald-600 shrink-0"></i>
                <div>
                    <strong class="block font-bold">{{ __('ui.track.epod_badge') }}</strong>
                    <span class="text-emerald-700">
                        {{ __('ui.track.epod_verified') }}
                        <span class="font-mono font-semibold">{{ ($shipment->actual_arrival ?? $shipment->estimated_arrival)->format('d M Y, H:i') }}</span>
                        @if($shipment->receiver_name)
                            • {{ $shipment->receiver_name }}
                        @endif
                    </span>
                </div>
            </div>
            <span class="font-mono text-[11px] font-bold text-emerald-800 bg-white px-2.5 py-1 rounded-lg border border-emerald-200 self-start sm:self-center">
                e-POD VERIFIED
            </span>
        </div>
    @endif
</div>
