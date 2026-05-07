@php
    $config = [
        'categories' => ['title' => 'Product Categories', 'label' => 'Category', 'model' => 'quickCategoryName', 'action' => 'quickCreateCategory', 'items' => $categories, 'icon' => 'fa-tags', 'color' => 'indigo'],
        'item_types' => ['title' => 'Item Classification', 'label' => 'Item Type', 'model' => 'quickItemTypeName', 'action' => 'quickCreateItemType', 'items' => $itemTypes, 'icon' => 'fa-boxes-packing', 'color' => 'emerald'],
        'makes' => ['title' => 'Automotive Makes', 'label' => 'Make', 'model' => 'quickMakeName', 'action' => 'quickCreateMake', 'items' => $makes, 'icon' => 'fa-car', 'color' => 'sky'],
        'brands' => ['title' => 'Commercial Brands', 'label' => 'Brand', 'model' => 'quickBrandName', 'action' => 'quickCreateBrand', 'items' => $brands, 'icon' => 'fa-copyright', 'color' => 'amber'],
        'suppliers' => ['title' => 'Supplier Registry', 'label' => 'Supplier', 'model' => 'quickSupplierName', 'action' => 'quickCreateSupplier', 'items' => $suppliers, 'icon' => 'fa-truck-moving', 'color' => 'rose'],
        'warranties' => ['title' => 'Warranty Protocols', 'label' => 'Warranty', 'model' => 'quickWarrantyName', 'action' => 'quickCreateWarranty', 'items' => $warranties, 'icon' => 'fa-shield-halved', 'color' => 'indigo'],
    ][$stockWorkspaceTab];
@endphp

<div class="rounded-[3rem] border border-slate-200 bg-white p-10 shadow-sm relative overflow-hidden">
    <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
    
    <div class="relative z-10">
        <div class="flex items-center justify-between gap-6 mb-12">
            <div class="flex items-center gap-6">
                <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200">
                    <i class="fas {{ $config['icon'] }} text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 leading-none mb-2">Protocol: Configuration</p>
                    <h3 class="text-3xl font-black tracking-tight text-slate-900">Registry: {{ $config['title'] }}</h3>
                    <p class="mt-2 text-sm font-medium text-slate-500 max-w-xl">Manage organizational classifications, brand identities, and sourcing metadata for the commerce ecosystem.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4 rounded-[1.75rem] bg-slate-50 px-6 py-4 border border-slate-100">
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Records</p>
                    <p class="text-lg font-black text-slate-900 leading-none">{{ count($config['items']) }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-12 lg:grid-cols-12">
            <!-- Entry Portal -->
            <div class="lg:col-span-5">
                <div class="rounded-[2.5rem] bg-slate-50/50 border border-slate-100 p-8">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Register New Entity</p>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="px-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $config['label'] }} Name</label>
                            <input type="text" wire:model.defer="{{ $config['model'] }}" placeholder="Enter unique name..." class="w-full rounded-2xl border-slate-100 bg-white px-6 py-4 text-sm font-black text-slate-900 shadow-sm focus:border-slate-900 focus:ring-0">
                        </div>
                        
                        @if($stockWorkspaceTab === 'warranties')
                            <div class="space-y-2">
                                <label class="px-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">Duration (Months)</label>
                                <input type="number" wire:model.defer="quickWarrantyDuration" class="w-full rounded-2xl border-slate-100 bg-white px-6 py-4 text-sm font-black text-slate-900 shadow-sm focus:border-slate-900 focus:ring-0">
                            </div>
                        @endif

                        <button type="button" wire:click="{{ $config['action'] }}" class="w-full mt-4 rounded-2xl bg-slate-900 px-8 py-5 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Complete Registration
                        </button>
                    </div>

                    <!-- System Governance Notice -->
                    <div class="mt-10 p-6 rounded-2xl bg-indigo-50/50 border border-indigo-100/30">
                        <div class="flex items-start gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <i class="fas fa-shield-halved text-[8px]"></i>
                            </div>
                            <p class="text-[10px] font-medium text-slate-500 leading-relaxed">
                                Metadata affects storefront navigation and indexing. Modifying established records may impact catalog integrity.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registry Ledger -->
            <div class="lg:col-span-7">
                <div class="rounded-[2.5rem] border border-slate-100 bg-white overflow-hidden shadow-sm">
                    <div class="bg-slate-50/50 border-b border-slate-100 px-8 py-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrative Ledger</p>
                    </div>
                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <thead class="sticky top-0 bg-white/80 backdrop-blur-sm z-10">
                                <tr class="text-left border-b border-slate-50">
                                    <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Identification</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Context</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($config['items'] as $item)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-8 py-4">
                                            <p class="text-xs font-black text-slate-900 uppercase tracking-tight">{{ $item->name }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">ID: #{{ $item->id }}</p>
                                        </td>
                                        <td class="px-8 py-4">
                                            @if($stockWorkspaceTab === 'warranties')
                                                <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-black text-indigo-600 uppercase tracking-tighter">
                                                    {{ $item->duration }} Months
                                                </span>
                                            @elseif(isset($item->stocks_count))
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                    {{ $item->stocks_count }} Products
                                                </span>
                                            @else
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                    Operational
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button 
                                                    wire:click="editRegistryEntity('{{ $stockWorkspaceTab }}', {{ $item->id }})"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 shadow-sm transition-all"
                                                >
                                                    <i class="fas fa-pen-nib text-[10px]"></i>
                                                </button>
                                                <button 
                                                    wire:confirm="Are you sure you want to remove this record? This may impact linked products."
                                                    wire:click="deleteRegistryEntity('{{ $stockWorkspaceTab }}', {{ $item->id }})"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-100 shadow-sm transition-all"
                                                >
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if(count($config['items']) === 0)
                                    <tr>
                                        <td colspan="3" class="px-8 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300">
                                                    <i class="fas fa-database text-lg"></i>
                                                </div>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No entities found in this registry</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
