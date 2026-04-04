<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Inventory Workspace</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Stock Management</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Manage products, stock levels, pricing, and barcode/model setup from one cleaner workspace.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button wire:click="startQuickIntake" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i class="fas fa-plus"></i>
                    <span>Quick Intake</span>
                </button>
                <button wire:click="startAdvancedIntake" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900">
                    <i class="fas fa-sliders"></i>
                    <span>Advanced Intake</span>
                </button>
                @if(count($selectedStockIds) > 0)
                    <button wire:click="printSelectedLabels" class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-amber-600">
                        <i class="fas fa-barcode"></i>
                        <span>Print Labels ({{ count($selectedStockIds) }})</span>
                    </button>
                @endif
                <button wire:click="exportCsv" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fas fa-file-csv"></i>
                    <span>CSV</span>
                </button>
                <button wire:click="exportPdf" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    <i class="fas fa-file-pdf"></i>
                    <span>Filtered PDF</span>
                </button>
                <button wire:click="exportAllPdf" class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>All PDF</span>
                </button>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Results</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stocks->total() }}</p>
                <p class="mt-1 text-sm text-slate-500">Filtered stock items.</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-500">Low Stock</p>
                <p class="mt-2 text-3xl font-black text-amber-700">{{ $this->lowStockCount }}</p>
                <p class="mt-1 text-sm text-amber-700/80">Below reorder threshold.</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Inventory Value</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">Rs {{ number_format($this->totalValue, 0) }}</p>
                <p class="mt-1 text-sm text-emerald-700/80">Based on unit cost.</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Page Size</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $perPage }}</p>
                <p class="mt-1 text-sm text-indigo-700/80">Rows per page.</p>
            </div>
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-500">Stock Out Today</p>
                    <p class="mt-2 text-2xl font-black text-sky-700">{{ $movementSummary['today_out'] }}</p>
                    <p class="mt-1 text-sm text-sky-700/80">Units sold or deducted.</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Restocked Today</p>
                    <p class="mt-2 text-2xl font-black text-emerald-700">{{ $movementSummary['today_in'] }}</p>
                    <p class="mt-1 text-sm text-emerald-700/80">Units added back in.</p>
                </div>
                <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-violet-500">Reversals</p>
                    <p class="mt-2 text-2xl font-black text-violet-700">{{ $movementSummary['reversals'] }}</p>
                    <p class="mt-1 text-sm text-violet-700/80">Cancellations and returns today.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Movement Events</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ $movementSummary['movements'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Logged inventory changes today.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Recent Movement</p>
                        <p class="mt-1 text-sm text-slate-500">Latest inventory actions recorded across sales and reversals.</p>
                    </div>
                    <a href="{{ route('admin.stock-movements') }}" wire:navigate class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Open Ledger</span>
                    </a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse($recentMovements as $movement)
                        <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $movement->stock?->name ?? 'Stock item removed' }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $movement->context ?? 'general')) }}
                                    @if($movement->user)
                                        · {{ $movement->user->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold {{ $movement->direction === 'out' ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $movement->direction === 'out' ? '-' : '+' }}{{ $movement->quantity }}
                                </p>
                                <p class="mt-1 text-xs text-slate-400">{{ $movement->created_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500">No stock movement has been logged yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Workspace Tabs</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">
                    @if($stockWorkspaceTab === 'inventory')
                        Inventory Board
                    @elseif($stockWorkspaceTab === 'intake')
                        Quick Intake Desk
                    @else
                        Structure Desk
                    @endif
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    @if($stockWorkspaceTab === 'inventory')
                        Search, scan, filter, print labels, and work through the live stock list.
                    @elseif($stockWorkspaceTab === 'intake')
                        Start new stock entries faster without hunting through the whole page.
                    @else
                        Create and maintain categories, makes, brands, suppliers, and item types from the stock page itself.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setStockWorkspaceTab('inventory')" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $stockWorkspaceTab === 'inventory' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                    <i class="fas fa-table-columns"></i>
                    <span>Inventory Board</span>
                </button>
                <button type="button" wire:click="setStockWorkspaceTab('intake')" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $stockWorkspaceTab === 'intake' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Quick Intake</span>
                </button>
                <button type="button" wire:click="setStockWorkspaceTab('structure')" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $stockWorkspaceTab === 'structure' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                    <i class="fas fa-sitemap"></i>
                    <span>Structure Desk</span>
                </button>
            </div>
        </div>
    </div>

    @if($stockWorkspaceTab === 'inventory')
    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Scanner Flow</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Scan barcode, SKU, or item code</h3>
                    <p class="mt-2 text-sm text-slate-500">If the code already exists, the stock item opens immediately. If it does not exist, the workspace starts a quick intake draft with that scanned code.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="$set('scanMode', 'open_or_create')" class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $scanMode === 'open_or_create' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">Open or Create</button>
                    <button type="button" wire:click="$set('scanMode', 'create_only')" class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $scanMode === 'create_only' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">Prefer New Intake</button>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 lg:flex-row">
                <div class="relative flex-1">
                    <i class="fas fa-barcode pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.defer="scanCode" wire:keydown.enter.prevent="processScan" placeholder="Scan or enter barcode, SKU, or item code..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                </div>
                <button type="button" wire:click="processScan" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i class="fas fa-magnifying-glass"></i>
                    <span>Process Scan</span>
                </button>
                <button type="button" wire:click="clearScan" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i class="fas fa-eraser"></i>
                    <span>Clear</span>
                </button>
            </div>
        </div>

        @php
            $quickFilterLabels = [
                'all' => 'All Items',
                'low_stock' => 'Low Stock',
                'out_of_stock' => 'Out of Stock',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'discontinued' => 'Discontinued',
            ];
            $quickFilterDescriptions = [
                'all' => 'Full inventory view',
                'low_stock' => 'Needs attention now',
                'out_of_stock' => 'Fully unavailable items',
                'active' => 'Live selling catalog',
                'inactive' => 'Hidden but reusable',
                'discontinued' => 'Archived selling lines',
            ];
            $cellPadding = $compactTableMode ? 'px-4 py-3' : 'px-6 py-4';
            $compactActionClass = $compactTableMode ? 'px-2.5 py-1.5 text-[11px]' : 'px-3 py-2 text-xs';
            $activeQuickFilterLabel = $quickFilterLabels[$inventoryQuickFilter] ?? 'All Items';
        @endphp

        <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Board Presets</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Pin the view you need and keep moving</h3>
                    <p class="mt-2 text-sm text-slate-500">Choose a board preset, keep filters tight, and trigger quick restocks without leaving the main table.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="toggleCompactTableMode" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                        <i class="fas {{ $compactTableMode ? 'fa-minimize' : 'fa-expand' }}"></i>
                        <span>{{ $compactTableMode ? 'Compact On' : 'Compact Off' }}</span>
                    </button>
                    <button type="button" wire:click="resetInventoryBoard" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                        <i class="fas fa-rotate-left"></i>
                        <span>Reset Board</span>
                    </button>
                </div>
            </div>

            <div class="mt-5 grid gap-3 lg:grid-cols-3 xl:grid-cols-6">
                @foreach($quickFilterLabels as $filterKey => $filterLabel)
                    <button type="button" wire:click="setInventoryQuickFilter('{{ $filterKey }}')" class="rounded-[1.25rem] border p-4 text-left transition {{ $inventoryQuickFilter === $filterKey ? 'border-slate-900 bg-slate-900 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-semibold uppercase tracking-[0.18em] {{ $inventoryQuickFilter === $filterKey ? 'text-white/70' : 'text-slate-400' }}">{{ $filterLabel }}</span>
                            <span class="inline-flex min-w-[2.25rem] items-center justify-center rounded-full px-2 py-1 text-xs font-bold {{ $inventoryQuickFilter === $filterKey ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                                {{ $inventoryQuickCounts[$filterKey] ?? 0 }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs {{ $inventoryQuickFilter === $filterKey ? 'text-white/80' : 'text-slate-500' }}">{{ $quickFilterDescriptions[$filterKey] ?? 'Board preset' }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mt-5 grid gap-4 xl:grid-cols-[minmax(0,1.2fr)_repeat(4,minmax(0,0.55fr))]">
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <div class="relative mt-2">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, SKU, model, barcode..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Category</label>
                <select wire:model.live="selectedCategory" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Make</label>
                <select wire:model.live="selectedMake" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All</option>
                    @foreach($makes as $make)<option value="{{ $make->id }}">{{ $make->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Brand</label>
                <select wire:model.live="selectedBrand" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All</option>
                    @foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Supplier</label>
                <select wire:model.live="selectedSupplier" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All</option>
                    @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-col gap-3 rounded-[1.5rem] border border-slate-200 bg-white p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">
                    <i class="fas fa-filter"></i>
                    {{ $activeQuickFilterLabel }}
                </span>
                <label class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                    <input type="checkbox" wire:model.live="showLowStockOnly" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    Show low stock only
                </label>
                <button type="button" wire:click="selectVisibleLabels" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i class="fas fa-check-double"></i>
                    <span>Select This Page</span>
                </button>
                @if(count($selectedStockIds) > 0)
                    <button type="button" wire:click="clearSelectedLabels" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                        <i class="fas fa-xmark"></i>
                        <span>Clear {{ count($selectedStockIds) }} Selected</span>
                    </button>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-slate-500">Rows</span>
                <select wire:model.live="perPage" class="rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Inventory Table</p>
                <h3 class="mt-1 text-lg font-bold text-slate-900">Dense operator view with inline restock actions</h3>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2">
                    <i class="fas fa-layer-group"></i>
                    {{ $compactTableMode ? 'Compact layout active' : 'Comfort layout active' }}
                </span>
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2">
                    <i class="fas fa-boxes-stacked"></i>
                    {{ $stocks->total() }} matched items
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="{{ $cellPadding }}">Labels</th>
                        <th wire:click="sortBy('sku')" class="cursor-pointer {{ $cellPadding }}">SKU</th>
                        <th wire:click="sortBy('name')" class="cursor-pointer {{ $cellPadding }}">Product</th>
                        <th class="{{ $cellPadding }}">Branding</th>
                        <th wire:click="sortBy('quantity')" class="cursor-pointer {{ $cellPadding }}">Stock</th>
                        <th class="{{ $cellPadding }}">Price</th>
                        <th class="{{ $cellPadding }}">Status</th>
                        <th class="{{ $cellPadding }}">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stocks as $stock)
                        <tr class="{{ $stock->isLowStock() ? 'bg-amber-50/40' : 'bg-white' }}">
                            <td class="{{ $cellPadding }} align-top">
                                <div class="flex flex-col items-center gap-2">
                                    <input type="checkbox" wire:model="selectedStockIds" value="{{ $stock->id }}" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                    <button type="button" wire:click="printRowLabel({{ $stock->id }})" class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-[11px] font-semibold text-amber-700 transition hover:bg-amber-100">
                                        <i class="fas fa-print"></i>
                                        <span>Print</span>
                                    </button>
                                </div>
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $stock->sku }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $stock->item_code }}</p>
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $stock->name }}</p>
                                @if(!$compactTableMode)
                                    <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($stock->description, 70) }}</p>
                                @endif
                                @if($stock->model_name || $stock->model_number)
                                    <p class="mt-2 text-xs font-medium text-indigo-600">{{ $stock->model_name ?? 'Model' }}{{ $stock->model_number ? ' · #' . $stock->model_number : '' }}</p>
                                @endif
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <p class="text-sm text-slate-700">{{ $stock->make->name ?? 'N/A' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $stock->brand->name ?? 'N/A' }}</p>
                                @if($stock->qualityLevel)
                                    <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" style="background-color: {{ $stock->qualityLevel->color }}20; color: {{ $stock->qualityLevel->color }};">{{ $stock->qualityLevel->name }}</span>
                                @endif
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <p class="text-sm font-semibold {{ $stock->isLowStock() ? 'text-amber-700' : 'text-slate-900' }}">{{ $stock->quantity }}</p>
                                <p class="mt-1 text-xs text-slate-400">Reorder at {{ $stock->reorder_level }}</p>
                                @if($stock->isLowStock())
                                    <p class="mt-2 text-xs font-semibold text-amber-600">Below reorder level</p>
                                @endif
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" wire:click="quickRestock({{ $stock->id }}, 1)" class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                        <i class="fas fa-plus"></i>
                                        <span>+1</span>
                                    </button>
                                    <button type="button" wire:click="quickRestock({{ $stock->id }}, 5)" class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                        <i class="fas fa-plus"></i>
                                        <span>+5</span>
                                    </button>
                                    @if($stock->reorder_level > $stock->quantity)
                                        <button type="button" wire:click="restockToReorderLevel({{ $stock->id }})" class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-[11px] font-semibold text-amber-700 transition hover:bg-amber-100">
                                            <i class="fas fa-arrow-up"></i>
                                            <span>To Level</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <p class="text-sm font-semibold text-slate-900">Rs {{ number_format($stock->selling_price, 2) }}</p>
                                <p class="mt-1 text-xs text-slate-400">Cost Rs {{ number_format($stock->unit_price, 2) }}</p>
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold @if($stock->status === 'active') bg-emerald-100 text-emerald-700 @elseif($stock->status === 'inactive') bg-slate-100 text-slate-600 @else bg-rose-100 text-rose-700 @endif">{{ ucfirst($stock->status) }}</span>
                            </td>
                            <td class="{{ $cellPadding }} align-top">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="openRestockModal({{ $stock->id }})" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 {{ $compactActionClass }} font-semibold text-emerald-700 transition hover:bg-emerald-100"><i class="fas fa-box-open"></i><span>Restock</span></button>
                                    <button wire:click="edit({{ $stock->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 {{ $compactActionClass }} font-semibold text-indigo-700 transition hover:bg-indigo-100"><i class="fas fa-pen"></i><span>Edit</span></button>
                                    <button wire:click="delete({{ $stock->id }})" onclick="confirm('Are you sure you want to delete this stock item?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 {{ $compactActionClass }} font-semibold text-rose-700 transition hover:bg-rose-100"><i class="fas fa-trash"></i><span>Delete</span></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-16 text-center text-sm text-slate-500">No stock items match the current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $stocks->links() }}</div>
    @elseif($stockWorkspaceTab === 'intake')
    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Intake Shortcuts</p>
            <h3 class="mt-2 text-xl font-bold text-slate-900">Open the right intake flow quickly</h3>
            <p class="mt-2 text-sm text-slate-500">Use quick intake for everyday items, advanced intake for richer media and storefront mapping, or scan first if the team is handling barcode-based receiving.</p>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <button type="button" wire:click="startQuickIntake" class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 text-left transition hover:border-slate-300 hover:bg-white">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white"><i class="fas fa-bolt"></i></span>
                    <h4 class="mt-4 text-base font-semibold text-slate-900">Quick Intake</h4>
                    <p class="mt-2 text-sm text-slate-500">Fast add flow for everyday stock entry with just the essential steps visible first.</p>
                </button>
                <button type="button" wire:click="startAdvancedIntake" class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 text-left transition hover:border-slate-300 hover:bg-white">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-600 text-white"><i class="fas fa-layer-group"></i></span>
                    <h4 class="mt-4 text-base font-semibold text-slate-900">Advanced Intake</h4>
                    <p class="mt-2 text-sm text-slate-500">Use the full step workflow for media-heavy listings, mapping, and complete setup.</p>
                </button>
            </div>

            <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Scanner Entry</p>
                <div class="mt-3 flex flex-col gap-3 lg:flex-row">
                    <div class="relative flex-1">
                        <i class="fas fa-barcode pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" wire:model.defer="scanCode" wire:keydown.enter.prevent="processScan" placeholder="Scan or enter barcode, SKU, or item code..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                    </div>
                    <button type="button" wire:click="processScan" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <i class="fas fa-play"></i>
                        <span>Process Scan</span>
                    </button>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" wire:click="$set('scanMode', 'open_or_create')" class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $scanMode === 'open_or_create' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">Open or Create</button>
                    <button type="button" wire:click="$set('scanMode', 'create_only')" class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $scanMode === 'create_only' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">Prefer New Intake</button>
                    <button type="button" wire:click="clearScan" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Clear</button>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Intake Guide</p>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">1. Identify the item</p>
                        <p class="mt-1 text-sm text-slate-500">Start with product name, category, make, brand, and supplier.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">2. Set pricing and quantity</p>
                        <p class="mt-1 text-sm text-slate-500">Opening balance and quantity edits will be recorded in the stock ledger automatically.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">3. Enrich only when needed</p>
                        <p class="mt-1 text-sm text-slate-500">Use AI, barcode generation, images, and videos when the item needs deeper storefront setup.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Today</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Restocked</p>
                        <p class="mt-2 text-2xl font-black text-emerald-700">{{ $movementSummary['today_in'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Low Stock Items</p>
                        <p class="mt-2 text-2xl font-black text-amber-700">{{ $this->lowStockCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="grid gap-6 xl:grid-cols-[1.08fr_0.92fr]">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Structure Desk</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Maintain stock structure without leaving stock management</h3>
                    <p class="mt-2 text-sm text-slate-500">Create and organize categories, item types, makes, brands, and suppliers from one page so intake stays uninterrupted.</p>
                </div>
                <button type="button" wire:click="startQuickIntake" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i class="fas fa-plus"></i>
                    <span>Start Intake</span>
                </button>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Category</label>
                    <div class="mt-3 flex gap-2">
                        <input type="text" wire:model.defer="quickCategoryName" placeholder="Create category" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <button type="button" wire:click="quickCreateCategory" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $categories->count() }} categories available</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Item Type</label>
                    <div class="mt-3 flex gap-2">
                        <input type="text" wire:model.defer="quickItemTypeName" placeholder="Create item type" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <button type="button" wire:click="quickCreateItemType" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $itemTypes->count() }} item types available</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Make</label>
                    <div class="mt-3 flex gap-2">
                        <input type="text" wire:model.defer="quickMakeName" placeholder="Create make" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <button type="button" wire:click="quickCreateMake" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $makes->count() }} makes available</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Brand</label>
                    <div class="mt-3 flex gap-2">
                        <input type="text" wire:model.defer="quickBrandName" placeholder="Create brand" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <button type="button" wire:click="quickCreateBrand" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $brands->count() }} brands available</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 md:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Supplier</label>
                    <div class="mt-3 flex gap-2">
                        <input type="text" wire:model.defer="quickSupplierName" placeholder="Create supplier" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                        <button type="button" wire:click="quickCreateSupplier" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $suppliers->count() }} suppliers available</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Structure Snapshot</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Categories</p>
                        <p class="mt-2 text-2xl font-black text-slate-900">{{ $categories->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Brands</p>
                        <p class="mt-2 text-2xl font-black text-slate-900">{{ $brands->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Makes</p>
                        <p class="mt-2 text-2xl font-black text-slate-900">{{ $makes->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Suppliers</p>
                        <p class="mt-2 text-2xl font-black text-slate-900">{{ $suppliers->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Why this tab exists</p>
                <div class="mt-4 space-y-3 text-sm text-slate-500">
                    <p>Operators can now maintain stock structure from the same page instead of leaving the workflow for separate CRUD screens.</p>
                    <p>Use this desk before intake if a category, make, brand, supplier, or item type is missing.</p>
                    <p>Once the structure is ready, switch back to `Quick Intake` or `Inventory Board` and continue immediately.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative z-10 w-full max-w-5xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit.prevent="store" class="flex max-h-[90vh] flex-col">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Stock Window</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $stock_id ? 'Edit Stock Item' : 'Add New Stock Item' }}</h3>
                                    <p class="mt-2 text-sm text-slate-500">This window is restructured into practical sections so the form is easier to read and use.</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <button type="button" wire:click="setEntryMode('quick')" class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $entryMode === 'quick' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">Quick Intake</button>
                                        <button type="button" wire:click="setEntryMode('advanced')" class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $entryMode === 'advanced' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">Advanced Setup</button>
                                        <button type="button" wire:click="runAiIntakeAssist" wire:loading.attr="disabled" class="rounded-full bg-violet-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-violet-700">
                                            <span wire:loading.remove wire:target="runAiIntakeAssist"><i class="fas fa-wand-magic-sparkles mr-2"></i>AI Intake Assist</span>
                                            <span wire:loading wire:target="runAiIntakeAssist"><i class="fas fa-spinner fa-spin mr-2"></i>Preparing...</span>
                                        </button>
                                        <button type="button" wire:click="toggleQuickSetup" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                                            <i class="fas fa-layer-group mr-2"></i>{{ $showQuickSetup ? 'Hide Quick Setup' : 'Quick Setup Lists' }}
                                        </button>
                                    </div>
                                </div>
                                <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            @php($stockSteps = [
                                'catalog' => ['label' => 'Catalog', 'copy' => 'Core details and structure'],
                                'inventory' => ['label' => 'Inventory', 'copy' => 'Quantity, pricing, and mapping'],
                                'media' => ['label' => 'Media', 'copy' => 'AI, barcode, images, and videos'],
                                'review' => ['label' => 'Review', 'copy' => 'Final checks before save'],
                            ])

                            <div class="mb-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Step Workspace</p>
                                        <h4 class="mt-2 text-lg font-bold text-slate-900">{{ $stockSteps[$stockFormStep]['label'] }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">{{ $stockSteps[$stockFormStep]['copy'] }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($stockSteps as $stepKey => $step)
                                            <button type="button" wire:click="setStockFormStep('{{ $stepKey }}')" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-semibold transition {{ $stockFormStep === $stepKey ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $stockFormStep === $stepKey ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $loop->iteration }}</span>
                                                <span>{{ $step['label'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600">
                                        <i class="fas fa-route text-slate-400"></i>
                                        {{ $stockWorkflowMode === 'edit' ? 'Adjustment workflow' : 'New intake workflow' }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                                        <i class="fas fa-chart-line"></i>
                                        Margin Rs {{ number_format($this->marginAmount, 2) }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700">
                                        <i class="fas fa-layer-group"></i>
                                        {{ $this->projectedStockHealth }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700">
                                        <i class="fas fa-arrow-right-arrow-left"></i>
                                        {{ $stock_id ? ($this->quantityDifference >= 0 ? '+' : '').$this->quantityDifference : '+'.(int) ($quantity ?: 0) }} qty change
                                    </span>
                                </div>
                            </div>

                            @if($stockFormStep === 'catalog')
                            <div class="grid gap-6 lg:grid-cols-2">
                                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                    <h4 class="text-base font-semibold text-slate-900">Core Details</h4>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div><label class="block text-sm font-medium text-slate-700">SKU</label><input type="text" wire:model="sku" readonly class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-100 text-sm shadow-none"></div>
                                        <div><label class="block text-sm font-medium text-slate-700">Item Code</label><input type="text" wire:model="item_code" readonly class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-100 text-sm shadow-none"></div>
                                        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700">Product Name *</label><input type="text" wire:model="name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">@error('name')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Status *</label><select wire:model="status" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="active">Active</option><option value="inactive">Inactive</option><option value="discontinued">Discontinued</option></select></div>
                                        <div><label class="block text-sm font-medium text-slate-700">Location</label><input type="text" wire:model="location" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"></div>
                                        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700">Description</label><textarea wire:model="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea></div>
                                    </div>
                                </div>

                                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5">
                                    <h4 class="text-base font-semibold text-slate-900">Classification</h4>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div><label class="block text-sm font-medium text-slate-700">Category *</label><select wire:model="category_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>@error('category_id')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Item Type</label><select wire:model="item_type_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($itemTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
                                        <div><label class="block text-sm font-medium text-slate-700">Make *</label><select wire:model.live="make_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($makes as $make)<option value="{{ $make->id }}">{{ $make->name }}</option>@endforeach</select>@error('make_id')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Brand *</label><select wire:model.live="brand_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach</select>@error('brand_id')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Supplier *</label><select wire:model="supplier_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select>@error('supplier_id')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Warranty</label><select wire:model="warranty_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($warranties as $warranty)<option value="{{ $warranty->id }}">{{ $warranty->name }} ({{ $warranty->duration }} months)</option>@endforeach</select></div>
                                        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700">Quality Level</label><select wire:model="quality_level" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($qualityLevels as $quality)<option value="{{ $quality->code }}">{{ $quality->name }}</option>@endforeach</select></div>
                                    </div>
                                    @if($showQuickSetup)
                                        <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
                                            <div class="mb-3 flex items-center justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">Quick setup for missing lists</p>
                                                    <p class="mt-1 text-xs text-slate-500">Create a missing category, make, brand, supplier, or item type right here without leaving stock intake.</p>
                                                </div>
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2">
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model.defer="quickCategoryName" placeholder="New category" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                                    <button type="button" wire:click="quickCreateCategory" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                                                </div>
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model.defer="quickItemTypeName" placeholder="New item type" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                                    <button type="button" wire:click="quickCreateItemType" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                                                </div>
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model.defer="quickMakeName" placeholder="New make" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                                    <button type="button" wire:click="quickCreateMake" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                                                </div>
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model.defer="quickBrandName" placeholder="New brand" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                                    <button type="button" wire:click="quickCreateBrand" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                                                </div>
                                                <div class="flex gap-2 md:col-span-2">
                                                    <input type="text" wire:model.defer="quickSupplierName" placeholder="New supplier" class="w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                                    <button type="button" wire:click="quickCreateSupplier" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($stockFormStep === 'inventory')
                            <div class="grid gap-6 lg:grid-cols-2">
                                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5">
                                    <h4 class="text-base font-semibold text-slate-900">Inventory and Pricing</h4>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div><label class="block text-sm font-medium text-slate-700">Quantity *</label><input type="number" wire:model="quantity" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">@error('quantity')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Reorder Level *</label><input type="number" wire:model="reorder_level" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">@error('reorder_level')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Unit Cost (Rs) *</label><input type="number" step="0.01" wire:model="unit_price" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">@error('unit_price')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div><label class="block text-sm font-medium text-slate-700">Selling Price (Rs) *</label><input type="number" step="0.01" wire:model="selling_price" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">@error('selling_price')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                                        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700">Wholesale Price (Rs)</label><input type="number" step="0.01" wire:model="wholesale_price" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"></div>
                                    </div>
                                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="grid gap-3 md:grid-cols-3">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Margin</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">Rs {{ number_format($this->marginAmount, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Markup</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">{{ number_format($this->marginPercent, 1) }}%</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Save Result</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $this->projectedStockHealth }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($entryMode === 'advanced')
                                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 lg:col-span-2">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h4 class="text-base font-semibold text-slate-900">Advanced Listing Mapping</h4>
                                                <p class="mt-1 text-sm text-slate-500">Use this when one stock item should point to a storefront category, make, brand, or model target different from its inventory source values.</p>
                                            </div>
                                            <label class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                                                <input type="checkbox" wire:model="enableTargetCategory" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                                Enable mapping
                                            </label>
                                        </div>
                                        @if($enableTargetCategory)
                                            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                                <div><label class="block text-sm font-medium text-slate-700">Target Category</label><select wire:model="target_category_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                                                <div><label class="block text-sm font-medium text-slate-700">Target Item Type</label><select wire:model="target_item_type_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($itemTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
                                                <div><label class="block text-sm font-medium text-slate-700">Target Make</label><select wire:model="target_make_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($makes as $make)<option value="{{ $make->id }}">{{ $make->name }}</option>@endforeach</select></div>
                                                <div><label class="block text-sm font-medium text-slate-700">Target Brand</label><select wire:model="target_brand_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"><option value="">Select</option>@foreach($brands as $brand)<option value="{{ $brand->id }}">{{ $brand->name }}</option>@endforeach</select></div>
                                                <div><label class="block text-sm font-medium text-slate-700">Target Model</label><input type="text" wire:model="target_model" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"></div>
                                                <div><label class="block text-sm font-medium text-slate-700">Target Model Number</label><input type="text" wire:model="target_model_number" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"></div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @endif

                            @if($stockFormStep === 'media')
                                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                    <h4 class="text-base font-semibold text-slate-900">Model, AI, and Media</h4>
                                    <p class="mt-2 text-sm text-slate-500">{{ $entryMode === 'quick' ? 'Quick mode keeps only the most useful product enrichment controls in front of the team.' : 'Advanced mode exposes richer media, notes, and AI guidance for complete item setup.' }}</p>
                                    <div class="mt-4 grid gap-4">
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div><label class="block text-sm font-medium text-slate-700">Model Name</label><input type="text" wire:model.live="model_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"></div>
                                            <div><label class="block text-sm font-medium text-slate-700">Model Number</label><input type="text" wire:model.live="model_number" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none"></div>
                                        </div>

                                        @if($showModelSuggestions && !empty($suggestedModelNumbers))
                                            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <p class="text-sm font-semibold text-indigo-900">Suggested model numbers</p>
                                                    <button type="button" wire:click="closeModelSuggestions" class="text-indigo-500 transition hover:text-indigo-700"><i class="fas fa-xmark"></i></button>
                                                </div>
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach($suggestedModelNumbers as $suggestion)
                                                        <button type="button" wire:click="selectModelSuggestion('{{ $suggestion }}')" class="rounded-full border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">{{ $suggestion }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div><label class="block text-sm font-medium text-slate-700">Search model numbers</label><input type="text" wire:model.live.debounce.500ms="modelSearchQuery" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="Search similar model patterns..."></div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" wire:click="generateAiDescription" wire:loading.attr="disabled" class="rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700"><i class="fas fa-sparkles mr-2"></i>Description</button>
                                            <button type="button" wire:click="getAiPricingSuggestion" wire:loading.attr="disabled" class="rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"><i class="fas fa-money-bill-trend-up mr-2"></i>Price</button>
                                            <button type="button" wire:click="generateSeoKeywords" wire:loading.attr="disabled" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"><i class="fas fa-hashtag mr-2"></i>SEO</button>
                                        </div>
                                        @if($aiSuggestion)
                                            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                                                <p class="text-sm font-semibold text-blue-900">AI Price Suggestion: Rs {{ number_format($aiSuggestion['suggested_price'], 2) }}</p>
                                                @if(isset($aiSuggestion['reasoning']))<p class="mt-2 text-sm text-blue-700">{{ $aiSuggestion['reasoning'] }}</p>@endif
                                                <button type="button" wire:click="applyAiSuggestion" class="mt-3 rounded-full bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Apply This Price</button>
                                            </div>
                                        @endif
                                        @if($aiDemandInsight)
                                            <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4">
                                                <p class="text-sm font-semibold text-cyan-900">AI Demand View</p>
                                                <p class="mt-2 text-sm leading-6 text-cyan-800">{{ $aiDemandInsight }}</p>
                                            </div>
                                        @endif
                                        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px]">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700">Barcode</label>
                                                <div class="mt-2 flex overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                                    <input type="text" wire:model="barcode" readonly class="flex-1 border-0 bg-transparent px-4 py-3 text-sm shadow-none focus:ring-0">
                                                    <button type="button" wire:click="generateBarcode" class="border-l border-slate-200 bg-slate-900 px-4 py-3 text-sm font-semibold text-white" {{ !$brand_id || !$model_number ? 'disabled' : '' }}>Generate</button>
                                                    <button type="button" wire:click="printBarcode" class="border-l border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700">Print</button>
                                                </div>
                                                <p class="mt-2 text-xs text-slate-400">Barcode values are generated from the current brand and model number, then made ready for label printing.</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-white p-3" x-data x-effect="window.renderStockBarcode && window.renderStockBarcode(@js($barcode ?: ''))">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Preview</p>
                                                <div class="mt-3 flex h-[96px] items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 px-2">
                                                    <svg id="stock-barcode-svg" class="max-h-[80px] w-full"></svg>
                                                </div>
                                                <p class="mt-2 truncate text-center text-[11px] font-semibold text-slate-600">{{ $barcode ?: 'Generate barcode to preview' }}</p>
                                            </div>
                                        </div>
                                        <div class="grid gap-4 md:grid-cols-3">
                                            <input type="text" wire:model="color" class="rounded-2xl border-slate-200 text-sm shadow-none" placeholder="Color">
                                            <input type="text" wire:model="size" class="rounded-2xl border-slate-200 text-sm shadow-none" placeholder="Size">
                                            <input type="number" step="0.01" wire:model="weight" min="0" class="rounded-2xl border-slate-200 text-sm shadow-none" placeholder="Weight (kg)">
                                        </div>
                                        @if($entryMode === 'advanced')
                                            <input type="text" wire:model="tags" class="rounded-2xl border-slate-200 text-sm shadow-none" placeholder="Tags">
                                            <textarea wire:model="notes" rows="3" class="rounded-2xl border-slate-200 text-sm shadow-none resize-none" placeholder="Additional notes"></textarea>
                                        @endif
                                        <div><label class="block text-sm font-medium text-slate-700">Images</label><input type="file" wire:model="tempImages" multiple accept="image/*" class="mt-2 block w-full text-sm text-slate-600"></div>
                                        @if($currentImages)
                                            <div>
                                                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Current Images</p>
                                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                                    @foreach($currentImages as $index => $imagePath)
                                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                                            <img src="{{ asset('storage/' . $imagePath) }}" class="h-28 w-full object-cover" alt="Stock image">
                                                            <button type="button" wire:click="removeCurrentImage({{ $index }})" class="flex w-full items-center justify-center gap-2 border-t border-slate-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                                <i class="fas fa-trash"></i>
                                                                <span>Delete image</span>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($tempImages)
                                            <div>
                                                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">New Uploads</p>
                                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                                    @foreach($tempImages as $image)<img src="{{ $image->temporaryUrl() }}" class="h-28 w-full rounded-2xl object-cover">@endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700">Videos</label>
                                            <input type="file" wire:model="tempVideos" multiple accept="video/mp4,video/quicktime,video/webm,video/x-msvideo,video/x-matroska" class="mt-2 block w-full text-sm text-slate-600">
                                            <p class="mt-2 text-xs text-slate-400">Use product videos for demos, unboxing, or fitting guidance. Supported: MP4, MOV, AVI, WEBM, MKV.</p>
                                            @error('tempVideos.*')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror
                                        </div>
                                        @if($currentVideos)
                                            <div>
                                                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Current Videos</p>
                                                <div class="grid gap-3 sm:grid-cols-2">
                                                    @foreach($currentVideos as $index => $videoPath)
                                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                                            <video src="{{ asset('storage/' . $videoPath) }}" controls class="h-40 w-full bg-black object-cover"></video>
                                                            <button type="button" wire:click="removeCurrentVideo({{ $index }})" class="flex w-full items-center justify-center gap-2 border-t border-slate-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                                <i class="fas fa-trash"></i>
                                                                <span>Delete video</span>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($tempVideos)
                                            <div>
                                                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">New Video Uploads</p>
                                                <div class="grid gap-3 sm:grid-cols-2">
                                                    @foreach($tempVideos as $video)
                                                        <video src="{{ $video->temporaryUrl() }}" controls class="h-40 w-full rounded-2xl bg-black object-cover"></video>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($stockFormStep === 'review')
                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5">
                                        <h4 class="text-base font-semibold text-slate-900">Ready to save</h4>
                                        <p class="mt-1 text-sm text-slate-500">This final step keeps the review compact so the team does not have to scroll back through every section again.</p>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Identity</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $name ?: 'Name not set' }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ $sku }} | {{ $item_code }}</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Structure</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">{{ $categories->firstWhere('id', $category_id)?->name ?? 'No category' }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ $brands->firstWhere('id', $brand_id)?->name ?? 'No brand' }} | {{ $suppliers->firstWhere('id', $supplier_id)?->name ?? 'No supplier' }}</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Inventory</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">{{ (int) ($quantity ?: 0) }} units</p>
                                                <p class="mt-1 text-xs text-slate-500">Reorder at {{ (int) ($reorder_level ?: 0) }}</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pricing</p>
                                                <p class="mt-2 text-sm font-bold text-slate-900">Rs {{ number_format((float) ($selling_price ?: 0), 2) }}</p>
                                                <p class="mt-1 text-xs text-slate-500">Cost Rs {{ number_format((float) ($unit_price ?: 0), 2) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                            <h4 class="text-base font-semibold text-slate-900">Checklist</h4>
                                            <div class="mt-4 space-y-3 text-sm">
                                                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                                    <span class="font-medium text-slate-600">Product name</span>
                                                    <span class="{{ $name ? 'text-emerald-600' : 'text-rose-500' }}">{{ $name ? 'Ready' : 'Missing' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                                    <span class="font-medium text-slate-600">Category / make / brand</span>
                                                    <span class="{{ $category_id && $make_id && $brand_id ? 'text-emerald-600' : 'text-rose-500' }}">{{ $category_id && $make_id && $brand_id ? 'Ready' : 'Missing' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                                    <span class="font-medium text-slate-600">Supplier and stock values</span>
                                                    <span class="{{ $supplier_id && $quantity !== '' && $selling_price !== '' ? 'text-emerald-600' : 'text-rose-500' }}">{{ $supplier_id && $quantity !== '' && $selling_price !== '' ? 'Ready' : 'Missing' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                                    <span class="font-medium text-slate-600">Media and barcode</span>
                                                    <span class="{{ $barcode || count($currentImages) || count($tempImages) || count($currentVideos) || count($tempVideos) ? 'text-emerald-600' : 'text-amber-600' }}">{{ $barcode || count($currentImages) || count($tempImages) || count($currentVideos) || count($tempVideos) ? 'Enriched' : 'Basic only' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-white p-5">
                                            <h4 class="text-base font-semibold text-slate-900">Save summary</h4>
                                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Images</p>
                                                    <p class="mt-2 text-2xl font-black text-slate-900">{{ count($currentImages) + count($tempImages) }}</p>
                                                </div>
                                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Videos</p>
                                                    <p class="mt-2 text-2xl font-black text-slate-900">{{ count($currentVideos) + count($tempVideos) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if(!$stock_id)
                                    <label class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600">
                                        <input type="checkbox" wire:model="saveAndAddAnother" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                        Keep this window open for the next item
                                    </label>
                                @else
                                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-500">
                                        Quantity changes here will be written to the stock ledger automatically
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            @if($stockFormStep !== 'catalog')
                                <button type="button" wire:click="previousStockFormStep" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                                    <i class="fas fa-arrow-left mr-2"></i>Previous
                                </button>
                            @endif
                            @if($stockFormStep !== 'review')
                                <button type="button" wire:click="nextStockFormStep" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900">
                                    Next Step<i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            @endif
                            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $stock_id ? 'Update Stock' : 'Save Stock' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="stock-barcode-print-zone" data-barcode="{{ $barcode ?: '' }}" class="hidden">
            <div class="mx-auto w-[360px] rounded-2xl border border-slate-300 bg-white p-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Stock Label</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $name ?: 'New Stock Item' }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $sku ?: 'SKU pending' }}</p>
                <div class="mt-4">
                    <svg id="stock-barcode-print-svg" class="mx-auto h-[90px] w-full"></svg>
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ $barcode ?: 'Generate barcode first' }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $brand_id ? ($brands->firstWhere('id', $brand_id)?->name ?? '') : '' }}{{ $model_number ? ' · '.$model_number : '' }}</p>
            </div>
        </div>
    @endif

    @php($printSiteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka')))

    <div id="stock-label-sheet-print-zone" class="hidden">
        <div class="mb-4 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">{{ $printSiteName }}</p>
            <h3 class="mt-2 text-xl font-bold text-slate-900">Stock Label Sheet</h3>
            <p class="mt-1 text-sm text-slate-500">Prepared {{ count($selectedLabelStocks) }} label{{ count($selectedLabelStocks) === 1 ? '' : 's' }} for printing.</p>
        </div>
        <div class="stock-label-sheet-grid grid gap-4 md:grid-cols-2">
            @foreach($selectedLabelStocks as $labelStock)
                @php($labelCode = $labelStock->barcode ?: $labelStock->sku ?: $labelStock->item_code)
                <div class="stock-label-card rounded-2xl border border-slate-300 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">Inventory Label</p>
                            <p class="mt-2 text-base font-bold text-slate-900">{{ $labelStock->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $labelStock->sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">Rs {{ number_format($labelStock->selling_price, 2) }}</p>
                            <p class="mt-1 text-[11px] text-slate-400">{{ $labelStock->quantity }} in stock</p>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-slate-500">
                        @if($labelStock->brand?->name)
                            <span>{{ $labelStock->brand->name }}</span>
                        @endif
                        @if($labelStock->model_number)
                            <span>· {{ $labelStock->model_number }}</span>
                        @endif
                        @if($labelStock->location)
                            <span>· {{ $labelStock->location }}</span>
                        @endif
                    </div>
                    <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                        <svg class="stock-label-barcode h-[68px] w-full" data-barcode="{{ $labelCode }}"></svg>
                    </div>
                    <div class="mt-2 text-center">
                        <p class="text-xs font-semibold tracking-[0.16em] text-slate-700">{{ $labelCode ?: 'CODE PENDING' }}</p>
                        <p class="mt-1 text-[11px] text-slate-400">{{ $labelStock->item_code }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($isRestockOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeRestockModal"></div>
                <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit.prevent="processRestock" class="flex max-h-[90vh] flex-col">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Inventory Action</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">Restock Item</h3>
                                    <p class="mt-2 text-sm text-slate-500">Add inventory while keeping a clean stock movement record for this product.</p>
                                </div>
                                <button type="button" wire:click="closeRestockModal" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            <div class="grid gap-4 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Product</p>
                                    <p class="mt-2 text-base font-bold text-slate-900">{{ $restockProductName }}</p>
                                </div>
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-500">Current Qty</p>
                                    <p class="mt-2 text-2xl font-black text-amber-700">{{ $restockCurrentQuantity }}</p>
                                </div>
                                <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-500">Reorder Level</p>
                                    <p class="mt-2 text-2xl font-black text-sky-700">{{ $restockReorderLevel }}</p>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Restock Quantity *</label>
                                    <input type="number" wire:model="restockQuantity" min="1" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('restockQuantity')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror
                                    <p class="mt-2 text-xs text-slate-400">Suggested starting point uses the reorder level so the team can restock faster.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Updated Unit Cost (Rs)</label>
                                    <input type="number" step="0.01" wire:model="restockUnitCost" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('restockUnitCost')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror
                                    <p class="mt-2 text-xs text-slate-400">Optional. Use this if the supplier cost changed with the new batch.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Restock Notes</label>
                                    <textarea wire:model="restockNotes" rows="4" class="mt-2 w-full resize-none rounded-2xl border-slate-200 text-sm shadow-none" placeholder="Example: Supplier delivery received, batch checked, shelf updated."></textarea>
                                    @error('restockNotes')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                <p class="text-sm font-semibold text-emerald-900">After this restock, the new quantity will be {{ $restockCurrentQuantity + (int) $restockQuantity }} units.</p>
                                <p class="mt-1 text-sm text-emerald-700">This action will be recorded in the stock movement ledger as a manual restock.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <button type="button" wire:click="closeRestockModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                <i class="fas fa-box-open mr-2"></i>Confirm Restock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @once
        <style>
            #stock-barcode-print-zone,
            #stock-label-sheet-print-zone {
                display: none;
            }

            @media print {
                body * {
                    visibility: hidden !important;
                }

                body.stock-print-single #stock-barcode-print-zone,
                body.stock-print-single #stock-barcode-print-zone *,
                body.stock-print-sheet #stock-label-sheet-print-zone,
                body.stock-print-sheet #stock-label-sheet-print-zone * {
                    visibility: visible !important;
                }

                body.stock-print-single #stock-barcode-print-zone,
                body.stock-print-sheet #stock-label-sheet-print-zone {
                    position: absolute;
                    left: 0;
                    top: 0;
                    display: block !important;
                    width: 100%;
                    padding: 24px;
                    background: #fff;
                }

                body.stock-print-sheet #stock-label-sheet-print-zone {
                    padding: 18px;
                }

                .stock-label-sheet-grid {
                    display: grid !important;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 16px;
                }

                .stock-label-card {
                    break-inside: avoid;
                    page-break-inside: avoid;
                }
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
        <script>
            const stockPrintModes = ['stock-print-single', 'stock-print-sheet'];

            const clearStockPrintModes = () => {
                document.body.classList.remove(...stockPrintModes);
            };

            const renderBarcodeNode = (node, value, options = {}) => {
                if (!node || typeof JsBarcode === 'undefined') return;

                const barcodeValue = value && String(value).trim() !== '' ? String(value).trim() : 'EMPTY';

                JsBarcode(node, barcodeValue, {
                    format: 'CODE128',
                    lineColor: '#111827',
                    width: 1.5,
                    height: 50,
                    displayValue: false,
                    margin: 4,
                    ...options,
                });
            };

            window.renderStockBarcode = function (value) {
                ['stock-barcode-svg', 'stock-barcode-print-svg'].forEach((id) => {
                    renderBarcodeNode(document.getElementById(id), value);
                });
            };

            window.renderStockLabelSheet = function () {
                document.querySelectorAll('#stock-label-sheet-print-zone .stock-label-barcode').forEach((node) => {
                    renderBarcodeNode(node, node.dataset.barcode, {
                        width: 1.2,
                        height: 42,
                        margin: 2,
                    });
                });
            };

            const runStockPrint = (mode, callback) => {
                clearStockPrintModes();
                document.body.classList.add(mode);

                requestAnimationFrame(() => {
                    callback();
                    setTimeout(() => window.print(), 60);
                });
            };

            window.addEventListener('print-stock-barcode', () => {
                runStockPrint('stock-print-single', () => {
                    const printNode = document.getElementById('stock-barcode-print-svg');
                    window.renderStockBarcode(printNode?.closest('#stock-barcode-print-zone')?.dataset?.barcode ?? '');
                });
            });

            window.addEventListener('print-stock-label-sheet', () => {
                runStockPrint('stock-print-sheet', () => {
                    window.renderStockLabelSheet();
                });
            });

            window.addEventListener('afterprint', clearStockPrintModes);
        </script>
    @endonce
</div>
