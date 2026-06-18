<div class="space-y-6">
    <!-- Registry Search & Scan Deck -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 group">
                <i class="fas fa-barcode absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
                <input type="text" wire:model.defer="scanCode" wire:keydown.enter.prevent="processScan" 
                    placeholder="Scan barcode, SKU, or item code to identify..." 
                    class="w-full rounded-2xl border-slate-100 bg-slate-50 pl-14 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                    <span class="rounded-lg bg-white px-2 py-1 text-[10px] font-black text-slate-400 border border-slate-100 uppercase tracking-widest shadow-sm">Enter to Search</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 rounded-2xl border border-slate-200 bg-slate-50 p-1 shadow-inner">
                    <button type="button" wire:click="$set('scanMode', 'open_or_create')" class="rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest transition-all {{ $scanMode === 'open_or_create' ? 'bg-white text-slate-900 shadow-sm border border-slate-100' : 'text-slate-400 hover:text-slate-600' }}">Smart Search</button>
                    <button type="button" wire:click="$set('scanMode', 'create_only')" class="rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest transition-all {{ $scanMode === 'create_only' ? 'bg-white text-slate-900 shadow-sm border border-slate-100' : 'text-slate-400 hover:text-slate-600' }}">Direct Intake</button>
                </div>
                <button type="button" wire:click="processScan" class="h-12 w-12 flex items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl shadow-slate-200 transition hover:scale-105 active:scale-95">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Filter Matrix -->
    <div class="flex overflow-x-auto scrollbar-none items-center gap-2 p-1.5 rounded-[1.5rem] bg-slate-100/50 border border-slate-200/60 w-full lg:w-fit">
        <div class="flex items-center gap-2 w-max">
            @foreach(['all' => 'All Assets', 'low_stock' => 'Critical', 'out_of_stock' => 'Deficit', 'active' => 'Operational', 'inactive' => 'Drafts', 'discontinued' => 'Archived'] as $filterKey => $filterLabel)
                <button type="button" wire:click="setInventoryQuickFilter('{{ $filterKey }}')" 
                    class="group relative flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all duration-300 {{ $inventoryQuickFilter === $filterKey ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-400 hover:text-slate-600' }}">
                    <span>{{ $filterLabel }}</span>
                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-lg px-1.5 py-0.5 text-[10px] font-black {{ $inventoryQuickFilter === $filterKey ? 'bg-slate-900 text-white shadow-md shadow-slate-200' : 'bg-slate-200 text-slate-500 group-hover:bg-slate-300' }}">{{ $inventoryQuickCounts[$filterKey] ?? 0 }}</span>
                </button>
            @endforeach
            <div class="mx-2 h-6 w-px bg-slate-200"></div>
            <button type="button" wire:click="toggleCompactTableMode" class="h-9 w-9 flex items-center justify-center rounded-xl {{ $compactTableMode ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-400 border border-slate-200 hover:text-slate-600' }}" title="Density Toggle">
                <i class="fas {{ $compactTableMode ? 'fa-align-justify' : 'fa-grip-lines' }} text-xs"></i>
            </button>
            <button type="button" wire:click="resetInventoryBoard" class="h-9 w-9 flex items-center justify-center rounded-xl bg-white text-rose-400 border border-slate-200 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Reset All Filters">
                <i class="fas fa-rotate-left text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Advanced Filter Hub -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <div class="space-y-2">
            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Product Name / SKU</label>
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter registry..." class="w-full rounded-2xl border-slate-100 bg-white pl-11 py-3 text-xs font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0">
            </div>
        </div>
        <div class="space-y-2">
            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Classification</label>
            <select wire:model.live="selectedCategory" class="w-full rounded-2xl border-slate-100 bg-white py-3 text-xs font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0">
                <option value="">All Categories</option>
                @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
            </select>
        </div>
        <div class="space-y-2">
            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Sourcing Make</label>
            <select wire:model.live="selectedMake" class="w-full rounded-2xl border-slate-100 bg-white py-3 text-xs font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0">
                <option value="">All Makes</option>
                @foreach($makes as $make)<option value="{{ $make->id }}">{{ $make->name }}</option>@endforeach
            </select>
        </div>
        <div class="space-y-2">
            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Brand Identity</label>
            <select wire:model.live="selectedBrand" class="w-full rounded-2xl border-slate-100 bg-white py-3 text-xs font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0">
                <option value="">All Brands</option>
                @foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach
            </select>
        </div>
        <div class="space-y-2">
            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Supplier Link</label>
            <select wire:model.live="selectedSupplier" class="w-full rounded-2xl border-slate-100 bg-white py-3 text-xs font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach
            </select>
        </div>
    </div>
</div>
