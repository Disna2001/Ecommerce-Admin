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
            <span class="font-medium text-slate-700 dark:text-slate-100">Cart</span>
        </nav>

        <div class="glass card-shadow mb-6 rounded-[2rem] px-6 py-7">
            <h1 class="text-adapt text-3xl font-black">
                Shopping Cart
                <span class="ml-2 text-base font-normal text-soft">({{ $count }} items)</span>
            </h1>
            <p class="mt-3 text-sm leading-7 text-soft">Review your selected products, update quantities, and move to checkout with the same storefront flow as the home page.</p>
        </div>

        @if(empty($cart))
            <div class="glass card-shadow rounded-[2rem] py-20 text-center">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full" style="background:color-mix(in srgb,var(--primary) 12%,white)">
                    <i class="fas fa-shopping-cart text-3xl" style="color:var(--primary)"></i>
                </div>
                <h2 class="mb-2 text-xl font-bold text-adapt">Your cart is empty</h2>
                <p class="mx-auto mb-6 max-w-xl text-soft">Discover products you will love, then come back here to review totals, apply coupons, and move to checkout confidently.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a wire:navigate href="{{ url('/products') }}" class="btn-gradient inline-block rounded-2xl px-7 py-3 font-bold">Shop Now</a>
                    <a wire:navigate href="{{ route('help-center') }}" class="inline-block rounded-2xl border border-slate-200 bg-white px-7 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Need Help?</a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-7 lg:grid-cols-3">
                <div class="space-y-3 lg:col-span-2">
                    @foreach($cart as $id => $item)
                        <div class="surface card-shadow flex items-start gap-4 rounded-[1.75rem] p-4">
                            <a href="{{ url('/products/'.$id) }}" class="flex-shrink-0">
                                <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-white to-violet-50 dark:from-slate-900 dark:to-slate-800">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-box text-2xl text-slate-300"></i>
                                    @endif
                                </div>
                            </a>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.18em] text-soft">{{ $item['brand'] ?? '' }}</p>
                                        <a href="{{ url('/products/'.$id) }}" class="text-sm font-semibold leading-snug text-adapt hover:opacity-75">{{ $item['name'] }}</a>
                                    </div>
                                    <button wire:click="removeItem({{ $id }})" class="flex-shrink-0 text-slate-300 transition hover:text-red-500">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex items-center overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
                                        <button wire:click="updateQuantity({{ $id }}, -1)" class="flex h-8 w-8 items-center justify-center font-bold text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800">-</button>
                                        <span class="w-10 text-center text-sm font-semibold">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $id }}, 1)" class="flex h-8 w-8 items-center justify-center font-bold text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800">+</button>
                                    </div>
                                    <div class="text-right">
                                        @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                            <p class="text-xs text-soft line-through">Rs {{ number_format($item['original_price'] * $item['quantity'],2) }}</p>
                                        @endif
                                        <p class="text-sm font-bold text-adapt">Rs {{ number_format($item['price'] * $item['quantity'],2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="surface card-shadow rounded-[1.75rem] p-5">
                        <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-adapt">
                            <i class="fas fa-tag" style="color:var(--primary)"></i> Coupon Code
                        </h3>
                        @if($couponApplied)
                            <div class="flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-2.5 text-sm">
                                <span class="font-semibold text-green-700">{{ $couponMsg }}</span>
                                <button wire:click="removeCoupon" class="ml-3 text-xs text-red-500 hover:underline">Remove</button>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <input type="text" wire:model="couponCode" wire:keydown.enter="applyCoupon" placeholder="Enter code" class="field flex-1 uppercase">
                                <button wire:click="applyCoupon" wire:loading.attr="disabled" class="btn-gradient rounded-xl px-5 py-2.5 text-sm font-semibold">
                                    <span wire:loading.remove wire:target="applyCoupon">Apply</span>
                                    <span wire:loading wire:target="applyCoupon"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                            @if($couponMsg)
                                <p class="mt-2 text-xs {{ $couponError ? 'text-red-500' : 'text-green-600' }}">{{ $couponMsg }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                <div>
                    <div class="surface card-shadow sticky top-24 rounded-[1.75rem] p-5">
                        <h3 class="mb-5 text-base font-bold text-adapt">Order Summary</h3>
                        <div class="mb-5 space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-soft">Subtotal</span>
                                <span class="font-medium">Rs {{ number_format($subtotal,2) }}</span>
                            </div>
                            @if($discountAmount > 0)
                                <div class="flex justify-between">
                                    <span class="text-green-600">Discount</span>
                                    <span class="font-medium text-green-600">-Rs {{ number_format($discountAmount,2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-soft">Shipping</span>
                                <span class="font-medium">{{ $shipping > 0 ? 'Rs '.number_format($shipping,2) : 'FREE' }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-3 font-bold dark:border-white/10">
                                <span class="text-adapt">Total</span>
                                <span class="text-xl" style="color:var(--primary)">Rs {{ number_format($total,2) }}</span>
                            </div>
                        </div>
                        <a href="{{ url('/checkout') }}" class="btn-gradient mb-3 block w-full rounded-2xl py-3.5 text-center text-sm font-bold">
                            <i class="fas fa-lock mr-2"></i>Checkout
                        </a>
                        <a href="{{ url('/products') }}" class="block w-full py-1.5 text-center text-sm text-soft transition hover:opacity-75">
                            <i class="fas fa-arrow-left mr-1"></i>Continue Shopping
                        </a>
                        <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-200 pt-4 text-center dark:border-white/10">
                            <div class="text-xs text-soft"><i class="fas fa-shield-alt mb-1 block text-base text-green-500"></i>Secure</div>
                            <div class="text-xs text-soft"><i class="fas fa-undo mb-1 block text-base text-blue-500"></i>Returns</div>
                            <div class="text-xs text-soft"><i class="fas fa-truck mb-1 block text-base text-purple-500"></i>Fast Ship</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
