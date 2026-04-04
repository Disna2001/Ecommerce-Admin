<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    <div class="rounded-[1.9rem] bg-gradient-to-r from-slate-900 via-indigo-900 to-sky-700 p-6 text-white shadow-xl">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/60">Billing Workspace</p>
                <h2 class="mt-2 text-3xl font-black">Manage invoice collections, follow-up, and billing actions from one place.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">This area now focuses on payment follow-up, unsent invoice email visibility, and quick movement from billing into counter sales.</p>
            </div>

            <a href="{{ route('admin.pos') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                <i class="fas fa-cash-register"></i>
                <span>New POS Sale</span>
            </a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Total Invoices</p>
                <p class="mt-2 text-3xl font-black">{{ $stats['total_invoices'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Revenue</p>
                <p class="mt-2 text-3xl font-black">Rs {{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Pending Amount</p>
                <p class="mt-2 text-3xl font-black">Rs {{ number_format($stats['pending_amount'], 2) }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Paid Invoices</p>
                <p class="mt-2 text-3xl font-black">{{ $stats['paid_count'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Overdue Count</p>
                <p class="mt-2 text-3xl font-black">{{ $stats['overdue_count'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Billing Attention</h3>
                    <p class="mt-1 text-sm text-slate-500">Use these queues to jump straight into what needs billing follow-up first.</p>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($this->attentionQueues as $queue)
                    <button wire:click="{{ $queue['action'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $queue['tone'] === 'rose' ? 'bg-rose-100 text-rose-600' : ($queue['tone'] === 'amber' ? 'bg-amber-100 text-amber-600' : ($queue['tone'] === 'sky' ? 'bg-sky-100 text-sky-600' : 'bg-violet-100 text-violet-600')) }}">
                                <i class="fas {{ $queue['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $queue['label'] }}</p>
                                <p class="mt-2 text-3xl font-black text-slate-900">{{ $queue['count'] }}</p>
                                <p class="mt-2 text-xs leading-6 text-slate-500">{{ $queue['description'] }}</p>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Recent Billing</h3>
                    <p class="mt-1 text-sm text-slate-500">The newest invoices created across POS and billing flow.</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @foreach($recentInvoices as $recentInvoice)
                    <button wire:click="openInvoice({{ $recentInvoice->id }})" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $recentInvoice->invoice_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $recentInvoice->customer_name ?: 'Walk-in customer' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">Rs {{ number_format($recentInvoice->total, 2) }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $recentInvoice->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.4fr)_220px_220px]">
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <div class="relative mt-2">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search invoice number, customer name, email, or phone..."
                        class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Status</label>
                <select wire:model.live="statusFilter" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Rows</label>
                <select wire:model.live="perPage" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </select>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">From Date</label>
                <input type="date" wire:model.live="dateFrom" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">To Date</label>
                <input type="date" wire:model.live="dateTo" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
            </div>
        </div>
        @if($search || $statusFilter || $dateFrom || $dateTo)
            <div class="mt-4">
                <button wire:click="clearFilters" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                    <i class="fas fa-xmark mr-2"></i>Clear Filters
                </button>
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th wire:click="sortBy('invoice_number')" class="cursor-pointer px-6 py-4">Invoice</th>
                        <th wire:click="sortBy('customer_name')" class="cursor-pointer px-6 py-4">Customer</th>
                        <th wire:click="sortBy('invoice_date')" class="cursor-pointer px-6 py-4">Dates</th>
                        <th wire:click="sortBy('total')" class="cursor-pointer px-6 py-4">Amounts</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        @php
                            $statusClasses = [
                                'draft' => 'bg-slate-100 text-slate-700',
                                'sent' => 'bg-blue-100 text-blue-700',
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'overdue' => 'bg-rose-100 text-rose-700',
                                'cancelled' => 'bg-amber-100 text-amber-700',
                            ];
                        @endphp
                        <tr class="bg-white">
                            <td class="px-6 py-4 align-top">
                                <button wire:click="openInvoice({{ $invoice->id }})" class="font-mono text-left text-sm font-bold text-slate-900 hover:text-indigo-600 hover:underline">{{ $invoice->invoice_number }}</button>
                                <p class="mt-1 text-xs text-slate-400">{{ optional($invoice->created_at)->format('M d, Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $invoice->customer_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $invoice->customer_email ?: 'No email' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $invoice->customer_phone ?: 'No phone' }}</p>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-slate-600">
                                <p>Issued: {{ $invoice->invoice_date?->format('M d, Y') }}</p>
                                <p class="mt-1 text-xs text-slate-400">Due: {{ $invoice->due_date?->format('M d, Y') ?: 'Not set' }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-bold text-slate-900">Rs {{ number_format($invoice->total, 2) }}</p>
                                <p class="mt-1 text-xs {{ $invoice->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ $invoice->balance_due > 0 ? 'Due Rs ' . number_format($invoice->balance_due, 2) : 'Fully paid' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$invoice->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($invoice->customer_email)
                                    @if($invoice->email_sent_at)
                                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            <i class="fas fa-envelope-circle-check"></i>
                                            Sent
                                        </span>
                                    @else
                                        <button wire:click="resendInvoiceEmail({{ $invoice->id }})" class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                            <i class="fas fa-paper-plane"></i>
                                            Send Email
                                        </button>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">No email</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-wrap gap-2">
                                    @if($invoice->status === 'sent')
                                        <button wire:click="markAsPaid({{ $invoice->id }})" onclick="confirm('Mark as paid?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                            <i class="fas fa-check"></i>
                                            Paid
                                        </button>
                                    @endif
                                    @if($invoice->status !== 'cancelled' && $invoice->status !== 'paid')
                                        <button wire:click="markAsCancelled({{ $invoice->id }})" onclick="confirm('Cancel this invoice?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                            <i class="fas fa-ban"></i>
                                            Cancel
                                        </button>
                                    @endif
                                    <button wire:click="downloadPdf({{ $invoice->id }})" class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-100">
                                        <i class="fas fa-file-pdf"></i>
                                        PDF
                                    </button>
                                    <button wire:click="delete({{ $invoice->id }})" onclick="confirm('Are you sure? This action cannot be undone.') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-500">No invoices match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $invoices->links() }}</div>

    @if($showDetailModal && $viewingInvoice)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 pb-8 pt-8">
                <div class="fixed inset-0 bg-black/50" wire:click="closeInvoice"></div>
                <div class="relative z-50 w-full max-w-4xl overflow-hidden rounded-[2rem] bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-900 px-6 py-5 text-white">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/50">Invoice Detail</p>
                            <h3 class="mt-2 text-xl font-bold">{{ $viewingInvoice->invoice_number }}</h3>
                            <p class="mt-1 text-sm text-white/60">{{ $viewingInvoice->invoice_date?->format('F d, Y') }}</p>
                        </div>
                        <button wire:click="closeInvoice" class="text-white/70 transition hover:text-white">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="max-h-[78vh] space-y-6 overflow-y-auto p-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="text-sm font-semibold text-slate-900">Customer</h4>
                                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $viewingInvoice->customer_name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $viewingInvoice->customer_email ?: 'No email' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $viewingInvoice->customer_phone ?: 'No phone' }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ $viewingInvoice->customer_address ?: 'No address provided' }}</p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <h4 class="text-sm font-semibold text-slate-900">Payment</h4>
                                <div class="mt-3 space-y-2 text-sm text-slate-600">
                                    <div class="flex justify-between"><span>Status</span><span class="font-semibold text-slate-900">{{ ucfirst($viewingInvoice->status) }}</span></div>
                                    <div class="flex justify-between"><span>Method</span><span class="font-semibold text-slate-900">{{ $viewingInvoice->payment_method ?: 'N/A' }}</span></div>
                                    <div class="flex justify-between"><span>Total</span><span class="font-semibold text-slate-900">Rs {{ number_format($viewingInvoice->total, 2) }}</span></div>
                                    <div class="flex justify-between"><span>Paid</span><span class="font-semibold text-slate-900">Rs {{ number_format($viewingInvoice->amount_paid, 2) }}</span></div>
                                    <div class="flex justify-between"><span>Balance</span><span class="font-semibold text-slate-900">Rs {{ number_format($viewingInvoice->balance_due, 2) }}</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white">
                            <div class="border-b border-slate-200 px-5 py-4">
                                <h4 class="text-sm font-semibold text-slate-900">Invoice Items</h4>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach($viewingInvoice->items as $item)
                                    <div class="flex items-center gap-4 px-5 py-4">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-slate-900">{{ $item->item_name }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $item->item_code ?: 'No item code' }}</p>
                                        </div>
                                        <div class="text-right text-sm text-slate-600">
                                            <p>{{ $item->quantity }} x Rs {{ number_format($item->unit_price, 2) }}</p>
                                            <p class="mt-1 font-semibold text-slate-900">Rs {{ number_format($item->total, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
