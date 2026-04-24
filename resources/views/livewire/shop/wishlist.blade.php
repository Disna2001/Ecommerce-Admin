<div>
    <div x-data="{ show:false,message:'',type:'success' }"
         x-on:notify.window="show=true;message=$event.detail.message;type=$event.detail.type;setTimeout(()=>show=false,3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success'?'bg-green-500':'bg-indigo-500'"
         style="display:none">
        <i class="fas fa-check-circle"></i><span x-text="message"></span>
    </div>

    <div class="mx-auto max-w-6xl py-8">
        <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400">
            <a href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="font-medium text-slate-700 dark:text-slate-100">Wishlist</span>
        </nav>

        <div class="storefront-hero card-shadow storefront-reveal mb-6 rounded-[2rem] px-5 py-6 sm:px-6 sm:py-7">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color:var(--primary)">Saved Items</p>
                    <h1 class="flex items-center gap-3 text-3xl font-black text-adapt">
                        <i class="fas fa-heart text-rose-500"></i> Wishlist
                        <span class="text-base font-normal text-soft">({{ $this->items->count() }})</span>
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-soft">Keep your favorite items in one place and send them to the cart when you are ready.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="storefront-stat rounded-[1.25rem] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-soft">Saved</p>
                        <p class="mt-2 text-2xl font-black text-adapt">{{ $this->items->count() }}</p>
                    </div>
                    @if($this->items->isNotEmpty())
                        <button wire:click="addAllToCart" wire:loading.attr="disabled" wire:target="addAllToCart" class="btn-gradient rounded-2xl px-5 py-3 text-sm font-semibold">
                            <span wire:loading.remove wire:target="addAllToCart"><i class="fas fa-shopping-cart mr-1"></i>Add All to Cart</span>
                            <span wire:loading wire:target="addAllToCart"><i class="fas fa-spinner fa-spin mr-1"></i>Adding...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        @if($this->items->isEmpty())
            <div class="glass card-shadow rounded-[2rem] py-20 text-center">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-rose-50">
                    <i class="fas fa-heart text-4xl text-rose-300"></i>
                </div>
                <h2 class="mb-2 text-xl font-bold text-adapt">Your wishlist is empty</h2>
                <p class="mx-auto mb-6 max-w-xl text-soft">Save items you love to come back to them later, compare prices, and move them into the cart when you are ready.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a wire:navigate href="{{ url('/products') }}" class="btn-gradient inline-block rounded-2xl px-7 py-3 font-bold">Discover Products</a>
                    <a wire:navigate href="{{ route('track-order') }}" class="inline-block rounded-2xl border border-slate-200 bg-white px-7 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Track an Order</a>
                </div>
            </div>
        @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($this->items as $product)
                    <div class="surface card-shadow storefront-reveal storefront-reveal-delay-1 group flex flex-col overflow-hidden rounded-[1.75rem] transition-all hover:-translate-y-1">
                        <a href="{{ url('/products/'.$product->id) }}" class="relative block">
                            <div class="flex h-56 items-center justify-center overflow-hidden bg-gradient-to-br from-white via-violet-50 to-cyan-50 dark:from-slate-900 dark:to-slate-800">
                                @if($product->primary_image_url)
                                    <picture class="block h-56 w-full">
                                        @if(!empty($product->primary_image_sources['webp']))
                                            <source srcset="{{ $product->primary_image_sources['webp'] }}" type="image/webp">
                                        @endif
                                        @if(!empty($product->primary_image_sources['jpeg']))
                                            <source srcset="{{ $product->primary_image_sources['jpeg'] }}" type="image/jpeg">
                                        @endif
                                        <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" loading="lazy" decoding="async" class="h-56 w-full object-cover transition duration-300 group-hover:scale-105">
                                    </picture>
                                @else
                                    <i class="fas fa-box text-4xl text-slate-300"></i>
                                @endif
                            </div>
                            @if($product->discount_badge)
                                <span class="absolute left-3 top-3 rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white">
                                    {{ $product->discount_badge }}
                                </span>
                            @endif
                            @if($product->quantity <= 0)
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold">Out of Stock</span>
                                </div>
                            @endif
                        </a>

                        <div class="relative -mt-4 z-10 flex justify-end pr-3">
                            <button wire:click="remove({{ $product->id }})" class="flex h-9 w-9 items-center justify-center rounded-full bg-white shadow-md transition hover:bg-rose-50">
                                <i class="fas fa-heart text-xs text-rose-500"></i>
                            </button>
                        </div>

                        <div class="flex flex-1 flex-col p-4 pt-2">
                            <p class="text-xs uppercase tracking-[0.18em] text-soft">{{ $product->brand?->name ?? '' }}</p>
                            <a href="{{ url('/products/'.$product->id) }}" class="mb-2 flex-1 text-sm font-semibold text-adapt transition hover:opacity-75">
                                {{ $product->name }}
                            </a>
                            <div class="mb-3">
                                @if($product->discount_badge)
                                    <span class="text-sm font-bold text-adapt">Rs {{ number_format($product->final_price,2) }}</span>
                                    <span class="ml-1 text-xs text-soft line-through">Rs {{ number_format($product->selling_price,2) }}</span>
                                @else
                                    <span class="text-sm font-bold text-adapt">Rs {{ number_format($product->selling_price,2) }}</span>
                                @endif
                            </div>
                            <button wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="addToCart({{ $product->id }})"
                                    class="btn-gradient w-full rounded-xl py-2.5 text-xs font-semibold"
                                    {{ $product->quantity <= 0 ? 'disabled style=opacity:.5' : '' }}>
                                <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                    <i class="fas fa-shopping-cart text-xs"></i>
                                    {{ $product->quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}
                                </span>
                                <span wire:loading wire:target="addToCart({{ $product->id }})">
                                    <i class="fas fa-spinner fa-spin text-xs"></i> Adding...
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
