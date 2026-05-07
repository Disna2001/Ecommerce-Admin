<div class="space-y-8">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-black text-emerald-700 shadow-sm animate-in fade-in slide-in-from-top-4">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm font-black text-rose-700 shadow-sm animate-in fade-in slide-in-from-top-4">{{ session('error') }}</div>
    @endif

    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200"><i class="fas fa-tower-broadcast text-2xl"></i></div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Communication Terminal</p>
                    <h2 class="mt-1 text-3xl font-black text-slate-900 tracking-tight">Notification Outbox</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">Global registry of outbound system signals, broadcasts, and automated triggers.</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                 <div class="flex items-center gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-1.5 shadow-inner">
                    <span class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200">Live Search</span>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter transmissions..." class="border-0 bg-transparent py-1 pl-8 pr-3 text-xs font-bold focus:ring-0 w-48">
                    </div>
                </div>
                <button type="button" wire:click="$refresh" class="h-12 w-12 flex items-center justify-center rounded-2xl bg-white text-slate-400 border border-slate-200 hover:text-slate-900 hover:border-slate-900 transition-all shadow-sm">
                    <i class="fas fa-rotate text-sm"></i>
                </button>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-50 bg-slate-50/50 px-4 py-3">
            <div class="flex items-center gap-2 px-2 border-r border-slate-200 mr-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pipeline:</span>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-100 px-3 py-1.5 text-[10px] font-black text-slate-600 shadow-sm">
                <i class="fas fa-layer-group text-slate-400"></i> {{ number_format($stats['total']) }} TOTAL
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-1.5 text-[10px] font-black text-emerald-700 shadow-sm">
                <i class="fas fa-circle-check"></i> {{ $stats['sent'] }} DELIVERED
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 border border-amber-100 px-3 py-1.5 text-[10px] font-black text-amber-700 shadow-sm">
                <i class="fas fa-clock-rotate-left"></i> {{ $stats['queued'] }} PENDING
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 border border-rose-100 px-3 py-1.5 text-[10px] font-black text-rose-700 shadow-sm">
                <i class="fas fa-triangle-exclamation"></i> {{ $stats['failed'] }} FAILED
            </span>
            <div class="ml-auto flex items-center gap-2 px-3 border-l border-slate-200">
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ $stats['failure_rate'] }}% FAILURE RATE</span>
            </div>
        </div>
    </div>

    <div class="grid gap-8 xl:grid-cols-[1fr_400px]">
        <div class="space-y-8">
            <x-admin.outbox.analytics :analytics="$analytics" />
            <x-admin.outbox.table :notifications="$notifications" />
        </div>

        <div class="space-y-8">
            <x-admin.outbox.filters :stats="$stats" />
            
            <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-indigo-500/10"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-white/40">Transmission Health</p>
                <p class="mt-4 text-xs font-medium leading-relaxed opacity-80 italic">The communication pipeline is currently processing <span class="text-indigo-400 font-black">{{ $stats['queued'] }} active signals</span>. Ensure SMTP and WhatsApp Cloud API heartbeat is stable.</p>
            </div>
        </div>
    </div>

    @if($showDetailModal && $selectedOutbox)
        <x-admin.outbox.detail-modal :selected-outbox="$selectedOutbox" />
    @endif
</div>
