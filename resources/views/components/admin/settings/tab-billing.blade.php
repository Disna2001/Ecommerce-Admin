@props([
    'billingProfiles' => [],
    'billingDefaultProfiles' => [],
    'billingPreviewCompany' => [],
    'billingPreviewDocuments' => [],
    'printerCatalog' => [],
    'currency' => 'Rs',
])

<div class="space-y-8">
    <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 shadow-inner"><i class="fas fa-receipt text-lg"></i></div>
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Commerce & Print Engine</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Document Engineering & Device Routing</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="addBillingProfile('invoice_pdf')" class="rounded-xl bg-slate-900 px-4 py-2.5 text-[10px] font-black text-white uppercase tracking-widest shadow-xl transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-file-pdf mr-2"></i>New Invoice
            </button>
            <button type="button" wire:click="addBillingProfile('pos_receipt')" class="rounded-xl bg-slate-900 px-4 py-2.5 text-[10px] font-black text-white uppercase tracking-widest shadow-xl transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-receipt mr-2"></i>New Receipt
            </button>
            <button type="button" wire:click="resetBillingProfiles" class="rounded-xl bg-amber-500 px-4 py-2.5 text-[10px] font-black text-white uppercase tracking-widest shadow-xl transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-rotate-left mr-2"></i>Reset
            </button>
        </div>
    </div>

    <!-- Primary Routing & Global Strategy -->
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Global Routing Strategy</p>
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Default Invoice Profile</label>
                    <select wire:model="billing_default_profiles.invoice_pdf" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                        @foreach($billingProfiles as $profile)
                            @if(in_array($profile['bill_type'], ['invoice_pdf', 'any'], true))
                                <option value="{{ $profile['id'] }}">{{ $profile['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Default Receipt Profile</label>
                    <select wire:model="billing_default_profiles.pos_receipt" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                        @foreach($billingProfiles as $profile)
                            @if(in_array($profile['bill_type'], ['pos_receipt', 'any'], true))
                                <option value="{{ $profile['id'] }}">{{ $profile['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
            <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-emerald-500/10"></div>
            <p class="text-[10px] font-black uppercase tracking-widest text-white/40">Device Intelligence</p>
            <p class="mt-4 text-xs font-medium leading-relaxed opacity-80 italic">Recognition is automatic based on browser capabilities. Printer routing uses local aliases stored in the POS environment.</p>
        </div>
    </div>

    <!-- Printer Registry -->
    <div class="space-y-6 rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between px-2">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operational Printer Registry</p>
            <button type="button" wire:click="addPrinter" class="text-[10px] font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-700 transition-colors">
                <i class="fas fa-plus-circle mr-1"></i> Register Hardware
            </button>
        </div>

        <div class="grid gap-4">
            @forelse($printerCatalog as $printerIndex => $printer)
                <div class="group relative rounded-3xl border border-slate-100 bg-slate-50/50 p-6 transition-all hover:bg-white hover:border-emerald-400 hover:shadow-xl hover:shadow-emerald-50">
                    <div class="grid gap-6 xl:grid-cols-[1fr_1fr_0.6fr_0.6fr_auto]">
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Hardware Alias</label>
                            <input type="text" wire:model="printer_catalog.{{ $printerIndex }}.alias" class="w-full rounded-2xl border-slate-100 bg-white px-4 py-2.5 text-sm font-bold shadow-inner focus:border-emerald-500 focus:ring-0 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">System Queue Name</label>
                            <input type="text" wire:model="printer_catalog.{{ $printerIndex }}.queue_name" placeholder="Xprinter XP-365B" class="w-full rounded-2xl border-slate-100 bg-white px-4 py-2.5 text-sm font-bold shadow-inner focus:border-emerald-500 focus:ring-0 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Connection</label>
                            <select wire:model="printer_catalog.{{ $printerIndex }}.connection_type" class="w-full rounded-2xl border-slate-100 bg-white px-4 py-2.5 text-sm font-bold shadow-inner focus:border-emerald-500 focus:ring-0 transition-all">
                                <option value="usb">USB</option>
                                <option value="network">Network</option>
                                <option value="shared">Shared</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Paper Format</label>
                            <select wire:model="printer_catalog.{{ $printerIndex }}.paper_size" class="w-full rounded-2xl border-slate-100 bg-white px-4 py-2.5 text-sm font-bold shadow-inner focus:border-emerald-500 focus:ring-0 transition-all">
                                <option value="a4">Standard A4</option>
                                <option value="letter">Letter</option>
                                <option value="thermal_80">Thermal 80mm</option>
                                <option value="thermal_58">Thermal 58mm</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 pt-5">
                            <button type="button" wire:click="removePrinter({{ $printerIndex }})" class="h-10 w-10 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash-can text-xs"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-200 p-12 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mb-4"><i class="fas fa-print text-2xl"></i></div>
                    <p class="text-sm font-bold text-slate-400 italic">No hardware registered in the registry.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Bill Profiles -->
    <div class="space-y-6">
        @forelse($billingProfiles as $index => $profile)
            @php
                $isInvoiceProfile = in_array($profile['bill_type'], ['invoice_pdf', 'any'], true);
                $preview = $isInvoiceProfile ? ($billingPreviewDocuments['invoice'] ?? []) : ($billingPreviewDocuments['receipt'] ?? []);
                $currency = $billingPreviewCompany['currency_symbol'] ?? 'Rs';
            @endphp
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm overflow-hidden relative group">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-{{ $isInvoiceProfile ? 'indigo' : 'emerald' }}-50 group-hover:scale-150 transition-transform"></div>
                
                <div class="relative z-10 flex flex-col gap-8 xl:flex-row xl:items-start">
                    <!-- Config Panel -->
                    <div class="flex-1 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Profile Archive #{{ $index + 1 }}</p>
                                <h4 class="mt-2 text-xl font-black text-slate-900">{{ $profile['name'] }}</h4>
                            </div>
                            <button type="button" wire:click="removeBillingProfile({{ $index }})" class="rounded-xl bg-rose-50 px-4 py-2 text-[10px] font-black text-rose-600 uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all">Destroy Profile</button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Profile Name</label>
                                <input type="text" wire:model="billing_profiles.{{ $index }}.name" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Internal ID Tag</label>
                                <input type="text" wire:model="billing_profiles.{{ $index }}.id" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-mono font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-4">
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Bill Classification</label>
                                <select wire:model="billing_profiles.{{ $index }}.bill_type" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-2.5 text-[11px] font-bold shadow-inner focus:ring-0 transition-all">
                                    <option value="invoice_pdf">Invoice PDF</option>
                                    <option value="pos_receipt">POS Receipt</option>
                                    <option value="any">Universal Bill</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Output Mode</label>
                                <select wire:model="billing_profiles.{{ $index }}.output_mode" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-2.5 text-[11px] font-bold shadow-inner focus:ring-0 transition-all">
                                    <option value="pdf">Direct PDF</option>
                                    <option value="browser_print">Browser UI Print</option>
                                    <option value="either">Hybrid Either</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Paper Target</label>
                                <select wire:model="billing_profiles.{{ $index }}.paper_size" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-2.5 text-[11px] font-bold shadow-inner focus:ring-0 transition-all">
                                    <option value="a4">Standard A4</option>
                                    <option value="letter">Letter</option>
                                    <option value="thermal_80">Thermal 80mm</option>
                                    <option value="thermal_58">Thermal 58mm</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Routing Alias</label>
                                <input list="printer-aliases" type="text" wire:model="billing_profiles.{{ $index }}.printer_match" placeholder="Auto-detect..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-2.5 text-[11px] font-bold shadow-inner focus:ring-0 transition-all">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="px-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Data Visibility Matrix</p>
                            <div class="grid gap-2 sm:grid-cols-3">
                                @foreach([
                                    'auto_print' => 'Auto-print Engine',
                                    'show_company_phone' => 'Brand Phone',
                                    'show_tax_id' => 'Commercial Tax ID',
                                    'show_customer_address' => 'Customer Geo',
                                    'show_customer_email' => 'Customer Email',
                                    'show_customer_phone' => 'Customer Phone',
                                    'show_payment_method' => 'Settle Method',
                                    'show_notes' => 'Append Notes',
                                    'show_terms' => 'Legal Terms',
                                ] as $key => $label)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-50 bg-slate-50/50 px-3 py-2 transition-all hover:bg-white hover:border-emerald-400">
                                        <input type="checkbox" wire:model="billing_profiles.{{ $index }}.{{ $key }}" class="h-3 w-3 rounded border-slate-300 text-emerald-600 focus:ring-0">
                                        <span class="text-[10px] font-bold text-slate-600">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Live Structural Preview -->
                    <div class="w-full xl:w-[420px] rounded-3xl border border-slate-100 bg-slate-50/50 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Structural Preview</p>
                            <div class="rounded-lg bg-white px-2 py-1 text-[8px] font-black uppercase tracking-widest text-slate-400 border border-slate-100">{{ $profile['paper_size'] }}</div>
                        </div>

                        <div class="rounded-2xl bg-white shadow-2xl p-6 min-h-[300px] relative overflow-hidden text-[10px] text-slate-600">
                            <!-- Watermark Simulation -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] rotate-[30deg] pointer-events-none">
                                <p class="text-4xl font-black tracking-[1em] text-slate-900 uppercase">{{ $preview['status'] ?? 'DRAFT' }}</p>
                            </div>

                            <div class="relative z-10 space-y-4">
                                <div class="flex justify-between border-b border-slate-50 pb-4">
                                    <div>
                                        <p class="text-xs font-black text-slate-900">{{ $billingPreviewCompany['display_name'] }}</p>
                                        <p class="mt-1 opacity-60">{{ $billingPreviewCompany['email'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-900">{{ $preview['number'] ?? 'INV-0000' }}</p>
                                        <p class="opacity-60">{{ $preview['date'] ?? now()->format('Y-m-d') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-[9px]">
                                    <div>
                                        <p class="font-black text-slate-900 uppercase opacity-30">Enrolled To</p>
                                        <p class="mt-1 font-bold">{{ $preview['customer_name'] ?? 'Walk-in Customer' }}</p>
                                        @if($profile['show_customer_phone']) <p class="opacity-60">{{ $preview['customer_phone'] ?? 'N/A' }}</p> @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-black text-slate-900 uppercase opacity-30">Settlement</p>
                                        <p class="mt-1 font-bold">{{ $preview['payment_method'] ?? 'Cash' }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2 pt-4">
                                    <div class="flex justify-between font-black text-slate-900 uppercase opacity-30 text-[8px]">
                                        <span>Item Description</span>
                                        <span>Sum</span>
                                    </div>
                                    @foreach($preview['items'] ?? [] as $item)
                                        <div class="flex justify-between items-center py-1 border-b border-slate-50">
                                            <div class="min-w-0 pr-4">
                                                <p class="font-bold truncate">{{ $item['name'] }}</p>
                                                <p class="text-[8px] opacity-40">Qty: {{ $item['quantity'] }}</p>
                                            </div>
                                            <p class="font-black text-slate-900">{{ $currency }} {{ number_format($item['total'], 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="pt-4 flex flex-col items-end gap-1 border-t border-slate-900/5">
                                    <div class="flex items-center gap-6">
                                        <span class="opacity-40 uppercase font-black text-[8px]">Subtotal</span>
                                        <span class="font-bold">{{ $currency }} {{ number_format($preview['total'] ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex items-center gap-6 text-xs text-slate-900">
                                        <span class="font-black uppercase text-[10px]">Total Due</span>
                                        <span class="font-black">{{ $currency }} {{ number_format($preview['total'] ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-[2.5rem] border border-dashed border-slate-200 p-24 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-slate-300 mb-6"><i class="fas fa-file-invoice text-3xl"></i></div>
                <h4 class="text-xl font-black text-slate-900">Archive Empty</h4>
                <p class="mt-2 text-sm font-medium text-slate-400">Initialize a new billing profile to begin document engineering.</p>
            </div>
        @endforelse
    </div>
</div>

<datalist id="printer-aliases">
    @foreach($printerCatalog as $printer)
        <option value="{{ $printer['alias'] }}">{{ $printer['queue_name'] ?: $printer['notes'] }}</option>
    @endforeach
</datalist>
