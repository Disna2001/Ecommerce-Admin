@props([
    'billingProfiles' => [],
    'billingDefaultProfiles' => [],
    'billingPreviewCompany' => [],
    'billingPreviewDocuments' => [],
    'printerCatalog' => [],
    'currency' => 'Rs',
])

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <i class="fas fa-receipt text-sm"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Commerce & Print</h3>
                <p class="text-xs text-slate-500">Manage invoice PDF templates, thermal receipts, and printers.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="addBillingProfile('invoice_pdf')" class="rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors shadow-xs">
                <i class="fas fa-file-pdf mr-1.5"></i>New Invoice Profile
            </button>
            <button type="button" wire:click="addBillingProfile('pos_receipt')" class="rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors shadow-xs">
                <i class="fas fa-receipt mr-1.5"></i>New Receipt Profile
            </button>
            <button type="button" wire:click="resetBillingProfiles" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                <i class="fas fa-rotate-left mr-1.5"></i>Reset Defaults
            </button>
        </div>
    </div>

    <!-- Default Routing & Device Scope -->
    <div class="grid gap-6 lg:grid-cols-3 text-xs font-semibold">
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Bill Profiles Routing</h4>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Default Invoice Profile</label>
                    <select wire:model="billing_default_profiles.invoice_pdf" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        @foreach($billingProfiles as $profile)
                            @if(in_array($profile['bill_type'], ['invoice_pdf', 'any'], true))
                                <option value="{{ $profile['id'] }}">{{ $profile['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Default Receipt Profile</label>
                    <select wire:model="billing_default_profiles.pos_receipt" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        @foreach($billingProfiles as $profile)
                            @if(in_array($profile['bill_type'], ['pos_receipt', 'any'], true))
                                <option value="{{ $profile['id'] }}">{{ $profile['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-900 p-6 text-white shadow-xs space-y-2">
            <h4 class="font-bold text-white text-sm">Printer Routing</h4>
            <p class="text-xs text-slate-300 leading-relaxed font-normal">Device recognition is automatic based on browser printing settings and configured printer catalog aliases.</p>
        </div>
    </div>

    <!-- Printers -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4 text-xs font-semibold">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h4 class="font-bold text-slate-900 text-sm">Printers</h4>
            <button type="button" wire:click="addPrinter" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                <i class="fas fa-plus-circle mr-1"></i> Add Printer
            </button>
        </div>

        <div class="grid gap-3">
            @forelse($printerCatalog as $printerIndex => $printer)
                <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 space-y-3">
                    <div class="grid gap-4 xl:grid-cols-[1fr_1fr_0.6fr_0.6fr_auto]">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Printer Alias</label>
                            <input type="text" wire:model="printer_catalog.{{ $printerIndex }}.alias" class="w-full rounded-lg border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-900 focus:ring-0">
                        </div>
                        @if(($printer['connection_type'] ?? 'usb') === 'network')
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-700">IP Address</label>
                                    <input type="text" wire:model="printer_catalog.{{ $printerIndex }}.ip_address" placeholder="192.168.1.100" class="w-full rounded-lg border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-900 focus:ring-0">
                                </div>
                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-700">Port</label>
                                    <input type="number" wire:model="printer_catalog.{{ $printerIndex }}.port" placeholder="9100" class="w-full rounded-lg border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-900 focus:ring-0">
                                </div>
                            </div>
                        @else
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">System Queue Name</label>
                                <input type="text" wire:model="printer_catalog.{{ $printerIndex }}.queue_name" placeholder="Xprinter XP-365B" class="w-full rounded-lg border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-900 focus:ring-0">
                            </div>
                        @endif
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Connection</label>
                            <select wire:model="printer_catalog.{{ $printerIndex }}.connection_type" class="w-full rounded-lg border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-900 focus:ring-0">
                                <option value="usb">USB</option>
                                <option value="network">Network</option>
                                <option value="shared">Shared</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Paper Format</label>
                            <select wire:model="printer_catalog.{{ $printerIndex }}.paper_size" class="w-full rounded-lg border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-900 focus:ring-0">
                                <option value="a4">Standard A4</option>
                                <option value="letter">Letter</option>
                                <option value="thermal_80">Thermal 80mm</option>
                                <option value="thermal_58">Thermal 58mm</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 pt-4">
                            <button type="button" wire:click="removePrinter({{ $printerIndex }})" class="h-8 w-8 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors"><i class="fas fa-trash-can text-xs"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 font-medium">No printers registered yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Bill Profiles List & Interactive Preview -->
    <div class="space-y-6 text-xs font-semibold">
        @forelse($billingProfiles as $index => $profile)
            @php
                $isInvoiceProfile = in_array($profile['bill_type'], ['invoice_pdf', 'any'], true);
                $preview = $isInvoiceProfile ? ($billingPreviewDocuments['invoice'] ?? []) : ($billingPreviewDocuments['receipt'] ?? []);
                $currency = $billingPreviewCompany['currency_symbol'] ?? 'Rs';
            @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Profile #{{ $index + 1 }}</span>
                        <h4 class="text-base font-bold text-slate-900 mt-0.5">{{ $profile['name'] }}</h4>
                    </div>
                    <button type="button" wire:click="removeBillingProfile({{ $index }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition-colors">Delete Profile</button>
                </div>

                <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
                    <!-- Config Form -->
                    <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Profile Name</label>
                                <input type="text" wire:model="billing_profiles.{{ $index }}.name" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Internal ID</label>
                                <input type="text" wire:model="billing_profiles.{{ $index }}.id" class="w-full rounded-lg border-slate-200 px-3 py-2 font-mono font-semibold text-slate-900 focus:ring-0">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Classification</label>
                                <select wire:model="billing_profiles.{{ $index }}.bill_type" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                                    <option value="invoice_pdf">Invoice PDF</option>
                                    <option value="pos_receipt">POS Receipt</option>
                                    <option value="any">Universal Bill</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Output Mode</label>
                                <select wire:model="billing_profiles.{{ $index }}.output_mode" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                                    <option value="pdf">Direct PDF</option>
                                    <option value="browser_print">Browser UI Print</option>
                                    <option value="either">Hybrid Either</option>
                                    <option value="raw_printer">Raw ESC/POS Printer</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Paper Target</label>
                                <select wire:model="billing_profiles.{{ $index }}.paper_size" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                                    <option value="a4">Standard A4</option>
                                    <option value="letter">Letter</option>
                                    <option value="thermal_80">Thermal 80mm</option>
                                    <option value="thermal_58">Thermal 58mm</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Printer Alias</label>
                                <input list="printer-aliases" type="text" wire:model="billing_profiles.{{ $index }}.printer_match" placeholder="Auto-detect..." class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block font-bold text-slate-700">Data Visibility</label>
                            <div class="grid gap-2 sm:grid-cols-3">
                                @foreach([
                                    'auto_print' => 'Auto-print Engine',
                                    'show_company_phone' => 'Brand Phone',
                                    'show_tax_id' => 'Commercial Tax ID',
                                    'show_customer_address' => 'Customer Geo',
                                    'show_customer_email' => 'Customer Email',
                                    'show_customer_phone' => 'Customer Phone',
                                    'show_payment_method' => 'Payment Method',
                                    'show_notes' => 'Append Notes',
                                    'show_terms' => 'Legal Terms',
                                ] as $key => $label)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 p-2 hover:bg-white transition-colors">
                                        <input type="checkbox" wire:model="billing_profiles.{{ $index }}.{{ $key }}" class="h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-0">
                                        <span class="text-xs font-semibold text-slate-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Highlighted Distinct Accent Card for Live Preview -->
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50/30 p-5 space-y-3 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700">Preview</span>
                            <span class="rounded-md bg-white border border-indigo-200 px-2 py-0.5 text-[10px] font-bold text-indigo-700 uppercase">{{ $profile['paper_size'] }}</span>
                        </div>

                        <div class="rounded-lg bg-white shadow-xs p-5 min-h-[260px] text-xs text-slate-700 space-y-3 border border-slate-200">
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $billingPreviewCompany['display_name'] }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $billingPreviewCompany['email'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-slate-900">{{ $preview['number'] ?? 'INV-0000' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $preview['date'] ?? now()->format('Y-m-d') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Customer</p>
                                    <p class="font-bold text-slate-900">{{ $preview['customer_name'] ?? 'Walk-in Customer' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Payment</p>
                                    <p class="font-bold text-slate-900">{{ $preview['payment_method'] ?? 'Cash' }}</p>
                                </div>
                            </div>

                            <div class="space-y-1 pt-2">
                                @foreach($preview['items'] ?? [] as $item)
                                    <div class="flex justify-between items-center py-1 border-b border-slate-50 text-xs">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                                            <p class="text-[10px] text-slate-400">Qty {{ $item['quantity'] }}</p>
                                        </div>
                                        <p class="font-bold text-slate-900">{{ $currency }} {{ number_format($item['total'], 2) }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-2 flex justify-between items-center text-xs font-bold text-slate-900 border-t border-slate-100">
                                <span>Total</span>
                                <span>{{ $currency }} {{ number_format($preview['total'] ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 font-medium">No billing profiles configured.</div>
        @endforelse
    </div>
</div>

<datalist id="printer-aliases">
    @foreach($printerCatalog as $printer)
        <option value="{{ $printer['alias'] }}">{{ $printer['queue_name'] ?: $printer['notes'] }}</option>
    @endforeach
</datalist>
