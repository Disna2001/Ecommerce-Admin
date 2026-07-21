@props(['product', 'badge' => null, 'inWishlist' => false, 'showViewDetails' => false])
@php
    $finalPrice = $product->final_price ?? $product->selling_price;
    $hasDiscount = filled($product->discount_badge) || ($product->selling_price > $finalPrice);
    $imageUrl = $product->primary_image_sources['fallback'] ?? $product->primary_image_url;
@endphp

<article wire:key="product-card-{{ $product->id }}" class="group relative flex flex-col h-full rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 p-3 sm:p-4 shadow-xs hover:shadow-xl hover:border-indigo-500/40 transition-all duration-300">
    <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden">
        <!-- Top Badges -->
        <div class="absolute left-2 top-2 z-10 flex flex-col gap-1">
            @if($badge)
                <span class="rounded-md px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-white shadow-xs" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                    {{ $badge }}
                </span>
            @elseif($product->discount_badge)
                <span class="rounded-md bg-rose-500 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-white shadow-xs">
                    {{ $product->discount_badge }}
                </span>
            @endif
        </div>

        <div class="absolute right-2 top-2 z-10">
            <span class="rounded-md px-1.5 py-0.5 text-[7px] font-bold uppercase tracking-wider {{ $product->quantity <= 0 ? 'bg-slate-900 text-white' : ($product->isLowStock() ? 'bg-amber-400 text-slate-900' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                {{ $product->quantity <= 0 ? 'Out of Stock' : ($product->isLowStock() ? 'Limited' : 'In Stock') }}
            </span>
        </div>

        <!-- Image Container -->
        <div class="flex h-32 sm:h-44 w-full items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800/50 p-2 overflow-hidden mb-2 sm:mb-3">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-105" loading="lazy">
            @else
                <div class="flex flex-col items-center justify-center text-slate-300 dark:text-slate-600">
                    <i class="fas fa-mobile-screen-button text-3xl mb-1"></i>
                    <span class="text-[8px] font-mono text-slate-400">No Image</span>
                </div>
            @endif
        </div>
    </a>

    <div class="flex flex-col flex-1">
        <!-- Brand & Wishlist -->
        <div class="flex items-center justify-between mb-1">
            <p class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-slate-400 truncate">
                {{ $product->brand?->name ?? $product->category?->name ?? 'Display Lanka' }}
            </p>
            <button wire:click="toggleWishlist({{ $product->id }})" type="button" class="text-slate-300 hover:text-rose-500 transition-colors" title="Wishlist">
                <i class="{{ $inWishlist ? 'fas fa-heart text-rose-500' : 'far fa-heart' }} text-[10px] sm:text-xs"></i>
            </button>
        </div>

        <!-- Product Name -->
        <h3 class="text-xs font-black text-slate-900 dark:text-white leading-snug mb-2 group-hover:text-[var(--primary)] transition-colors line-clamp-2">
            <a wire:navigate href="{{ url('/products/'.$product->id) }}">{{ $product->name }}</a>
        </h3>

        <!-- Pricing & Action Row -->
        <div class="mt-auto flex items-center justify-between gap-1 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div>
                <p class="text-xs sm:text-sm font-black text-[var(--primary)] dark:text-indigo-400">
                    Rs {{ number_format($finalPrice, 0) }}
                </p>
                @if($hasDiscount)
                    <p class="text-[8px] sm:text-[9px] text-slate-400 line-through">
                        Rs {{ number_format($product->selling_price, 0) }}
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-1.5">
                @if($showViewDetails)
                    <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="hidden sm:inline-flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-1.5 text-[9px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition-colors">
                        View Details
                    </a>
                @endif
                <button
                    wire:click="addToCart({{ $product->id }})"
                    type="button"
                    class="shop-cart-btn flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-xl bg-slate-900 text-white shadow-xs hover:bg-[var(--primary)] hover:scale-105 transition-all shrink-0 dark:bg-white dark:text-slate-900 dark:hover:bg-indigo-500 dark:hover:text-white"
                    data-id="{{ $product->id }}"
                    title="Add to Cart"
                >
                    <i class="fas fa-shopping-bag text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>
</article>
