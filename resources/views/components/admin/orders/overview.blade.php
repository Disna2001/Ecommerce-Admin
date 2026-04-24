<div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
    <div class="space-y-6">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Attention Queues</h3>
                    <p class="mt-1 text-sm text-slate-500">Start with the highest-signal items before routine browsing.</p>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($this->attentionQueues as $queue)
                    <button wire:click="{{ $queue['action'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $queue['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-600' : ($queue['tone'] === 'amber' ? 'bg-amber-100 text-amber-600' : ($queue['tone'] === 'sky' ? 'bg-sky-100 text-sky-600' : 'bg-rose-100 text-rose-600')) }}">
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
            <div class="mb-5 flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <i class="fas fa-sliders"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Filters</h3>
                    <p class="mt-1 text-sm text-slate-500">Search and narrow the queue without breaking flow.</p>
                </div>
            </div>
            <div class="grid gap-4 xl:grid-cols-[1.4fr_repeat(4,minmax(0,0.7fr))]">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Search</label>
                    <div class="relative mt-2">
                        <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Order no, name, email, phone..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select wire:model.live="filterStatus" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <option value="">All</option>
                        @foreach(\App\Models\Order::STATUSES as $key => $status)
                            <option value="{{ $key }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Payment</label>
                    <select wire:model.live="filterPayment" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <option value="">All</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">From</label>
                    <input type="date" wire:model.live="dateFrom" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">To</label>
                    <input type="date" wire:model.live="dateTo" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">Results: <span class="font-semibold text-slate-900">{{ $orders->total() }}</span></div>
                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                    Per page:
                    <select wire:model.live="perPage" class="ml-2 border-0 bg-transparent pr-7 text-sm font-semibold text-slate-900 focus:ring-0">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                @if($search || $filterStatus || $filterPayment || $dateFrom || $dateTo)
                    <button wire:click="clearFilters" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                        <i class="fas fa-xmark mr-2"></i>Clear
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                    <i class="fas fa-list-check"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Queue Snapshot</h3>
                    <p class="mt-1 text-sm text-slate-500">A short list of active orders likely to need action next.</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($recentQueue as $queueOrder)
                    <button wire:click="viewOrder({{ $queueOrder->id }})" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $queueOrder->order_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $queueOrder->customer_name }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $queueOrder->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background:{{ $queueOrder->status_bg }}; color:{{ $queueOrder->status_color }};">{{ $queueOrder->status_label }}</span>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Rs {{ number_format($queueOrder->total, 2) }}</p>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No active queue items are available.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
