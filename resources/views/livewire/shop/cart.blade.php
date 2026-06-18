<div>
    <div x-data="{ show:false,message:'',type:'success' }"
         x-on:notify.window="show=true;message=$event.detail.message;type=$event.detail.type;setTimeout(()=>show=false,3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success'?'bg-emerald-500':'bg-rose-500'"
         style="display:none">
        <i class="fas fa-check-circle"></i><span x-text="message"></span>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <header class="mb-12">
            <nav class="mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <a href="/" class="hover:text-[var(--primary)] transition-colors">Matrix</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-slate-900 dark:text-white">Acquisition Buffer</span>
            </nav>
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-[var(--primary)] text-[10px] font-black uppercase tracking-[0.3em] mb-4">Cart Workspace</p>
                    <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">Acquisition Buffer</h1>
                    <p class="mt-4 max-w-2xl text-sm font-bold text-slate-400 leading-relaxed">Review your selected assets, calibrate quantities, and initialize the final acquisition protocol.</p>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @foreach([
                        ['Assets', $count],
                        ['Subtotal', 'Rs '.number_format($subtotal, 0)]
                    ] as [$label, $value])
                        <div class="premium-card !p-6 !rounded-3xl border border-slate-100 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ $label }}</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ $value }}</p>
                        </div>
                    @endforeach
                    <div class="hidden sm:block premium-card !p-6 !rounded-3xl bg-slate-900 shadow-xl">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Protocol Status</p>
                        <p class="text-xs font-black text-white tracking-widest uppercase">{{ $discountAmount > 0 ? 'Verified' : 'Ready' }}</p>
                    </div>
                </div>
            </div>
        </header>

        @if(empty($cart))
            <div class="glass py-24 text-center rounded-[3.5rem]">
                <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-slate-50 text-slate-300 dark:bg-white/5">
                    <i class="fas fa-shopping-bag text-4xl"></i>
                </div>
                <h2 class="mb-4 text-2xl font-black text-slate-900 dark:text-white tracking-tight">Buffer is Currently Empty</h2>
                <p class="mx-auto mb-10 max-w-md text-sm font-bold text-slate-400 leading-relaxed">Your acquisition buffer contains no assets. Explore the registry to initialize new deployment protocols.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a wire:navigate href="{{ url('/products') }}" class="h-14 px-10 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-105 transition-all">Explore Registry</a>
                    <a wire:navigate href="{{ route('help-center') }}" class="h-14 px-10 flex items-center justify-center rounded-full border border-slate-100 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500 shadow-sm transition-all hover:bg-slate-50">Support Node</a>
                </div>
            </div>
        @else
            <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="space-y-8">
                    @foreach($cart as $id => $item)
                        <div class="premium-card !p-4 sm:!p-6 !rounded-[1.5rem] sm:!rounded-[2.5rem] group border border-slate-50 hover:border-[var(--primary)] transition-all dark:border-white/5">
                            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-2xl bg-slate-50 dark:bg-white/5">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-300"><i class="fas fa-box text-2xl"></i></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">{{ $item['brand'] ?? 'Premium Asset' }}</p>
                                            <a href="{{ url('/products/'.$id) }}" class="text-base font-black text-slate-900 dark:text-white tracking-tight hover:text-[var(--primary)] transition-colors">{{ $item['name'] }}</a>
                                        </div>
                                        <button wire:click="removeItem({{ $id }})" class="h-8 w-8 flex items-center justify-center rounded-full bg-slate-50 text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition-all dark:bg-white/5"><i class="fas fa-times text-xs"></i></button>
                                    </div>
                                    <div class="flex items-center justify-between mt-6">
                                        <div class="flex items-center h-10 rounded-full border border-slate-100 bg-slate-50 dark:border-white/5 dark:bg-white/5 overflow-hidden">
                                            <button wire:click="updateQuantity({{ $id }}, -1)" class="w-10 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"><i class="fas fa-minus text-[10px]"></i></button>
                                            <span class="w-12 text-center text-[11px] font-black text-slate-900 dark:text-white">{{ $item['quantity'] }}</span>
                                            <button wire:click="updateQuantity({{ $id }}, 1)" class="w-10 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"><i class="fas fa-plus text-[10px]"></i></button>
                                        </div>
                                        <div class="text-right">
                                            @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                <p class="text-[10px] font-black text-slate-300 line-through">Rs {{ number_format($item['original_price'] * $item['quantity'], 0) }}</p>
                                            @endif
                                            <p class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Rs {{ number_format($item['price'] * $item['quantity'], 0) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="premium-card !p-8 !rounded-[2.5rem] border border-slate-50 dark:border-white/5">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Credential Injection</p>
                        @if($couponApplied)
                            <div class="flex items-center justify-between p-6 rounded-3xl bg-emerald-50 border border-emerald-100 dark:bg-emerald-500/5 dark:border-emerald-500/10">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500 mb-1">Coupon Verified</p>
                                    <p class="text-sm font-black text-emerald-900 dark:text-emerald-200">{{ $couponMsg }}</p>
                                </div>
                                <button wire:click="removeCoupon" class="text-[9px] font-black uppercase tracking-widest text-rose-500 hover:underline">Revoke</button>
                            </div>
                        @else
                            <div class="flex gap-4">
                                <input type="text" wire:model="couponCode" wire:keydown.enter="applyCoupon" placeholder="PROTOCOL CODE" class="flex-1 h-14 rounded-3xl border-slate-100 bg-slate-50 px-6 text-sm font-black tracking-widest text-slate-900 placeholder:text-slate-300 focus:border-[var(--primary)] focus:ring-0 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <button wire:click="applyCoupon" wire:loading.attr="disabled" class="h-14 px-10 rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:scale-105 transition-all">
                                    <span wire:loading.remove wire:target="applyCoupon">Apply Code</span>
                                    <span wire:loading wire:target="applyCoupon"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                            @if($couponMsg)
                                <p class="mt-4 text-[9px] font-black uppercase tracking-widest {{ $couponError ? 'text-rose-500' : 'text-emerald-500' }} ml-6">{{ $couponMsg }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                <aside>
                    <div class="premium-card !p-10 !rounded-[3rem] sticky top-24 border border-slate-50 shadow-2xl dark:border-white/5">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter mb-10">Ledger Metrics</h3>
                        <div class="space-y-6">
                            @foreach([
                                ['Buffer Subtotal', 'Rs '.number_format($subtotal, 0), false],
                                ['Injected Discount', $discountAmount > 0 ? '-Rs '.number_format($discountAmount, 0) : 'None', true],
                                ['Logistics Contribution', $shipping > 0 ? 'Rs '.number_format($shipping, 0) : 'Calculated at Deployment', false]
                            ] as [$label, $value, $isDiscount])
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</span>
                                    <span class="text-sm font-black {{ ($isDiscount && $discountAmount > 0) ? 'text-emerald-500' : 'text-slate-900 dark:text-white' }}">{{ $value }}</span>
                                </div>
                            @endforeach
                            <div class="pt-8 mt-4 border-t border-slate-100 dark:border-white/5 flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Final Ledger</p>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Rs {{ number_format($total, 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12 space-y-4">
                            <a href="{{ url('/checkout') }}" class="w-full h-16 flex items-center justify-center rounded-full bg-gradient-to-r from-[var(--primary)] to-[var(--secondary)] text-[10px] font-black uppercase tracking-widest text-white shadow-[0_20px_50px_-10px_var(--primary-glow)] hover:scale-[1.02] transition-all">
                                <i class="fas fa-shield-check mr-3 text-xs"></i> Initialize Acquisition
                            </a>
                            <a href="{{ url('/products') }}" class="w-full h-14 flex items-center justify-center rounded-full bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 transition-all dark:bg-white/5 dark:hover:bg-white/10">
                                Continue Exploration
                            </a>
                        </div>
                        <div class="mt-10 grid grid-cols-3 gap-6 pt-10 border-t border-slate-50 dark:border-white/5 text-center">
                            @foreach([['Shield', 'Secure', 'text-emerald-500'], ['Undo', 'Registry', 'text-blue-500'], ['Truck', 'Deploy', 'text-purple-500']] as [$icon, $label, $color])
                                <div>
                                    <i class="fas fa-{{ $icon }} mb-3 block text-base {{ $color }}"></i>
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </div>

    <!-- Mobile Sticky Checkout Bar -->
    @if(!empty($cart))
        @php
            $isApp = str_contains(request()->userAgent(), 'DisplayLankaApp');
        @endphp
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-950/95 border-t border-slate-100 dark:border-white/5 p-4 flex items-center justify-between shadow-[0_-10px_30px_rgba(0,0,0,0.05)] {{ $isApp ? 'mb-0' : 'mb-[64px]' }}">
            <div>
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Valuation</span>
                <p class="text-xl font-black text-slate-900 dark:text-white">Rs {{ number_format($total, 0) }}</p>
            </div>
            <a href="{{ url('/checkout') }}" class="h-11 px-6 flex items-center justify-center rounded-xl bg-gradient-to-r from-[var(--primary)] to-[var(--secondary)] text-white text-xs font-black uppercase tracking-widest transition shadow-lg">
                Checkout
            </a>
        </div>
    @endif
</div>
