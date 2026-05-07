<div class="space-y-6">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200"><i class="fas fa-fingerprint text-2xl"></i></div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">System Integrity</p>
                    <h2 class="mt-1 text-3xl font-black text-slate-900 tracking-tight">Audit Terminal</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">Real-time surveillance of administrative protocols and state mutations.</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button wire:click="exportCsv" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-[10px] font-black text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </button>
                <button wire:click="exportPdf" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-[10px] font-black text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                    <i class="fas fa-file-pdf mr-2"></i>Audit Report
                </button>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-50 bg-slate-50/50 px-4 py-3">
            <div class="flex items-center gap-2 px-2 border-r border-slate-200 mr-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Metrics:</span>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-100 px-3 py-1.5 text-[10px] font-black text-slate-600 shadow-sm">
                <i class="fas fa-list-check text-slate-400"></i> {{ number_format($stats['total']) }} TOTAL
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-1.5 text-[10px] font-black text-emerald-700 shadow-sm">
                <i class="fas fa-bolt"></i> {{ $stats['today'] }} TODAY
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 border border-indigo-100 px-3 py-1.5 text-[10px] font-black text-indigo-700 shadow-sm">
                <i class="fas fa-cart-shopping"></i> {{ $stats['order_actions'] }} ORDERS
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 border border-amber-100 px-3 py-1.5 text-[10px] font-black text-amber-700 shadow-sm">
                <i class="fas fa-sliders"></i> {{ $stats['settings_changes'] }} CONFIG
            </span>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        <div class="space-y-6">
            <x-admin.activity.filters :stats="$stats" :users="$users" />
            <x-admin.activity.table :logs="$logs" />
        </div>

        <div class="space-y-6">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Action Intelligence</p>
                <div class="space-y-4">
                    @foreach([
                        ['Orders', 'order.', 'fa-cart-shopping', 'indigo'],
                        ['Inventory', 'stock.', 'fa-box-archive', 'emerald'],
                        ['Settings', 'settings.', 'fa-sliders', 'amber'],
                        ['Auth', 'auth.', 'fa-lock', 'rose'],
                    ] as [$label, $prefix, $icon, $color])
                        <button type="button" wire:click="$set('actionFilter', '{{ $prefix }}')" class="group w-full flex items-center justify-between rounded-2xl p-4 transition-all {{ $actionFilter === $prefix ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $actionFilter === $prefix ? 'bg-white/20' : 'bg-white shadow-sm text-slate-400 group-hover:text-'.$color.'-500' }} transition-colors"><i class="fas {{ $icon }} text-xs"></i></div>
                                <span class="text-sm font-black tracking-tight">{{ $label }}</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-emerald-500/10"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-white/40">Terminal Status</p>
                <p class="mt-4 text-xs font-medium leading-relaxed opacity-80 italic">Audit trails are immutable and cryptographic signatures ensure chain of custody for all system-wide state changes.</p>
            </div>
        </div>
    </div>

    @if($showDetailModal && $selectedLog)
        <x-admin.activity.detail-modal :selected-log="$selectedLog" />
    @endif
</div>
