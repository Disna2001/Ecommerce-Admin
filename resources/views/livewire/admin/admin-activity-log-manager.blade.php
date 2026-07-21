<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Audit & Log</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Activity Log</h2>
                <p class="mt-1 text-xs text-slate-500">Track administrator actions, order updates, and configuration changes.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="exportCsv" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                    <i class="fas fa-file-csv text-slate-400"></i>Export CSV
                </button>
                <button wire:click="exportPdf" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                    <i class="fas fa-file-pdf text-slate-400"></i>Export Report
                </button>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3 rounded-lg border border-slate-100 bg-slate-50 px-3.5 py-2.5 text-xs font-medium">
            <span class="text-slate-500 font-semibold">Metrics:</span>
            <span class="inline-flex items-center gap-1.5 rounded-md bg-white border border-slate-200 px-2.5 py-1 text-[11px] font-bold text-slate-700 shadow-xs">
                <i class="fas fa-list-check text-slate-400"></i> {{ number_format($stats['total']) }} TOTAL
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 border border-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700 shadow-xs">
                <i class="fas fa-bolt"></i> {{ $stats['today'] }} TODAY
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 border border-indigo-100 px-2.5 py-1 text-[11px] font-bold text-indigo-700 shadow-xs">
                <i class="fas fa-cart-shopping"></i> {{ $stats['order_actions'] }} ORDERS
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 border border-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700 shadow-xs">
                <i class="fas fa-sliders"></i> {{ $stats['settings_changes'] }} CONFIG
            </span>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_280px]">
        <div class="space-y-6">
            <x-admin.activity.filters :stats="$stats" :users="$users" />
            <x-admin.activity.table :logs="$logs" />
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-4">Filter by Category</p>
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
