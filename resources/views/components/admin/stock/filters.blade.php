<div x-data="{ showMoreFilters: false }" class="space-y-4">
    <!-- Registry Search & Scan Deck -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 group">
                <i class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-slate-900 transition-colors"></i>
                <input type="text" wire:model.defer="scanCode" wire:keydown.enter.prevent="processScan" 
                    placeholder="Scan barcode, SKU, or item code to identify..." 
                    class="w-full rounded-xl border-slate-200 bg-slate-50 pl-11 pr-28 py-2.5 text-xs font-semibold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                    <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-400 border border-slate-200 uppercase tracking-wider">Enter</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 p-1 shadow-inner">
                    <button type="button" wire:click="$set('scanMode', 'open_or_create')" class="rounded-lg px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all {{ $scanMode === 'open_or_create' ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">Smart Search</button>
                    <button type="button" wire:click="$set('scanMode', 'create_only')" class="rounded-lg px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all {{ $scanMode === 'create_only' ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">Direct Intake</button>
                </div>
                <button type="button" wire:click="processScan" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-900 text-white shadow-xs transition hover:scale-105 active:scale-95">
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Filter Bar: Status Pills & Search Input -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Search Field -->
            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search inventory..." class="w-full rounded-xl border-slate-200 bg-white pl-9 pr-3 py-2 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
            </div>

            <!-- Status Pills -->
            @foreach(['all' => 'All Assets', 'low_stock' => 'Critical', 'out_of_stock' => 'Deficit', 'active' => 'Operational', 'inactive' => 'Drafts', 'discontinued' => 'Archived'] as $filterKey => $filterLabel)
                <button type="button" wire:click="setInventoryQuickFilter('{{ $filterKey }}')" 
                    class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition-all {{ $inventoryQuickFilter === $filterKey ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <span>{{ $filterLabel }}</span>
                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-md px-1.5 py-0.2 text-[10px] font-bold {{ $inventoryQuickFilter === $filterKey ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $inventoryQuickCounts[$filterKey] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        <div class="flex items-center gap-2 self-start lg:self-auto">
            <!-- More Filters Toggle Button -->
            <button 
                type="button" 
                @click="showMoreFilters = !showMoreFilters" 
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs"
            >
                <i class="fas fa-filter text-xs text-slate-400"></i>
                <span>More Filters</span>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': showMoreFilters }"></i>
            </button>

            <!-- Table Density Toggle -->
            <button type="button" wire:click="toggleCompactTableMode" class="h-8 w-8 flex items-center justify-center rounded-lg {{ $compactTableMode ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-400 border border-slate-200 hover:text-slate-600' }}" title="Density Toggle">
                <i class="fas {{ $compactTableMode ? 'fa-align-justify' : 'fa-grip-lines' }} text-xs"></i>
            </button>

            <!-- Reset Filters -->
            <button type="button" wire:click="resetInventoryBoard" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white text-rose-400 border border-slate-200 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Reset All Filters">
                <i class="fas fa-rotate-left text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Collapsible Advanced Attribute Filters Drawer -->
    <div x-show="showMoreFilters" x-transition.opacity.duration.200ms class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
        <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Classification</label>
            <select wire:model.live="selectedCategory" class="w-full rounded-lg border-slate-200 bg-slate-50 py-2 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                <option value="">All Categories</option>
                @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sourcing Make</label>
            <select wire:model.live="selectedMake" class="w-full rounded-lg border-slate-200 bg-slate-50 py-2 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                <option value="">All Makes</option>
                @foreach($makes as $make)<option value="{{ $make->id }}">{{ $make->name }}</option>@endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Brand Identity</label>
            <select wire:model.live="selectedBrand" class="w-full rounded-lg border-slate-200 bg-slate-50 py-2 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                <option value="">All Brands</option>
                @foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Supplier Link</label>
            <select wire:model.live="selectedSupplier" class="w-full rounded-lg border-slate-200 bg-slate-50 py-2 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach
            </select>
        </div>
    </div>
</div>
