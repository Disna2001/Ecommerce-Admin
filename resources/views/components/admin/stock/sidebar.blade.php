<!-- Mobile Horizontal Navigation Bar -->
<div class="block lg:hidden w-full overflow-x-auto scrollbar-none py-1.5 mb-3">
    <div class="flex items-center gap-1.5 w-max px-1">
        @foreach([
            ['inventory', 'Inventory Board', 'fa-table-columns'],
            ['intake', 'Intake Desk', 'fa-plus-circle'],
            ['import', 'Bulk Registry', 'fa-file-csv']
        ] as [$tab, $label, $icon])
            <button 
                type="button" 
                wire:click="setStockWorkspaceTab('{{ $tab }}')" 
                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all {{ $stockWorkspaceTab === $tab ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}"
            >
                <i class="fas {{ $icon }} text-[10px]"></i>
                <span>{{ $label }}</span>
            </button>
        @endforeach
    </div>
</div>

<!-- Desktop Vertical Sidebar -->
<div class="hidden lg:block w-full rounded-xl border border-slate-200 bg-white p-2.5 shadow-xs h-fit sticky top-20">
    <nav class="flex flex-col gap-0.5">
        <p class="mb-1.5 px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory Workspace</p>
        
        @foreach([
            ['inventory', 'Inventory Board', 'fa-table-columns', 'slate'],
            ['intake', 'Intake Desk', 'fa-plus-circle', 'emerald'],
            ['import', 'Bulk Registry', 'fa-file-csv', 'indigo']
        ] as [$tab, $label, $icon, $color])
            <button 
                type="button" 
                wire:click="setStockWorkspaceTab('{{ $tab }}')" 
                class="group flex items-center justify-between rounded-lg px-2.5 py-1.5 transition-all text-xs font-semibold {{ $stockWorkspaceTab === $tab ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >
                <div class="flex items-center gap-2.5">
                    <i class="fas {{ $icon }} w-4 text-center text-xs {{ $stockWorkspaceTab === $tab ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' }}"></i>
                    <span>{{ $label }}</span>
                </div>
                @if($stockWorkspaceTab === $tab)
                    <i class="fas fa-chevron-right text-[9px] opacity-60"></i>
                @endif
            </button>
        @endforeach
    </nav>
</div>
