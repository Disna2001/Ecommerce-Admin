<x-admin.ui.panel title="Product Filters" description="Narrow the catalog first, then assign products to each storefront rail.">
    <div class="flex flex-wrap gap-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search products" class="min-w-[240px] flex-1 rounded-2xl border-slate-200 text-sm shadow-none focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        <select wire:model.live="selectedCategory" class="rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <option value="">All categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="inventoryFilter" class="rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <option value="all">All inventory</option>
            <option value="low_stock">Low stock only</option>
            <option value="out_of_stock">Out of stock</option>
        </select>
    </div>
</x-admin.ui.panel>
