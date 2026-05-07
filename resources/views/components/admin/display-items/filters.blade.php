<div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="relative flex-1 group">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search registry by name or SKU..." class="w-full rounded-xl border-none bg-slate-50 pl-11 py-2.5 text-xs font-bold text-slate-900 shadow-inner focus:ring-0 focus:bg-white transition-all">
    </div>
    <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
        @foreach([
            'all' => 'Entire Registry',
            'low_stock' => 'Critical Stock',
            'out_of_stock' => 'Depleted'
        ] as $val => $label)
            <button 
                wire:click="$set('inventoryFilter', '{{ $val }}')"
                class="whitespace-nowrap px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $inventoryFilter === $val ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-900 hover:bg-slate-50' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>
