<div>
    <div x-data="{ show:false,message:'',type:'success' }"
         x-on:notify.window="show=true;message=$event.detail.message;type=$event.detail.type;setTimeout(()=>show=false,3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success'?'bg-green-500':'bg-red-500'"
         style="display:none">
        <i class="fas fa-info-circle"></i><span x-text="message"></span>
    </div>

    <div class="mx-auto max-w-6xl py-8">
        <div class="storefront-hero card-shadow storefront-reveal mb-8 rounded-[2rem] px-5 py-6 sm:px-6 sm:py-7">
            <div class="mb-6 flex items-center justify-center gap-0">
                @foreach(['Cart'=>'fa-shopping-cart','Shipping'=>'fa-truck','Payment'=>'fa-credit-card','Done'=>'fa-check'] as $step=>$icon)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-bold {{ in_array($step,['Cart','Shipping']) ? 'border-transparent text-white' : 'border-slate-200 text-slate-300 dark:border-white/10' }}"
                                 style="{{ in_array($step,['Cart','Shipping']) ? 'background:var(--primary)' : '' }}">
                                <i class="fas {{ $icon }} text-xs"></i>
                            </div>
                            <span class="mt-2 text-xs {{ $step==='Shipping' ? 'font-semibold' : 'text-soft' }}" style="{{ $step==='Shipping' ? 'color:var(--primary)' : '' }}">{{ $step }}</span>
                        </div>
                        @if($step !== 'Done')
                            <div class="mx-1 mb-4 h-px w-12 bg-slate-200 dark:bg-white/10"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color:var(--primary)">Secure Checkout</p>
                    <h1 class="text-adapt mt-3 text-3xl font-black">Checkout</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-soft">Complete shipping and payment details in the same premium storefront style used across the site.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="storefront-stat rounded-[1.25rem] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-soft">Items</p>
                        <p class="mt-2 text-2xl font-black text-adapt">{{ $count }}</p>
                    </div>
                    <div class="storefront-stat rounded-[1.25rem] px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-soft">Payment</p>
                        <p class="mt-2 text-2xl font-black text-adapt">{{ count($paymentOptions) }}</p>
                    </div>
                    <div class="rounded-[1.25rem] px-4 py-4 text-white shadow-[0_16px_42px_rgba(109,40,217,0.18)]" style="background:linear-gradient(135deg,var(--primary),var(--secondary))">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/75">Total</p>
                        <p class="mt-2 text-lg font-black">Rs {{ number_format($total,2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white/70 px-4 py-4 dark:border-white/10 dark:bg-slate-900/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Secure Flow</p>
                    <p class="mt-2 text-sm font-semibold text-adapt">Protected checkout inputs</p>
                    <p class="mt-1 text-xs leading-6 text-soft">Use shipping and payment details in one short guided flow.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white/70 px-4 py-4 dark:border-white/10 dark:bg-slate-900/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Verification</p>
                    <p class="mt-2 text-sm font-semibold text-adapt">Clear payment paths</p>
                    <p class="mt-1 text-xs leading-6 text-soft">Only bank transfers need receipt upload. Card payments stay separate.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white/70 px-4 py-4 dark:border-white/10 dark:bg-slate-900/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Updates</p>
                    <p class="mt-2 text-sm font-semibold text-adapt">Status emails each step</p>
                    <p class="mt-1 text-xs leading-6 text-soft">You’ll get order progress updates after placement and review steps.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-7 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <div class="surface card-shadow storefront-reveal storefront-reveal-delay-1 rounded-[1.75rem] p-6">
                    <h2 class="mb-5 flex items-center gap-2 font-bold text-adapt">
                        <i class="fas fa-map-marker-alt" style="color:var(--primary)"></i> Shipping Information
                    </h2>
                    @if($savedAddresses->isNotEmpty())
                        <div class="mb-5 grid gap-3 md:grid-cols-2">
                            @foreach($savedAddresses as $savedAddress)
                                <button
                                    type="button"
                                    wire:click="applyAddress({{ $savedAddress->id }})"
                                    class="rounded-2xl border-2 p-4 text-left transition hover:border-violet-300 {{ $selected_address_id === $savedAddress->id ? 'bg-violet-50' : 'bg-white/80 dark:bg-slate-900/50' }}"
                                    style="{{ $selected_address_id === $savedAddress->id ? 'border-color:var(--primary)' : 'border-color:#e2e8f0' }}"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-adapt">{{ $savedAddress->name }}</p>
                                            <p class="mt-1 text-xs leading-5 text-soft">{{ $savedAddress->address }}, {{ $savedAddress->city }}</p>
                                            <p class="mt-1 text-xs text-soft">{{ $savedAddress->phone ?: 'No phone saved' }}{{ $savedAddress->postal_code ? ' | '.$savedAddress->postal_code : '' }}</p>
                                        </div>
                                        @if($savedAddress->is_default)
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-700">Default</span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">First Name *</label>
                            <input type="text" wire:model="first_name" class="field">
                            @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Last Name *</label>
                            <input type="text" wire:model="last_name" class="field">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Email *</label>
                            <input type="email" wire:model="email" class="field">
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Phone *</label>
                            <input type="tel" wire:model="phone" class="field" placeholder="+94 XX XXX XXXX">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Address *</label>
                            <input type="text" wire:model="address" class="field">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">City *</label>
                            <input type="text" wire:model="city" class="field">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Postal Code</label>
                            <input type="text" wire:model="postal_code" class="field">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Order Notes</label>
                            <textarea wire:model="notes" rows="2" class="field resize-none" placeholder="Special instructions..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="surface card-shadow storefront-reveal storefront-reveal-delay-2 rounded-[1.75rem] p-6">
                    <h2 class="mb-5 flex items-center gap-2 font-bold text-adapt">
                        <i class="fas fa-credit-card" style="color:var(--primary)"></i> Payment Method
                    </h2>
                    <div class="space-y-2.5">
                        @foreach($paymentOptions as $option)
                            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border-2 p-4 transition hover:border-violet-300"
                                   style="{{ $payment_method===$option['value'] ? 'border-color:var(--primary);background:color-mix(in srgb,var(--primary) 5%,white)' : 'border-color:#f1f5f9' }}">
                                <input type="radio" wire:model.live="payment_method" value="{{ $option['value'] }}" class="sr-only">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl {{ $option['bg'] }}">
                                    <i class="fas {{ $option['icon'] }} {{ $option['text'] }}"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-adapt">{{ $option['label'] }}</p>
                                    <p class="text-xs text-soft">{{ $option['description'] }}</p>
                                </div>
                                <div class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border-2"
                                     style="{{ $payment_method===$option['value'] ? 'border-color:var(--primary)' : 'border-color:#d1d5db' }}">
                                    @if($payment_method===$option['value'])
                                        <div class="h-2.5 w-2.5 rounded-full" style="background:var(--primary)"></div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if($payment_method === 'bank' && $selectedPaymentOption)
                        <div class="mt-4 space-y-4 rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-900/60">
                            <div class="rounded-2xl border border-violet-100 bg-white/80 p-4 text-sm text-slate-600 dark:border-white/10 dark:bg-slate-950/50 dark:text-slate-300">
                                <p class="font-semibold text-adapt">{{ $selectedPaymentOption['instruction_title'] ?? 'Payment verification' }}</p>
                                <p class="mt-2 leading-7">
                                    {{ $selectedPaymentOption['instruction_body'] ?? 'Upload your payment proof and reference for review.' }}
                                </p>
                            </div>

                            @if($payment_method === 'bank' && (
                                ($selectedPaymentOption['bank_name'] ?? '') ||
                                ($selectedPaymentOption['account_name'] ?? '') ||
                                ($selectedPaymentOption['account_number'] ?? '') ||
                                ($selectedPaymentOption['bank_branch'] ?? '')
                            ))
                                <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-sm dark:border-white/10 dark:bg-slate-950/50 md:grid-cols-2">
                                    @if($selectedPaymentOption['bank_name'] ?? false)
                                        <div><span class="text-soft">Bank Name</span><p class="mt-1 font-semibold text-adapt">{{ $selectedPaymentOption['bank_name'] }}</p></div>
                                    @endif
                                    @if($selectedPaymentOption['account_name'] ?? false)
                                        <div><span class="text-soft">Account Name</span><p class="mt-1 font-semibold text-adapt">{{ $selectedPaymentOption['account_name'] }}</p></div>
                                    @endif
                                    @if($selectedPaymentOption['account_number'] ?? false)
                                        <div><span class="text-soft">Account Number</span><p class="mt-1 font-semibold text-adapt">{{ $selectedPaymentOption['account_number'] }}</p></div>
                                    @endif
                                    @if($selectedPaymentOption['bank_branch'] ?? false)
                                        <div><span class="text-soft">Branch</span><p class="mt-1 font-semibold text-adapt">{{ $selectedPaymentOption['bank_branch'] }}</p></div>
                                    @endif
                                </div>
                            @endif

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Reference Number *</label>
                                <input type="text" wire:model="payment_reference" placeholder="Transaction ID / bank ref" class="field font-mono">
                                @error('payment_reference')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Proof Upload *</label>
                                <input type="file" wire:model="payment_proof" accept="image/*" class="field px-4 py-3">
                                @error('payment_proof')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                <p class="mt-2 text-xs text-soft">Accepted: JPG, PNG, WEBP up to 4MB.</p>
                                <div wire:loading wire:target="payment_proof" class="mt-2 text-xs text-violet-600">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>Uploading proof...
                                </div>
                            </div>

                            @if($payment_proof)
                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-white/10 dark:bg-slate-950/50">
                                    <img src="{{ $payment_proof->temporaryUrl() }}" alt="Payment proof preview" class="h-48 w-full object-cover">
                                </div>
                            @endif

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Payment Note</label>
                                <textarea wire:model="payment_note" rows="3" class="field resize-none" placeholder="Add any note for verification, sender name, time paid, or bank used."></textarea>
                                @error('payment_note')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <p class="flex items-center gap-1.5 text-xs text-soft">
                                <i class="fas fa-shield-check text-green-500"></i> Your order will be marked for payment review after submission.
                            </p>
                        </div>
                    @endif

                    @if($payment_method === 'card' && $selectedPaymentOption)
                        <div class="mt-4 space-y-4 rounded-[1.5rem] border border-purple-100 bg-purple-50/70 p-5 text-sm text-slate-700 dark:border-purple-500/20 dark:bg-purple-500/10 dark:text-slate-200">
                            <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-950/40">
                                <p class="font-semibold text-adapt">{{ $selectedPaymentOption['instruction_title'] ?? 'Card payment selected' }}</p>
                                <p class="mt-2 leading-7">
                                    {{ $selectedPaymentOption['instruction_body'] ?? 'Card payments do not need bank transfer receipt upload. Use the hosted payment gateway when it is available.' }}
                                </p>
                            </div>
                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="rounded-2xl bg-white/80 p-4 dark:bg-slate-950/40">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">No Receipt</p>
                                    <p class="mt-2 font-semibold text-adapt">Card orders no longer ask for bank proof.</p>
                                </div>
                                <div class="rounded-2xl bg-white/80 p-4 dark:bg-slate-950/40">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Best Option</p>
                                    <p class="mt-2 font-semibold text-adapt">Enable PayHere for instant card checkout.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($payment_method === 'payhere' && $selectedPaymentOption)
                        <div class="mt-4 space-y-4 rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-900/60">
                            <div class="rounded-2xl border border-amber-100 bg-white/80 p-4 text-sm text-slate-600 dark:border-white/10 dark:bg-slate-950/50 dark:text-slate-300">
                                <p class="font-semibold text-adapt">{{ $selectedPaymentOption['instruction_title'] ?? 'Secure online payment' }}</p>
                                <p class="mt-2 leading-7">
                                    {{ $selectedPaymentOption['instruction_body'] ?? 'You will be redirected to the secure payment gateway after order confirmation.' }}
                                </p>
                            </div>

                            <div class="grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm dark:border-white/10 dark:bg-slate-950/50">
                                    <p class="text-xs uppercase tracking-[0.22em] text-soft">Checkout</p>
                                    <p class="mt-2 font-semibold text-adapt">Hosted by PayHere</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm dark:border-white/10 dark:bg-slate-950/50">
                                    <p class="text-xs uppercase tracking-[0.22em] text-soft">Confirmation</p>
                                    <p class="mt-2 font-semibold text-adapt">Verified by server callback</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm dark:border-white/10 dark:bg-slate-950/50">
                                    <p class="text-xs uppercase tracking-[0.22em] text-soft">Methods</p>
                                    <p class="mt-2 font-semibold text-adapt">Cards, wallets, local apps</p>
                                </div>
                            </div>

                            @if(empty($selectedPaymentOption['merchant_ready']))
                                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-200">
                                    This gateway is visible, but merchant credentials are not configured yet. Save the PayHere merchant ID and secret in admin before customers use this option.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="surface card-shadow storefront-reveal storefront-reveal-delay-2 sticky top-24 rounded-[1.75rem] p-5">
                    <h3 class="mb-5 font-bold text-adapt">Order Summary</h3>
                    <div class="mb-4 max-h-52 space-y-2 overflow-y-auto">
                        @foreach($cart as $id => $item)
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-white to-violet-50 dark:from-slate-900 dark:to-slate-800">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-box text-xs text-slate-300"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-medium text-adapt">{{ $item['name'] }}</p>
                                    <p class="text-xs text-soft">x{{ $item['quantity'] }}</p>
                                </div>
                                <p class="flex-shrink-0 text-xs font-bold text-adapt">Rs {{ number_format($item['price']*$item['quantity'],2) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-5 space-y-2 border-t border-slate-200 pt-3 text-sm dark:border-white/10">
                        <div class="flex justify-between"><span class="text-soft">Subtotal</span><span>Rs {{ number_format($subtotal,2) }}</span></div>
                        @if($discountAmount > 0)
                            <div class="flex justify-between"><span class="text-green-600">Discount</span><span class="text-green-600">-Rs {{ number_format($discountAmount,2) }}</span></div>
                        @endif
                        <div class="flex justify-between"><span class="text-soft">Shipping</span><span>{{ $shipping > 0 ? 'Rs '.number_format($shipping,2) : 'FREE' }}</span></div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 font-bold dark:border-white/10">
                            <span class="text-adapt">Total</span>
                            <span class="text-lg" style="color:var(--primary)">Rs {{ number_format($total,2) }}</span>
                        </div>
                    </div>
                    <button wire:click="placeOrder" wire:loading.attr="disabled" class="btn-gradient flex w-full items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold">
                        <span wire:loading.remove wire:target="placeOrder"><i class="fas fa-lock mr-1"></i> Place Order</span>
                        <span wire:loading wire:target="placeOrder"><i class="fas fa-spinner fa-spin mr-1"></i> Processing...</span>
                    </button>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-slate-900/60">
                        <p class="text-sm font-semibold text-adapt">What happens next</p>
                        <div class="mt-3 space-y-2 text-xs leading-6 text-soft">
                            <p><i class="fas fa-check-circle mr-2 text-emerald-500"></i>Your order is created immediately after confirmation.</p>
                            <p><i class="fas fa-envelope mr-2" style="color:var(--primary)"></i>You’ll receive email updates for placement, verification, and next status changes.</p>
                            <p><i class="fas fa-receipt mr-2 text-amber-500"></i>Bank transfer needs proof review, while card and PayHere stay separate from receipt uploads.</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-shield-check mt-0.5 text-emerald-600 dark:text-emerald-300"></i>
                            <div>
                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Checkout confidence</p>
                                <p class="mt-1 text-xs leading-6 text-emerald-700/90 dark:text-emerald-200/80">Totals are shown before payment, saved addresses can be selected, and order progress stays traceable after checkout.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
