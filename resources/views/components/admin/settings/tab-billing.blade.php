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
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 px-5 py-12 text-center text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                No bill profiles configured yet. Add an invoice or receipt profile to start routing output.
            </div>
        @endforelse
    </div>
</x-admin.ui.panel>
