@props([
    'billingProfiles' => [],
    'billingDefaultProfiles' => [],
    'billingPreviewCompany' => [],
    'billingPreviewDocuments' => [],
])

<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Bill customization and printer routing</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control which printer profile should handle invoice PDFs and POS receipts, then tune paper size, footer text, and visibility rules per bill type.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="addBillingProfile('invoice_pdf')" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <i class="fas fa-file-pdf mr-2"></i>Add Invoice Profile
                </button>
                <button type="button" wire:click="addBillingProfile('pos_receipt')" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <i class="fas fa-receipt mr-2"></i>Add Receipt Profile
                </button>
                <button type="button" wire:click="resetBillingProfiles" class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                    <i class="fas fa-rotate-left mr-2"></i>Reset Defaults
                </button>
            </div>
        </div>
    </x-slot:header>

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
            <h4 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Default Routing</h4>
            <div class="mt-4 grid gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Invoice PDF profile</label>
                    <select wire:model="billing_default_profiles.invoice_pdf" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @foreach($billingProfiles as $profile)
                            @if(in_array($profile['bill_type'], ['invoice_pdf', 'any'], true))
                                <option value="{{ $profile['id'] }}">{{ $profile['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">POS receipt profile</label>
                    <select wire:model="billing_default_profiles.pos_receipt" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @foreach($billingProfiles as $profile)
                            @if(in_array($profile['bill_type'], ['pos_receipt', 'any'], true))
                                <option value="{{ $profile['id'] }}">{{ $profile['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 text-sm text-sky-900 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-100">
            <h4 class="font-semibold uppercase tracking-[0.18em]">Recognition Notes</h4>
            <p class="mt-3 leading-6 text-sky-800 dark:text-sky-100/85">
                Device recognition is automatic in the browser using screen and pointer capabilities. Printer recognition works through operator-selected printer aliases saved on the POS browser, because normal web browsers do not expose installed printer names directly.
            </p>
        </div>
    </div>

    <div class="mt-6 space-y-5">
        @forelse($billingProfiles as $index => $profile)
            @php
                $isInvoiceProfile = in_array($profile['bill_type'], ['invoice_pdf', 'any'], true);
                $preview = $isInvoiceProfile ? ($billingPreviewDocuments['invoice'] ?? []) : ($billingPreviewDocuments['receipt'] ?? []);
                $currency = $billingPreviewCompany['currency_symbol'] ?? 'Rs';
            @endphp
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Profile {{ $index + 1 }}</p>
                        <div class="mt-3 grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Profile name</label>
                                <input type="text" wire:model="billing_profiles.{{ $index }}.name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Internal ID</label>
                                <input type="text" wire:model="billing_profiles.{{ $index }}.id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm font-mono shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <label class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                            <input type="checkbox" wire:model="billing_profiles.{{ $index }}.enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                            Enabled
                        </label>
                        <button type="button" wire:click="removeBillingProfile({{ $index }})" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                            Remove
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Bill type</label>
                        <select wire:model="billing_profiles.{{ $index }}.bill_type" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="invoice_pdf">Invoice PDF</option>
                            <option value="pos_receipt">POS Receipt</option>
                            <option value="any">Any bill</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Output mode</label>
                        <select wire:model="billing_profiles.{{ $index }}.output_mode" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="pdf">PDF</option>
                            <option value="browser_print">Browser Print</option>
                            <option value="either">Either</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Paper size</label>
                        <select wire:model="billing_profiles.{{ $index }}.paper_size" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="a4">A4</option>
                            <option value="letter">Letter</option>
                            <option value="thermal_80">Thermal 80mm</option>
                            <option value="thermal_58">Thermal 58mm</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Orientation</label>
                        <select wire:model="billing_profiles.{{ $index }}.orientation" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Device match</label>
                        <select wire:model="billing_profiles.{{ $index }}.device_match" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="any">Any device</option>
                            <option value="desktop">Desktop</option>
                            <option value="tablet">Tablet</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Input mode match</label>
                        <select wire:model="billing_profiles.{{ $index }}.input_match" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <option value="any">Any input</option>
                            <option value="keyboard_scanner">Keyboard / Scanner</option>
                            <option value="touch">Touch</option>
                            <option value="manual">Manual keyboard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Printer alias match</label>
                        <input type="text" wire:model="billing_profiles.{{ $index }}.printer_match" placeholder="Counter Thermal" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    </div>
                    <div class="grid gap-4 xl:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Copies</label>
                            <input type="number" min="1" max="5" wire:model="billing_profiles.{{ $index }}.copies" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Font scale</label>
                            <input type="number" step="0.01" min="0.7" max="1.4" wire:model="billing_profiles.{{ $index }}.font_scale" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Header note</label>
                        <input type="text" wire:model="billing_profiles.{{ $index }}.header_note" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Footer note</label>
                        <input type="text" wire:model="billing_profiles.{{ $index }}.footer_note" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        'auto_print' => 'Auto-print when matched',
                        'show_company_phone' => 'Show company phone',
                        'show_tax_id' => 'Show tax ID',
                        'show_customer_address' => 'Show customer address',
                        'show_customer_email' => 'Show customer email',
                        'show_customer_phone' => 'Show customer phone',
                        'show_payment_method' => 'Show payment method',
                        'show_notes' => 'Show notes and footer notes',
                        'show_terms' => 'Show terms and conditions',
                    ] as $key => $label)
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                            <input type="checkbox" wire:model="billing_profiles.{{ $index }}.{{ $key }}" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                    <div class="rounded-[1.5rem] border border-violet-100 bg-violet-50/70 p-5 dark:border-violet-500/15 dark:bg-violet-500/5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-500">Live Preview</p>
                                <h4 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{{ $isInvoiceProfile ? 'Invoice PDF' : 'POS Receipt' }}</h4>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Changes here update the preview instantly before you save.</p>
                            </div>
                            <div class="rounded-full border border-violet-200 bg-white px-3 py-1.5 text-xs font-semibold text-violet-700 dark:border-violet-400/20 dark:bg-slate-950 dark:text-violet-200">
                                {{ strtoupper(str_replace('_', ' ', $profile['paper_size'] ?? 'a4')) }}
                            </div>
                        </div>

                        @if($isInvoiceProfile)
                            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
                                <div class="relative p-5">
                                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center text-5xl font-black tracking-[0.35em] {{ ($preview['status'] ?? 'unpaid') === 'paid' ? 'text-emerald-100 dark:text-emerald-500/10' : 'text-amber-100 dark:text-amber-500/10' }} -rotate-[24deg]">
                                        {{ strtoupper(($preview['status'] ?? 'unpaid') === 'paid' ? 'PAID' : 'UNPAID') }}
                                    </div>
                                    <div class="relative">
                                        <div class="border-b border-slate-200 pb-4 dark:border-slate-800">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <h5 class="text-2xl font-black text-slate-900 dark:text-white">{{ $billingPreviewCompany['display_name'] ?? 'Display Lanka' }}</h5>
                                                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $billingPreviewCompany['address'] ?? 'Sri Lanka' }}</p>
                                                    @if(($profile['show_company_phone'] ?? true) && filled($billingPreviewCompany['phone'] ?? null))
                                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Phone: {{ $billingPreviewCompany['phone'] }}</p>
                                                    @endif
                                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Email: {{ $billingPreviewCompany['email'] ?? 'support@example.com' }}</p>
                                                    @if(($profile['show_tax_id'] ?? true) && filled($billingPreviewCompany['tax_id'] ?? null))
                                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Business / Tax ID: {{ $billingPreviewCompany['tax_id'] }}</p>
                                                    @endif
                                                    @if(filled($profile['header_note'] ?? null))
                                                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $profile['header_note'] }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Invoice</p>
                                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $preview['number'] ?? 'INV-0001' }}</p>
                                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $preview['date'] ?? now()->format('M d, Y') }}</p>
                                                    <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ ($preview['status'] ?? 'unpaid') === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200' }}">
                                                        {{ ucfirst($preview['status'] ?? 'paid') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 border-b border-slate-200 py-4 text-sm dark:border-slate-800 md:grid-cols-2">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Bill To</p>
                                                <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $preview['customer_name'] ?? 'Customer Name' }}</p>
                                                @if(($profile['show_customer_email'] ?? true) && filled($preview['customer_email'] ?? null))
                                                    <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $preview['customer_email'] }}</p>
                                                @endif
                                                @if(($profile['show_customer_phone'] ?? true) && filled($preview['customer_phone'] ?? null))
                                                    <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $preview['customer_phone'] }}</p>
                                                @endif
                                                @if(($profile['show_customer_address'] ?? true) && filled($preview['customer_address'] ?? null))
                                                    <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $preview['customer_address'] }}</p>
                                                @endif
                                            </div>
                                            <div class="md:text-right">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Payment</p>
                                                @if(($profile['show_payment_method'] ?? true) && filled($preview['payment_method'] ?? null))
                                                    <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $preview['payment_method'] }}</p>
                                                @endif
                                                <p class="mt-1 text-slate-500 dark:text-slate-400">Due: {{ $preview['due_date'] ?? now()->addDay()->format('M d, Y') }}</p>
                                            </div>
                                        </div>

                                        <div class="overflow-hidden py-4">
                                            <div class="grid grid-cols-[minmax(0,1.6fr)_0.7fr_0.8fr_0.9fr] gap-3 border-b border-slate-200 pb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400 dark:border-slate-800">
                                                <span>Item</span>
                                                <span class="text-right">Qty</span>
                                                <span class="text-right">Price</span>
                                                <span class="text-right">Total</span>
                                            </div>
                                            <div class="space-y-3 pt-3">
                                                @foreach(($preview['items'] ?? []) as $item)
                                                    <div class="grid grid-cols-[minmax(0,1.6fr)_0.7fr_0.8fr_0.9fr] gap-3 text-sm">
                                                        <div class="min-w-0">
                                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</p>
                                                            @if(filled($item['description'] ?? null))
                                                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $item['description'] }}</p>
                                                            @endif
                                                        </div>
                                                        <span class="text-right text-slate-500 dark:text-slate-400">{{ $item['quantity'] }}</span>
                                                        <span class="text-right text-slate-500 dark:text-slate-400">{{ $currency }} {{ number_format((float) $item['price'], 2) }}</span>
                                                        <span class="text-right font-semibold text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) $item['total'], 2) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="grid gap-3 border-t border-slate-200 pt-4 text-sm dark:border-slate-800 sm:grid-cols-2">
                                            <div>
                                                @if(($profile['show_notes'] ?? true) && filled($preview['notes'] ?? null))
                                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Notes</p>
                                                    <p class="mt-2 text-slate-500 dark:text-slate-400">{{ $preview['notes'] }}</p>
                                                @endif
                                                @if(($profile['show_terms'] ?? true) && filled($preview['terms'] ?? null))
                                                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Terms</p>
                                                    <p class="mt-2 text-slate-500 dark:text-slate-400">{{ $preview['terms'] }}</p>
                                                @endif
                                                @if(filled($profile['footer_note'] ?? null))
                                                    <p class="mt-4 text-slate-500 dark:text-slate-400">{{ $profile['footer_note'] }}</p>
                                                @endif
                                            </div>
                                            <div class="space-y-2 sm:text-right">
                                                <div class="flex items-center justify-between gap-4 sm:justify-end sm:gap-8">
                                                    <span class="text-slate-500 dark:text-slate-400">Subtotal</span>
                                                    <span class="font-medium text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) ($preview['subtotal'] ?? 0), 2) }}</span>
                                                </div>
                                                <div class="flex items-center justify-between gap-4 sm:justify-end sm:gap-8">
                                                    <span class="text-slate-500 dark:text-slate-400">Tax</span>
                                                    <span class="font-medium text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) ($preview['tax_amount'] ?? 0), 2) }}</span>
                                                </div>
                                                <div class="flex items-center justify-between gap-4 sm:justify-end sm:gap-8">
                                                    <span class="text-slate-500 dark:text-slate-400">Discount</span>
                                                    <span class="font-medium text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) ($preview['discount_amount'] ?? 0), 2) }}</span>
                                                </div>
                                                <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-2 sm:justify-end sm:gap-8 dark:border-slate-800">
                                                    <span class="font-semibold text-slate-900 dark:text-white">Total</span>
                                                    <span class="text-lg font-black text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) ($preview['total'] ?? 0), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[#f8fafc] shadow-sm dark:border-slate-800 dark:bg-slate-950">
                                <div class="mx-auto max-w-[360px] bg-white p-5 text-sm shadow-sm dark:bg-slate-900">
                                    <div class="text-center">
                                        <p class="text-lg font-black text-slate-900 dark:text-white">{{ $billingPreviewCompany['display_name'] ?? 'Display Lanka' }}</p>
                                        @if(($profile['show_company_phone'] ?? true) && filled($billingPreviewCompany['phone'] ?? null))
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $billingPreviewCompany['phone'] }}</p>
                                        @endif
                                        @if(($profile['show_tax_id'] ?? true) && filled($billingPreviewCompany['tax_id'] ?? null))
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tax ID: {{ $billingPreviewCompany['tax_id'] }}</p>
                                        @endif
                                        @if(filled($profile['header_note'] ?? null))
                                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $profile['header_note'] }}</p>
                                        @endif
                                    </div>
                                    <div class="mt-4 border-y border-dashed border-slate-300 py-3 dark:border-slate-700">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-slate-900 dark:text-white">{{ $preview['number'] ?? 'POS-0001' }}</span>
                                            <span class="text-slate-500 dark:text-slate-400">{{ ucfirst($preview['status'] ?? 'paid') }}</span>
                                        </div>
                                        @if(($profile['show_customer_phone'] ?? true) && filled($preview['customer_phone'] ?? null))
                                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $preview['customer_name'] ?? 'Walk-in customer' }} | {{ $preview['customer_phone'] }}</p>
                                        @endif
                                        @if(($profile['show_payment_method'] ?? true) && filled($preview['payment_method'] ?? null))
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Payment: {{ $preview['payment_method'] }}</p>
                                        @endif
                                    </div>
                                    <div class="space-y-2 py-3">
                                        @foreach(($preview['items'] ?? []) as $item)
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400">Qty {{ $item['quantity'] }}</p>
                                                </div>
                                                <p class="whitespace-nowrap font-semibold text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) $item['total'], 2) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="border-t border-dashed border-slate-300 pt-3 dark:border-slate-700">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-900 dark:text-white">Total</span>
                                            <span class="text-base font-black text-slate-900 dark:text-white">{{ $currency }} {{ number_format((float) ($preview['total'] ?? 0), 2) }}</span>
                                        </div>
                                        @if(($profile['show_notes'] ?? true) && filled($preview['notes'] ?? null))
                                            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">{{ $preview['notes'] }}</p>
                                        @endif
                                        @if(filled($profile['footer_note'] ?? null))
                                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $profile['footer_note'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Preview Notes</p>
                        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <p>The preview uses your current unsaved values for paper size, notes, visibility toggles, and tax display.</p>
                            <p>Company contact details come from the current system settings form, so update email, phone, address, and tax ID here before saving.</p>
                            <p>For PDF invoices, logo and watermark styling are applied on the generated file. The live panel here focuses on structure and visibility behavior.</p>
                        </div>

                        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Branding</p>
                            <div class="mt-3 flex items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900">
                                    @if(filled($billingPreviewCompany['logo_url'] ?? null))
                                        <img src="{{ $billingPreviewCompany['logo_url'] }}" alt="Logo preview" class="h-full w-full object-contain p-2">
                                    @else
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">No logo</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $billingPreviewCompany['display_name'] ?? 'Display Lanka' }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PDF downloads use the live site logo and apply the invoice watermark automatically.</p>
                                </div>
                            </div>
                        </div>

                        @if($isInvoiceProfile)
                            <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Invoice Preview Controls</p>
                                <div class="mt-4 grid gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Payment state</label>
                                        <select wire:model.live="billing_preview_invoice_status" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                            <option value="overdue">Overdue</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Customer name</label>
                                        <input type="text" wire:model.live.debounce.250ms="billing_preview_invoice_customer_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Customer email</label>
                                        <input type="text" wire:model.live.debounce.250ms="billing_preview_invoice_customer_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Customer phone</label>
                                        <input type="text" wire:model.live.debounce.250ms="billing_preview_invoice_customer_phone" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Customer address</label>
                                        <textarea wire:model.live.debounce.250ms="billing_preview_invoice_customer_address" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea>
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Item one</label>
                                            <input type="text" wire:model.live.debounce.250ms="billing_preview_invoice_item_one_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Item two</label>
                                            <input type="text" wire:model.live.debounce.250ms="billing_preview_invoice_item_two_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Receipt Preview Controls</p>
                                <div class="mt-4 grid gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Receipt state</label>
                                        <select wire:model.live="billing_preview_receipt_status" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                            <option value="paid">Paid</option>
                                            <option value="pending">Pending</option>
                                            <option value="refunded">Refunded</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Customer name</label>
                                        <input type="text" wire:model.live.debounce.250ms="billing_preview_receipt_customer_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Customer phone</label>
                                        <input type="text" wire:model.live.debounce.250ms="billing_preview_receipt_customer_phone" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Item one</label>
                                            <input type="text" wire:model.live.debounce.250ms="billing_preview_receipt_item_one_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Item two</label>
                                            <input type="text" wire:model.live.debounce.250ms="billing_preview_receipt_item_two_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 px-5 py-12 text-center text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                No bill profiles configured yet. Add an invoice or receipt profile to start routing output.
            </div>
        @endforelse
    </div>
</x-admin.ui.panel>
