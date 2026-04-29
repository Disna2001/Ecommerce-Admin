<div class="space-y-4" x-data="{
    showPaymentModal: @entangle('showPaymentModal'),
    showSuccessModal: @entangle('showSuccessModal'),
    showStockModal: @entangle('showStockModal'),
    showCustomerCreateModal: @entangle('showCustomerCreateModal'),
    toastOpen: false,
    toastMessage: '',
    toastTone: 'success',
    defaultPrinterHint: @js($receiptProfile['printer_match'] ?? ''),
    printerHint: '',
    inputMode: @entangle('input_mode').live,
    deviceType: 'desktop',
    fullscreenActive: false,
    initPrintRouting() {
        this.printerHint = localStorage.getItem('posPreferredPrinter') || this.defaultPrinterHint;
        this.inputMode = localStorage.getItem('posInputMode') || this.inputMode || 'keyboard_scanner';
        const coarsePointer = window.matchMedia('(pointer: coarse)').matches;
        if (window.innerWidth < 640) {
            this.deviceType = 'mobile';
        } else if (coarsePointer || window.innerWidth < 1024) {
            this.deviceType = 'tablet';
        } else {
            this.deviceType = 'desktop';
        }
    },
    persistPrinterHint() {
        localStorage.setItem('posPreferredPrinter', this.printerHint || '');
    },
    persistInputMode() {
        localStorage.setItem('posInputMode', this.inputMode || 'keyboard_scanner');
    },
    initFullscreen() {
        this.fullscreenActive = !!document.fullscreenElement;
        document.addEventListener('fullscreenchange', () => {
            this.fullscreenActive = !!document.fullscreenElement;
        });
    },
    async toggleFullscreen() {
        if (!document.fullscreenElement) {
            await document.documentElement.requestFullscreen?.();
        } else {
            await document.exitFullscreen?.();
        }
    }
}" x-init="initPrintRouting(); initFullscreen(); $nextTick(() => { if (window.innerWidth >= 768) { $refs.productSearch?.focus(); } })"
    x-on:keydown.window="
        const tag = ($event.target.tagName || '').toUpperCase();
        const editing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(tag);
        if (($event.key === '/' || ($event.ctrlKey && $event.key.toLowerCase() === 'k')) && !editing) {
            $event.preventDefault();
            $refs.productSearch?.focus();
            $refs.productSearch?.select?.();
        }
        if ($event.key === 'F8') {
            $event.preventDefault();
            $wire.openPaymentModal();
        }
    "
    x-on:item-added.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'success'; $nextTick(() => { $refs.productSearch?.focus(); $refs.productSearch?.select?.(); }); setTimeout(() => toastOpen = false, 2600)"
    x-on:show-success.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'success'; setTimeout(() => toastOpen = false, 3200)"
    x-on:show-error.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'error'; setTimeout(() => toastOpen = false, 3200)"
    x-on:show-warning.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'warning'; setTimeout(() => toastOpen = false, 3200)">

    <div x-show="toastOpen" x-transition class="fixed bottom-5 right-5 z-50 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-2xl"
        :class="toastTone === 'success' ? 'bg-emerald-600' : (toastTone === 'error' ? 'bg-rose-600' : 'bg-amber-500')"
        style="display:none;">
        <span x-text="toastMessage"></span>
    </div>

    <section class="pos-shell">
        <div class="pos-hero">
            <div>
                <p class="pos-kicker">Counter Workspace</p>
                <h1 class="pos-title">{{ $siteName }} POS</h1>
                <p class="pos-copy">Compact counter flow for search, cart, settlement, and receipt handling without the usual admin-page clutter.</p>
            </div>

            <div class="pos-actions">
                <div class="pos-chip pos-chip--mono">
                    <span>Invoice</span>
                    <strong>{{ $invoice_number }}</strong>
                </div>
                @if($heldInvoiceId)
                    <div class="pos-chip">
                        <span>Held Sale</span>
                        <strong>Resumed</strong>
                    </div>
                @endif
                <label class="pos-chip pos-chip--interactive">
                    <span>Mode</span>
                    <select x-model="inputMode" @change="persistInputMode()" class="pos-chip__select">
                        <option value="keyboard_scanner">Scanner</option>
                        <option value="touch">Touch</option>
                        <option value="manual">Manual</option>
                    </select>
                </label>
                <div class="pos-chip">
                    <span>/ or Ctrl+K</span>
                    <strong>Focus Search</strong>
                </div>
                <div class="pos-chip">
                    <span>F8</span>
                    <strong>Review Sale</strong>
                </div>
                <a href="{{ route('admin.invoices') }}" class="pos-button pos-button--ghost">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Invoices</span>
                </a>
                <button type="button" @click="toggleFullscreen()" class="pos-button pos-button--ghost">
                    <i class="fas" :class="fullscreenActive ? 'fa-compress' : 'fa-expand'"></i>
                    <span x-text="fullscreenActive ? 'Exit Fullscreen' : 'Fullscreen'"></span>
                </button>
                <button type="button" wire:click="holdSale" class="pos-button pos-button--ghost" @disabled(count($cart) === 0)>
                    <i class="fas fa-pause-circle"></i>
                    <span>Hold Sale</span>
                </button>
                <button type="button" wire:click="clearCart" class="pos-button pos-button--ghost" @disabled(count($cart) === 0)>
                    <i class="fas fa-rotate-left"></i>
                    <span>Reset Sale</span>
                </button>
            </div>
        </div>

        <div class="pos-metrics">
            <div class="pos-metric">
                <span>Cart Lines</span>
                <strong>{{ count($cart) }}</strong>
                <small>Active items in the current sale</small>
            </div>
            <div class="pos-metric pos-metric--indigo">
                <span>Units</span>
                <strong>{{ $this->cartItemsCount }}</strong>
                <small>Total pieces across the cart</small>
            </div>
            <div class="pos-metric pos-metric--emerald">
                <span>Total</span>
                <strong>Rs {{ number_format($cartTotal, 2) }}</strong>
                <small>Current sale value</small>
            </div>
            <div class="pos-metric pos-metric--amber">
                <span>Savings</span>
                <strong>Rs {{ number_format($this->cartSavings, 2) }}</strong>
                <small>Discount impact on this bill</small>
            </div>
            <div class="pos-metric pos-metric--sky">
                <span>Today Sales</span>
                <strong>{{ $this->posSummary['today_sales'] }}</strong>
                <small>Invoices created today</small>
            </div>
            <div class="pos-metric pos-metric--violet">
                <span>Revenue</span>
                <strong>Rs {{ number_format($this->posSummary['today_revenue'], 0) }}</strong>
                <small>Counter revenue today</small>
            </div>
        </div>

        <div class="pos-workspace">
            <section class="pos-panel pos-panel--tall">
                <div class="pos-panel__header">
                    <div>
                        <p class="pos-panel__eyebrow">Product Search</p>
                        <h2 class="pos-panel__title">Scan or search inventory</h2>
                    </div>
                    <button type="button" wire:click="$set('searchTerm', '')" class="pos-mini-link">Clear</button>
                </div>

                <div class="relative">
                    <i class="fas fa-barcode pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="text"
                        x-ref="productSearch"
                        wire:model.live.debounce.250ms="searchTerm"
                        wire:keydown.enter.prevent="selectTopSearchResult"
                        placeholder="Search by name, SKU, barcode, item code, or model..."
                        class="w-full rounded-2xl border-slate-200 bg-white py-3 pl-12 pr-4 text-sm shadow-none focus:ring-0"
                    >
                </div>

                <div class="pos-search-hints">
                    <span class="pos-chip" x-show="inputMode === 'keyboard_scanner'">Exact barcode/SKU auto-add in scanner mode</span>
                    <span class="pos-chip" x-show="inputMode !== 'keyboard_scanner'">Press Enter to add the top matching result</span>
                    <span class="pos-chip">Use stock-in when counter stock arrives</span>
                </div>

                <div class="pos-result-list">
                    @if($showResults && count($searchResults) > 0)
                        @foreach($searchResults as $product)
                            <div class="pos-result-card">
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $product->sku }}
                                                @if($product->item_code)
                                                    · {{ $product->item_code }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="pos-badge">{{ $product->quantity }} in stock</span>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500">
                                        {{ $product->brand->name ?? 'Unbranded' }}
                                        @if($product->model_name)
                                            · {{ $product->model_name }}
                                        @endif
                                    </p>
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <strong class="text-base font-black text-indigo-700">Rs {{ number_format($product->selling_price, 2) }}</strong>
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="openStockIntake({{ $product->id }})" class="pos-icon-button" title="Quick stock in">
                                            <i class="fas fa-boxes-stacked"></i>
                                        </button>
                                        <button type="button" wire:click="selectProduct({{ $product->id }})" class="pos-button pos-button--primary">
                                            <i class="fas fa-plus"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif(strlen($searchTerm) >= 2)
                        <div class="pos-empty-state">
                            <i class="fas fa-magnifying-glass"></i>
                            <p>No matching products found for this search.</p>
                        </div>
                    @else
                        <div class="pos-empty-state">
                            <i class="fas fa-barcode"></i>
                            <p>Start typing at least two characters to load sale-ready stock items.</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="pos-panel pos-panel--tall">
                <div class="pos-panel__header">
                    <div>
                        <p class="pos-panel__eyebrow">Active Cart</p>
                        <h2 class="pos-panel__title">Sale items</h2>
                    </div>
                    @if(count($cart) > 0)
                        <button wire:click="clearCart" class="pos-mini-link text-rose-600">Clear sale</button>
                    @endif
                </div>

                <div class="pos-cart-list">
                    @forelse($cart as $index => $item)
                        <article class="pos-cart-item">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $item['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item['sku'] }}</p>
                                    <p class="mt-2 text-xs {{ $item['stock_quantity'] <= 3 ? 'font-semibold text-amber-600' : 'text-slate-400' }}">
                                        Available stock: {{ $item['stock_quantity'] }}
                                    </p>
                                </div>
                                <button wire:click="removeItem({{ $index }})" class="pos-icon-button pos-icon-button--danger" title="Remove item">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-[92px_1fr_96px] gap-3">
                                <label class="pos-field-group">
                                    <span>Qty</span>
                                    <div class="pos-stepper">
                                        <button type="button" wire:click="decrementQuantity({{ $index }})" class="pos-stepper__button" @disabled($item['quantity'] <= 1)>-</button>
                                        <input type="number" wire:change="updateQuantity({{ $index }}, $event.target.value)" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock_quantity'] }}" class="pos-field pos-stepper__input text-center">
                                        <button type="button" wire:click="incrementQuantity({{ $index }})" class="pos-stepper__button" @disabled($item['quantity'] >= $item['stock_quantity'])>+</button>
                                    </div>
                                </label>
                                <label class="pos-field-group">
                                    <span>Discount %</span>
                                    <input type="number" wire:change="updateDiscount({{ $index }}, $event.target.value)" value="{{ $item['discount'] }}" min="0" max="100" class="pos-field text-right">
                                </label>
                                <div class="pos-line-total">
                                    <span>Total</span>
                                    <strong>Rs {{ number_format($item['total'], 2) }}</strong>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="pos-empty-state">
                            <i class="fas fa-cart-shopping"></i>
                            <p>No items added yet. Search on the left and build the sale here.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="pos-rail">
                <div class="pos-panel">
                    <div class="pos-panel__header">
                        <div>
                            <p class="pos-panel__eyebrow">Customer Desk</p>
                            <h2 class="pos-panel__title">Customer and payment</h2>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="pos-field-group relative">
                            <div class="flex items-center justify-between gap-3">
                                <span>Find customer</span>
                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="openCustomerCreate" class="pos-mini-link">New customer</button>
                                    <button type="button" wire:click="setWalkInCustomer" class="pos-mini-link">Walk-in</button>
                                </div>
                            </div>
                            <input type="text" wire:model.live.debounce.250ms="customerLookup" class="pos-field" placeholder="Search by customer name, phone, or email">

                            @if($showCustomerResults && count($customerResults) > 0)
                                <div class="pos-customer-results">
                                    @foreach($customerResults as $result)
                                        <button type="button" wire:click="selectCustomerProfile('{{ $result['type'] }}', {{ $result['id'] }})" class="pos-customer-result">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-slate-900">{{ $result['name'] }}</p>
                                                <p class="mt-1 truncate text-xs text-slate-500">
                                                    {{ $result['email'] ?: ($result['phone'] ?: 'No contact saved') }}
                                                </p>
                                            </div>
                                            <span class="pos-badge">{{ $result['source'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <label class="pos-field-group">
                            <span>Customer name</span>
                            <input type="text" wire:model="customer_name" class="pos-field">
                            @error('customer_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </label>

                        <label class="pos-field-group">
                            <span>Customer email @if($sendInvoiceEmail)<span class="text-rose-500">*</span>@endif</span>
                            <input type="email" wire:model="customer_email" class="pos-field" placeholder="customer@example.com">
                            @error('customer_email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </label>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="pos-field-group">
                                <span>Phone</span>
                                <input type="text" wire:model="customer_phone" class="pos-field">
                            </label>
                            <label class="pos-field-group">
                                <span>Payment method</span>
                                <select wire:model="payment_method" class="pos-field">
                                    @foreach($paymentMethods as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <label class="pos-field-group">
                            <span>Address</span>
                            <textarea wire:model="customer_address" rows="2" class="pos-field resize-none"></textarea>
                        </label>

                        <label class="pos-field-group">
                            <span>Sale notes</span>
                            <textarea wire:model="notes" rows="2" class="pos-field resize-none" placeholder="Counter notes, collection details, special requests..."></textarea>
                        </label>

                        <label class="flex items-start gap-3 rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                            <input type="checkbox" wire:model="sendInvoiceEmail" class="mt-1 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block font-semibold">Send invoice email automatically</span>
                                <span class="mt-1 block text-xs text-indigo-600/80">When enabled, the customer receives the invoice immediately after settlement.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="pos-panel">
                    <div class="pos-panel__header">
                        <div>
                            <p class="pos-panel__eyebrow">Settlement</p>
                            <h2 class="pos-panel__title">Totals and tender</h2>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="pos-summary-row"><span>Subtotal</span><strong>Rs {{ number_format($cartSubtotal, 2) }}</strong></div>
                        <div class="pos-summary-row"><span>Tax</span><strong>Rs {{ number_format($cartTax, 2) }}</strong></div>
                        <div class="pos-summary-row"><span>Discount impact</span><strong>Rs {{ number_format($this->cartSavings, 2) }}</strong></div>
                        <div class="pos-summary-row pos-summary-row--total"><span>Total</span><strong>Rs {{ number_format($cartTotal, 2) }}</strong></div>

                        <label class="pos-field-group">
                            <span>Amount paid</span>
                            <input type="number" wire:model.live="amount_paid" min="0" step="0.01" class="pos-field text-right">
                            @error('amount_paid') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </label>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quick Tender</p>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <button wire:click="applyQuickTender('exact')" type="button" class="pos-quick-button">Exact</button>
                                <button wire:click="applyQuickTender('plus_500')" type="button" class="pos-quick-button">+ 500</button>
                                <button wire:click="applyQuickTender('plus_1000')" type="button" class="pos-quick-button">+ 1000</button>
                                <button wire:click="applyQuickTender('plus_5000')" type="button" class="pos-quick-button">+ 5000</button>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600">Change Due</p>
                            <p class="mt-2 text-3xl font-black text-emerald-700">Rs {{ number_format($change_due, 2) }}</p>
                            <p class="mt-2 text-sm text-emerald-700">
                                @if($amount_paid >= $cartTotal)
                                    Full payment captured. Receipt and invoice are ready.
                                @elseif($amount_paid >= ($cartTotal * 0.5))
                                    Partial payment accepted. Outstanding balance will remain on the invoice.
                                @else
                                    Enter at least 50% of the total to complete this sale.
                                @endif
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="openPaymentModal"
                            class="pos-button pos-button--primary w-full justify-center"
                            @disabled(count($cart) === 0)
                        >
                            <i class="fas fa-check-circle"></i>
                            <span>Review and Complete Sale</span>
                        </button>

                        <p class="text-center text-[11px] font-medium uppercase tracking-[0.16em] text-slate-400">
                            Hotkey: F8 opens the settlement review
                        </p>
                    </div>
                </div>

                <div class="pos-panel">
                    <div class="pos-panel__header">
                        <div>
                            <p class="pos-panel__eyebrow">Held Sales</p>
                            <h2 class="pos-panel__title">Parked counter carts</h2>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($held_sales as $heldSale)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $heldSale->invoice_number }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500">{{ $heldSale->customer_name ?: 'Walk-in customer' }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $heldSale->items->count() }} lines · Rs {{ number_format($heldSale->total, 2) }}</p>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                        Held
                                    </span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <button type="button" wire:click="resumeHeldSale({{ $heldSale->id }})" class="pos-button pos-button--primary pos-button--tiny">
                                        <i class="fas fa-play"></i>
                                        <span>Resume</span>
                                    </button>
                                    <button type="button" wire:click="discardHeldSale({{ $heldSale->id }})" class="pos-button pos-button--danger pos-button--tiny">
                                        <i class="fas fa-trash"></i>
                                        <span>Discard</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="pos-empty-state pos-empty-state--compact">
                                <i class="fas fa-layer-group"></i>
                                <p>No held sales yet. Park busy counter sales here and resume them later.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pos-panel">
                    <div class="pos-panel__header">
                        <div>
                            <p class="pos-panel__eyebrow">Recent Invoices</p>
                            <h2 class="pos-panel__title">Latest counter activity</h2>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($recent_invoices as $invoice)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500">{{ $invoice->customer_name }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ optional($invoice->created_at)->diffForHumans() }}</span>
                                    <strong class="text-slate-900">Rs {{ number_format($invoice->total, 2) }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </section>

    <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
            <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Final Check</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900">Review settlement before closing</h3>
                        </div>
                        <button @click="showPaymentModal = false" class="pos-icon-button" type="button">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px]">
                    <div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm"><span class="text-slate-500">Customer</span><strong class="text-slate-900">{{ $customer_name ?: 'Walk-in customer' }}</strong></div>
                                <div class="flex items-center justify-between text-sm"><span class="text-slate-500">Email</span><strong class="text-slate-900">{{ $customer_email ?: '-' }}</strong></div>
                                <div class="flex items-center justify-between text-sm"><span class="text-slate-500">Method</span><strong class="text-slate-900">{{ $paymentMethods[$payment_method] ?? $payment_method }}</strong></div>
                                <div class="flex items-center justify-between text-sm"><span class="text-slate-500">Cart lines</span><strong class="text-slate-900">{{ count($cart) }}</strong></div>
                            </div>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200">
                            <table class="min-w-full">
                                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Item</th>
                                        <th class="px-4 py-3">Qty</th>
                                        <th class="px-4 py-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($cart as $item)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $item['name'] }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $item['quantity'] }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">Rs {{ number_format($item['total'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600">Settlement</p>
                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between text-sm"><span class="text-emerald-700">Subtotal</span><strong class="text-emerald-700">Rs {{ number_format($cartSubtotal, 2) }}</strong></div>
                            <div class="flex items-center justify-between text-sm"><span class="text-emerald-700">Total</span><strong class="text-emerald-700">Rs {{ number_format($cartTotal, 2) }}</strong></div>
                            <div class="flex items-center justify-between text-sm"><span class="text-emerald-700">Paid</span><strong class="text-emerald-700">Rs {{ number_format($amount_paid, 2) }}</strong></div>
                            <div class="flex items-center justify-between text-sm"><span class="text-emerald-700">Change</span><strong class="text-emerald-700">Rs {{ number_format($change_due, 2) }}</strong></div>
                        </div>

                        <div class="mt-5 rounded-2xl bg-white/80 p-4 text-sm text-emerald-800">
                            @if($amount_paid >= $cartTotal)
                                Full payment captured. This sale can be completed now.
                            @elseif($amount_paid >= ($cartTotal * 0.5))
                                Partial payment is allowed. The invoice will carry the remaining balance.
                            @else
                                Enter at least 50% of the sale total to proceed.
                            @endif
                        </div>

                        <div class="mt-5 flex gap-3">
                            <button @click="showPaymentModal = false" class="pos-button pos-button--ghost w-full justify-center" type="button">Cancel</button>
                            <button wire:click="processPayment" class="pos-button pos-button--primary w-full justify-center" @disabled($amount_paid < ($cartTotal * 0.5) || count($cart) === 0)>
                                Complete Sale
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showStockModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
            <div class="relative z-10 w-full max-w-lg overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quick Stock In</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $quickStockName }}</h3>
                            <p class="mt-1 text-sm text-slate-500">Current stock: {{ $quickStockCurrentQuantity }}</p>
                        </div>
                        <button type="button" wire:click="closeStockIntake" class="pos-icon-button">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-4 p-6">
                    <label class="pos-field-group">
                        <span>Add quantity</span>
                        <input type="number" wire:model="quickStockAddQuantity" min="1" class="pos-field">
                        @error('quickStockAddQuantity') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </label>

                    <label class="pos-field-group">
                        <span>Notes</span>
                        <textarea wire:model="quickStockNotes" rows="3" class="pos-field resize-none"></textarea>
                        @error('quickStockNotes') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </label>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="$set('quickStockAddQuantity', 1)" class="pos-quick-button">+1</button>
                        <button type="button" wire:click="$set('quickStockAddQuantity', 5)" class="pos-quick-button">+5</button>
                        <button type="button" wire:click="$set('quickStockAddQuantity', 10)" class="pos-quick-button">+10</button>
                    </div>
                </div>

                <div class="flex gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button type="button" wire:click="closeStockIntake" class="pos-button pos-button--ghost w-full justify-center">Cancel</button>
                    <button type="button" wire:click="receiveStock" class="pos-button pos-button--primary w-full justify-center">
                        <i class="fas fa-boxes-stacked"></i>
                        <span>Confirm Stock In</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showCustomerCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
            <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Quick Customer</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900">Create customer at the counter</h3>
                            <p class="mt-1 text-sm text-slate-500">Save the buyer once and load the profile into the current sale immediately.</p>
                        </div>
                        <button type="button" wire:click="closeCustomerCreate" class="pos-icon-button">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-4 p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="pos-field-group sm:col-span-2">
                            <span>Customer name</span>
                            <input type="text" wire:model="quickCustomerName" class="pos-field">
                            @error('quickCustomerName') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </label>

                        <label class="pos-field-group">
                            <span>Email</span>
                            <input type="email" wire:model="quickCustomerEmail" class="pos-field" placeholder="customer@example.com">
                            @error('quickCustomerEmail') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </label>

                        <label class="pos-field-group">
                            <span>Phone</span>
                            <input type="text" wire:model="quickCustomerPhone" class="pos-field">
                            @error('quickCustomerPhone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="pos-field-group">
                        <span>Address</span>
                        <textarea wire:model="quickCustomerAddress" rows="3" class="pos-field resize-none"></textarea>
                        @error('quickCustomerAddress') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </label>

                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                        This creates a regular customer profile for future checkout lookup. A secure password is generated automatically for the account record.
                    </div>
                </div>

                <div class="flex gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button type="button" wire:click="closeCustomerCreate" class="pos-button pos-button--ghost w-full justify-center">Cancel</button>
                    <button type="button" wire:click="createQuickCustomer" class="pos-button pos-button--primary w-full justify-center">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Customer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
            <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="px-6 py-8 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <h3 class="mt-5 text-2xl font-bold text-slate-900">Sale Completed</h3>
                    @if($createdInvoice)
                        <p class="mt-2 text-sm text-slate-500">Invoice <span class="font-mono font-bold text-slate-900">{{ $createdInvoice->invoice_number }}</span> was created successfully.</p>

                        <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 text-left">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Customer</span>
                                <span class="font-semibold text-slate-900">{{ $createdInvoice->customer_name }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Total</span>
                                <span class="font-semibold text-slate-900">Rs {{ number_format($createdInvoice->total, 2) }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Payment</span>
                                <span class="font-semibold text-slate-900">{{ $paymentMethods[$createdInvoice->payment_method] ?? $createdInvoice->payment_method }}</span>
                            </div>
                            @if($createdInvoice->customer_email)
                                <div class="mt-4 rounded-2xl bg-white px-4 py-3 text-sm">
                                    @if($createdInvoice->email_sent_at)
                                        <span class="font-semibold text-emerald-700">Invoice email sent to {{ $createdInvoice->customer_email }}</span>
                                    @else
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-amber-600">Invoice email not sent yet.</span>
                                            <button wire:click="resendInvoiceEmail" class="rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                                Resend Email
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <button wire:click="printReceipt" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            <i class="fas fa-print"></i>
                            <span>Print Receipt</span>
                        </button>
                        <button wire:click="newSale" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            <i class="fas fa-plus"></i>
                            <span>Start New Sale</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php($receiptInvoice = $createdInvoice?->loadMissing('items'))
    @if($receiptInvoice)
        <div
            id="pos-receipt-print-zone"
            class="hidden"
            data-profiles='@json($billingProfiles)'
            data-default-profile='@json($receiptProfile)'
            data-default-printer='@json($receiptProfile['printer_match'] ?? '')'
        >
            <div id="pos-receipt-sheet" class="receipt-paper">
                <div class="receipt-header">
                    <p class="receipt-site">{{ $company['name'] }}</p>
                    <p data-receipt-company-phone>{{ $company['phone'] }}</p>
                    <p>{{ $company['email'] }}</p>
                    <p>{{ $company['address'] }}</p>
                    <p data-receipt-tax-id>Tax ID: {{ $company['tax_id'] }}</p>
                    <p data-receipt-header-note class="receipt-muted"></p>
                </div>

                <div class="receipt-section">
                    <div class="receipt-row"><span>Invoice</span><strong>{{ $receiptInvoice->invoice_number }}</strong></div>
                    <div class="receipt-row"><span>Date</span><strong>{{ $receiptInvoice->invoice_date?->format('Y-m-d H:i') }}</strong></div>
                    <div class="receipt-row"><span>Customer</span><strong>{{ $receiptInvoice->customer_name }}</strong></div>
                    <div class="receipt-row" data-receipt-customer-email><span>Email</span><strong>{{ $receiptInvoice->customer_email ?: '-' }}</strong></div>
                    <div class="receipt-row" data-receipt-customer-phone><span>Phone</span><strong>{{ $receiptInvoice->customer_phone ?: '-' }}</strong></div>
                    <div class="receipt-row" data-receipt-customer-address><span>Address</span><strong>{{ $receiptInvoice->customer_address ?: '-' }}</strong></div>
                    <div class="receipt-row" data-receipt-payment-method><span>Payment</span><strong>{{ $paymentMethods[$receiptInvoice->payment_method] ?? $receiptInvoice->payment_method }}</strong></div>
                </div>

                <div class="receipt-section">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="number">Qty</th>
                                <th class="number">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receiptInvoice->items as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td class="number">{{ $item->quantity }}</td>
                                    <td class="number">{{ $company['currency_symbol'] }} {{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="receipt-section">
                    <div class="receipt-row"><span>Subtotal</span><strong>{{ $company['currency_symbol'] }} {{ number_format($receiptInvoice->subtotal, 2) }}</strong></div>
                    <div class="receipt-row"><span>Total</span><strong>{{ $company['currency_symbol'] }} {{ number_format($receiptInvoice->total, 2) }}</strong></div>
                    <div class="receipt-row"><span>Paid</span><strong>{{ $company['currency_symbol'] }} {{ number_format($receiptInvoice->amount_paid, 2) }}</strong></div>
                    <div class="receipt-row"><span>Balance</span><strong>{{ $company['currency_symbol'] }} {{ number_format($receiptInvoice->balance_due, 2) }}</strong></div>
                </div>

                <div class="receipt-section" data-receipt-notes>
                    <p class="receipt-label">Notes</p>
                    <p>{{ $receiptInvoice->notes ?: 'No additional notes.' }}</p>
                </div>

                <div class="receipt-section" data-receipt-terms>
                    <p class="receipt-label">Terms</p>
                    <p>{{ $receiptInvoice->terms_conditions ?: 'Thank you for shopping with us.' }}</p>
                </div>

                <div class="receipt-footer">
                    <p data-receipt-footer-note></p>
                    <p class="receipt-muted">Printed from {{ $siteName }} POS</p>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .pos-shell {
            display: grid;
            gap: 1rem;
        }

        .pos-hero,
        .pos-panel,
        .pos-metric {
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: rgba(255, 255, 255, 0.84);
            border-radius: 1.4rem;
            box-shadow: 0 10px 28px -24px rgba(15, 23, 42, 0.22);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .dark .pos-hero,
        .dark .pos-panel,
        .dark .pos-metric {
            border-color: rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.72);
        }

        .pos-hero {
            padding: 1rem 1.15rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .pos-kicker {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .pos-title {
            margin-top: 0.25rem;
            font-size: clamp(1.5rem, 2vw, 2rem);
            font-weight: 800;
            line-height: 1.05;
            color: #0f172a;
        }

        .dark .pos-title {
            color: #f8fafc;
        }

        .pos-copy {
            margin-top: 0.4rem;
            max-width: 44rem;
            font-size: 0.92rem;
            line-height: 1.6;
            color: #64748b;
        }

        .dark .pos-copy {
            color: #cbd5e1;
        }

        .pos-actions,
        .pos-search-hints {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .pos-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.52rem 0.8rem;
            border-radius: 999px;
            background: rgba(79, 70, 229, 0.08);
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .dark .pos-chip {
            background: rgba(99, 102, 241, 0.16);
            color: #e2e8f0;
        }

        .pos-chip--interactive {
            padding-right: 0.45rem;
        }

        .pos-chip--mono strong {
            font-family: "Courier New", monospace;
        }

        .pos-chip__select {
            border: none;
            background: transparent;
            color: inherit;
            font-size: 0.78rem;
            font-weight: 800;
            padding-right: 0.25rem;
            outline: none;
        }

        .pos-chip__select option {
            color: #0f172a;
        }

        .pos-button,
        .pos-icon-button,
        .pos-quick-button,
        .pos-mini-link {
            transition: all 0.2s ease;
        }

        .pos-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            border-radius: 999px;
            padding: 0.78rem 1rem;
            font-size: 0.86rem;
            font-weight: 700;
            text-decoration: none;
        }

        .pos-button--tiny {
            padding: 0.55rem 0.85rem;
            font-size: 0.76rem;
        }

        .pos-button--primary {
            background: #0f172a;
            color: #fff;
        }

        .pos-button--primary:hover {
            background: #1e293b;
        }

        .pos-button--ghost {
            border: 1px solid #cbd5e1;
            background: rgba(255, 255, 255, 0.85);
            color: #334155;
        }

        .pos-button--danger {
            border: 1px solid #fecdd3;
            background: #fff1f2;
            color: #be123c;
        }

        .dark .pos-button--ghost {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.7);
            color: #e2e8f0;
        }

        .dark .pos-button--danger {
            border-color: rgba(190, 24, 93, 0.4);
            background: rgba(76, 5, 25, 0.65);
            color: #fecdd3;
        }

        .pos-icon-button {
            width: 2.3rem;
            height: 2.3rem;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: rgba(255, 255, 255, 0.9);
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .dark .pos-icon-button {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.7);
            color: #e2e8f0;
        }

        .pos-icon-button:hover,
        .pos-quick-button:hover,
        .pos-mini-link:hover {
            transform: translateY(-1px);
        }

        .pos-icon-button--danger {
            color: #e11d48;
            border-color: #fecdd3;
            background: #fff1f2;
        }

        .pos-mini-link {
            font-size: 0.78rem;
            font-weight: 700;
            color: #6366f1;
            background: none;
            border: none;
            cursor: pointer;
        }

        .pos-stepper {
            display: grid;
            grid-template-columns: 2rem minmax(0, 1fr) 2rem;
            gap: 0.35rem;
            align-items: center;
        }

        .pos-stepper__button {
            height: 2.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            color: #334155;
            font-size: 1rem;
            font-weight: 800;
        }

        .dark .pos-stepper__button {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.78);
            color: #e2e8f0;
        }

        .pos-stepper__button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .pos-stepper__input {
            min-width: 0;
        }

        .pos-metrics {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pos-metric {
            padding: 0.9rem 1rem;
        }

        .pos-metric span {
            display: block;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .pos-metric strong {
            display: block;
            margin-top: 0.45rem;
            font-size: 1.4rem;
            line-height: 1.1;
            color: #0f172a;
        }

        .dark .pos-metric strong {
            color: #f8fafc;
        }

        .pos-metric small {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.78rem;
            color: #64748b;
        }

        .dark .pos-metric small {
            color: #cbd5e1;
        }

        .pos-metric--indigo { border-color: #c7d2fe; background: rgba(238, 242, 255, 0.92); }
        .pos-metric--emerald { border-color: #a7f3d0; background: rgba(236, 253, 245, 0.94); }
        .pos-metric--amber { border-color: #fde68a; background: rgba(255, 251, 235, 0.94); }
        .pos-metric--sky { border-color: #bae6fd; background: rgba(240, 249, 255, 0.94); }
        .pos-metric--violet { border-color: #ddd6fe; background: rgba(245, 243, 255, 0.94); }

        .dark .pos-metric--indigo,
        .dark .pos-metric--emerald,
        .dark .pos-metric--amber,
        .dark .pos-metric--sky,
        .dark .pos-metric--violet {
            background: rgba(15, 23, 42, 0.78);
        }

        .pos-workspace {
            display: grid;
            gap: 1rem;
            align-items: start;
        }

        .pos-panel {
            padding: 1rem;
        }

        .pos-panel--tall {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .pos-panel__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.9rem;
        }

        .pos-panel__eyebrow {
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .pos-panel__title {
            margin-top: 0.2rem;
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .dark .pos-panel__title {
            color: #f8fafc;
        }

        .pos-result-list,
        .pos-cart-list {
            display: grid;
            gap: 0.75rem;
            min-height: 0;
            overflow: auto;
        }

        .pos-result-card,
        .pos-cart-item {
            border: 1px solid #e2e8f0;
            border-radius: 1.1rem;
            background: rgba(248, 250, 252, 0.84);
            padding: 0.85rem;
        }

        .dark .pos-result-card,
        .dark .pos-cart-item {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.68);
        }

        .pos-badge {
            flex-shrink: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.35rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 800;
            color: #475569;
        }

        .dark .pos-badge {
            background: rgba(15, 23, 42, 0.8);
            color: #e2e8f0;
        }

        .pos-empty-state {
            display: grid;
            place-items: center;
            gap: 0.65rem;
            min-height: 11rem;
            border: 1px dashed #cbd5e1;
            border-radius: 1.2rem;
            background: rgba(248, 250, 252, 0.75);
            padding: 1.2rem;
            text-align: center;
            color: #64748b;
        }

        .dark .pos-empty-state {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.62);
            color: #cbd5e1;
        }

        .pos-empty-state i {
            font-size: 1.5rem;
            color: #94a3b8;
        }

        .pos-empty-state--compact {
            min-height: 7rem;
            padding: 0.95rem;
        }

        .pos-field-group {
            display: grid;
            gap: 0.42rem;
        }

        .pos-field-group span {
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
        }

        .dark .pos-field-group span {
            color: #cbd5e1;
        }

        .pos-field {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid #cbd5e1;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.72rem 0.85rem;
            font-size: 0.92rem;
            color: #0f172a;
            box-shadow: none;
        }

        .dark .pos-field {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.75);
            color: #f8fafc;
        }

        .pos-customer-results {
            position: absolute;
            top: calc(100% + 0.45rem);
            left: 0;
            right: 0;
            z-index: 20;
            display: grid;
            gap: 0.45rem;
            max-height: 16rem;
            overflow: auto;
            border: 1px solid #e2e8f0;
            border-radius: 1.1rem;
            background: rgba(255, 255, 255, 0.98);
            padding: 0.45rem;
            box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.4);
        }

        .dark .pos-customer-results {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.96);
        }

        .pos-customer-result {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.95rem;
            background: rgba(248, 250, 252, 0.88);
            padding: 0.72rem 0.8rem;
            text-align: left;
        }

        .dark .pos-customer-result {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.84);
        }

        .pos-customer-result:hover {
            border-color: #a5b4fc;
            background: rgba(238, 242, 255, 0.96);
        }

        .dark .pos-customer-result:hover {
            background: rgba(30, 41, 59, 0.94);
        }

        .pos-line-total {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.75);
            padding: 0.72rem 0.85rem;
        }

        .dark .pos-line-total {
            background: rgba(15, 23, 42, 0.72);
        }

        .pos-line-total span {
            font-size: 0.72rem;
            font-weight: 700;
            color: #94a3b8;
        }

        .pos-line-total strong {
            margin-top: 0.2rem;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .dark .pos-line-total strong {
            color: #f8fafc;
        }

        .pos-rail {
            display: grid;
            gap: 1rem;
        }

        .pos-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.88rem;
            color: #475569;
        }

        .pos-summary-row strong {
            color: #0f172a;
        }

        .dark .pos-summary-row {
            color: #cbd5e1;
        }

        .dark .pos-summary-row strong {
            color: #f8fafc;
        }

        .pos-summary-row--total {
            padding-top: 0.55rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.98rem;
            font-weight: 800;
        }

        .dark .pos-summary-row--total {
            border-color: #334155;
        }

        .pos-quick-button {
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.88);
            padding: 0.6rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
        }

        .dark .pos-quick-button {
            border-color: #334155;
            background: rgba(15, 23, 42, 0.7);
            color: #e2e8f0;
        }

        #pos-receipt-print-zone {
            display: none;
        }

        .receipt-paper {
            width: 80mm;
            padding: 8px 10px;
            font-family: "Courier New", monospace;
            color: #0f172a;
            background: #fff;
        }

        .receipt-paper[data-paper-size="thermal_58"] {
            width: 58mm;
        }

        .receipt-paper[data-paper-size="a4"],
        .receipt-paper[data-paper-size="letter"] {
            width: 210mm;
            padding: 18px 20px;
            font-family: "Helvetica", sans-serif;
        }

        .receipt-header,
        .receipt-footer {
            text-align: center;
        }

        .receipt-site {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .receipt-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #94a3b8;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 4px;
            font-size: 12px;
        }

        .receipt-row strong,
        .receipt-table .number {
            text-align: right;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .receipt-table th,
        .receipt-table td {
            padding: 5px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .receipt-label,
        .receipt-muted {
            font-size: 11px;
            color: #64748b;
        }

        @media (min-width: 1100px) {
            .pos-shell {
                height: calc(100vh - 7.4rem);
                grid-template-rows: auto auto minmax(0, 1fr);
            }

            .pos-metrics {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }

            .pos-workspace {
                min-height: 0;
                grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.05fr) 360px;
            }

            .pos-panel--tall {
                height: 100%;
            }

            .pos-result-list,
            .pos-cart-list,
            .pos-rail {
                min-height: 0;
                max-height: 100%;
            }

            .pos-rail {
                align-content: start;
                overflow: auto;
            }

            .pos-rail > .pos-panel:nth-child(2) {
                position: sticky;
                top: 0;
                z-index: 3;
            }
        }

        @media (max-width: 1099px) {
            .pos-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .pos-actions {
                width: 100%;
            }

            .pos-button {
                justify-content: center;
                flex: 1 1 calc(50% - 0.5rem);
            }

            .pos-metrics {
                grid-template-columns: 1fr;
            }

            .pos-cart-item .grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            body.pos-receipt-print-active * {
                visibility: hidden !important;
            }

            body.pos-receipt-print-active #pos-receipt-print-zone,
            body.pos-receipt-print-active #pos-receipt-print-zone * {
                visibility: visible !important;
            }

            body.pos-receipt-print-active #pos-receipt-print-zone {
                display: block !important;
                position: absolute;
                inset: 0;
                background: #fff;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const detectDeviceType = () => {
                const coarsePointer = window.matchMedia('(pointer: coarse)').matches;

                if (window.innerWidth < 640) return 'mobile';
                if (coarsePointer || window.innerWidth < 1024) return 'tablet';
                return 'desktop';
            };

            const matchScore = (expected, actual, score) => {
                if (expected === 'any') return 4;
                return expected === actual ? score : -20;
            };

            const printerScore = (expected, actual) => {
                const normalizedExpected = (expected || '').trim().toLowerCase();
                const normalizedActual = (actual || '').trim().toLowerCase();

                if (!normalizedExpected) return 3;
                return normalizedActual.includes(normalizedExpected) ? 22 : -10;
            };

            const resolveProfile = (profiles, defaultProfile, context) => {
                const eligible = (profiles || []).filter((profile) => {
                    return profile.enabled && ['pos_receipt', 'any'].includes(profile.bill_type);
                });

                if (!eligible.length) return defaultProfile;

                return eligible
                    .map((profile) => {
                        let score = profile.id === defaultProfile.id ? 50 : 0;
                        score += profile.bill_type === 'pos_receipt' ? 30 : 10;
                        score += matchScore(profile.device_match, context.deviceType, 18);
                        score += matchScore(profile.input_match, context.inputMode, 12);
                        score += printerScore(profile.printer_match, context.printerHint);
                        return { profile, score };
                    })
                    .sort((left, right) => right.score - left.score)[0]?.profile || defaultProfile;
            };

            const toggleVisibility = (selector, visible) => {
                document.querySelectorAll(selector).forEach((node) => {
                    node.style.display = visible ? '' : 'none';
                });
            };

            window.addEventListener('print-receipt', () => {
                const printZone = document.getElementById('pos-receipt-print-zone');
                const receiptSheet = document.getElementById('pos-receipt-sheet');

                if (!printZone || !receiptSheet) {
                    return;
                }

                const defaultProfile = JSON.parse(printZone.dataset.defaultProfile || '{}');
                const profiles = JSON.parse(printZone.dataset.profiles || '[]');
                const context = {
                    deviceType: detectDeviceType(),
                    inputMode: localStorage.getItem('posInputMode') || 'keyboard_scanner',
                    printerHint: localStorage.getItem('posPreferredPrinter') || printZone.dataset.defaultPrinter || '',
                };
                const profile = resolveProfile(profiles, defaultProfile, context);

                receiptSheet.dataset.paperSize = profile.paper_size || 'thermal_80';
                receiptSheet.style.fontSize = `${profile.font_scale || 1}rem`;

                toggleVisibility('[data-receipt-company-phone]', !!profile.show_company_phone);
                toggleVisibility('[data-receipt-tax-id]', !!profile.show_tax_id);
                toggleVisibility('[data-receipt-customer-address]', !!profile.show_customer_address);
                toggleVisibility('[data-receipt-customer-email]', !!profile.show_customer_email);
                toggleVisibility('[data-receipt-customer-phone]', !!profile.show_customer_phone);
                toggleVisibility('[data-receipt-payment-method]', !!profile.show_payment_method);
                toggleVisibility('[data-receipt-notes]', !!profile.show_notes);
                toggleVisibility('[data-receipt-terms]', !!profile.show_terms);

                const headerNoteNode = document.querySelector('[data-receipt-header-note]');
                if (headerNoteNode) {
                    headerNoteNode.textContent = profile.header_note || '';
                    headerNoteNode.style.display = profile.header_note ? '' : 'none';
                }

                const footerNoteNode = document.querySelector('[data-receipt-footer-note]');
                if (footerNoteNode) {
                    footerNoteNode.textContent = profile.footer_note || '';
                    footerNoteNode.style.display = profile.footer_note ? '' : 'none';
                }

                document.body.classList.add('pos-receipt-print-active');
                setTimeout(() => window.print(), 60);
            });

            window.addEventListener('afterprint', () => {
                document.body.classList.remove('pos-receipt-print-active');
            });
        })();
    </script>
@endpush
