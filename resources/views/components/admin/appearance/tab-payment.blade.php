<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 shadow-inner">
            <i class="fas fa-credit-card text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Checkout Protocol</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Payment Gateways & Offline Settlements</p>
        </div>
    </div>

    <!-- Integration Notice -->
    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-amber-600 shadow-sm">
                    <i class="fas fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-amber-900 leading-none">Security Integration Notice</p>
                    <p class="mt-1 text-[11px] font-bold text-amber-700/80 leading-relaxed max-w-lg">Automatic payment verification requires valid Merchant IDs and Secrets. Ensure your public URL is correctly configured in system settings.</p>
                </div>
            </div>
            <a href="https://www.payhere.lk/" target="_blank" class="flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-[10px] font-black text-amber-900 uppercase tracking-widest shadow-sm hover:bg-amber-100 transition-colors">
                Provider Console <i class="fas fa-external-link-alt text-[8px]"></i>
            </a>
        </div>
    </div>

    <div class="grid gap-6">
        @foreach([
            'cod' => [
                'icon' => 'fa-hand-holding-dollar',
                'title' => 'Cash on Delivery',
                'desc' => 'Simplified settlement upon physical order reception.',
                'color' => 'indigo',
                'enable_property' => 'enable_cod',
                'fields' => [['label', 'Display Label'], ['description', 'Flow Description']]
            ],
            'bank' => [
                'icon' => 'fa-building-columns',
                'title' => 'Direct Bank Transfer',
                'desc' => 'Structured manual review flow with account data and slip uploads.',
                'color' => 'sky',
                'enable_property' => 'enable_bank_transfer',
                'fields' => [['label', 'Display Label'], ['description', 'Flow Description'], ['instruction_title', 'Instruction Headline'], ['name', 'Bank Name'], ['account_name', 'Account Holder'], ['account_number', 'Account Number'], ['branch', 'Branch Location']]
            ],
            'card' => [
                'icon' => 'fa-credit-card',
                'title' => 'Online Confirmation',
                'desc' => 'External digital gateway or manual transaction proof flow.',
                'color' => 'violet',
                'enable_property' => 'enable_card_payment',
                'fields' => [['label', 'Display Label'], ['description', 'Flow Description'], ['instruction_title', 'Instruction Headline']]
            ]
        ] as $type => $config)
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm group">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $config['color'] }}-50 text-{{ $config['color'] }}-600 shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fas {{ $config['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-900 tracking-tight">{{ $config['title'] }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $config['desc'] }}</p>
                        </div>
                    </div>
                    <button 
                        wire:click="$set('{{ $config['enable_property'] }}', {{ !${$config['enable_property']} ? 'true' : 'false' }})"
                        class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors focus:outline-none {{ ${$config['enable_property']} ? 'bg-'.$config['color'].'-500' : 'bg-slate-100' }}"
                    >
                        <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ ${$config['enable_property']} ? 'translate-x-9' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                @if(${$config['enable_property']})
                    <div class="grid gap-6 md:grid-cols-2 animate-in fade-in slide-in-from-top-4">
                        @foreach($config['fields'] as [$key, $label])
                            <div class="group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within/input:text-{{ $config['color'] }}-500 transition-colors">{{ $label }}</label>
                                <input type="text" wire:model="{{ $type }}_{{ $key }}" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner transition-all focus:border-{{ $config['color'] }}-500 focus:ring-4 focus:ring-{{ $config['color'] }}-500/10">
                            </div>
                        @endforeach
                        
                        @if($type === 'bank')
                            <div class="md:col-span-2 group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Transaction Instructions</label>
                                <textarea wire:model="bank_instruction_body" rows="3" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                            </div>
                        @endif

                        @if($type === 'card')
                             <div class="md:col-span-2 group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Confirmation Protocol</label>
                                <textarea wire:model="card_instruction_body" rows="3" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-center text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em] py-4">This payment vector is currently dormant</p>
                @endif
            </div>
        @endforeach

        <!-- PayHere Bridge -->
        <div class="rounded-[2.5rem] border-2 border-slate-900 bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/5 group-hover:scale-110 transition-transform"></div>
            
            <div class="relative z-10 flex items-center justify-between mb-10">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-indigo-500 shadow-xl shadow-indigo-500/20">
                        <i class="fas fa-bolt-lightning text-xl text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-black tracking-tight">PayHere Gateway Bridge</h4>
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">Automated Card & Wallet Settlement</p>
                    </div>
                </div>
                <button 
                    wire:click="$set('enable_payhere_gateway', {{ !$enable_payhere_gateway ? 'true' : 'false' }})"
                    class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors focus:outline-none {{ $enable_payhere_gateway ? 'bg-indigo-500' : 'bg-white/10' }}"
                >
                    <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $enable_payhere_gateway ? 'translate-x-9' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if($enable_payhere_gateway)
                <div class="relative z-10 grid gap-8 md:grid-cols-2 animate-in fade-in slide-in-from-top-6">
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Gateway Identity (Label)</label>
                            <input type="text" wire:model="payhere_label" class="mt-2 w-full rounded-xl border-white/10 bg-white/5 px-4 py-3 text-xs font-black text-white focus:border-indigo-400 focus:ring-0">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Service Narrative</label>
                            <textarea wire:model="payhere_description" rows="2" class="mt-2 w-full rounded-xl border-white/10 bg-white/5 px-4 py-3 text-xs font-black text-white focus:border-indigo-400 focus:ring-0"></textarea>
                        </div>
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10">
                             <button 
                                wire:click="$set('payhere_sandbox', {{ !$payhere_sandbox ? 'true' : 'false' }})"
                                class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors focus:outline-none {{ $payhere_sandbox ? 'bg-amber-500' : 'bg-white/10' }}"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $payhere_sandbox ? 'translate-x-7' : 'translate-x-1' }}"></span>
                            </button>
                            <div>
                                <p class="text-[10px] font-black text-white uppercase tracking-widest leading-none">Sandbox Mode</p>
                                <p class="text-[9px] font-bold text-white/40 uppercase tracking-tighter mt-1">{{ $payhere_sandbox ? 'Simulated Transactions' : 'Live Production' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-white/40 group-focus-within:text-indigo-400 transition-colors">Merchant ID</label>
                            <div class="mt-2 relative">
                                <input type="text" wire:model="payhere_merchant_id" class="w-full rounded-xl border-white/10 bg-white/5 px-4 py-3 text-xs font-black text-white focus:border-indigo-400 focus:ring-0">
                                <i class="fas fa-id-card absolute right-4 top-1/2 -translate-y-1/2 text-white/10"></i>
                            </div>
                        </div>
                        <div class="group" x-data="{ show: false }">
                            <label class="text-[10px] font-black uppercase tracking-widest text-white/40 group-focus-within:text-indigo-400 transition-colors">Merchant Secret</label>
                            <div class="mt-2 relative">
                                <input :type="show ? 'text' : 'password'" wire:model="payhere_merchant_secret" class="w-full rounded-xl border-white/10 bg-white/5 px-4 py-3 text-xs font-black text-white focus:border-indigo-400 focus:ring-0">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                         <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Redirection Instructions</label>
                            <textarea wire:model="payhere_instruction_body" rows="2" class="mt-2 w-full rounded-xl border-white/10 bg-white/5 px-4 py-3 text-xs font-black text-white focus:border-indigo-400 focus:ring-0"></textarea>
                        </div>
                    </div>
                </div>
            @else
                 <div class="relative z-10 py-12 text-center">
                    <i class="fas fa-plug-circle-exclamation text-white/10 text-4xl mb-4"></i>
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] leading-relaxed">The PayHere bridge is currently disconnected.<br>Enable to activate automated checkout.</p>
                </div>
            @endif
        </div>
    </div>
</div>
