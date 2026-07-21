@php
    $primaryColor = \App\Models\SiteSetting::get('primary_color', '#6d28d9');
    $secondaryColor = \App\Models\SiteSetting::get('secondary_color', '#7c3aed');
    $siteName = \App\Models\SiteSetting::get('site_name', 'DISPLAY LANKA.LK');
@endphp

<div>
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type === 'success' ? 'bg-emerald-500' : (type === 'error' ? 'bg-red-500' : 'bg-violet-500')"
         style="display:none">
        <i class="fas" :class="type === 'success' ? 'fa-check-circle' : 'fa-info-circle'"></i>
        <span x-text="message"></span>
    </div>

    <div class="storefront-page-shell">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Consolidated Single-Row Filter & Sort Toolbar -->
            <div class="glass storefront-reveal rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/70 dark:border-white/5 mb-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <!-- Left: Title & Count -->
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">Products</h1>
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-bold text-[var(--primary)] dark:text-indigo-400">
                            {{ $products->total() }} found
                        </span>
                    </div>

                    <!-- Right: Compact Inline Controls -->
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <!-- Category Dropdown -->
                        <div class="relative">
                            <select wire:model.live="category" class="appearance-none rounded-xl border border-slate-200 bg-white pl-3 pr-8 py-2 text-xs font-bold text-slate-700 shadow-xs focus:border-[var(--primary)] focus:outline-none dark:bg-slate-900 dark:border-white/10 dark:text-white">
                                <option value="">Category: All</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none"></i>
                        </div>

                        <!-- Price Range Min / Max -->
                        <div class="flex items-center gap-1.5">
                            <input type="number" wire:model.live.debounce.600ms="min_price" placeholder="Min Rs" class="w-20 sm:w-24 rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-xs font-bold text-slate-700 outline-none focus:border-[var(--primary)] dark:bg-slate-900 dark:border-white/10 dark:text-white">
                            <span class="text-xs text-slate-400 font-bold">-</span>
                            <input type="number" wire:model.live.debounce.600ms="max_price" placeholder="Max Rs" class="w-20 sm:w-24 rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-xs font-bold text-slate-700 outline-none focus:border-[var(--primary)] dark:bg-slate-900 dark:border-white/10 dark:text-white">
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="relative">
                            <select wire:model.live="sort" class="appearance-none rounded-xl border border-slate-200 bg-white pl-3 pr-8 py-2 text-xs font-bold text-slate-700 shadow-xs focus:border-[var(--primary)] focus:outline-none dark:bg-slate-900 dark:border-white/10 dark:text-white">
                                <option value="newest">Sort: Newest First</option>
                                <option value="price_asc">Sort: Price Low–High</option>
                                <option value="price_desc">Sort: Price High–Low</option>
                                <option value="name">Sort: Alphabetical</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none"></i>
                        </div>

                        <!-- Clear Filters Button -->
                        @if($category || $min_price || $max_price || $search || $sort !== 'newest')
                            <button wire:click="clearFilters" class="rounded-xl bg-rose-50 dark:bg-rose-950/50 px-3 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors">
                                <i class="fas fa-times mr-1 text-[10px]"></i> Clear
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Full-Width Dense 4-Column Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @forelse($products as $product)
                    @php($inWishlist = in_array($product->id, $wishlist))
                    @include('storefront.partials.product-card', ['product' => $product, 'inWishlist' => $inWishlist, 'showViewDetails' => true])
                @empty
                    <div class="col-span-full py-20 text-center glass rounded-[2.5rem]">
                        <i class="fas fa-box-open text-4xl text-slate-300 mb-6"></i>
                        <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">No products found matching your search</p>
                        <button wire:click="clearFilters" class="mt-8 text-[10px] font-black uppercase tracking-widest text-[var(--primary)] hover:underline">Clear Filters</button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-10">{{ $products->links() }}</div>
        </div>
    </div>
</div>
