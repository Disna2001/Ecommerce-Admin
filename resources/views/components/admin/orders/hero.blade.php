@php
    $stats = $this->stats;
@endphp

<div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
    @foreach([
        ['Processing Queue', $stats['processing'], 'fa-conveyor-belt', 'indigo', 'Operations'],
        ['Payment Reviews', $stats['payment_reviews'], 'fa-shield-check', 'emerald', 'Financial'],
        ['Awaiting Tracking', $stats['awaiting_tracking'], 'fa-truck-ramp-box', 'amber', 'Logistics'],
        ['Return Proposals', $stats['returns'], 'fa-rotate-left', 'rose', 'Service']
    ] as [$label, $val, $icon, $color, $category])
        <div class="group relative rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-2xl hover:-translate-y-1 overflow-hidden">
            <div class="absolute right-0 top-0 -mr-16 -mt-16 h-48 w-48 rounded-full bg-{{ $color }}-50/50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 shadow-inner group-hover:bg-{{ $color }}-600 group-hover:text-white transition-colors">
                        <i class="fas {{ $icon }} text-sm"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-{{ $color }}-600 bg-{{ $color }}-50 px-3 py-1 rounded-full">{{ $category }}</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2">{{ $label }}</p>
                <h3 class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($val) }}</h3>
            </div>
        </div>
    @endforeach
</div>
