@props(['orders', 'recentQueue', 'search', 'filterStatus', 'filterPayment', 'dateFrom', 'dateTo', 'perPage'])

<div class="space-y-6">
    <!-- Quick Filter Action Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($this->attentionQueues as $queue)
            @php
                $isActive = match($queue['action']) {
                    'focusPaymentReviews' => $filterPayment === 'pending_review',
                    'focusReturns' => $filterStatus === 'return_requested',
                    'focusReadyToShip' => $filterStatus === 'confirmed',
                    'focusCancelled' => $filterStatus === 'cancelled',
                    default => false
                };
            @endphp
            <button 
                wire:click="{{ $queue['action'] }}"
                class="group flex flex-col justify-between p-4 rounded-xl border bg-white transition-all text-left shadow-xs {{ $isActive ? 'border-slate-900 ring-1 ring-slate-900 bg-slate-50/50' : 'border-slate-200 hover:border-slate-300 hover:shadow-sm' }}"
            >
                <div class="flex items-center justify-between mb-3 w-full">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-{{ $queue['tone'] }}-50 text-{{ $queue['tone'] }}-600 shrink-0">
                        <i class="fas {{ $queue['icon'] }} text-xs"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold font-mono {{ $queue['count'] > 0 ? 'bg-'.$queue['tone'].'-50 text-'.$queue['tone'].'-700 border border-'.$queue['tone'].'-100' : 'bg-slate-100 text-slate-500' }}">
                        {{ $queue['count'] }}
                    </span>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-900 group-hover:text-slate-900 flex items-center justify-between">
                        <span>{{ $queue['label'] }}</span>
                        @if($isActive)
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active</span>
                        @endif
                    </h5>
                    <p class="mt-0.5 text-[11px] font-medium text-slate-500 line-clamp-1">{{ $queue['description'] }}</p>
                </div>
            </button>
        @endforeach
    </div>

    <!-- Search & Filter Bar -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end">
            <!-- Search Input -->
            <div class="flex-1 space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Search</label>
                <div class="relative mt-1">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Order #, Name, Email, or Phone..." class="w-full rounded-lg border-slate-200 pl-9 pr-8 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Dropdowns & Date Filters -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:w-2/3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Stage</label>
                    <select wire:model.live="filterStatus" class="w-full rounded-lg border-slate-200 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                        <option value="">All Stages</option>
                        @foreach(\App\Models\Order::STATUSES as $val => $label)
                            <option value="{{ $val }}">{{ $label['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Payment</label>
                    <select wire:model.live="filterPayment" class="w-full rounded-lg border-slate-200 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="pending_review">Pending Review</option>
                        <option value="paid">Paid</option>
                        <option value="partially_paid">Partial</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Date</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full rounded-lg border-slate-200 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Per Page</label>
                    <select wire:model.live="perPage" class="w-full rounded-lg border-slate-200 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                        @foreach([15, 30, 50, 100] as $size)
                            <option value="{{ $size }}">{{ $size }} per page</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Clear Button -->
            <div class="flex items-center">
                <button wire:click="clearFilters" class="inline-flex items-center gap-1.5 h-[38px] px-3 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-xs" title="Clear Filters">
                    <i class="fas fa-rotate-left text-xs text-slate-400"></i>
                    <span>Clear</span>
                </button>
            </div>
        </div>
    </div>
</div>
