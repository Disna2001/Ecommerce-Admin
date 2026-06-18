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
        <div class="glass storefront-reveal rounded-[1.5rem] sm:rounded-[2.5rem] p-5 sm:p-10 shadow-lg sm:shadow-2xl mb-6 sm:mb-12">
            <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <nav class="mb-4 sm:mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">
                        <a href="/" class="hover:text-[var(--primary)] transition-colors">Registry</a>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-slate-900 dark:text-white">Active Catalog</span>
                    </nav>
                    <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $catalogSettings['title'] }}
                        <span class="block text-xs sm:text-sm font-bold uppercase tracking-[0.3em] text-[var(--primary)] mt-3 sm:mt-4">{{ $products->total() }} Records in Ledger</span>
                    </h1>
                </div>
 
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <button @click="mobileFilters = !mobileFilters"
                            class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-5 sm:px-6 py-3 sm:py-4 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm transition-all hover:bg-slate-50 lg:hidden dark:bg-slate-900 dark:border-white/10 dark:text-white">
                        <i class="fas fa-sliders-h text-xs"></i>
                        Filters
                    </button>
 
                    <div class="relative group">
                        <select wire:model.live="sort"
                                class="appearance-none rounded-full border border-slate-200 bg-white pl-8 pr-12 py-3 sm:py-4 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm focus:border-[var(--primary)] focus:outline-none focus:ring-8 focus:ring-[var(--primary)]/5 dark:bg-slate-900 dark:border-white/10 dark:text-white">
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
                <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-slate-100 dark:border-white/5">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 mb-4 sm:mb-6">Quick Classification</p>
                    <div class="flex overflow-x-auto pb-3 gap-2 custom-scrollbar lg:flex-wrap lg:overflow-visible lg:pb-0">
                        <button wire:click="$set('category', '')"
                                class="shrink-0 rounded-full px-6 sm:px-8 py-3 sm:py-4 text-[10px] font-black uppercase tracking-widest transition-all {{ $category === '' ? 'bg-slate-900 text-white shadow-xl scale-105 dark:bg-white dark:text-slate-900' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-400' }}">
                            Full Registry
                        </button>
                        @foreach($categories->take(12) as $cat)
                            <button wire:click="$set('category', '{{ $cat->id }}')"
                                    class="shrink-0 rounded-full px-6 sm:px-8 py-3 sm:py-4 text-[10px] font-black uppercase tracking-widest transition-all {{ (string) $category === (string) $cat->id ? 'text-white shadow-xl scale-105' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-400' }}"
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
                    <div class="grid grid-cols-2 gap-4 sm:gap-8 xl:grid-cols-3">
                        @forelse($products as $product)
                            @php($inWishlist = in_array($product->id, $wishlist))
                            <article wire:key="product-card-{{ $product->id }}" class="premium-card !rounded-[1.5rem] sm:!rounded-[2.5rem] lg:!rounded-[3rem] group overflow-hidden flex flex-col h-full">
                                <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden p-2 sm:p-3 pb-0">
                                    <div class="absolute left-4 top-4 sm:left-6 sm:top-6 z-10 flex flex-col gap-1.5 sm:gap-2">
                                        @if($product->discount_badge)
                                            <div class="rounded-full px-2.5 sm:px-3 py-1 text-[8px] font-black uppercase tracking-[0.2em] text-white shadow-lg" style="background: linear-gradient(90deg, #f97316, #ef4444)">{{ $product->discount_badge }}</div>
                                        @endif
                                        <div class="rounded-full px-2.5 sm:px-3 py-1 text-[8px] font-black uppercase tracking-[0.2em] {{ $product->isLowStock() ? 'bg-amber-400 text-slate-900' : 'bg-white text-slate-900' }} shadow-sm">
                                            {{ $product->isLowStock() ? 'Limited' : 'In Stock' }}
                                        </div>
                                    </div>
                                    <div class="flex h-36 sm:h-64 items-center justify-center rounded-[1rem] sm:rounded-[2rem] bg-slate-50 dark:bg-slate-900/50 p-4 sm:p-8 overflow-hidden">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <i class="fas fa-box-open text-2xl sm:text-4xl text-slate-200"></i>
                                        @endif
                                    </div>
                                </a>

                                <div class="p-4 sm:p-6 pt-3 sm:pt-5 flex flex-col flex-1">
                                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                                        <p class="text-[8px] sm:text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $product->brand?->name ?? 'Premium Registry' }}</p>
                                        <button wire:click="toggleWishlist({{ $product->id }})" class="text-slate-300 hover:text-rose-500 transition-colors">
                                            <i class="{{ $inWishlist ? 'fas fa-heart text-rose-500' : 'far fa-heart' }} text-[10px] sm:text-xs"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-xs sm:text-base font-black text-slate-900 dark:text-white leading-tight mb-2 sm:mb-4 group-hover:text-[var(--primary)] transition-colors line-clamp-2">{{ $product->name }}</h3>
                                    
                                    <div class="mt-auto flex items-end justify-between gap-2 sm:gap-4">
                                        <div>
                                            <p class="text-sm sm:text-xl font-black text-slate-900 dark:text-white">Rs {{ number_format($product->final_price, 2) }}</p>
                                            @if($product->discount_badge)<p class="text-[8px] sm:text-[10px] text-slate-400 line-through">Rs {{ number_format($product->selling_price, 2) }}</p>@endif
                                        </div>
                                        <div class="flex gap-1.5 sm:gap-2">
                                            <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="hidden sm:flex h-10 px-4 items-center justify-center rounded-full bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-900 hover:bg-slate-200 transition-all">Record</a>
                                            <button wire:click="addToCart({{ $product->id }})" class="h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center rounded-full text-white shadow-lg hover:shadow-indigo-500/30 transition-all hover:scale-110 shrink-0" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                                                <i class="fas fa-shopping-bag text-[10px] sm:text-xs"></i>
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

    <!-- Mobile Filters Overlay -->
    <div x-show="mobileFilters" style="display:none;" class="fixed inset-0 z-[100] lg:hidden">
        <div x-show="mobileFilters" x-transition.opacity class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="mobileFilters = false"></div>
        <div x-show="mobileFilters" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" 
             class="absolute bottom-0 left-0 top-0 w-4/5 max-w-sm overflow-y-auto bg-white/95 p-6 shadow-2xl backdrop-blur-xl dark:bg-slate-950/95 flex flex-col custom-scrollbar">
            <div class="flex items-center justify-between mb-8">
                <span class="text-lg font-black tracking-tighter text-slate-900 dark:text-white">Filters</span>
                <button @click="mobileFilters = false" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-10">
                <!-- Search Protocol -->
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 mb-4">Search Registry</p>
                    <div class="relative group">
                        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Refine results..."
                               class="w-full rounded-2xl border border-slate-200 bg-white px-12 py-4 text-xs font-bold uppercase tracking-widest text-slate-700 outline-none transition-all focus:border-[var(--primary)] focus:ring-8 focus:ring-[var(--primary)]/5 dark:bg-slate-900/50 dark:border-white/10 dark:text-white">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 group-focus-within:text-[var(--primary)] transition-colors"></i>
                    </div>
                </div>

                <!-- Valuation Range -->
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 mb-4">Valuation Range</p>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" wire:model.live.debounce.600ms="min_price" placeholder="Min"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-4 text-xs font-bold text-slate-700 outline-none focus:border-[var(--primary)] dark:bg-slate-900/50 dark:border-white/10 dark:text-white">
                        <input type="number" wire:model.live.debounce.600ms="max_price" placeholder="Max"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-4 text-xs font-bold text-slate-700 outline-none focus:border-[var(--primary)] dark:bg-slate-900/50 dark:border-white/10 dark:text-white">
                    </div>
                </div>

                <!-- Classification Ledger -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400">Classifications</p>
                        <button @click="mobileFilters = false" wire:click="clearFilters" class="text-[9px] font-black uppercase tracking-widest text-rose-500 hover:underline">Flush</button>
                    </div>
                    <div class="space-y-2 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                        <label class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 cursor-pointer group transition-all dark:hover:bg-slate-900/50">
                            <input type="radio" wire:model.live="category" value="" class="w-4 h-4 text-[var(--primary)] border-slate-300 focus:ring-0">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white">All Categories</span>
                        </label>
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 cursor-pointer group transition-all dark:hover:bg-slate-900/50">
                                <input type="radio" wire:model.live="category" value="{{ $cat->id }}" class="w-4 h-4 text-[var(--primary)] border-slate-300 focus:ring-0">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <button @click="mobileFilters = false" class="mt-8 w-full rounded-full py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl hover:scale-[1.02] transition-all" style="background: linear-gradient(90deg, var(--primary), var(--secondary))">
                View Ledger
            </button>
        </div>
    </div>
</div>
