<div class="min-h-screen bg-[#F8FAFC] p-4 lg:p-8">
    <div class="mx-auto max-w-[1600px] space-y-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white shadow-lg">
                        <i class="fas fa-boxes-stacked text-[10px]"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Warehouse Intelligence</p>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900">Inventory Management</h1>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button wire:click="exportCsv" class="flex h-12 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-6 text-[10px] font-black uppercase tracking-widest text-slate-600 transition-all hover:bg-slate-50 hover:text-slate-900 shadow-sm">
                    <i class="fas fa-file-export text-slate-400"></i>
                    Export Registry
                </button>
                <button wire:click="openModal" class="flex h-12 items-center gap-3 rounded-2xl bg-slate-900 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-slate-200 transition-all hover:scale-105 active:scale-95">
                    <i class="fas fa-plus text-[10px] opacity-50"></i>
                    New Product Intake
                </button>
            </div>
        </div>

        <!-- Metric Summary -->
        <x-admin.stock.summary :stocks="$stocks" :movementSummary="$movementSummary" />

        <div class="grid gap-8 lg:grid-cols-12">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-3 xl:col-span-2">
                <x-admin.stock.sidebar :stockWorkspaceTab="$stockWorkspaceTab" :movementSummary="$movementSummary" />
            </div>

            <!-- Main Workspace -->
            <div class="lg:col-span-9 xl:col-span-10 space-y-8">
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
                @elseif($stockWorkspaceTab === 'intake')
                    <x-admin.stock.intake :scanMode="$scanMode" />
                @elseif($stockWorkspaceTab === 'import')
                    <x-admin.stock.import :import-file="$importFile" />
                @elseif(in_array($stockWorkspaceTab, ['categories', 'brands', 'makes', 'suppliers', 'item_types', 'warranties', 'quality_levels']))
                    <x-admin.stock.registries 
                        :stock-workspace-tab="$stockWorkspaceTab" 
                        :categories="$categories" 
                        :brands="$brands" 
                        :makes="$makes" 
                        :suppliers="$suppliers" 
                        :item-types="$itemTypes" 
                        :warranties="$warranties"
                        :quality-levels="$qualityLevels"
                    />
                @endif
            </div>
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
        class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
        x-cloak
    >
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative w-full max-w-lg rounded-[3rem] border border-slate-200 bg-white shadow-2xl p-10"
        >
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 leading-none mb-2">Protocol: Refinement</p>
                    <h3 class="text-2xl font-black tracking-tight text-slate-900">Edit Registry Record</h3>
                </div>
                <button @click="show = false" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveRegistryEntity" class="space-y-6">
                <div class="space-y-2">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Display Name</label>
                    <input type="text" wire:model.defer="editingRegistryName" class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                </div>

                @if($editingRegistryType === 'warranties')
                    <div class="space-y-2">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Duration (Months)</label>
                        <input type="number" wire:model.defer="editingRegistryDuration" class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                    </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full rounded-2xl bg-slate-900 px-8 py-5 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Update Registry Entry
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global AI Insights Hub (Floating) -->
    <div class="fixed bottom-8 right-8 z-50">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-white shadow-2xl shadow-slate-400 transition-all hover:scale-110 active:scale-95">
                <i class="fas fa-sparkles text-lg" :class="open ? 'rotate-45' : ''"></i>
            </button>
            
            <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-20 right-0 w-80 rounded-[2.5rem] border border-slate-200 bg-white p-6 shadow-2xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Inventory AI Pulse</p>
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[11px] font-black text-slate-900 uppercase leading-none mb-2">Demand Insight</p>
                        <p class="text-[10px] font-medium text-slate-500 leading-relaxed">{{ $aiDemandInsight ?: 'Analyze a product to receive demand projections based on current market signals and seasonal trends.' }}</p>
                    </div>
                    @if($aiSuggestion)
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <p class="text-[11px] font-black text-emerald-900 uppercase leading-none mb-2">Pricing Intelligence</p>
                            <p class="text-[10px] font-medium text-emerald-700 leading-relaxed">Suggested: Rs {{ number_format($aiSuggestion['suggested_price'], 2) }}</p>
                            <button wire:click="applyAiSuggestion" class="mt-3 text-[9px] font-black text-emerald-900 uppercase tracking-widest underline underline-offset-4">Apply Suggestion</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</div>
