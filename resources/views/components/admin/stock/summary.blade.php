@props([
    'stocks',
    'movementSummary'
])

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
    @foreach([
        ['total', 'Total Assets', $stocks->total() . ' items', 'fa-boxes-stacked', 'slate'],
        ['low_stock', 'Critical Alert', $this->lowStockCount . ' low stock', 'fa-triangle-exclamation', 'amber'],
        ['restocked', 'Restock Flow', $movementSummary['today_in'] . ' units in', 'fa-arrow-down', 'emerald'],
        ['sold', 'Sales Flow', $movementSummary['today_out'] . ' units out', 'fa-arrow-up', 'sky'],
        ['value', 'Inventory Value', 'Rs ' . number_format($this->totalValue, 0), 'fa-coins', 'indigo']
    ] as [$key, $label, $sub, $icon, $color])
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 group-hover:bg-{{ $color }}-500 group-hover:text-white transition-colors">
                    <i class="fas {{ $icon }} text-xs"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none mb-1">{{ $label }}</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-900 leading-none">{{ explode(' ', $sub)[0] }}</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ implode(' ', array_slice(explode(' ', $sub), 1)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
