@php
    $primaryColor = \App\Models\SiteSetting::get('primary_color', '#6d28d9');
    $secondaryColor = \App\Models\SiteSetting::get('secondary_color', '#7c3aed');
    $siteName = \App\Models\SiteSetting::get('site_name', 'DISPLAY LANKA.LK');
@endphp

<div x-data="{ mobileFilters: @entangle('showFilters') }">
    <style>
        .products-shell {
            background:
                radial-gradient(circle at top left, rgba(109, 40, 217, 0.10), transparent 24%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.08), transparent 18%),
                linear-gradient(180deg, #f8f8ff 0%, #fdfcff 52%, #f7fbff 100%);
        }
        .dark .products-shell {
            background:
                radial-gradient(circle at top left, rgba(109, 40, 217, 0.22), transparent 24%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.14), transparent 18%),
                linear-gradient(180deg, #111428 0%, #10192b 52%, #0c1324 100%);
        }
        .products-glass {
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(139,92,246,0.10);
            backdrop-filter: blur(16px);
        }
        .dark .products-glass {
            background: rgba(15,23,42,0.76);
            border-color: rgba(255,255,255,0.08);
        }
        .products-card {
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(139,92,246,0.10);
            box-shadow: 0 18px 48px rgba(88,28,135,0.08);
        }
        .dark .products-card {
            background: rgba(15,23,42,0.92);
            border-color: rgba(255,255,255,0.06);
            box-shadow: 0 20px 60px rgba(0,0,0,0.32);
        }
        .products-chip {
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(139,92,246,0.10);
            box-shadow: 0 12px 30px rgba(88,28,135,0.06);
        }
        .dark .products-chip {
            background: rgba(15,23,42,0.82);
            border-color: rgba(255,255,255,0.08);
            box-shadow: 0 18px 44px rgba(0,0,0,0.22);
        }
    </style>

    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type === 'success' ? 'bg-emerald-500' : (type === 'error' ? 'bg-red-500' : 'bg-violet-500')"
         style="display:none">
        <i class="fas" :class="type === 'success' ? 'fa-check-circle' : 'fa-info-circle'"></i>
        <span x-text="message"></span>
    </div>

    <div class="products-shell">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="products-glass rounded-[2rem] p-6 shadow-[0_25px_80px_rgba(88,28,135,0.08)] sm:p-8">
                <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400 dark:text-slate-400">
                    <a href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="font-medium text-slate-700 dark:text-slate-100">Products</span>
                </nav>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color: {{ $primaryColor }}">Browse Store</p>
                        <h1 class="mt-3 text-3xl font-black text-slate-900 dark:text-slate-50 sm:text-4xl">Find the right product faster.</h1>
                        <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-300 sm:text-base">
                            Explore premium digital products, filter by category or brand, and sort results the way you want.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="rounded-2xl bg-white/80 px-4 py-3 text-sm text-slate-600 shadow-sm dark:bg-slate-900/70 dark:text-slate-300">
                            Showing <span class="font-bold text-slate-900 dark:text-slate-50">{{ $products->total() }}</span> products
                        </div>

                        <button @click="mobileFilters = !mobileFilters"
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 lg:hidden">
                            <i class="fas fa-sliders-h text-xs"></i>
                            Filters
                        </button>

                        <select wire:model.live="sort"
                                class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                            <option value="newest">Newest</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="name">Name A-Z</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <div class="products-chip rounded-[1.5rem] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Live Results</p>
                        <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-50">{{ $products->total() }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">Products currently matching your filters.</p>
                    </div>
                    <div class="products-chip rounded-[1.5rem] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Categories</p>
                        <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-50">{{ $categories->count() }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">Browse by category for a faster shortlist.</p>
                    </div>
                    <div class="products-chip rounded-[1.5rem] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Brands</p>
                        <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-50">{{ $brands->count() }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">Compare trusted brands in one clean view.</p>
                    </div>
                    <div class="rounded-[1.5rem] px-4 py-4 text-white shadow-[0_18px_50px_rgba(109,40,217,0.20)]"
                         style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }})">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/75">Shopping Flow</p>
                        <p class="mt-2 text-lg font-black">Search, compare, add to cart.</p>
                        <p class="mt-1 text-sm text-white/75">Everything stays in one simple browsing flow.</p>
                    </div>
                </div>

                @if($categories->isNotEmpty())
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button wire:click="$set('category', '')"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $category === '' ? 'text-white shadow-lg' : 'products-chip text-slate-600 hover:text-slate-900' }}"
                                @if($category === '') style="background: linear-gradient(90deg, {{ $primaryColor }}, {{ $secondaryColor }})" @endif>
                            All Products
                        </button>
                        @foreach($categories->take(6) as $cat)
                            <button wire:click="$set('category', '{{ $cat->id }}')"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ (string) $category === (string) $cat->id ? 'text-white shadow-lg' : 'products-chip text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-slate-100' }}"
                                    @if((string) $category === (string) $cat->id) style="background: linear-gradient(90deg, {{ $primaryColor }}, {{ $secondaryColor }})" @endif>
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[300px_minmax(0,1fr)]">
                <aside class="hidden lg:block">
                    <div class="products-glass sticky top-24 rounded-[1.75rem] p-5 shadow-[0_20px_60px_rgba(88,28,135,0.06)]">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">Filters</h2>
                            <button wire:click="clearFilters" class="text-xs font-semibold text-rose-500 hover:underline">Clear all</button>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Search</label>
                                <div class="relative mt-2">
                                    <input type="text" wire:model.live.debounce.400ms="search"
                                           placeholder="Search products..."
                                           class="w-full rounded-2xl border border-slate-200 bg-white px-10 py-3 text-sm text-slate-700 focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-xs text-slate-300"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Category</label>
                                <div class="mt-3 space-y-2">
                                    <label class="flex items-center gap-3 rounded-2xl bg-white px-3 py-2 text-sm text-slate-600 dark:bg-slate-900/70 dark:text-slate-200">
                                        <input type="radio" wire:model.live="category" value="" class="accent-violet-600">
                                        All Categories
                                    </label>
                                    @foreach($categories as $cat)
                                        <label class="flex items-center gap-3 rounded-2xl bg-white px-3 py-2 text-sm text-slate-600 dark:bg-slate-900/70 dark:text-slate-200">
                                            <input type="radio" wire:model.live="category" value="{{ $cat->id }}" class="accent-violet-600">
                                            {{ $cat->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Price Range</label>
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <input type="number" wire:model.live.debounce.600ms="min_price" placeholder="Min"
                                           class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                    <input type="number" wire:model.live.debounce.600ms="max_price" placeholder="Max"
                                           class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Brand</label>
                                <div class="mt-3 max-h-56 space-y-2 overflow-y-auto pr-1">
                                    <label class="flex items-center gap-3 rounded-2xl bg-white px-3 py-2 text-sm text-slate-600 dark:bg-slate-900/70 dark:text-slate-200">
                                        <input type="radio" wire:model.live="brand" value="" class="accent-violet-600">
                                        All Brands
                                    </label>
                                    @foreach($brands as $b)
                                        <label class="flex items-center gap-3 rounded-2xl bg-white px-3 py-2 text-sm text-slate-600 dark:bg-slate-900/70 dark:text-slate-200">
                                            <input type="radio" wire:model.live="brand" value="{{ $b->id }}" class="accent-violet-600">
                                            {{ $b->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="min-w-0">
                    <div x-show="mobileFilters" x-transition class="mb-5 lg:hidden">
                        <div class="products-glass rounded-[1.75rem] p-5 shadow-[0_20px_60px_rgba(88,28,135,0.06)]">
                            <div class="mb-5 flex items-center justify-between">
                                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">Filters</h2>
                                <button @click="mobileFilters = false" class="text-xs font-semibold text-slate-400 hover:text-slate-700 dark:hover:text-slate-100">Close</button>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Search</label>
                                    <input type="text" wire:model.live.debounce.400ms="search"
                                           placeholder="Search products..."
                                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Category</label>
                                    <select wire:model.live="category" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                        <option value="">All</option>
                                        @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Brand</label>
                                    <select wire:model.live="brand" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                        <option value="">All</option>
                                        @foreach($brands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Min Price</label>
                                    <input wire:model.live.debounce.600ms="min_price" type="number" placeholder="0"
                                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-400">Max Price</label>
                                    <input wire:model.live.debounce.600ms="max_price" type="number" placeholder="Any"
                                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-0 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100">
                                </div>
                            </div>

                            <button wire:click="clearFilters" class="mt-4 text-sm font-semibold text-rose-500 hover:underline">Clear all filters</button>
                        </div>
                    </div>

                    <div class="mb-5 flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-300">
                        <span wire:loading wire:target="search,category,brand,min_price,max_price,sort" class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-violet-600 shadow-sm dark:bg-slate-900/70">
                            <i class="fas fa-spinner fa-spin text-xs"></i>
                            Updating results
                        </span>

                        @if($search)
                            <span class="rounded-full bg-white px-3 py-2 shadow-sm dark:bg-slate-900/70">Search: <strong class="text-slate-800 dark:text-slate-100">{{ $search }}</strong></span>
                        @endif
                        @if($category)
                            <span class="rounded-full bg-white px-3 py-2 shadow-sm dark:bg-slate-900/70">Category selected</span>
                        @endif
                        @if($brand)
                            <span class="rounded-full bg-white px-3 py-2 shadow-sm dark:bg-slate-900/70">Brand selected</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 transition-opacity duration-200" wire:loading.class="opacity-60">
                        @forelse($products as $product)
                            @php($inWishlist = in_array($product->id, $wishlist))

                            <article wire:key="product-card-{{ $product->id }}" class="products-card group flex h-full max-w-sm flex-col overflow-hidden rounded-[1.75rem] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_60px_rgba(88,28,135,0.14)]">
                                <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden">
                                    <div class="h-56 bg-gradient-to-br from-white via-violet-50 to-cyan-50 p-4 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
                                        <div class="flex h-full items-center justify-center overflow-hidden rounded-[1.25rem] bg-white dark:bg-slate-900/70">
                                            @if($product->primary_image_url)
                                                <picture class="block h-full w-full">
                                                    @if(!empty($product->primary_image_sources['webp']))
                                                        <source srcset="{{ $product->primary_image_sources['webp'] }}" type="image/webp">
                                                    @endif
                                                    @if(!empty($product->primary_image_sources['jpeg']))
                                                        <source srcset="{{ $product->primary_image_sources['jpeg'] }}" type="image/jpeg">
                                                    @endif
                                                    <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}"
                                                         alt="{{ $product->name }}"
                                                         loading="lazy"
                                                         decoding="async"
                                                         class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                                </picture>
                                            @else
                                                <div class="text-center text-slate-300">
                                                    <i class="fas fa-box-open text-5xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="absolute left-4 top-4 flex flex-col gap-2">
                                        @if($product->discount_badge)
                                            <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white" style="background: linear-gradient(90deg, #f97316, #ef4444)">
                                                {{ $product->discount_badge }}
                                            </span>
                                        @endif
                                        @if($product->isLowStock())
                                            <span class="rounded-full bg-amber-500 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white">Low Stock</span>
                                        @endif
                                    </div>
                                </a>

                                <div class="flex items-start justify-between px-4">
                                    <button wire:click="toggleWishlist({{ $product->id }})"
                                            class="-mt-5 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-md transition hover:scale-110 dark:bg-slate-900">
                                        <i class="{{ $inWishlist ? 'fas fa-heart text-rose-500' : 'far fa-heart text-slate-400' }} text-sm"></i>
                                    </button>
                                </div>

                                <div class="flex flex-1 flex-col p-4 pt-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-400">
                                            {{ $product->brand?->name ?? $product->category?->name ?? $siteName }}
                                        </p>
                                        @if($product->category?->name)
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <a wire:navigate href="{{ url('/products/'.$product->id) }}">
                                        <h3 class="mt-2 line-clamp-2 text-base font-semibold leading-7 text-slate-900 transition hover:opacity-75 dark:text-slate-50">{{ $product->name }}</h3>
                                    </a>

                                    <div class="mt-3 flex items-center gap-1 text-amber-400">
                                        @for($i=0;$i<5;$i++)<i class="fas fa-star text-xs"></i>@endfor
                                        <span class="ml-1 text-xs text-slate-400 dark:text-slate-400">(0)</span>
                                    </div>

                                    <div class="mt-4 flex flex-1 items-end justify-between gap-3">
                                        <div>
                                            @if($product->discount_badge)
                                                <p class="text-lg font-black" style="color: {{ $primaryColor }}">Rs {{ number_format($product->final_price,2) }}</p>
                                                <p class="text-xs text-slate-400 line-through dark:text-slate-400">Rs {{ number_format($product->selling_price,2) }}</p>
                                            @else
                                                <p class="text-lg font-black text-slate-900 dark:text-slate-50">Rs {{ number_format($product->selling_price,2) }}</p>
                                            @endif
                                            <p class="mt-2 text-xs text-slate-400 dark:text-slate-400">
                                                {{ $product->quantity > 0 ? $product->quantity.' in stock' : 'Currently unavailable' }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <a wire:navigate href="{{ url('/products/'.$product->id) }}"
                                               class="inline-flex h-10 items-center justify-center rounded-full border border-slate-200 px-4 text-xs font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:text-slate-100 dark:hover:bg-slate-800">
                                                View
                                            </a>
                                            <button wire:click="addToCart({{ $product->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="addToCart({{ $product->id }})"
                                                    class="inline-flex h-10 min-w-[44px] items-center justify-center rounded-full px-4 text-xs font-bold text-white transition hover:opacity-85 disabled:cursor-not-allowed disabled:opacity-50"
                                                    style="background: linear-gradient(90deg, {{ $primaryColor }}, {{ $secondaryColor }})"
                                                    {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                                <span wire:loading.remove wire:target="addToCart({{ $product->id }})"><i class="fas fa-plus text-xs"></i></span>
                                                <span wire:loading wire:target="addToCart({{ $product->id }})"><i class="fas fa-spinner fa-spin text-xs"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="products-glass col-span-full rounded-[1.75rem] px-6 py-20 text-center">
                                <i class="fas fa-search text-5xl text-slate-300"></i>
                                <p class="mt-5 text-xl font-semibold text-slate-700 dark:text-slate-50">No products found</p>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-300">Try a different search or clear the active filters.</p>
                                <div class="mt-6 flex flex-wrap justify-center gap-3">
                                    <button wire:click="clearFilters"
                                            class="inline-flex items-center rounded-full px-6 py-3 text-sm font-semibold text-white"
                                            style="background: linear-gradient(90deg, {{ $primaryColor }}, {{ $secondaryColor }})">
                                        Clear Filters
                                    </button>
                                    <a wire:navigate href="{{ route('help-center') }}"
                                       class="inline-flex items-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                                        Help Center
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
