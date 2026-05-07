<div>
    <div x-data="{ show:false,message:'',type:'success' }"
         x-on:notify.window="show=true;message=$event.detail.message;type=$event.detail.type;setTimeout(()=>show=false,3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success'?'bg-emerald-500':'bg-indigo-500'"
         style="display:none">
        <i class="fas fa-check-circle"></i><span x-text="message"></span>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <header class="mb-12">
            <nav class="mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <a href="/" class="hover:text-[var(--primary)] transition-colors">Matrix</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-slate-900 dark:text-white">Asset Buffer</span>
            </nav>
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-[var(--primary)] text-[10px] font-black uppercase tracking-[0.3em] mb-4">Saved Registry</p>
                    <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">Asset Buffer</h1>
                    <p class="mt-4 max-w-2xl text-sm font-bold text-slate-400 leading-relaxed">Synchronize your favorite assets in one central buffer. Ready for acquisition deployment when required.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="premium-card !p-6 !rounded-3xl border border-slate-100 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Saved Assets</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ $this->items->count() }}</p>
                    </div>
                    @if($this->items->isNotEmpty())
                        <button wire:click="addAllToCart" wire:loading.attr="disabled" wire:target="addAllToCart" class="h-16 px-10 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-105 transition-all">
                            <span wire:loading.remove wire:target="addAllToCart"><i class="fas fa-shopping-cart mr-3 text-xs"></i>Synchronize All to Cart</span>
                            <span wire:loading wire:target="addAllToCart"><i class="fas fa-spinner fa-spin mr-3 text-xs"></i>Syncing...</span>
                        </button>
                    @endif
                </div>
            </div>
        </header>

        @if($this->items->isEmpty())
            <div class="glass py-24 text-center rounded-[3.5rem]">
                <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-slate-50 text-rose-300 dark:bg-white/5">
                    <i class="fas fa-heart text-4xl"></i>
                </div>
                <h2 class="mb-4 text-2xl font-black text-slate-900 dark:text-white tracking-tight">Buffer is Currently Empty</h2>
                <p class="mx-auto mb-10 max-w-md text-sm font-bold text-slate-400 leading-relaxed">Your asset buffer contains no records. Explore the registry to identify and save priority assets for future deployment.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a wire:navigate href="{{ url('/products') }}" class="h-14 px-10 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-105 transition-all">Explore Registry</a>
                    <a wire:navigate href="{{ route('track-order') }}" class="h-14 px-10 flex items-center justify-center rounded-full border border-slate-100 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500 shadow-sm transition-all hover:bg-slate-50">Sync Protocol</a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($this->items as $product)
                    <div class="premium-card !p-0 !rounded-[2.5rem] group overflow-hidden border border-slate-50 hover:border-[var(--primary)] transition-all dark:border-white/5 bg-white dark:bg-slate-900 shadow-sm hover:shadow-2xl">
                        <div class="relative h-64 overflow-hidden bg-slate-50 dark:bg-white/5">
                            @if($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-200"><i class="fas fa-box text-5xl"></i></div>
                            @endif

                            @if($product->discount_badge)
                                <div class="absolute top-4 left-4 h-8 px-4 flex items-center justify-center rounded-full bg-emerald-500 text-[9px] font-black uppercase tracking-widest text-white shadow-lg">{{ $product->discount_badge }}</div>
                            @endif

                            @if($product->storefront_available_quantity <= 0)
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 backdrop-blur-[2px]">
                                    <span class="px-6 py-2 rounded-full bg-white text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-xl">Depleted</span>
                                </div>
                            @endif

                            <button wire:click="remove({{ $product->id }})" class="absolute top-4 right-4 h-10 w-10 flex items-center justify-center rounded-full bg-white/90 text-rose-500 shadow-lg hover:scale-110 transition-all backdrop-blur-sm"><i class="fas fa-heart text-xs"></i></button>
                        </div>

                        <div class="p-6">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ $product->brand?->name ?? 'Premium Asset' }}</p>
                            <a href="{{ url('/products/'.$product->id) }}" class="block text-base font-black text-slate-900 dark:text-white tracking-tight hover:text-[var(--primary)] transition-colors mb-4 line-clamp-1">{{ $product->name }}</a>
                            
                            <div class="flex items-center gap-3 mb-6">
                                @if($product->discount_badge)
                                    <span class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Rs {{ number_format($product->final_price, 0) }}</span>
                                    <span class="text-[10px] font-black text-slate-300 line-through">Rs {{ number_format($product->selling_price, 0) }}</span>
                                @else
                                    <span class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Rs {{ number_format($product->selling_price, 0) }}</span>
                                @endif
                            </div>

                            <button wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="addToCart({{ $product->id }})"
                                    class="w-full h-12 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-lg hover:scale-[1.02] transition-all disabled:opacity-50"
                                    {{ $product->storefront_available_quantity <= 0 ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                    <i class="fas fa-shopping-cart mr-2 text-[10px]"></i>
                                    {{ $product->storefront_available_quantity > 0 ? 'Initialize Acquisition' : 'Asset Depleted' }}
                                </span>
                                <span wire:loading wire:target="addToCart({{ $product->id }})">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
