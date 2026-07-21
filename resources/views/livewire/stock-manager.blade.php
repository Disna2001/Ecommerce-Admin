<div class="space-y-6">
    <!-- Top Bar / Page Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Inventory Board</h1>
            <p class="text-xs text-slate-500">Manage stock inventory, items, arrivals, and dispatches.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="exportCsv" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                <i class="fas fa-file-export text-slate-400 text-xs"></i>
                <span>Export CSV</span>
            </button>
            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">
                <i class="fas fa-plus text-xs"></i>
                <span>New Product Intake</span>
            </button>
        </div>
    </div>

    <!-- Metric Summary (Contextual: Only on Inventory Board) -->
    @if($stockWorkspaceTab === 'inventory')
        <x-admin.stock.summary :stocks="$stocks" :movementSummary="$movementSummary" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <!-- Sidebar Navigation -->
        <div class="w-full lg:col-span-3 xl:col-span-2">
            <x-admin.stock.sidebar :stockWorkspaceTab="$stockWorkspaceTab" :movementSummary="$movementSummary" />
        </div>

        <!-- Main Workspace -->
        <div class="w-full lg:col-span-9 xl:col-span-10 space-y-6">
            @if($stockWorkspaceTab === 'inventory')
                <x-admin.stock.filters 
                    :categories="$categories" 
                    :makes="$makes" 
                    :brands="$brands" 
                    :suppliers="$suppliers" 
                    :inventoryQuickFilter="$inventoryQuickFilter" 
                    :inventoryQuickCounts="$inventoryQuickCounts"
                    :compactTableMode="$compactTableMode"
                    :scanMode="$scanMode"
                />
                
                <x-admin.stock.table 
                    :stocks="$stocks" 
                    :inventoryQuickFilter="$inventoryQuickFilter" 
                    :selectedStockIds="$selectedStockIds"
                />

                <!-- Recent Movements Preview Panel -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock-rotate-left text-slate-500 text-xs"></i>
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Recent Inventory Movements</h4>
                        </div>
                        <a href="{{ route('admin.stock-movements') }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>View Stock Movements</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="space-y-2">
                        @forelse($this->recentMovements as $log)
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200/80 bg-slate-50/50 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $log->direction === 'in' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : ($log->direction === 'out' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-amber-50 text-amber-600 border border-amber-100') }}">
                                        {{ $log->direction }}
                                    </span>
                                    <span class="font-semibold text-slate-900">{{ $log->stock->name ?? ($log->stock->sku ?? 'Stock #'.$log->stock_id) }}</span>
                                    <span class="text-slate-500 text-[11px]">({{ $log->quantity > 0 ? '+'.$log->quantity : $log->quantity }})</span>
                                </div>
                                <div class="flex items-center gap-3 text-slate-400 text-[11px]">
                                    <span>{{ ucwords(str_replace('_', ' ', $log->context ?? 'adjustment')) }}</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400 font-medium">No recent inventory movements recorded.</div>
                        @endforelse
                    </div>
                </div>
            @elseif($stockWorkspaceTab === 'intake')
                <x-admin.stock.intake :scanMode="$scanMode" />
            @elseif($stockWorkspaceTab === 'import')
                <x-admin.stock.import :import-file="$importFile" />
            @endif
        </div>
    </div>

    <!-- Modals -->
    <x-admin.stock.modal 
        :is-open="$isOpen" 
        :stock-workflow-mode="$stockWorkflowMode"
        :entry-mode="$entryMode"
        :stock-form-step="$stockFormStep"
        :stock_id="$stock_id"
        :categories="$categories"
        :makes="$makes"
        :brands="$brands"
        :item-types="$itemTypes"
        :suppliers="$suppliers"
        :warranties="$warranties"
        :quality-levels="$qualityLevels"
        :showQuickSetup="$showQuickSetup"
        :enableTargetCategory="$enableTargetCategory"
        :currentImages="$currentImages"
        :images="$images"
        :tempImages="$tempImages"
        :tempVideos="$tempVideos"
        :tempVideosList="$tempVideosList"
        :stock-steps="$stockSteps"
        :name="$name"
        :sku="$sku"
        :item-code="$item_code"
        :barcode="$barcode"
        :description="$description"
        :category-id="$category_id"
        :make-id="$make_id"
        :brand-id="$brand_id"
        :item-type-id="$item_type_id"
        :supplier-id="$supplier_id"
        :warranty-id="$warranty_id"
        :quantity="$quantity"
        :reorder-level="$reorder_level"
        :unit-price="$unit_price"
        :selling-price="$selling_price"
        :location="$location"
        :status="$status"
        :model-name="$model_name"
        :model-number="$model_number"
        :color="$color"
        :size="$size"
        :weight="$weight"
        :tags="$tags"
        :notes="$notes"
        :quality-level="$quality_level"
        :wholesale-price="$wholesale_price"
    />

    <x-admin.stock.restock-modal 
        :restockProductName="$restockProductName"
        :restockCurrentQuantity="$restockCurrentQuantity"
        :restockReorderLevel="$restockReorderLevel"
        :restock-quantity="$restockQuantity"
        :restock-unit-cost="$restockUnitCost"
    />

    <!-- Registry Edit Modal -->
    <div 
        x-data="{ show: @entangle('isRegistryEditModalOpen') }" 
        x-show="show" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-cloak
    >
        <div 
            x-show="show" 
            class="relative w-full max-w-md rounded-xl border border-slate-200 bg-white shadow-xl p-6 space-y-6"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Edit Item</h3>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>

            <form wire:submit.prevent="saveRegistryEntity" class="space-y-4 text-xs">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Display Name</label>
                    <input type="text" wire:model.defer="editingRegistryName" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>

                @if($editingRegistryType === 'warranties')
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Duration (Months)</label>
                        <input type="number" wire:model.defer="editingRegistryDuration" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="show = false" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Insights Floating Hub -->
    <div class="fixed bottom-6 right-6 z-50">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg transition-transform hover:scale-105">
                <i class="fas fa-sparkles text-sm" :class="open ? 'rotate-45' : ''"></i>
            </button>
            
            <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 w-80 rounded-xl border border-slate-200 bg-white p-5 shadow-xl space-y-4 text-xs">
                <p class="font-bold text-slate-900">Inventory AI Insights</p>
                <div class="space-y-3">
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                        <p class="font-bold text-slate-900 mb-1">Demand Insight</p>
                        <p class="text-slate-500 font-medium leading-relaxed text-[11px]">{{ $aiDemandInsight ?: 'Analyze a product to receive demand projections based on current market signals.' }}</p>
                    </div>
                    @if($aiSuggestion)
                        <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                            <p class="font-bold text-emerald-900 mb-1">Pricing Recommendation</p>
                            <p class="text-emerald-700 font-medium text-[11px]">Suggested: Rs {{ number_format($aiSuggestion['suggested_price'], 2) }}</p>
                            <button wire:click="applyAiSuggestion" class="mt-2 text-[10px] font-bold text-emerald-800 underline">Apply Suggestion</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
