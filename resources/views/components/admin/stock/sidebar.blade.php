<div class="rounded-[2.5rem] border border-slate-200 bg-white p-4 shadow-sm h-fit sticky top-24">
    <nav class="flex flex-col gap-1">
        <p class="mb-4 px-4 text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Inventory Hub</p>
        
        @foreach([
            ['inventory', 'Inventory Board', 'fa-table-columns', 'slate'],
            ['intake', 'Intake Desk', 'fa-plus-circle', 'emerald'],
            ['import', 'Bulk Registry', 'fa-file-csv', 'indigo']
        ] as [$tab, $label, $icon, $color])
            <button 
                type="button" 
                wire:click="setStockWorkspaceTab('{{ $tab }}')" 
                class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $stockWorkspaceTab === $tab ? 'bg-slate-900 text-white shadow-xl shadow-slate-200 translate-x-2' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <div class="flex items-center gap-3">
                    <i class="fas {{ $icon }} w-5 text-center text-xs {{ $stockWorkspaceTab === $tab ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' }}"></i>
                    <span class="text-xs font-black uppercase tracking-tight">{{ $label }}</span>
                </div>
                @if($stockWorkspaceTab === $tab)
                    <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
                @endif
            </button>
        @endforeach

        <div class="my-6 border-t border-slate-100"></div>
        
        <p class="mb-4 px-4 text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Registry Config</p>
        
        @foreach([
            ['categories', 'Categories', 'fa-tags'],
            ['brands', 'Brands', 'fa-copyright'],
            ['makes', 'Makes', 'fa-car'],
            ['suppliers', 'Suppliers', 'fa-truck-moving'],
            ['item_types', 'Item Types', 'fa-boxes-packing'],
            ['warranties', 'Warranties', 'fa-shield-halved'],
            ['quality_levels', 'Quality Tiers', 'fa-award']
        ] as [$tab, $label, $icon])
            <button 
                type="button" 
                wire:click="setStockWorkspaceTab('{{ $tab }}')" 
                class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-bold transition-all {{ $stockWorkspaceTab === $tab ? 'bg-slate-900 text-white shadow-lg translate-x-2' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <i class="fas {{ $icon }} w-5 text-center text-[10px] {{ $stockWorkspaceTab === $tab ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' }}"></i>
                <span class="uppercase tracking-widest text-[10px]">{{ $label }}</span>
            </button>
        @endforeach
    </nav>

    <!-- Sidebar Quick Insight -->
    <div class="mt-8 rounded-3xl bg-slate-50 p-6 border border-slate-100">
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-3">Today's Pulse</p>
        <div class="space-y-4">
             <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Movements</span>
                <span class="text-xs font-black text-slate-900">{{ $movementSummary['movements'] }}</span>
             </div>
             <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Alerts</span>
                <span class="text-xs font-black text-rose-500">{{ $this->lowStockCount }}</span>
             </div>
        </div>
        <a href="{{ route('admin.stock-movements') }}" wire:navigate class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 py-2.5 text-[9px] font-black text-slate-900 uppercase tracking-widest shadow-sm hover:bg-slate-900 hover:text-white transition-all">
            <i class="fas fa-list-check"></i>
            Full Ledger
        </a>
    </div>
</div>
