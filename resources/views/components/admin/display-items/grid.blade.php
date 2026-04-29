<x-admin.ui.panel title="Product Selection Grid" description="Publish products from main stock, control storefront quantity, and then assign them to homepage rails." padding="p-0">
    <div class="p-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse($stocks as $stock)
                <article class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-slate-800 dark:bg-slate-950/80">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $stock->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $stock->sku }} | {{ $stock->category->name ?? 'N/A' }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] {{ $stock->storefront_enabled ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $stock->storefront_enabled ? 'Published' : 'Private' }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-800 dark:text-slate-200">Rs {{ number_format($stock->selling_price, 2) }}</p>
                        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Warehouse stock</p>
                                    <p class="mt-1 text-xl font-black text-slate-900 dark:text-white">{{ $stock->quantity }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Storefront live: {{ $stock->storefront_available_quantity }} | Reorder at {{ $stock->reorder_level }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="adjustStorefrontQuantity({{ $stock->id }}, 1)" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">Site +1</button>
                                    <button wire:click="adjustStorefrontQuantity({{ $stock->id }}, 5)" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">Site +5</button>
                                    <button wire:click="adjustStorefrontQuantity({{ $stock->id }}, -1)" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Site -1</button>
                                    <button wire:click="adjustStorefrontQuantity({{ $stock->id }}, -5)" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Site -5</button>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button wire:click="toggleStorefront({{ $stock->id }})" class="w-full rounded-xl border px-3 py-2 text-xs font-semibold transition {{ $stock->storefront_enabled ? 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-900' : 'border-indigo-600 bg-indigo-600 text-white hover:bg-indigo-500' }}">
                                    {{ $stock->storefront_enabled ? 'Unpublish from storefront' : 'Publish to storefront' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 border-t border-slate-100 px-4 pb-4 pt-3 dark:border-slate-800">
                        <button wire:click="toggleFeatured({{ $stock->id }})" class="rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ in_array($stock->id, $featuredIds) ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-indigo-200 bg-white text-indigo-600 hover:bg-indigo-50 dark:border-indigo-400/30 dark:bg-slate-950 dark:text-indigo-300 dark:hover:bg-indigo-400/10' }}">Featured</button>
                        <button wire:click="toggleNewArrival({{ $stock->id }})" class="rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ in_array($stock->id, $newArrivalsIds) ? 'border-fuchsia-600 bg-fuchsia-600 text-white' : 'border-fuchsia-200 bg-white text-fuchsia-600 hover:bg-fuchsia-50 dark:border-fuchsia-400/30 dark:bg-slate-950 dark:text-fuchsia-300 dark:hover:bg-fuchsia-400/10' }}">New</button>
                        <button wire:click="toggleDeal({{ $stock->id }})" class="rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ in_array($stock->id, $dealIds) ? 'border-orange-600 bg-orange-600 text-white' : 'border-orange-200 bg-white text-orange-600 hover:bg-orange-50 dark:border-orange-400/30 dark:bg-slate-950 dark:text-orange-300 dark:hover:bg-orange-400/10' }}">Deal</button>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-16"><x-admin.ui.empty-state title="No active products found" description="Add products or adjust filters to start curating storefront display rails." /></div>
            @endforelse
        </div>
        <div class="mt-6">{{ $stocks->links() }}</div>
    </div>
</x-admin.ui.panel>
