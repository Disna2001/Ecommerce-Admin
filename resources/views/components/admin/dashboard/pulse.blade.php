@props(['todayStockOut', 'todayStockIn', 'todayReversals', 'sentOutboxToday'])

<div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-center gap-4 mb-8">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg">
            <i class="fas fa-wave-square text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Warehouse Pulse</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Real-time Movement Telemetry</p>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Stock In', $todayStockIn, 'fa-arrow-down-long', 'emerald'],
            ['Stock Out', $todayStockOut, 'fa-arrow-up-long', 'rose'],
            ['Reversals', $todayReversals, 'fa-rotate-left', 'amber'],
            ['Outbox Pulse', $sentOutboxToday, 'fa-paper-plane', 'indigo']
        ] as [$label, $val, $icon, $color])
            <div class="flex flex-col items-center justify-center p-6 rounded-[2rem] bg-slate-50 border border-slate-100 group hover:bg-white hover:border-slate-200 hover:shadow-lg transition-all duration-500">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-inner text-{{ $color }}-600 group-hover:bg-{{ $color }}-600 group-hover:text-white transition-all duration-500 mb-4">
                    <i class="fas {{ $icon }} text-xs"></i>
                </div>
                <p class="text-[8px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1.5">{{ $label }}</p>
                <span class="text-xl font-black text-slate-900">{{ number_format($val, 0) }}</span>
            </div>
        @endforeach
    </div>
</div>
