@php
    $primaryColor = \App\Models\SiteSetting::get('primary_color', '#6d28d9');
    $secondaryColor = \App\Models\SiteSetting::get('secondary_color', '#7c3aed');
    $siteName = \App\Models\SiteSetting::get('site_name', 'DISPLAY LANKA.LK');
@endphp

<div x-data="{ mobileFilters: @entangle('showFilters') }">
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
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Catalog Governance Header -->
        <div class="glass storefront-reveal rounded-[2.5rem] p-10 shadow-2xl mb-12">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <nav class="mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">
                        <a href="/" class="hover:text-[var(--primary)] transition-colors">Registry</a>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-slate-900 dark:text-white">Active Catalog</span>
                    </nav>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight sm:text-5xl">
                        {{ $catalogSettings['title'] }}
                        <span class="block text-sm font-bold uppercase tracking-[0.3em] text-[var(--primary)] mt-4">{{ $products->total() }} Records in Ledger</span>
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <button @click="mobileFilters = !mobileFilters"
                            class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm transition-all hover:bg-slate-50 lg:hidden">
                        <i class="fas fa-sliders-h text-xs"></i>
                        Filters
                    </button>

                    <div class="relative group">
                        <select wire:model.live="sort"
                                class="appearance-none rounded-full border border-slate-200 bg-white pl-8 pr-12 py-4 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm focus:border-[var(--primary)] focus:outline-none focus:ring-8 focus:ring-[var(--primary)]/5">
                            <option value="newest">Latest Entries</option>
                            <option value="price_asc">Valuation: Low-High</option>
                            <option value="price_desc">Valuation: High-Low</option>
                            <option value="name">Alphanumeric</option>
                        </select>
                        <i class="fas fa-sort absolute left-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 group-focus-within:text-[var(--primary)]"></i>
                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Classification Strip -->
            @if($categories->isNotEmpty())
                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-white/5">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 mb-6">Quick Classification</p>
                    <div class="flex flex-wrap gap-3">
                        <button wire:click="$set('category', '')"
                                class="rounded-full px-8 py-4 text-[10px] font-black uppercase tracking-widest transition-all {{ $category === '' ? 'bg-slate-900 text-white shadow-xl scale-105' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                            Full Registry
                        </button>
                        @foreach($categories->take(12) as $cat)
                            <button wire:click="$set('category', '{{ $cat->id }}')"
                                    class="rounded-full px-8 py-4 text-[10px] font-black uppercase tracking-widest transition-all {{ (string) $category === (string) $cat->id ? 'text-white shadow-xl scale-105' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                    @if((string) $category === (string) $cat->id) style="background: linear-gradient(90deg, var(--primary), var(--secondary))" @endif>
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

            <div class="mt-8 grid gap-12 lg:grid-cols-[280px_minmax(0,1fr)]">
                <aside class="hidden lg:block">
                    <div class="sticky top-32 space-y-12">
                        <!-- Search Protocol -->
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 mb-6">Search Registry</p>
                            <div class="relative group">
                                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Refine results..."
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-12 py-4 text-xs font-bold uppercase tracking-widest text-slate-700 outline-none transition-all focus:border-[var(--primary)] focus:ring-8 focus:ring-[var(--primary)]/5">
                                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 group-focus-within:text-[var(--primary)] transition-colors"></i>
                            </div>
                        </div>

                        <!-- Valuation Range -->
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 mb-6">Valuation Range</p>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="number" wire:model.live.debounce.600ms="min_price" placeholder="Min"
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-4 text-xs font-bold text-slate-700 outline-none focus:border-[var(--primary)]">
                                <input type="number" wire:model.live.debounce.600ms="max_price" placeholder="Max"
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-4 text-xs font-bold text-slate-700 outline-none focus:border-[var(--primary)]">
                            </div>
                        </div>

                        <!-- Classification Ledger -->
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400">Classifications</p>
                                <button wire:click="clearFilters" class="text-[9px] font-black uppercase tracking-widest text-rose-500 hover:underline">Flush</button>
                            </div>
                            <div class="space-y-2 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                                <label class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 cursor-pointer group transition-all">
                                    <input type="radio" wire:model.live="category" value="" class="w-4 h-4 text-[var(--primary)] border-slate-300 focus:ring-0">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-slate-900">All Categories</span>
                                </label>
                                @foreach($categories as $cat)
                                    <label class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 cursor-pointer group transition-all">
                                        <input type="radio" wire:model.live="category" value="{{ $cat->id }}" class="w-4 h-4 text-[var(--primary)] border-slate-300 focus:ring-0">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-slate-900">{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="min-w-0">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($products as $product)
                            @php($inWishlist = in_array($product->id, $wishlist))
                            <article wire:key="product-card-{{ $product->id }}" class="premium-card group overflow-hidden flex flex-col h-full">
                                <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden p-3 pb-0">
                                    <div class="absolute left-6 top-6 z-10 flex flex-col gap-2">
                                        @if($product->discount_badge)
                                            <div class="rounded-full px-3 py-1 text-[8px] font-black uppercase tracking-[0.2em] text-white shadow-lg" style="background: linear-gradient(90deg, #f97316, #ef4444)">{{ $product->discount_badge }}</div>
                                        @endif
                                        <div class="rounded-full px-3 py-1 text-[8px] font-black uppercase tracking-[0.2em] {{ $product->isLowStock() ? 'bg-amber-400 text-slate-900' : 'bg-white text-slate-900' }} shadow-sm">
                                            {{ $product->isLowStock() ? 'Limited' : 'In Stock' }}
                                        </div>
                                    </div>
                                    <div class="flex h-64 items-center justify-center rounded-[2.2rem] bg-slate-50 dark:bg-slate-900/50 p-8 overflow-hidden">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <i class="fas fa-box-open text-4xl text-slate-200"></i>
                                        @endif
                                    </div>
                                </a>

                                <div class="p-6 pt-5 flex flex-col flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $product->brand?->name ?? 'Premium Registry' }}</p>
                                        <button wire:click="toggleWishlist({{ $product->id }})" class="text-slate-300 hover:text-rose-500 transition-colors">
                                            <i class="{{ $inWishlist ? 'fas fa-heart text-rose-500' : 'far fa-heart' }} text-xs"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-base font-black text-slate-900 leading-tight mb-4 group-hover:text-[var(--primary)] transition-colors line-clamp-2">{{ $product->name }}</h3>
                                    
                                    <div class="mt-auto flex items-end justify-between gap-4">
                                        <div>
                                            <p class="text-xl font-black text-slate-900">Rs {{ number_format($product->final_price, 2) }}</p>
                                            @if($product->discount_badge)<p class="text-[10px] text-slate-400 line-through">Rs {{ number_format($product->selling_price, 2) }}</p>@endif
                                        </div>
                                        <div class="flex gap-2">
                                            <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="h-10 px-4 flex items-center justify-center rounded-full bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-900 hover:bg-slate-200 transition-all">Record</a>
                                            <button wire:click="addToCart({{ $product->id }})" class="h-10 w-10 flex items-center justify-center rounded-full text-white shadow-lg hover:shadow-indigo-500/30 transition-all hover:scale-110" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                                                <i class="fas fa-shopping-bag text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full py-20 text-center glass rounded-[2.5rem]">
                                <i class="fas fa-database text-4xl text-slate-200 mb-6"></i>
                                <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Zero matches found in ledger</p>
                                <button wire:click="clearFilters" class="mt-8 text-[10px] font-black uppercase tracking-widest text-[var(--primary)] hover:underline">Clear Protocol</button>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-12">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
