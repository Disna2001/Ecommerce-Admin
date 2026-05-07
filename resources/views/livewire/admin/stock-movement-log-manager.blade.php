<div class="space-y-8">
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200"><i class="fas fa-truck-ramp-box text-2xl"></i></div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Logistics Intelligence</p>
                    <h2 class="mt-1 text-3xl font-black text-slate-900 tracking-tight">Stock Mutations</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">Comprehensive audit trail of inventory influx, depletion, and state corrections.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                 <div class="flex items-center gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-1.5 shadow-inner">
                    <span class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200">Live Search</span>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search SKU or Context..." class="border-0 bg-transparent py-1 pl-8 pr-3 text-xs font-bold focus:ring-0 w-48">
                    </div>
                </div>
                <button type="button" wire:click="$refresh" class="h-12 w-12 flex items-center justify-center rounded-2xl bg-white text-slate-400 border border-slate-200 hover:text-slate-900 hover:border-slate-900 transition-all shadow-sm">
                    <i class="fas fa-rotate text-sm"></i>
                </button>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-50 bg-slate-50/50 px-4 py-3">
            <div class="flex items-center gap-2 px-2 border-r border-slate-200 mr-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Inventory Pulse:</span>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-100 px-3 py-1.5 text-[10px] font-black text-slate-600 shadow-sm">
                <i class="fas fa-list-ul text-slate-400"></i> {{ number_format($stats['total']) }} TOTAL
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-1.5 text-[10px] font-black text-emerald-700 shadow-sm">
                <i class="fas fa-arrow-down-long"></i> {{ $stats['in'] }} INFLUX
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 border border-rose-100 px-3 py-1.5 text-[10px] font-black text-rose-700 shadow-sm">
                <i class="fas fa-arrow-up-long"></i> {{ $stats['out'] }} DEPLETION
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 border border-amber-100 px-3 py-1.5 text-[10px] font-black text-amber-700 shadow-sm">
                <i class="fas fa-rotate-left"></i> {{ $stats['restored'] }} RESTORED
            </span>
        </div>
    </div>

    <div class="grid gap-8 xl:grid-cols-[1fr_360px]">
        <div class="space-y-8">
            <x-admin.movements.table :movements="$movements" />
        </div>

        <div class="space-y-8">
            <x-admin.movements.filters :stats="$stats" :contexts="$contexts" />
            
            <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-emerald-500/10"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-white/40">Logistics Status</p>
                <p class="mt-4 text-xs font-medium leading-relaxed opacity-80 italic">Inventory integrity is verified. <span class="text-emerald-400 font-black">{{ number_format($stats['checkout']) }} mutations</span> originated from active customer checkouts.</p>
            </div>
        </div>
    </div>

    @if($showDetailModal && $selectedMovement)
        <x-admin.movements.detail-modal :selected-movement="$selectedMovement" />
    @endif
</div>
