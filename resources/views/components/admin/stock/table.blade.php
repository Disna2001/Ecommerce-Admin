<div class="space-y-6">
    <!-- Bulk Operations Bar -->
    <div class="flex flex-col gap-4 rounded-3xl border border-slate-100 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-white shadow-lg shadow-slate-200">
                <i class="fas fa-layer-group text-[10px]"></i>
                <span class="text-xs font-black uppercase tracking-wider">{{ $inventoryQuickFilter }} Protocol</span>
            </div>
            <label class="group inline-flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-2 text-[10px] font-black text-slate-400 transition-colors hover:bg-slate-100 uppercase tracking-widest">
                <input type="checkbox" wire:model.live="showLowStockOnly" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0 transition-all">
                <span class="group-hover:text-slate-900">Critical Stock Only</span>
            </label>
            <div class="h-6 w-px bg-slate-100"></div>
            <button type="button" wire:click="selectVisibleLabels" class="inline-flex items-center gap-2 rounded-2xl border border-slate-100 bg-white px-4 py-2 text-[10px] font-black text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-900 shadow-sm uppercase tracking-widest">
                <i class="fas fa-check-double text-indigo-500"></i>
                <span>Select Page</span>
            </button>
            @if(count($selectedStockIds) > 0)
                <button type="button" wire:click="clearSelectedLabels" class="inline-flex items-center gap-2 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-2 text-[10px] font-black text-rose-500 transition-all hover:bg-rose-100 uppercase tracking-widest">
                    <i class="fas fa-xmark"></i>
                    <span>Reset ({{ count($selectedStockIds) }})</span>
                </button>
            @endif
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Page Density</span>
                <select wire:model.live="perPage" class="rounded-xl border-slate-100 bg-slate-50 px-3 py-1.5 text-xs font-bold shadow-none focus:ring-0">
                    <option value="10">10 Rows</option>
                    <option value="25">25 Rows</option>
                    <option value="50">50 Rows</option>
                    <option value="100">100 Rows</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Inventory Ledger Table -->
    <!-- Inventory Ledger Table (Desktop Only) -->
    <div class="hidden lg:block overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Identity</th>
                        <th class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Product Profile</th>
                        <th class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Classification</th>
                        <th class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Stock Status</th>
                        <th class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Commercials</th>
                        <th class="px-6 py-4 text-right text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($stocks as $stock)
                        <tr class="group transition-colors hover:bg-slate-50/30">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" wire:model="selectedStockIds" value="{{ $stock->id }}" class="h-4 w-4 rounded border-slate-200 text-slate-900 focus:ring-0 transition-all">
                                    <div class="space-y-1">
                                        <p class="text-xs font-black text-slate-900 tracking-tight">{{ $stock->sku }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $stock->item_code }}</span>
                                            @if($stock->barcode)
                                                <i class="fas fa-barcode text-[10px] text-slate-300"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-inner group-hover:scale-105 transition-transform">
                                        @php $firstImage = collect($stock->images)->first(); @endphp
                                        @if($firstImage)
                                            <img src="{{ asset('storage/' . ($firstImage->path ?? $firstImage['path'] ?? '')) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-slate-300">
                                                <i class="fas fa-image text-lg"></i>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/5 transition-colors"></div>
                                    </div>
                                    <div class="max-w-[240px] space-y-1">
                                        <p class="truncate text-sm font-black text-slate-900 tracking-tight">{{ $stock->name }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-1.5 py-0.5 text-[9px] font-black text-indigo-600 uppercase tracking-widest border border-indigo-100">
                                                <i class="fas fa-microchip text-[8px]"></i> {{ $stock->model_number ?: 'GENERAL' }}
                                            </span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter truncate">{{ \Illuminate\Support\Str::limit($stock->description, 25) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-1.5">
                                    <p class="text-[10px] font-black text-slate-700 uppercase tracking-widest">{{ $stock->category->name ?? 'Unset' }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $stock->brand->name ?? 'No Brand' }}</span>
                                        <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $stock->make->name ?? 'No Make' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2 min-w-[120px]">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-black {{ $stock->isLowStock() ? 'text-rose-600' : 'text-slate-900' }}">{{ $stock->quantity }} <span class="text-[9px] font-bold text-slate-400 uppercase ml-1">Units</span></p>
                                        @if($stock->isLowStock())
                                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-50 text-rose-500 animate-pulse">
                                                <i class="fas fa-triangle-exclamation text-[8px]"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-700 {{ $stock->isLowStock() ? 'bg-rose-500 shadow-lg shadow-rose-200' : 'bg-emerald-500 shadow-lg shadow-emerald-200' }}" style="width: {{ min(100, ($stock->quantity / max(1, $stock->reorder_level * 2)) * 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-1">
                                    <p class="text-sm font-black text-slate-900 tracking-tight">Rs {{ number_format($stock->selling_price, 0) }}</p>
                                    <div class="flex items-center gap-1.5">
                                         <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">{{ number_format($stock->margin_percent, 1) }}% Margin</span>
                                         @if($stock->wholesale_price)
                                            <span class="text-[9px] font-bold text-slate-300 uppercase">| Rs {{ number_format($stock->wholesale_price, 0) }} WS</span>
                                         @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openRestockModal({{ $stock->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Restock Protocol">
                                        <i class="fas fa-plus-circle text-xs"></i>
                                    </button>
                                    <button wire:click="edit({{ $stock->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-all shadow-lg shadow-slate-200" title="Edit Profile">
                                        <i class="fas fa-pen-nib text-xs"></i>
                                    </button>
                                    <button 
                                        onclick="confirm('Decommission this asset from registry?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $stock->id }})" 
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm"
                                        title="Archive Asset"
                                    >
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[2rem] bg-slate-50 text-slate-200 shadow-sm mb-6">
                                    <i class="fas fa-box-open text-3xl"></i>
                                </div>
                                <p class="text-sm font-black text-slate-900 tracking-tight">No assets identified in the current view</p>
                                <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Adjust your registry filters or search query<br>to locate specific inventory items.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="block lg:hidden space-y-4">
        @forelse($stocks as $stock)
            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex items-start gap-4">
                    <div class="relative h-16 w-16 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-inner">
                        @php $firstImage = collect($stock->images)->first(); @endphp
                        @if($firstImage)
                            <img src="{{ asset('storage/' . ($firstImage->path ?? $firstImage['path'] ?? '')) }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-slate-300">
                                <i class="fas fa-image text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="truncate text-sm font-black text-slate-900 tracking-tight leading-tight">{{ $stock->name }}</h4>
                            <input type="checkbox" wire:model="selectedStockIds" value="{{ $stock->id }}" class="mt-1 h-4 w-4 rounded border-slate-200 text-slate-900 focus:ring-0 transition-all">
                        </div>
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mt-1">
                            {{ $stock->category->name ?? 'Unset' }} · {{ $stock->brand->name ?? 'No Brand' }}
                        </p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                            SKU: {{ $stock->sku }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Stock Level</p>
                        <p class="mt-1 text-xs font-black {{ $stock->isLowStock() ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $stock->quantity }} Units
                        </p>
                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 shadow-inner">
                            <div class="h-full rounded-full transition-all duration-700 {{ $stock->isLowStock() ? 'bg-rose-500 shadow-lg shadow-rose-200' : 'bg-emerald-500 shadow-lg shadow-emerald-200' }}" style="width: {{ min(100, ($stock->quantity / max(1, $stock->reorder_level * 2)) * 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Commercials</p>
                        <p class="mt-1 text-xs font-black text-slate-900">
                            Rs {{ number_format($stock->selling_price, 0) }}
                        </p>
                        <p class="mt-1 text-[9px] font-black text-emerald-600 uppercase tracking-widest">
                            {{ number_format($stock->margin_percent, 1) }}% Margin
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <button wire:click="openRestockModal({{ $stock->id }})" class="flex-1 flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all text-xs font-bold uppercase tracking-wider" title="Restock">
                        <i class="fas fa-plus-circle"></i>
                        <span>Restock</span>
                    </button>
                    <button wire:click="edit({{ $stock->id }})" class="flex-1 flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-all text-xs font-bold uppercase tracking-wider" title="Edit">
                        <i class="fas fa-pen-nib"></i>
                        <span>Edit</span>
                    </button>
                    <button 
                        onclick="confirm('Decommission this asset from registry?') || event.stopImmediatePropagation()"
                        wire:click="delete({{ $stock->id }})" 
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all"
                        title="Archive"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-[2rem] border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-200 mb-4">
                    <i class="fas fa-box-open text-2xl"></i>
                </div>
                <p class="text-sm font-black text-slate-900 tracking-tight">No assets identified</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Hub -->
    <div class="rounded-[2rem] bg-white p-4 shadow-sm border border-slate-100">
        {{ $stocks->links() }}
    </div>
</div>
