<div class="space-y-6" x-data="{
    showPaymentModal: @entangle('showPaymentModal'),
    showSuccessModal: @entangle('showSuccessModal'),
    toastOpen: false,
    toastMessage: '',
    toastTone: 'success'
}"
    x-on:item-added.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'success'; setTimeout(() => toastOpen = false, 2600)"
    x-on:show-success.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'success'; setTimeout(() => toastOpen = false, 3200)"
    x-on:show-error.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'error'; setTimeout(() => toastOpen = false, 3200)"
    x-on:show-warning.window="toastOpen = true; toastMessage = $event.detail.message; toastTone = 'warning'; setTimeout(() => toastOpen = false, 3200)">

    <div x-show="toastOpen" x-transition class="fixed bottom-5 right-5 z-50 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-2xl"
        :class="toastTone === 'success' ? 'bg-emerald-600' : (toastTone === 'error' ? 'bg-rose-600' : 'bg-amber-500')"
        style="display:none;">
        <span x-text="toastMessage"></span>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Counter Workspace</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $siteName }} POS</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Run faster counter sales with cleaner cart handling, invoice email delivery, partial-payment support, and quick tender actions.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Invoice Number</p>
                    <p class="mt-2 font-mono text-sm font-bold text-slate-900">{{ $invoice_number }}</p>
                </div>
                <a href="{{ route('admin.invoices') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Invoices</span>
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Cart Lines</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ count($cart) }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Items</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $this->cartItemsCount }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Total</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">Rs {{ number_format($cartTotal, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-500">Savings</p>
                <p class="mt-2 text-3xl font-black text-amber-700">Rs {{ number_format($this->cartSavings, 2) }}</p>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-500">Today Sales</p>
                <p class="mt-2 text-2xl font-black text-sky-700">{{ $this->posSummary['today_sales'] }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Today Revenue</p>
                <p class="mt-2 text-2xl font-black text-emerald-700">Rs {{ number_format($this->posSummary['today_revenue'], 0) }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Fully Paid</p>
                <p class="mt-2 text-2xl font-black text-indigo-700">{{ $this->posSummary['today_paid'] }}</p>
            </div>
            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-violet-500">Partial Balance</p>
                <p class="mt-2 text-2xl font-black text-violet-700">{{ $this->posSummary['today_partial'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_1fr_360px]">
        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Product Search</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Scan or search stock items</h3>
                    </div>
                    <button type="button" wire:click="$set('searchTerm', '')" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">
                        Clear Search
                    </button>
                </div>

                <div class="relative mt-5">
                    <i class="fas fa-barcode pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.250ms="searchTerm" placeholder="Search by name, SKU, barcode, item code, or model..."
                        class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-sm shadow-none focus:ring-0">
                </div>

                @if($showResults && count($searchResults) > 0)
                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        @foreach($searchResults as $product)
                            <button wire:click="selectProduct({{ $product->id }})" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $product->sku }} @if($product->item_code) · {{ $product->item_code }} @endif</p>
                                        <p class="mt-2 text-xs text-slate-500">{{ $product->brand->name ?? 'Unbranded' }} @if($product->model_name) · {{ $product->model_name }} @endif</p>
                                    </div>
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-slate-700 shadow-sm">{{ $product->quantity }} in stock</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-lg font-black text-indigo-700">Rs {{ number_format($product->selling_price, 2) }}</span>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white">
                                        <i class="fas fa-plus"></i>
                                        Add
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @elseif(strlen($searchTerm) >= 2)
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                        No matching products found for this search.
                    </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Active Cart</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">Sale Items</h3>
                    </div>
                    @if(count($cart) > 0)
                        <button wire:click="clearCart" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                            <i class="fas fa-trash mr-2"></i>Clear Sale
                        </button>
                    @endif
                </div>

                @if(count($cart) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-4">Product</th>
                                    <th class="px-5 py-4">Qty</th>
                                    <th class="px-5 py-4">Price</th>
                                    <th class="px-5 py-4">Discount</th>
                                    <th class="px-5 py-4">Line Total</th>
                                    <th class="px-5 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($cart as $index => $item)
                                    <tr class="bg-white">
                                        <td class="px-5 py-4 align-top">
                                            <p class="text-sm font-semibold text-slate-900">{{ $item['name'] }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $item['sku'] }}</p>
                                            <p class="mt-2 text-xs {{ $item['stock_quantity'] <= 3 ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                                                Available stock: {{ $item['stock_quantity'] }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-4 align-top">
                                            <input type="number" wire:change="updateQuantity({{ $index }}, $event.target.value)" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock_quantity'] }}"
                                                class="w-20 rounded-2xl border-slate-200 text-center text-sm shadow-none">
                                        </td>
                                        <td class="px-5 py-4 align-top text-sm font-semibold text-slate-700">
                                            Rs {{ number_format($item['unit_price'], 2) }}
                                        </td>
                                        <td class="px-5 py-4 align-top">
                                            <div class="flex items-center gap-2">
                                                <input type="number" wire:change="updateDiscount({{ $index }}, $event.target.value)" value="{{ $item['discount'] }}" min="0" max="100"
                                                    class="w-20 rounded-2xl border-slate-200 text-right text-sm shadow-none">
                                                <span class="text-xs font-semibold text-slate-400">%</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 align-top">
                                            <p class="text-sm font-bold text-slate-900">Rs {{ number_format($item['total'], 2) }}</p>
                                        </td>
                                        <td class="px-5 py-4 align-top">
                                            <button wire:click="removeItem({{ $index }})" class="rounded-full border border-rose-200 bg-rose-50 p-2 text-rose-600 transition hover:bg-rose-100">
                                                <i class="fas fa-xmark"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas fa-cart-shopping text-2xl"></i>
                        </div>
                        <p class="mt-5 text-sm font-semibold text-slate-700">No items added yet</p>
                        <p class="mt-1 text-sm text-slate-500">Search for a product above and add it to the current sale.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Customer Desk</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900">Customer & Invoice Details</h3>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Customer Name</label>
                        <input type="text" wire:model="customer_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        @error('customer_name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Customer Email @if($sendInvoiceEmail)<span class="text-rose-500">*</span>@endif</label>
                        <input type="email" wire:model="customer_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="customer@example.com">
                        @error('customer_email') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Phone</label>
                            <input type="text" wire:model="customer_phone" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Payment Method</label>
                            <select wire:model="payment_method" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                @foreach($paymentMethods as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Address</label>
                        <textarea wire:model="customer_address" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sale Notes</label>
                        <textarea wire:model="notes" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none" placeholder="Counter notes, collection details, special requests..."></textarea>
                    </div>
                    <label class="flex items-start gap-3 rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                        <input type="checkbox" wire:model="sendInvoiceEmail" class="mt-1 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block font-semibold">Send invoice email automatically</span>
                            <span class="mt-1 block text-xs text-indigo-600/80">If enabled, the customer receives the sale invoice immediately after payment.</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Payment Summary</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900">Totals</h3>

                <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-900">Rs {{ number_format($cartSubtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Tax</span>
                        <span class="font-semibold text-slate-900">Rs {{ number_format($cartTax, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Discount Savings</span>
                        <span class="font-semibold text-amber-600">Rs {{ number_format($this->cartSavings, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-4">
                        <span class="text-base font-semibold text-slate-900">Total Payable</span>
                        <span class="text-2xl font-black text-emerald-700">Rs {{ number_format($cartTotal, 2) }}</span>
                    </div>
                </div>

                <div class="mt-5 grid gap-3">
                    <button wire:click="openPaymentModal"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        @disabled(count($cart) === 0)>
                        <i class="fas fa-credit-card"></i>
                        <span>Process Payment</span>
                    </button>
                    <button wire:click="clearCart" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                        <i class="fas fa-rotate-right"></i>
                        <span>Reset Sale</span>
                    </button>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Recent Activity</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Latest Invoices</h3>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse($recent_invoices as $invoice)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $invoice->customer_name ?: 'Walk-in customer' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-emerald-700">Rs {{ number_format($invoice->total, 2) }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $invoice->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No recent invoices yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
            <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Payment Desk</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Process Sale Payment</h3>
                        </div>
                        <button @click="showPaymentModal = false" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid gap-6 md:grid-cols-[1fr_280px]">
                        <div class="space-y-5">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-500">Sale Total</span>
                                    <span class="font-bold text-slate-900">Rs {{ number_format($cartTotal, 2) }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <span class="text-slate-500">Minimum to proceed</span>
                                    <span class="font-semibold text-slate-900">Rs {{ number_format($cartTotal * 0.5, 2) }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Payment Method</label>
                                <select wire:model="payment_method" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @foreach($paymentMethods as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Amount Paid</label>
                                <input type="number" wire:model.live="amount_paid" step="0.01" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-right text-lg font-bold shadow-none">
                                @error('amount_paid') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <p class="text-sm font-medium text-slate-700">Quick Tender</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button wire:click="applyQuickTender('exact')" type="button" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Exact</button>
                                    <button wire:click="applyQuickTender('plus_500')" type="button" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">+ 500</button>
                                    <button wire:click="applyQuickTender('plus_1000')" type="button" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">+ 1000</button>
                                    <button wire:click="applyQuickTender('plus_5000')" type="button" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">+ 5000</button>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Settlement</p>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm text-emerald-700">Change Due</p>
                                    <p class="mt-1 text-3xl font-black text-emerald-700">Rs {{ number_format($change_due, 2) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/80 p-4 text-sm text-emerald-800">
                                    @if($amount_paid >= $cartTotal)
                                        Full payment captured. Receipt and invoice are ready to close.
                                    @elseif($amount_paid >= ($cartTotal * 0.5))
                                        Partial payment accepted. Invoice will remain with outstanding balance.
                                    @else
                                        Enter at least 50% of the total to proceed with this sale.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button @click="showPaymentModal = false" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                    <button wire:click="processPayment"
                        class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        @disabled($amount_paid < ($cartTotal * 0.5) || count($cart) === 0)>
                        Complete Sale
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
</div>
