<div>
    <div x-data="{ show:false,message:'',type:'success' }"
         x-on:notify.window="show=true;message=$event.detail.message;type=$event.detail.type;setTimeout(()=>show=false,3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success'?'bg-emerald-500':'bg-rose-500'"
         style="display:none">
        <i class="fas fa-info-circle"></i><span x-text="message"></span>
    </div>

    @php
        $panel = "premium-card !p-8 !rounded-[2.5rem]";
        $muted = "text-[10px] font-black uppercase tracking-[0.2em] text-slate-400";
        $input = "w-full h-14 rounded-3xl border-slate-100 bg-slate-50 px-6 text-sm font-bold text-slate-900 focus:border-[var(--primary)] focus:ring-0 transition-all dark:border-white/5 dark:bg-white/5 dark:text-white";
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <div class="glass !p-12 mb-12 rounded-[3.5rem] relative overflow-hidden">
            <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                <i class="fas fa-shield-check text-[120px]"></i>
            </div>
            
            <div class="mb-12 flex flex-wrap items-center justify-center gap-4 lg:justify-start">
                @foreach(['Cart'=>'fa-shopping-cart','Shipping'=>'fa-truck','Payment'=>'fa-credit-card','Done'=>'fa-check'] as $step=>$icon)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 transition-all {{ in_array($step,['Cart','Shipping']) ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-[0_0_20px_var(--primary-glow)]' : 'border-slate-100 text-slate-300 dark:border-white/5' }}">
                                <i class="fas {{ $icon }} text-xs"></i>
                            </div>
                            <span class="mt-3 text-[10px] font-black uppercase tracking-widest {{ $step==='Shipping' ? 'text-[var(--primary)]' : 'text-slate-400' }}">{{ $step }}</span>
                        </div>
                        @if($step !== 'Done')
                            <div class="mx-4 mb-6 h-px w-12 bg-slate-100 dark:bg-white/5"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-10 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-[var(--primary)] text-[10px] font-black uppercase tracking-[0.3em] mb-4">Secure Terminal</p>
                    <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">Acquisition Protocol</h1>
                    <p class="mt-4 max-w-2xl text-sm font-bold text-slate-400 leading-relaxed">Execute final logistics and payment verification steps within the secure storefront environment.</p>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @foreach([
                        ['Assets', $count, false],
                        ['Gateways', count($paymentOptions), false],
                        ['Grand Ledger', 'Rs '.number_format($total, 0), true]
                    ] as [$label, $value, $primary])
                        <div class="{{ $primary ? 'bg-slate-900' : 'bg-white/50 dark:bg-white/5' }} premium-card !p-6 !rounded-3xl border border-slate-50 shadow-sm dark:border-white/5">
                            <p class="text-[9px] font-black uppercase tracking-widest {{ $primary ? 'text-slate-500' : 'text-slate-400' }} mb-2">{{ $label }}</p>
                            <p class="text-xl font-black {{ $primary ? 'text-white' : 'text-slate-900 dark:text-white' }} tracking-tight">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach([
                    ['Defensive Flow', 'Encrypted input matrices', 'Operational security standards applied.'],
                    ['Verification', 'Verified payment nodes', 'Receipt audit protocols active for transfers.'],
                    ['Synchronization', 'Real-time ledger updates', 'Status notifications synchronized via SMTP.']
                ] as [$title, $subtitle, $desc])
                    <div class="p-6 rounded-3xl bg-slate-50/50 dark:bg-white/5">
                        <p class="{{ $muted }} mb-2">{{ $title }}</p>
                        <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-1">{{ $subtitle }}</p>
                        <p class="text-[10px] font-bold text-slate-400 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-12">
                <section class="{{ $panel }}">
                    <p class="{{ $muted }} mb-8">Geospatial Distribution Node</p>
                    <div class="flex flex-col gap-8">
                        @if($savedAddresses->isNotEmpty())
                            <div class="grid gap-4 md:grid-cols-2">
                                @foreach($savedAddresses as $savedAddress)
                                    <button type="button" wire:click="applyAddress({{ $savedAddress->id }})" class="group relative rounded-[2rem] border-2 p-6 text-left transition-all {{ $selected_address_id === $savedAddress->id ? 'border-[var(--primary)] bg-[var(--primary)]/5 shadow-xl' : 'border-slate-50 bg-slate-50/30 hover:border-slate-200 dark:border-white/5 dark:bg-white/5' }}">
                                        @if($savedAddress->is_default)
                                            <span class="absolute top-4 right-4 rounded-full bg-emerald-500/10 px-3 py-1 text-[8px] font-black uppercase tracking-widest text-emerald-500">Primary Node</span>
                                        @endif
                                        <p class="text-sm font-black text-slate-900 dark:text-white mb-2 tracking-tight">{{ $savedAddress->name }}</p>
                                        <p class="text-[11px] font-bold text-slate-400 leading-relaxed">{{ $savedAddress->address }}, {{ $savedAddress->city }}</p>
                                        <div class="mt-4 flex items-center gap-3">
                                            <div class="h-1.5 w-1.5 rounded-full {{ $selected_address_id === $savedAddress->id ? 'bg-[var(--primary)] animate-pulse' : 'bg-slate-200 dark:bg-white/10' }}"></div>
                                            <span class="text-[9px] font-black uppercase tracking-widest {{ $selected_address_id === $savedAddress->id ? 'text-[var(--primary)]' : 'text-slate-400' }}">{{ $selected_address_id === $savedAddress->id ? 'Node Selected' : 'Available Node' }}</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <div class="grid gap-6 md:grid-cols-2">
                            @foreach([
                                ['First Identification', 'first_name', 'text', 'First Name'],
                                ['Last Identification', 'last_name', 'text', 'Last Name'],
                                ['Registry Email', 'email', 'email', 'identity@matrix.com'],
                                ['Communication Channel', 'phone', 'tel', '+94 XX XXX XXXX']
                            ] as [$label, $model, $type, $placeholder])
                                <div class="space-y-2">
                                    <label class="{{ $muted }}">{{ $label }}</label>
                                    <input type="{{ $type }}" wire:model="{{ $model }}" class="{{ $input }}" placeholder="{{ $placeholder }}">
                                    @error($model)<p class="mt-2 text-[9px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                            <div class="md:col-span-2 space-y-2">
                                <label class="{{ $muted }}">Geospatial Coordinates (Address)</label>
                                <input type="text" wire:model="address" class="{{ $input }}">
                                @error('address')<p class="mt-2 text-[9px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                            </div>
                            @foreach([
                                ['Distribution City', 'city', 'text'],
                                ['Postal Node Index', 'postal_code', 'text']
                            ] as [$label, $model, $type])
                                <div class="space-y-2">
                                    <label class="{{ $muted }}">{{ $label }}</label>
                                    <input type="{{ $type }}" wire:model="{{ $model }}" class="{{ $input }}">
                                    @error($model)<p class="mt-2 text-[9px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                            <div class="md:col-span-2 space-y-2">
                                <label class="{{ $muted }}">Operational Notes</label>
                                <textarea wire:model="notes" rows="2" class="{{ $input }} !h-auto !py-4 resize-none" placeholder="Administrative or delivery metadata..."></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="{{ $panel }}">
                    <p class="{{ $muted }} mb-8">Settlement Configuration Matrix</p>
                    <div class="grid gap-4">
                        @foreach($paymentOptions as $option)
                            <label class="group cursor-pointer relative rounded-[2rem] border-2 p-6 transition-all {{ $payment_method===$option['value'] ? 'border-[var(--primary)] bg-[var(--primary)]/5 shadow-lg' : 'border-slate-50 bg-slate-50/30 hover:border-slate-200 dark:border-white/5 dark:bg-white/5' }}">
                                <input type="radio" wire:model.live="payment_method" value="{{ $option['value'] }}" class="sr-only">
                                <div class="flex items-center gap-6">
                                    <div class="h-14 w-14 flex-shrink-0 flex items-center justify-center rounded-2xl {{ $option['bg'] }} shadow-sm transition-transform group-hover:scale-110">
                                        <i class="fas {{ $option['icon'] }} {{ $option['text'] }} text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base font-black text-slate-900 dark:text-white tracking-tight mb-1">{{ $option['label'] }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $option['description'] }}</p>
                                    </div>
                                    <div class="h-6 w-6 rounded-full border-4 transition-all {{ $payment_method===$option['value'] ? 'border-[var(--primary)] bg-white dark:bg-slate-900' : 'border-slate-100 dark:border-white/5' }}"></div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-8 transition-all duration-500">
                        @if($payment_method === 'bank' && $selectedPaymentOption)
                            <div class="p-8 rounded-[2.5rem] bg-slate-900 shadow-2xl space-y-8">
                                <div class="flex items-center gap-4 text-[var(--primary)]">
                                    <i class="fas fa-university text-xl"></i>
                                    <p class="text-xs font-black uppercase tracking-[0.2em]">Manual Transfer Protocol</p>
                                </div>
                                
                                <div class="p-6 rounded-3xl bg-white/5 border border-white/10">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-4">Destination Intelligence</p>
                                    <div class="grid gap-6 sm:grid-cols-2">
                                        @foreach([
                                            ['Bank Name', $selectedPaymentOption['bank_name'] ?? 'N/A'],
                                            ['Account Identity', $selectedPaymentOption['account_name'] ?? 'N/A'],
                                            ['Protocol Number', $selectedPaymentOption['account_number'] ?? 'N/A'],
                                            ['Node Branch', $selectedPaymentOption['bank_branch'] ?? 'N/A']
                                        ] as [$label, $value])
                                            <div>
                                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">{{ $label }}</p>
                                                <p class="text-sm font-black text-white tracking-tight">{{ $value }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="grid gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4">Verification Index (Ref #)</label>
                                        <input type="text" wire:model="payment_reference" class="{{ $input }} !bg-white/5 !border-white/10 !text-white placeholder:text-slate-700" placeholder="TXN_REFERENCE_ID">
                                        @error('payment_reference')<p class="mt-2 text-[9px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-4">Artifact Upload (Proof)</label>
                                        <div class="relative h-14 rounded-3xl bg-white/5 border border-dashed border-white/10 flex items-center px-6 group hover:border-[var(--primary)] transition-all cursor-pointer overflow-hidden">
                                            <input type="file" wire:model="payment_proof" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                            <i class="fas fa-cloud-upload-alt text-slate-500 group-hover:text-[var(--primary)] transition-colors mr-3"></i>
                                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">{{ $payment_proof ? 'Artifact Attached' : 'Select Proof Artifact' }}</span>
                                        </div>
                                        @error('payment_proof')<p class="mt-2 text-[9px] font-black uppercase tracking-widest text-rose-500 ml-4">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(in_array($payment_method, ['card', 'payhere']) && $selectedPaymentOption)
                            <div class="p-8 rounded-[2.5rem] bg-slate-900 shadow-2xl">
                                <div class="flex items-center gap-4 text-emerald-500 mb-8">
                                    <i class="fas fa-bolt text-xl"></i>
                                    <p class="text-xs font-black uppercase tracking-[0.2em]">Automated Gateway Protocol</p>
                                </div>
                                <div class="grid gap-6 md:grid-cols-2">
                                    <div class="p-6 rounded-3xl bg-white/5 border border-white/10">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Sync Status</p>
                                        <p class="text-xs font-black text-white uppercase tracking-widest">Server-Side Verified</p>
                                    </div>
                                    <div class="p-6 rounded-3xl bg-white/5 border border-white/10">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Redirect</p>
                                        <p class="text-xs font-black text-white uppercase tracking-widest">Secure External Node</p>
                                    </div>
                                </div>
                                @if($payment_method === 'payhere' && empty($selectedPaymentOption['merchant_ready']))
                                    <div class="mt-8 p-6 rounded-3xl bg-rose-500/10 border border-rose-500/20 text-rose-500">
                                        <p class="text-[9px] font-black uppercase tracking-widest mb-2">Configuration Warning</p>
                                        <p class="text-[11px] font-bold leading-relaxed">Merchant credentials for this gateway node have not been synchronized. Verification may fail.</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <aside>
                <div class="premium-card !p-10 !rounded-[3rem] sticky top-24 border border-slate-50 shadow-2xl dark:border-white/5">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter mb-10">Grand Ledger Summary</h3>
                    
                    <div class="space-y-4 mb-10 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($cart as $id => $item)
                            <div class="flex items-center gap-4 p-3 rounded-2xl bg-slate-50/50 dark:bg-white/5">
                                <div class="h-10 w-10 flex-shrink-0 rounded-lg overflow-hidden bg-white shadow-sm dark:bg-slate-900">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-200"><i class="fas fa-box text-xs"></i></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-black text-slate-900 dark:text-white tracking-tight truncate">{{ $item['name'] }}</p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <p class="text-xs font-black text-slate-900 dark:text-white">Rs {{ number_format($item['price']*$item['quantity'], 0) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-6 pt-10 border-t border-slate-100 dark:border-white/5">
                        <div class="flex justify-between items-center">
                            <span class="{{ $muted }}">Subtotal Ledger</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white">Rs {{ number_format($subtotal, 0) }}</span>
                        </div>
                        @if($discountAmount > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Protocol Discount</span>
                                <span class="text-sm font-black text-emerald-500">-Rs {{ number_format($discountAmount, 0) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="{{ $muted }}">Logistics Contribution</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white">{{ $shipping > 0 ? 'Rs '.number_format($shipping, 0) : 'FREE' }}</span>
                        </div>
                        <div class="pt-8 mt-4 border-t-2 border-slate-900 flex justify-between items-end dark:border-white">
                            <div>
                                <p class="{{ $muted }} mb-1">Final Settlement</p>
                                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Rs {{ number_format($total, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12">
                        <button wire:click="placeOrder" wire:loading.attr="disabled" class="w-full h-16 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-[1.02] transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="placeOrder"><i class="fas fa-shield-check mr-3"></i> Execute Acquisition</span>
                            <span wire:loading wire:target="placeOrder"><i class="fas fa-spinner fa-spin mr-3"></i> Synchronizing...</span>
                        </button>
                    </div>

                    <div class="mt-10 p-6 rounded-[2rem] bg-emerald-500/5 border border-emerald-500/10">
                        <div class="flex items-start gap-4">
                            <i class="fas fa-info-circle text-emerald-500 mt-1"></i>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 mb-2">Protocol Intelligence</p>
                                <p class="text-[10px] font-bold text-emerald-700/80 leading-relaxed">Orders are initialized immediately. Status nodes will update via secure channels throughout the fulfilment process.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
