@props(['orders', 'recentQueue', 'search', 'filterStatus', 'filterPayment', 'dateFrom', 'dateTo', 'perPage'])

<div class="space-y-6">
    <!-- Attention Protocol Zone -->
    <div class="grid gap-6 lg:grid-cols-4">
        @foreach($this->attentionQueues as $queue)
            <button 
                wire:click="{{ $queue['action'] }}"
                class="group flex flex-col justify-between p-6 rounded-[2rem] border border-slate-200 bg-white hover:bg-slate-900 hover:border-slate-900 hover:shadow-2xl transition-all duration-500 text-left"
            >
                <div class="flex items-center justify-between mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $queue['tone'] }}-50 text-{{ $queue['tone'] }}-600 shadow-inner group-hover:bg-white/10 group-hover:text-white transition-colors">
                        <i class="fas {{ $queue['icon'] }} text-sm"></i>
                    </div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-{{ $queue['tone'] }}-500 text-white text-xs font-black shadow-lg">
                        {{ $queue['count'] }}
                    </div>
                </div>
                <div>
                    <h5 class="text-[11px] font-black text-slate-900 group-hover:text-white uppercase tracking-widest">{{ $queue['label'] }}</h5>
                    <p class="mt-2 text-[9px] font-bold text-slate-400 group-hover:text-white/40 uppercase tracking-tighter leading-relaxed">{{ $queue['description'] }}</p>
                </div>
            </button>
        @endforeach
    </div>

    <!-- Registry Search & Filter Blueprint -->
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end">
            <div class="flex-1 space-y-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-magnifying-glass text-[10px] text-slate-400"></i>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Registry Search</label>
                </div>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Order #, Name, Email, or Phone..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
                            <i class="fas fa-circle-xmark text-sm"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 lg:w-2/3">
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-1">Stage</label>
                    <select wire:model.live="filterStatus" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        <option value="">All Stages</option>
                        @foreach(\App\Models\Order::STATUSES as $val => $label)
                            <option value="{{ $val }}">{{ $label['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-1">Payment</label>
                    <select wire:model.live="filterPayment" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="partially_paid">Partial</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-1">Date Origin</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-1">Batch Size</label>
                    <select wire:model.live="perPage" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        @foreach([15, 30, 50, 100] as $size)
                            <option value="{{ $size }}">{{ $size }} Per Page</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="clearFilters" class="h-14 w-14 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors shadow-inner" title="Clear All Protocols">
                    <i class="fas fa-trash-can text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>
