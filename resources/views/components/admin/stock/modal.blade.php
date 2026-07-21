@props([
    'stock_id' => null,
    'stockWorkflowMode' => 'create',
    'entryMode' => 'quick',
    'stockSteps' => [],
    'stockFormStep' => 'catalog',
    'categories' => [],
    'makes' => [],
    'brands' => [],
    'itemTypes' => [],
    'suppliers' => [],
    'warranties' => [],
    'qualityLevels' => [],
    'showQuickSetup' => false,
    'enableTargetCategory' => false,
    'currentImages' => [],
    'images' => [],
    'currentVideos' => [],
    'tempVideosList' => [],
    'name' => '',
    'sku' => '',
    'item_code' => '',
    'barcode' => '',
    'description' => '',
    'category_id' => null,
    'make_id' => null,
    'brand_id' => null,
    'item_type_id' => null,
    'supplier_id' => null,
    'warranty_id' => null,
    'quantity' => 0,
    'reorder_level' => 10,
    'unit_price' => 0,
    'selling_price' => 0,
    'location' => '',
    'status' => 'active',
    'model_name' => '',
    'model_number' => '',
    'color' => '',
    'size' => '',
    'weight' => null,
    'tags' => '',
    'notes' => '',
    'quality_level' => null,
    'wholesale_price' => null
])

<div 
    x-data="{ show: @entangle('isOpen') }" 
    x-show="show" 
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto custom-scrollbar"
    x-cloak
>
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="relative w-full max-w-7xl rounded-2xl sm:rounded-[3rem] border border-slate-200 bg-white shadow-2xl overflow-hidden"
    >
        <form wire:submit.prevent="store" class="flex flex-col h-[90vh]">
            <!-- Modal Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-100 bg-slate-50/50 px-4 py-4 sm:px-8 sm:py-6 shrink-0">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-[1rem] sm:rounded-[1.25rem] bg-slate-900 text-white shadow-xl shrink-0">
                            <i class="fas {{ $stock_id ? 'fa-pen-nib' : 'fa-plus' }} text-xs sm:text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none mb-1">Stock Ledger</p>
                            <h3 class="text-lg sm:text-2xl font-black tracking-tight text-slate-900">{{ $stock_id ? 'Edit Product' : 'New Product Intake' }}</h3>
                        </div>
                    </div>
                    <div class="hidden sm:block h-10 w-px bg-slate-200 mx-2"></div>
                    <div class="flex items-center gap-1 rounded-[1.25rem] border border-slate-200 bg-white p-1 shadow-inner w-full sm:w-auto justify-center">
                        <button type="button" wire:click="setEntryMode('quick')" class="flex-1 sm:flex-initial rounded-xl px-4 sm:px-5 py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ $entryMode === 'quick' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-900' }}">Quick Mode</button>
                        <button type="button" wire:click="setEntryMode('advanced')" class="flex-1 sm:flex-initial rounded-xl px-4 sm:px-5 py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ $entryMode === 'advanced' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-900' }}">Advanced</button>
                    </div>
                </div>
                <div class="flex items-center justify-between md:justify-end gap-4 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="text-left sm:text-right">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Projected Status</p>
                            <p class="text-[11px] font-black text-slate-900 uppercase">{{ $this->projectedStockHealth }}</p>
                        </div>
                        <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fas fa-chart-line text-[10px]"></i>
                        </div>
                    </div>
                    <button type="button" @click="show = false" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm transition-all hover:bg-rose-500 hover:text-white">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Workflow Stepper -->
            <div class="px-4 sm:px-8 py-3 bg-white border-b border-slate-100 overflow-x-auto custom-scrollbar shrink-0">
                <div class="flex items-center gap-2 w-full max-w-4xl">
                    @foreach($stockSteps as $stepKey => $step)
                        <button type="button" wire:click="setStockFormStep('{{ $stepKey }}')" 
                            class="flex-1 group relative flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-bold transition-all duration-200 {{ $stockFormStep === $stepKey ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-900 border border-slate-200/60' }} shrink-0">
                            <span class="flex h-5 w-5 items-center justify-center rounded-md text-[10px] font-bold transition-colors {{ $stockFormStep === $stepKey ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $loop->iteration }}</span>
                            <span class="truncate">{{ $step['label'] }}</span>
                        </button>
                        @if(!$loop->last)
                            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Modal Workspace -->
            <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-10 sm:py-10 custom-scrollbar">
                @if($stockFormStep === 'catalog')
                    <div class="grid gap-6 lg:gap-12 lg:grid-cols-2">
                        <!-- Identity & Details -->
                        <div class="space-y-8">
                            <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-8 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-md"><i class="fas fa-id-card text-xs"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Basic Details</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Name and where this item sits in your catalog.</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between px-1">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">SKU</label>
                                                <button type="button" wire:click="generateSku" class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-800 transition-colors"><i class="fas fa-wand-magic-sparkles mr-1"></i> Auto</button>
                                            </div>
                                            <input type="text" wire:model="sku" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            @error('sku') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between px-1">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Internal Code</label>
                                                <button type="button" wire:click="generateItemCode" class="text-[9px] font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-800 transition-colors"><i class="fas fa-wand-magic-sparkles mr-1"></i> Auto</button>
                                            </div>
                                            <input type="text" wire:model="item_code" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            @error('item_code') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Product Name</label>
                                        <input type="text" wire:model="name" placeholder="e.g. Premium Series X1..." class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        @error('name') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</label>
                                            <select wire:model="status" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="active">Active</option>
                                                <option value="inactive">Draft</option>
                                                <option value="discontinued">Archived</option>
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Warehouse Location</label>
                                            <input type="text" wire:model="location" placeholder="e.g. Rack A-12" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between px-1">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Description</label>
                                            <button type="button" wire:click="generateAiDescription" class="text-[9px] font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-800 transition-colors"><i class="fas fa-sparkles mr-1"></i> AI Description</button>
                                        </div>
                                        <textarea wire:model="description" rows="5" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-4 text-[13px] font-medium text-slate-600 shadow-inner focus:bg-white focus:ring-0 resize-none leading-relaxed"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Classification & Attributes -->
                        <div class="space-y-8">
                            <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-8 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md"><i class="fas fa-layer-group text-xs"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Classification</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Category, make, brand, and supplier.</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Category</label>
                                            <select wire:model="category_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                                            </select>
                                            @error('category_id') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Item Type</label>
                                            <select wire:model="item_type_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="">Select Type</option>
                                                @foreach($itemTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Make</label>
                                            <select wire:model.live="make_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="">Select Make</option>
                                                @foreach($makes as $make)<option value="{{ $make->id }}">{{ $make->name }}</option>@endforeach
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Brand</label>
                                            <select wire:model.live="brand_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Supplier</label>
                                        <select wire:model="supplier_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Warranty</label>
                                            <select wire:model="warranty_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="">No Warranty</option>
                                                @foreach($warranties as $warranty)<option value="{{ $warranty->id }}">{{ $warranty->name }} ({{ $warranty->duration }} mo)</option>@endforeach
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Quality Tier</label>
                                            <select wire:model="quality_level" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                                <option value="">Select Tier</option>
                                                @foreach($qualityLevels as $quality)<option value="{{ $quality->code }}">{{ $quality->name }}</option>@endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($showQuickSetup)
                                <div class="rounded-2xl sm:rounded-3xl border border-dashed border-slate-200 bg-slate-50/30 p-5 sm:p-8 animate-in fade-in slide-in-from-top-4">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-200 text-slate-600"><i class="fas fa-bolt text-[10px]"></i></div>
                                        <div>
                                            <p class="text-xs font-black text-slate-900 uppercase tracking-widest leading-none">Quick Add</p>
                                            <p class="mt-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Add missing categories, brands, or warranties.</p>
                                        </div>
                                    </div>
                                    <div class="grid gap-4">
                                        <div class="flex gap-2">
                                            <input type="text" wire:model.defer="quickCategoryName" placeholder="New Category..." class="flex-1 rounded-xl border-slate-200 bg-white px-4 py-2 text-xs font-bold shadow-sm focus:ring-0">
                                            <button type="button" wire:click="quickCreateCategory" class="rounded-xl bg-slate-900 px-4 py-2 text-[10px] font-black text-white uppercase tracking-widest">Add</button>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" wire:model.defer="quickBrandName" placeholder="New Brand..." class="flex-1 rounded-xl border-slate-200 bg-white px-4 py-2 text-xs font-bold shadow-sm focus:ring-0">
                                            <button type="button" wire:click="quickCreateBrand" class="rounded-xl bg-slate-900 px-4 py-2 text-[10px] font-black text-white uppercase tracking-widest">Add</button>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" wire:model.defer="quickWarrantyName" placeholder="New Warranty (e.g. 1 Year)..." class="flex-1 rounded-xl border-slate-200 bg-white px-4 py-2 text-xs font-bold shadow-sm focus:ring-0">
                                            <input type="number" wire:model.defer="quickWarrantyDuration" placeholder="Months" class="w-20 rounded-xl border-slate-200 bg-white px-3 py-2 text-xs font-bold shadow-sm focus:ring-0">
                                            <button type="button" wire:click="quickCreateWarranty" class="rounded-xl bg-slate-900 px-4 py-2 text-[10px] font-black text-white uppercase tracking-widest">Add</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($stockFormStep === 'inventory')
                    <div class="grid gap-6 lg:gap-12 lg:grid-cols-2">
                        <!-- Pricing & Stock -->
                        <div class="space-y-8">
                            <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-8 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md"><i class="fas fa-coins text-xs"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Pricing & Stock</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Set quantities, cost, and selling price.</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Opening Quantity</label>
                                            <input type="number" wire:model="quantity" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            @error('quantity') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Reorder Level</label>
                                            <input type="number" wire:model="reorder_level" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            @error('reorder_level') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cost Price (per unit)</label>
                                            <div class="relative">
                                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300">Rs.</span>
                                                <input type="number" step="0.01" wire:model="unit_price" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 pl-12 pr-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            </div>
                                            @error('unit_price') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between px-1">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Selling Price</label>
                                                <button type="button" wire:click="getAiPricingSuggestion" class="text-[9px] font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-800 transition-colors"><i class="fas fa-sparkles mr-1"></i> AI Suggest</button>
                                            </div>
                                            <div class="relative">
                                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300">Rs.</span>
                                                <input type="number" step="0.01" wire:model="selling_price" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 pl-12 pr-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                            </div>
                                            @error('selling_price') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Wholesale Price</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300">Rs.</span>
                                            <input type="number" step="0.01" wire:model="wholesale_price" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 pl-12 pr-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        </div>
                                    </div>

                                    <!-- Margin Intelligence Bar -->
                                    <div class="mt-6 sm:mt-8 rounded-2xl sm:rounded-[2rem] bg-slate-900 p-5 sm:p-8 text-white shadow-2xl shadow-slate-200 relative overflow-hidden">
                                        <div class="absolute right-0 top-0 h-32 w-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
                                        <div class="relative z-10">
                                            <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-6">
                                                <div>
                                                    <p class="text-[10px] font-black text-white/40 uppercase tracking-widest leading-none mb-1">Profit Margins</p>
                                                    <p class="text-sm font-black tracking-tight">Estimated Margin</p>
                                                </div>
                                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10 text-white"><i class="fas fa-chart-pie text-xs"></i></div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-8">
                                                <div>
                                                    <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Profit per Unit</p>
                                                    <p class="text-2xl font-black tracking-tighter">Rs {{ number_format($this->marginAmount, 2) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Margin</p>
                                                    <p class="text-2xl font-black tracking-tighter text-emerald-400">{{ number_format($this->marginPercent, 1) }}%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Specs & Storefront -->
                        <div class="space-y-8">
                            <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-8 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md"><i class="fas fa-microchip text-xs"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Model & Specifications</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Model details, dimensions, and variant info.</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Model Name</label>
                                            <input type="text" wire:model.live="model_name" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between px-1">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Model Number</label>
                                                <button type="button" wire:click="getModelSuggestions" class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-800 transition-colors"><i class="fas fa-search mr-1"></i> Scan Registry</button>
                                            </div>
                                            <input type="text" wire:model.live="model_number" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        </div>
                                    </div>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Color</label>
                                            <input type="text" wire:model="color" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Size</label>
                                            <input type="text" wire:model="size" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between px-1">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Weight (kg)</label>
                                        </div>
                                        <input type="number" step="0.01" wire:model="weight" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                    </div>
                                </div>
                            </div>

                            @if($entryMode === 'advanced')
                                <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-slate-50/50 p-5 sm:p-8 animate-in fade-in slide-in-from-top-4">
                                    <div class="flex items-center justify-between mb-8">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-md"><i class="fas fa-map-location-dot text-xs"></i></div>
                                            <div>
                                                <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Storefront Overrides</h4>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Override display category or brand for the online store.</p>
                                            </div>
                                        </div>
                                        <button 
                                            type="button"
                                            wire:click="$set('enableTargetCategory', {{ !$enableTargetCategory ? 'true' : 'false' }})"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $enableTargetCategory ? 'bg-indigo-600' : 'bg-slate-300' }}"
                                        >
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $enableTargetCategory ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </div>

                                    @if($enableTargetCategory)
                                        <div class="grid gap-6 animate-in fade-in zoom-in-95">
                                            <div class="grid gap-6 md:grid-cols-2">
                                                <div class="space-y-2">
                                                    <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Public Category</label>
                                                    <select wire:model="target_category_id" class="w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-900 shadow-sm focus:ring-0">
                                                        <option value="">Same as Category</option>
                                                        @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                                                    </select>
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Public Brand</label>
                                                    <select wire:model="target_brand_id" class="w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-900 shadow-sm focus:ring-0">
                                                        <option value="">Same as Brand</option>
                                                        @foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($stockFormStep === 'media')
                    <div class="grid gap-6 lg:gap-12 lg:grid-cols-2">
                        <!-- Visual Assets -->
                        <div class="space-y-8">
                            <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-8 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-md"><i class="fas fa-panorama text-xs"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Image Gallery</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Product photos for storefront and admin.</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-8">
                                    @foreach($currentImages as $index => $img)
                                        <div class="relative group aspect-square rounded-2xl overflow-hidden border border-slate-100 bg-slate-50">
                                            <img src="{{ asset('storage/' . ($img['path'] ?? '')) }}" class="h-full w-full object-cover">
                                            <button type="button" wire:click="removeCurrentImage({{ $index }})" class="absolute top-2 right-2 h-6 w-6 flex items-center justify-center rounded-lg bg-rose-500 text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-times text-[10px]"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                    
                                    @foreach($images as $index => $img)
                                        <div class="relative group aspect-square rounded-2xl overflow-hidden border-2 border-emerald-400 bg-emerald-50">
                                            <img src="{{ $img->temporaryUrl() }}" class="h-full w-full object-cover">
                                            <div class="absolute inset-0 bg-emerald-500/10"></div>
                                            <div class="absolute top-2 right-2 flex gap-1">
                                                <button type="button" wire:click="removeTempImage({{ $index }})" class="h-6 w-6 flex items-center justify-center rounded-lg bg-rose-500 text-white shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-times text-[8px]"></i>
                                                </button>
                                                <div class="h-6 w-6 flex items-center justify-center rounded-lg bg-emerald-500 text-white shadow-lg group-hover:opacity-0 transition-opacity">
                                                    <i class="fas fa-check text-[8px]"></i>
                                                </div>
                                            </div>
                                            <div class="absolute bottom-2 left-2 right-2">
                                                <div class="bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-center shadow-sm">
                                                    <p class="text-[7px] font-black text-emerald-600 uppercase tracking-widest">Ready to upload</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <label class="flex aspect-square cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-100 bg-slate-50/50 transition-all hover:border-slate-900 hover:bg-white group">
                                        <i class="fas fa-plus text-slate-300 group-hover:text-slate-900 transition-colors"></i>
                                        <span class="mt-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">Add Image</span>
                                        <input type="file" wire:model="tempImages" multiple class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Motion Assets -->
                        <div class="space-y-8">
                             <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-8 shadow-sm">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md"><i class="fas fa-video text-xs"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900 uppercase tracking-tight">Product Videos</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Upload demo videos or clip previews.</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    @foreach($currentVideos as $index => $vid)
                                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm"><i class="fas fa-play text-[10px]"></i></div>
                                                <div>
                                                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">Saved Video #{{ $index + 1 }}</p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Uploaded</p>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="removeCurrentVideo({{ $index }})" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-300 hover:text-rose-500 transition-all shadow-sm"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                        </div>
                                    @endforeach

                                    @foreach($tempVideosList as $index => $vid)
                                        <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm border border-emerald-100">
                                                    <i class="fas fa-video text-[10px]"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-black text-emerald-900 uppercase tracking-tight">New Video</p>
                                                    <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Ready to upload</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="h-6 w-6 flex items-center justify-center rounded-lg bg-emerald-500 text-white shadow-md">
                                                    <i class="fas fa-check text-[8px]"></i>
                                                </div>
                                                <button type="button" wire:click="removeTempVideo({{ $index }})" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-emerald-200 text-emerald-300 hover:text-rose-500 transition-all shadow-sm"><i class="fas fa-times text-[10px]"></i></button>
                                            </div>
                                        </div>
                                    @endforeach

                                    <label class="flex cursor-pointer items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-slate-100 bg-slate-50/50 p-6 transition-all hover:border-slate-900 hover:bg-white group">
                                        <i class="fas fa-film text-slate-300 group-hover:text-slate-900 transition-colors"></i>
                                        <span class="text-[10px] font-black text-slate-400 group-hover:text-slate-900 uppercase tracking-widest">Upload Video</span>
                                        <input type="file" wire:model="tempVideos" multiple class="hidden" accept="video/*">
                                    </label>
                                </div>
                             </div>
                        </div>
                    </div>
                @endif

                @if($stockFormStep === 'review')
                    <div class="grid gap-6 lg:gap-12 lg:grid-cols-2">
                        <!-- Summary -->
                        <div class="space-y-8">
                             <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-5 sm:p-10 shadow-sm">
                                <div class="flex items-center gap-4 sm:gap-6 mb-6 sm:mb-12">
                                    <div class="flex h-12 w-12 sm:h-16 sm:w-16 items-center justify-center rounded-[1rem] sm:rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200 shrink-0">
                                        <i class="fas fa-microscope text-lg sm:text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 leading-none mb-2">Final Review</p>
                                        <h3 class="text-xl sm:text-3xl font-black tracking-tight text-slate-900">Product Summary</h3>
                                    </div>
                                </div>

                                <div class="space-y-10">
                                     <div class="grid gap-8 md:grid-cols-2">
                                         <div>
                                             <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Product Name</p>
                                             <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $name }}</p>
                                         </div>
                                         <div>
                                             <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">SKU</p>
                                             <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $sku }}</p>
                                         </div>
                                     </div>

                                     <div class="p-5 sm:p-8 rounded-2xl sm:rounded-[2rem] bg-slate-50 border border-slate-100">
                                         <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Categories & Attributes</p>
                                         <div class="flex flex-wrap gap-2 sm:gap-4">
                                             <span class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-[10px] font-black text-slate-900 uppercase tracking-tight shadow-sm">{{ $categories->find($category_id)?->name ?? 'General' }}</span>
                                             <span class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-[10px] font-black text-slate-900 uppercase tracking-tight shadow-sm">{{ $brands->find($brand_id)?->name ?? 'Universal' }}</span>
                                             <span class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-[10px] font-black text-slate-900 uppercase tracking-tight shadow-sm">{{ $makes->find($make_id)?->name ?? 'Standard' }}</span>
                                         </div>
                                     </div>

                                     <div>
                                         <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Description</p>
                                         <p class="text-xs font-medium text-slate-500 leading-relaxed italic line-clamp-4">"{{ $description ?: 'No description provided for this product.' }}"</p>
                                     </div>
                                </div>
                             </div>
                        </div>

                        <!-- Operational Impact -->
                        <div class="space-y-8">
                            <div class="rounded-2xl sm:rounded-3xl bg-slate-900 p-5 sm:p-10 text-white shadow-2xl shadow-slate-300 relative overflow-hidden">
                                <div class="absolute left-0 top-0 h-64 w-64 bg-white/5 rounded-full -ml-32 -mt-32"></div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-4 mb-6 sm:mb-12">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-white shadow-xl"><i class="fas fa-radar text-lg"></i></div>
                                        <div>
                                            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest leading-none mb-1">Summary</p>
                                            <h4 class="text-xl font-black tracking-tight">Financial Overview</h4>
                                        </div>
                                    </div>

                                    <div class="space-y-8">
                                        <div class="grid gap-6 sm:gap-10 md:grid-cols-2">
                                            <div class="space-y-6">
                                                <div class="flex justify-between border-b border-white/10 pb-4">
                                                    <span class="text-[11px] font-bold text-white/60 uppercase tracking-tight">Cost Price</span>
                                                    <span class="text-sm font-black tracking-tight">Rs {{ number_format($unit_price, 2) }}</span>
                                                </div>
                                                <div class="flex justify-between border-b border-white/10 pb-4">
                                                    <span class="text-[11px] font-bold text-white/60 uppercase tracking-tight">Selling Price</span>
                                                    <span class="text-sm font-black tracking-tight text-emerald-400">Rs {{ number_format($selling_price, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="space-y-6">
                                                <div class="flex justify-between border-b border-white/10 pb-4">
                                                    <span class="text-[11px] font-bold text-white/60 uppercase tracking-tight">Margin</span>
                                                    <span class="text-sm font-black tracking-tight">+{{ number_format($this->marginPercent, 1) }}%</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-[11px] font-bold text-white/60 uppercase tracking-tight">Quantity</span>
                                                    <span class="text-sm font-black tracking-tight text-emerald-400">{{ (int)$quantity }} Units</span>
                                                </div>
                                             </div>
                                         </div>
                                     </div>
                                </div>

                                <div class="mt-8 sm:mt-12 flex flex-col items-center justify-center p-4 sm:p-8 border-t border-white/10">
                                     <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-4 rounded-[2rem] bg-white px-6 sm:px-16 py-4 sm:py-6 text-xs font-black text-slate-900 uppercase tracking-[0.3em] shadow-2xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                                        <i class="fas fa-check text-sm opacity-50"></i>
                                        Save Product
                                     </button>
                                     <p class="mt-4 text-[9px] font-bold text-white/40 uppercase tracking-widest text-center">Saving will update inventory levels and product details in the catalog.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-slate-100 bg-slate-50/50 px-4 sm:px-10 py-4 sm:py-8 flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-4 shrink-0">
                <div class="flex items-center justify-center sm:justify-start w-full sm:w-auto">
                    <button type="button" @click="show = false" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">Discard Draft</button>
                </div>
                <div class="flex items-center justify-center sm:justify-end gap-3 w-full sm:w-auto">
                    @if($stockFormStep !== 'catalog')
                         <button type="button" wire:click="setStockFormStep('{{ array_keys($stockSteps)[array_search($stockFormStep, array_keys($stockSteps)) - 1] }}')" class="flex-1 sm:flex-initial text-center rounded-2xl border border-slate-200 bg-white px-4 sm:px-8 py-3.5 sm:py-4 text-[10px] font-black text-slate-900 uppercase tracking-widest transition-all hover:border-slate-900 shadow-sm">
                            <span>Previous</span>
                         </button>
                    @endif
                    
                    @if($stockFormStep !== 'review')
                         <button type="button" wire:click="setStockFormStep('{{ array_keys($stockSteps)[array_search($stockFormStep, array_keys($stockSteps)) + 1] }}')" class="flex-1 sm:flex-initial flex items-center justify-center gap-3 rounded-2xl bg-slate-900 px-6 sm:px-10 py-3.5 sm:py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <span>Continue</span>
                            <i class="fas fa-arrow-right text-[10px] opacity-50"></i>
                         </button>
                    @else
                         <button type="submit" class="flex-1 sm:flex-initial flex items-center justify-center gap-3 rounded-2xl bg-emerald-600 px-6 sm:px-10 py-3.5 sm:py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-emerald-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fas fa-check text-[10px] opacity-50"></i>
                            <span>Save Product</span>
                         </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
