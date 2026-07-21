@props(['todayStockOut', 'todayStockIn', 'todayReversals', 'sentOutboxToday'])

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-semibold">
            <i class="fas fa-boxes-stacked text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900">Stock Activity</h3>
            <p class="text-xs text-slate-500 font-medium">Real-time inventory movements today</p>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Stock In', $todayStockIn, 'fa-arrow-down-long', 'emerald'],
            ['Stock Out', $todayStockOut, 'fa-arrow-up-long', 'rose'],
            ['Reversals', $todayReversals, 'fa-rotate-left', 'amber'],
            ['Notifications Sent', $sentOutboxToday, 'fa-paper-plane', 'indigo']
        ] as [$label, $val, $icon, $color])
            <x-admin.dashboard.stat-card
                :label="$label"
                :value="number_format($val, 0)"
                :icon="$icon"
                :tone="$color"
            />
        @endforeach
    </div>
</div>
