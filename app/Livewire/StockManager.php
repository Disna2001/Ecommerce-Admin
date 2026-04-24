<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Make;
use App\Models\Brand;
use App\Models\ItemType;
use App\Models\Supplier;
use App\Models\Warranty;
use App\Models\ItemQualityLevel;
use App\Models\StockMovementLog;
use Illuminate\Support\Str;
use App\Services\OpenAIService;
use App\Services\Inventory\StockMovementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;

class StockManager extends Component
{
    use WithPagination, WithFileUploads;

    // Basic Properties
    public $stock_id;
    public $sku;
    public $item_code;
    public $name;
    public $description;
    public $category_id;
    public $make_id;
    public $brand_id;
    public $item_type_id;
    public $supplier_id;
    public $warranty_id;
    public $quantity;
    public $reorder_level = 10;        // FIX: default value set here
    public $unit_price;
    public $selling_price;
    public $location = 'Main Warehouse'; // FIX: default value set here
    public $barcode;
    public $status = 'active';          // FIX: default value set here

    // Quality levels from database
    public $qualityLevels = [];

    // Additional Properties
    public $model_name;
    public $model_number;
    public $color;
    public $size;
    public $weight;
    public $specifications = [];
    public $images = [];
    public $tags;
    public $notes;

    // AI Model Suggestions
    public $suggestedModelNumbers = [];
    public $showModelSuggestions = false;
    public $selectedModelSuggestion = '';
    public $isLoadingModels = false;
    public $modelSearchQuery = '';

    // New Properties for Reorganized Layout
    public $quality_level;
    public $enableTargetCategory = false;
    public $target_category_id;
    public $target_item_type_id;
    public $target_make_id;
    public $target_brand_id;
    public $target_model;
    public $target_model_number;
    public $wholesale_price;

    // UI State properties
    public $isOpen = false;
    public $search = '';
    public $selectedCategory = '';
    public $selectedMake = '';
    public $selectedBrand = '';
    public $selectedSupplier = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showLowStockOnly = false;
    public string $stockWorkspaceTab = 'inventory';
    public string $inventoryQuickFilter = 'all';
    public bool $compactTableMode = true;
    public string $scanCode = '';
    public string $scanMode = 'open_or_create';
    public array $selectedStockIds = [];
    public $aiSuggestion = null;
    public $isLoading = false;
    public $seoKeywords = [];

    // For file uploads
    public $tempImages = [];
    public $currentImages = [];
    public $tempVideos = [];
    public $currentVideos = [];

    // Restock Workspace
    public $isRestockOpen = false;
    public $restockStockId = null;
    public $restockProductName = '';
    public $restockCurrentQuantity = 0;
    public $restockReorderLevel = 0;
    public $restockQuantity = 1;
    public $restockUnitCost = null;
    public $restockNotes = '';
    public bool $saveAndAddAnother = false;
    public string $stockWorkflowMode = 'create';
    public string $entryMode = 'quick';
    public string $stockFormStep = 'catalog';
    public bool $showQuickSetup = false;
    public string $quickCategoryName = '';
    public string $quickMakeName = '';
    public string $quickBrandName = '';
    public string $quickSupplierName = '';
    public string $quickItemTypeName = '';
    public ?string $aiDemandInsight = null;

    protected $openAIService;

    // FIX: Converted $rules property to rules() method so unique validation
    // ignores the current record's own ID during edit, and can be computed dynamically.
    protected function rules()
    {
        return [
            'sku'               => 'required|string|unique:stocks,sku,' . ($this->stock_id ?: 'NULL'),
            'item_code'         => 'required|string|unique:stocks,item_code,' . ($this->stock_id ?: 'NULL'),
            'name'              => 'required|string|min:3',
            'description'       => 'nullable|string',
            'category_id'       => 'required|exists:categories,id',
            'make_id'           => 'required|exists:makes,id',
            'brand_id'          => 'required|exists:brands,id',
            'item_type_id'      => 'nullable|exists:item_types,id',
            'supplier_id'       => 'required|exists:suppliers,id',
            'warranty_id'       => 'nullable|exists:warranties,id',
            'quantity'          => 'required|integer|min:0',
            'reorder_level'     => 'required|integer|min:0',
            'unit_price'        => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'location'          => 'nullable|string',
            'barcode'           => 'nullable|string|unique:stocks,barcode,' . ($this->stock_id ?: 'NULL'),
            'status'            => 'required|in:active,inactive,discontinued',
            'model_name'        => 'nullable|string',
            'model_number'      => 'nullable|string',
            'color'             => 'nullable|string',
            'size'              => 'nullable|string',
            'weight'            => 'nullable|numeric|min:0',
            'tags'              => 'nullable|string',
            'notes'             => 'nullable|string',
            'quality_level'     => 'nullable|exists:item_quality_levels,code',
            'target_category_id'  => 'nullable|exists:categories,id',
            'target_item_type_id' => 'nullable|exists:item_types,id',
            'target_make_id'      => 'nullable|exists:makes,id',
            'target_brand_id'     => 'nullable|exists:brands,id',
            'target_model'        => 'nullable|string',
            'target_model_number' => 'nullable|string',
            'wholesale_price'     => 'nullable|numeric|min:0',
            'tempImages'          => 'nullable|array',
            'tempImages.*'        => 'nullable|image|max:10240',
            'tempVideos'          => 'nullable|array',
            'tempVideos.*'        => 'nullable|file|mimes:mp4,mov,avi,webm,mkv|max:51200',
        ];
    }

    public function boot(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function mount()
    {
        $this->resetInputFields();
        $this->generateSku();
        $this->generateItemCode();
        $this->loadQualityLevels();
    }

    public function loadQualityLevels()
    {
        $this->qualityLevels = ItemQualityLevel::where('is_active', true)
            ->orderBy('level_order')
            ->get();
    }

    public function generateSku()
    {
        $this->sku = 'SKU-' . strtoupper(Str::random(8));
    }

    public function generateItemCode()
    {
        $prefix = 'ITM';
        $year   = date('Y');
        $month  = date('m');
        $random = strtoupper(Str::random(4));
        $this->item_code = $prefix . '-' . $year . $month . '-' . $random;
    }

    // -------------------------------------------------------------------------
    // Barcode
    // -------------------------------------------------------------------------

    public function generateBarcode()
    {
        if (!$this->brand_id || !$this->model_number) {
            $this->dispatch('notify', [
                'type'    => 'warning',
                'message' => 'Please select a brand and enter model number first.',
            ]);
            return;
        }

        $brand = Brand::find($this->brand_id);
        if (!$brand) return;

        $brandCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $brand->name), 0, 4));
        $modelCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->model_number));
        $random    = strtoupper(Str::random(3));

        $this->barcode = $brandCode . '-' . $modelCode . '-' . $random;

        while (Stock::where('barcode', $this->barcode)
                     ->when($this->stock_id, fn($q) => $q->where('id', '!=', $this->stock_id))
                     ->exists()) {
            $random        = strtoupper(Str::random(3));
            $this->barcode = $brandCode . '-' . $modelCode . '-' . $random;
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Barcode generated successfully!']);
    }

    public function updatedBrandId()
    {
        if ($this->model_number) $this->generateBarcode();
        $this->suggestedModelNumbers = [];
        $this->showModelSuggestions  = false;
    }

    public function updatedModelNumber()
    {
        if ($this->brand_id) $this->generateBarcode();
    }

    public function updatedTempImages(): void
    {
        $this->tempImages = array_values(array_filter(Arr::wrap($this->tempImages)));
        $this->validate([
            'tempImages' => 'nullable|array',
            'tempImages.*' => 'nullable|image|max:10240',
        ]);
    }

    public function updatedTempVideos(): void
    {
        $this->tempVideos = array_values(array_filter(Arr::wrap($this->tempVideos)));
        $this->validate([
            'tempVideos' => 'nullable|array',
            'tempVideos.*' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv|max:51200',
        ]);
    }

    // -------------------------------------------------------------------------
    // AI Model Number Suggestions
    // -------------------------------------------------------------------------

    public function getModelSuggestions()
    {
        if (!$this->brand_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Please select a brand first.']);
            return;
        }
        if (!$this->model_name) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Please enter a model name first.']);
            return;
        }

        $this->isLoadingModels       = true;
        $this->suggestedModelNumbers = [];

        try {
            $brand = Brand::findOrFail($this->brand_id);

            $existingModels = Stock::where('brand_id', $this->brand_id)
                ->whereNotNull('model_number')
                ->pluck('model_number')
                ->toArray();

            $suggestions = $this->openAIService->suggestModelNumbers(
                $brand->name,
                $this->model_name,
                $existingModels
            );

            if (!empty($suggestions)) {
                $this->suggestedModelNumbers = $suggestions;
                $this->showModelSuggestions  = true;
                $this->dispatch('notify', ['type' => 'success', 'message' => count($suggestions) . ' model number suggestions found!']);
            } else {
                $this->loadFallbackSuggestions($brand->name);
            }
        } catch (\Exception $e) {
            Log::error('Model Suggestion Error: ' . $e->getMessage());
            $this->loadFallbackSuggestions(Brand::find($this->brand_id)?->name ?? 'Unknown');
        }

        $this->isLoadingModels = false;
    }

    private function loadFallbackSuggestions(string $brandName)
    {
        $this->suggestedModelNumbers = $this->generateFallbackModelSuggestions($brandName, $this->model_name);
        $this->showModelSuggestions  = true;
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Using pattern-based model suggestions.']);
    }

    public function searchModelNumbers()
    {
        if (strlen($this->modelSearchQuery) < 2) return;

        $this->isLoadingModels = true;

        try {
            $brand = Brand::find($this->brand_id);

            $suggestions = $this->openAIService->searchModelNumbers(
                $brand?->name ?? 'Unknown',
                $this->modelSearchQuery
            );

            if (!empty($suggestions)) {
                $this->suggestedModelNumbers = $suggestions;
                $this->showModelSuggestions  = true;
            }
        } catch (\Exception $e) {
            Log::error('Model Search Error: ' . $e->getMessage());
        }

        $this->isLoadingModels = false;
    }

    public function selectModelSuggestion($suggestion)
    {
        $this->model_number           = $suggestion;
        $this->selectedModelSuggestion = $suggestion;
        $this->showModelSuggestions   = false;
        $this->modelSearchQuery       = '';

        if ($this->brand_id && $this->model_number) $this->generateBarcode();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Model number selected!']);
    }

    public function closeModelSuggestions()
    {
        $this->showModelSuggestions = false;
    }

    private function generateFallbackModelSuggestions($brandName, $modelName)
    {
        $brandCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $brandName), 0, 3));
        $modelCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $modelName), 0, 3));
        $year      = date('y');
        $nextYear  = date('y', strtotime('+1 year'));

        $suggestions = [
            $brandCode . '-' . $modelCode . '-' . $year . '01',
            $brandCode . '-' . $modelCode . '-' . $year . '02',
            $brandCode . '-' . $modelCode . '-' . $nextYear . '01',
            $brandCode . $year . $modelCode,
            $modelCode . '-' . $year . '-' . $brandCode,
            strtoupper(substr($brandName, 0, 1)) . $year . strtoupper(substr($modelName, 0, 2)) . '00',
            $brandCode . '-' . $this->generateRandomString(4),
            $modelCode . $year . $this->generateRandomString(3),
        ];

        $existingPatterns = Stock::where('brand_id', $this->brand_id)
            ->whereNotNull('model_number')
            ->limit(5)
            ->pluck('model_number')
            ->toArray();

        foreach ($existingPatterns as $pattern) {
            if (!in_array($pattern, $suggestions)) $suggestions[] = $pattern;
        }

        return array_slice(array_unique($suggestions), 0, 10);
    }

    private function generateRandomString($length = 4)
    {
        return strtoupper(Str::random($length));
    }

    public function updatedModelName()
    {
        $this->suggestedModelNumbers = [];
        $this->showModelSuggestions  = false;
        $this->modelSearchQuery      = '';

        if ($this->brand_id && strlen($this->model_name) >= 3) {
            $this->getModelSuggestions();
        }
    }

    public function updatedModelSearchQuery()
    {
        if (strlen($this->modelSearchQuery) >= 2) {
            $this->searchModelNumbers();
        } else {
            $this->suggestedModelNumbers = [];
            $this->showModelSuggestions  = false;
        }
    }

    // -------------------------------------------------------------------------
    // AI Features
    // -------------------------------------------------------------------------

    public function generateAiDescription()
    {
        if (!$this->name) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please enter product name first.']);
            return;
        }

        $this->isLoading = true;

        try {
            $features = [];
            if ($this->color)        $features[] = "Color: {$this->color}";
            if ($this->size)         $features[] = "Size: {$this->size}";
            if ($this->weight)       $features[] = "Weight: {$this->weight}kg";
            if ($this->model_name)   $features[] = "Model: {$this->model_name}";
            if ($this->model_number) $features[] = "Model Number: {$this->model_number}";
            if ($this->make_id)      $features[] = 'Make: ' . (Make::find($this->make_id)?->name ?? '');
            if ($this->brand_id)     $features[] = 'Brand: ' . (Brand::find($this->brand_id)?->name ?? '');

            $categoryName = Category::find($this->category_id)?->name ?? 'General';

            $description = $this->openAIService->generateDescription($this->name, $categoryName, $features);

            if ($description) {
                $this->description = $description;
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Description generated successfully!']);
            } else {
                $this->fallbackDescription();
            }
        } catch (\Exception $e) {
            Log::error('AI Description Generation Error: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'AI service error: ' . $e->getMessage()]);
            $this->fallbackDescription();
        }

        $this->isLoading = false;
    }

    public function generateSeoKeywords()
    {
        if (!$this->name) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please enter product name first.']);
            return;
        }

        $this->isLoading = true;

        try {
            $categoryName = Category::find($this->category_id)?->name ?? 'General';

            $keywords = $this->openAIService->generateSeoKeywords(
                $this->name,
                $categoryName,
                $this->description ?? 'No description available'
            );

            if (!empty($keywords)) {
                $this->seoKeywords = $keywords;
                $this->tags        = implode(', ', array_slice($keywords, 0, 5));
                $this->dispatch('notify', ['type' => 'success', 'message' => 'SEO keywords generated and added to tags!']);
            }
        } catch (\Exception $e) {
            Log::error('SEO Keywords Generation Error: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Could not generate SEO keywords.']);
        }

        $this->isLoading = false;
    }

    public function getAiPricingSuggestion()
    {
        if (!$this->name || !$this->unit_price) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please enter product name and unit price first.']);
            return;
        }

        $this->isLoading = true;

        try {
            $categoryName    = Category::find($this->category_id)?->name ?? 'General';
            $competitorPrices = $this->getCompetitorPrices($categoryName);

            $suggestion = $this->openAIService->suggestPrice(
                $this->name,
                $categoryName,
                $this->unit_price,
                $competitorPrices
            );

            if ($suggestion && isset($suggestion['suggested_price'])) {
                $this->aiSuggestion = $suggestion;
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Price suggestion received!']);
            } else {
                $this->fallbackPriceSuggestion();
            }
        } catch (\Exception $e) {
            Log::error('AI Price Suggestion Error: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'AI service error: ' . $e->getMessage()]);
            $this->fallbackPriceSuggestion();
        }

        $this->isLoading = false;
    }

    public function predictDemand()
    {
        if (!$this->name) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please enter product name first.']);
            return;
        }

        $this->isLoading = true;

        try {
            $categoryName = Category::find($this->category_id)?->name ?? 'General';
            $prediction   = $this->openAIService->predictDemand($this->name, $categoryName, [], $this->getCurrentSeason());

            if ($prediction) {
                $this->dispatch('notify', ['type' => 'info', 'message' => 'Demand prediction: ' . $prediction]);
            }
        } catch (\Exception $e) {
            Log::error('Demand Prediction Error: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Could not generate demand prediction.']);
        }

        $this->isLoading = false;
    }

    public function applyAiSuggestion()
    {
        if ($this->aiSuggestion && isset($this->aiSuggestion['suggested_price'])) {
            $this->selling_price = $this->aiSuggestion['suggested_price'];
            $this->aiSuggestion  = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Price applied successfully!']);
        }
    }

    private function getCompetitorPrices($categoryName)
    {
        $similarProducts = Stock::where('category_id', $this->category_id)
            ->when($this->stock_id, fn($q) => $q->where('id', '!=', $this->stock_id))
            ->whereNotNull('selling_price')
            ->limit(5)
            ->pluck('selling_price')
            ->toArray();

        if (!empty($similarProducts)) {
            return array_map(fn($p) => 'Rs ' . number_format($p, 2), $similarProducts);
        }

        return ['Rs 99.99', 'Rs 129.99', 'Rs 79.99'];
    }

    private function getCurrentSeason()
    {
        $month = (int) date('n');
        if ($month >= 3 && $month <= 5)  return 'Spring';
        if ($month >= 6 && $month <= 8)  return 'Summer';
        if ($month >= 9 && $month <= 11) return 'Fall';
        return 'Winter';
    }

    private function fallbackDescription()
    {
        $make     = $this->make_id     ? (Make::find($this->make_id)?->name     ?? 'Premium') : 'Premium';
        $brand    = $this->brand_id    ? (Brand::find($this->brand_id)?->name   ?? 'Quality') : 'Quality';
        $category = $this->category_id ? (Category::find($this->category_id)?->name ?? 'product') : 'product';

        $features = array_filter([
            $this->model_name,
            $this->color,
            $this->size  ? "size {$this->size}"     : null,
            $this->weight ? "weight {$this->weight}kg" : null,
        ]);

        $featuresText = !empty($features) ? ' with ' . implode(', ', $features) : '';

        $this->description =
            "The {$make} {$brand} {$this->name} is a high-quality {$category}{$featuresText}. " .
            "Designed for optimal performance and durability, this product meets the highest standards of quality. " .
            "Perfect for both professional and personal use, it delivers exceptional value and reliability.";

        $this->dispatch('notify', ['type' => 'info', 'message' => 'Template description generated.']);
    }

    private function fallbackPriceSuggestion()
    {
        $markup = rand(30, 60) / 100;

        $this->aiSuggestion = [
            'suggested_price'  => round($this->unit_price * (1 + $markup), 2),
            'confidence_score' => rand(70, 85) . '%',
            'market_trend'     => rand(0, 1) ? 'upward' : 'stable',
            'reasoning'        => 'Based on typical markup percentages (fallback — AI service unavailable).',
        ];
    }

    // -------------------------------------------------------------------------
    // Computed Properties
    // -------------------------------------------------------------------------

    public function getLowStockCountProperty()
    {
        return Stock::whereColumn('quantity', '<=', 'reorder_level')->count();
    }

    public function getTotalValueProperty()
    {
        return Stock::selectRaw('SUM(unit_price * quantity) as total')->value('total') ?? 0;
    }

    public function getMovementSummaryProperty(): array
    {
        return [
            'today_out' => StockMovementLog::whereDate('created_at', today())
                ->where('direction', 'out')
                ->sum('quantity'),
            'today_in' => StockMovementLog::whereDate('created_at', today())
                ->where('direction', 'in')
                ->sum('quantity'),
            'reversals' => StockMovementLog::whereDate('created_at', today())
                ->whereIn('context', ['order_cancelled', 'invoice_cancelled', 'refunded', 'returned'])
                ->count(),
            'movements' => StockMovementLog::whereDate('created_at', today())->count(),
        ];
    }

    public function getInventoryQuickCountsProperty(): array
    {
        return [
            'all' => Stock::count(),
            'low_stock' => Stock::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'out_of_stock' => Stock::where('quantity', '<=', 0)->count(),
            'active' => Stock::where('status', 'active')->count(),
            'inactive' => Stock::where('status', 'inactive')->count(),
            'discontinued' => Stock::where('status', 'discontinued')->count(),
        ];
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    protected function getStocksQuery()
    {
        return Stock::query()
            ->with(['category', 'make', 'brand', 'itemType', 'supplier', 'warranty', 'qualityLevel'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name',         'like', '%' . $this->search . '%')
                       ->orWhere('sku',         'like', '%' . $this->search . '%')
                       ->orWhere('item_code',   'like', '%' . $this->search . '%')
                       ->orWhere('model_name',  'like', '%' . $this->search . '%')
                       ->orWhere('model_number','like', '%' . $this->search . '%')
                       ->orWhere('barcode',     'like', '%' . $this->search . '%')
                       ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->when($this->selectedMake,     fn($q) => $q->where('make_id',     $this->selectedMake))
            ->when($this->selectedBrand,    fn($q) => $q->where('brand_id',    $this->selectedBrand))
            ->when($this->selectedSupplier, fn($q) => $q->where('supplier_id', $this->selectedSupplier))
            ->when($this->inventoryQuickFilter === 'low_stock', fn($q) => $q->whereColumn('quantity', '<=', 'reorder_level'))
            ->when($this->inventoryQuickFilter === 'out_of_stock', fn($q) => $q->where('quantity', '<=', 0))
            ->when($this->inventoryQuickFilter === 'active', fn($q) => $q->where('status', 'active'))
            ->when($this->inventoryQuickFilter === 'inactive', fn($q) => $q->where('status', 'inactive'))
            ->when($this->inventoryQuickFilter === 'discontinued', fn($q) => $q->where('status', 'discontinued'))
            ->when($this->showLowStockOnly, fn($q) => $q->whereColumn('quantity', '<=', 'reorder_level'));
    }

    public function render()
    {
        $stocks = $this->getStocksQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->loadQualityLevels();

        return view('livewire.stock-manager', [
            'stocks'        => $stocks,
            'categories'    => Category::all(),
            'makes'         => Make::all(),
            'brands'        => Brand::all(),
            'itemTypes'     => ItemType::all(),
            'suppliers'     => Supplier::all(),
            'warranties'    => Warranty::all(),
            'qualityLevels' => $this->qualityLevels,
            'movementSummary' => $this->movementSummary,
            'inventoryQuickCounts' => $this->inventoryQuickCounts,
            'selectedLabelStocks' => $this->selectedLabelStocks,
            'recentMovements' => StockMovementLog::with(['stock', 'user'])
                ->latest()
                ->take(5)
                ->get(),
        ])->layout('layouts.admin');
    }

    // -------------------------------------------------------------------------
    // Modal
    // -------------------------------------------------------------------------

    public function openModal()
    {
        if (!$this->stock_id) {
            $this->resetInputFields();
        }

        $this->stockWorkflowMode = $this->stock_id ? 'edit' : 'create';
        if ($this->stock_id) {
            $this->entryMode = 'advanced';
        } elseif (!in_array($this->entryMode, ['quick', 'advanced'], true)) {
            $this->entryMode = 'quick';
        }
        $this->isOpen = true;
        $this->resetValidation();
        $this->loadQualityLevels();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->stockWorkflowMode = 'create';
        $this->resetInputFields();
    }

    public function openRestockModal($id)
    {
        $stock = Stock::findOrFail($id);

        $this->resetValidation();
        $this->restockStockId = $stock->id;
        $this->restockProductName = $stock->name;
        $this->restockCurrentQuantity = (int) $stock->quantity;
        $this->restockReorderLevel = (int) $stock->reorder_level;
        $this->restockQuantity = max(1, (int) ($stock->reorder_level ?: 1));
        $this->restockUnitCost = $stock->unit_price;
        $this->restockNotes = '';
        $this->isRestockOpen = true;
    }

    public function closeRestockModal()
    {
        $this->isRestockOpen = false;
        $this->restockStockId = null;
        $this->restockProductName = '';
        $this->restockCurrentQuantity = 0;
        $this->restockReorderLevel = 0;
        $this->restockQuantity = 1;
        $this->restockUnitCost = null;
        $this->restockNotes = '';
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->stock_id     = null;
        $this->name         = '';
        $this->description  = '';
        $this->category_id  = '';
        $this->make_id      = '';
        $this->brand_id     = '';
        $this->item_type_id = '';
        $this->supplier_id  = '';
        $this->warranty_id  = '';
        $this->quantity     = '';
        $this->reorder_level = 10;           // FIX: reset to default
        $this->unit_price   = '';
        $this->selling_price = '';
        $this->location     = 'Main Warehouse'; // FIX: reset to default
        $this->barcode      = '';
        $this->status       = 'active';      // FIX: reset to default
        $this->model_name   = '';
        $this->model_number = '';
        $this->color        = '';
        $this->size         = '';
        $this->weight       = '';
        $this->specifications = [];
        $this->tags         = '';
        $this->notes        = '';
        $this->tempImages   = [];
        $this->currentImages = [];
        $this->tempVideos   = [];
        $this->currentVideos = [];
        $this->aiSuggestion = null;
        $this->seoKeywords  = [];
        $this->suggestedModelNumbers  = [];
        $this->showModelSuggestions   = false;
        $this->selectedModelSuggestion = '';
        $this->modelSearchQuery        = '';

        // New fields
        $this->quality_level          = '';
        $this->enableTargetCategory   = false;
        $this->target_category_id     = '';
        $this->target_item_type_id    = '';
        $this->target_make_id         = '';
        $this->target_brand_id        = '';
        $this->target_model           = '';
        $this->target_model_number    = '';
        $this->wholesale_price        = '';
        $this->scanCode               = '';
        $this->scanMode               = 'open_or_create';
        $this->selectedStockIds       = [];
        $this->stockWorkspaceTab      = 'inventory';
        $this->saveAndAddAnother      = false;
        $this->stockWorkflowMode      = 'create';
        $this->entryMode              = 'quick';
        $this->stockFormStep          = 'catalog';
        $this->showQuickSetup         = true;
        $this->quickCategoryName      = '';
        $this->quickMakeName          = '';
        $this->quickBrandName         = '';
        $this->quickSupplierName      = '';
        $this->quickItemTypeName      = '';
        $this->aiDemandInsight        = null;

        $this->generateSku();
        $this->generateItemCode();
    }

    // -------------------------------------------------------------------------
    // CRUD
    // -------------------------------------------------------------------------

    public function getMarginAmountProperty(): float
    {
        $selling = (float) ($this->selling_price ?: 0);
        $cost = (float) ($this->unit_price ?: 0);

        return round($selling - $cost, 2);
    }

    public function getMarginPercentProperty(): float
    {
        $cost = (float) ($this->unit_price ?: 0);

        if ($cost <= 0) {
            return 0.0;
        }

        return round(($this->marginAmount / $cost) * 100, 1);
    }

    public function getProjectedStockHealthProperty(): string
    {
        $quantity = (int) ($this->quantity ?: 0);
        $reorderLevel = (int) ($this->reorder_level ?: 0);

        if ($quantity <= 0) {
            return 'Out of stock on save';
        }

        if ($quantity <= $reorderLevel) {
            return 'Low stock warning on save';
        }

        return 'Healthy stock level';
    }

    public function getQuantityDifferenceProperty(): int
    {
        if (!$this->stock_id) {
            return (int) ($this->quantity ?: 0);
        }

        $stock = Stock::find($this->stock_id);

        if (!$stock) {
            return 0;
        }

        return (int) ($this->quantity ?: 0) - (int) $stock->quantity;
    }

    public function getSelectedLabelStocksProperty()
    {
        if (empty($this->selectedStockIds)) {
            return collect();
        }

        return Stock::query()
            ->whereIn('id', $this->selectedStockIds)
            ->orderBy('name')
            ->get();
    }

    public function processScan(): void
    {
        $code = trim($this->scanCode);

        if ($code === '') {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Scan or enter a barcode, SKU, or item code first.']);
            return;
        }

        $stock = Stock::query()
            ->where('barcode', $code)
            ->orWhere('sku', $code)
            ->orWhere('item_code', $code)
            ->first();

        if ($stock && $this->scanMode !== 'create_only') {
            $this->edit($stock->id);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Existing stock found and opened from scan.']);
            return;
        }

        if ($stock && $this->scanMode === 'create_only') {
            $this->edit($stock->id);
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Matching stock already exists, so the existing item was opened to avoid duplicate barcode conflicts.']);
            return;
        }

        $this->startQuickIntakeFromScan($code);
    }

    protected function startQuickIntakeFromScan(string $code): void
    {
        $this->resetInputFields();
        $this->openModal();
        $this->entryMode = 'quick';
        $this->applyScannedCodeToDraft($code);

        $this->dispatch('notify', ['type' => 'info', 'message' => 'No existing stock matched. Quick intake started with the scanned code.']);
    }

    protected function applyScannedCodeToDraft(string $code): void
    {
        $normalized = strtoupper($code);

        if (Str::startsWith($normalized, 'SKU-')) {
            $this->sku = $code;
            return;
        }

        if (Str::startsWith($normalized, 'ITM-')) {
            $this->item_code = $code;
            return;
        }

        $this->barcode = $code;
    }

    public function clearScan(): void
    {
        $this->scanCode = '';
    }

    public function setInventoryQuickFilter(string $filter): void
    {
        $allowed = ['all', 'low_stock', 'out_of_stock', 'active', 'inactive', 'discontinued'];
        $this->inventoryQuickFilter = in_array($filter, $allowed, true) ? $filter : 'all';
        $this->resetPage();
    }

    public function toggleCompactTableMode(): void
    {
        $this->compactTableMode = !$this->compactTableMode;
    }

    public function resetInventoryBoard(): void
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->selectedMake = '';
        $this->selectedBrand = '';
        $this->selectedSupplier = '';
        $this->scanCode = '';
        $this->showLowStockOnly = false;
        $this->inventoryQuickFilter = 'all';
        $this->selectedStockIds = [];
        $this->resetPage();
    }

    public function setStockWorkspaceTab(string $tab): void
    {
        $allowed = ['inventory', 'intake', 'structure'];
        $this->stockWorkspaceTab = in_array($tab, $allowed, true) ? $tab : 'inventory';
    }

    public function startQuickIntake(): void
    {
        $this->resetInputFields();
        $this->stockWorkspaceTab = 'intake';
        $this->entryMode = 'quick';
        $this->stockFormStep = 'catalog';
        $this->openModal();
    }

    public function startAdvancedIntake(): void
    {
        $this->resetInputFields();
        $this->stockWorkspaceTab = 'intake';
        $this->entryMode = 'advanced';
        $this->stockFormStep = 'catalog';
        $this->openModal();
    }

    public function selectVisibleLabels(): void
    {
        $ids = $this->getStocksQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->forPage($this->getPage(), (int) $this->perPage)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $this->selectedStockIds = $ids;

        $this->dispatch('notify', [
            'type' => empty($ids) ? 'warning' : 'success',
            'message' => empty($ids)
                ? 'No stock items are available on this page to label.'
                : count($ids).' stock items selected for label printing.',
        ]);
    }

    public function clearSelectedLabels(): void
    {
        $this->selectedStockIds = [];
    }

    public function quickRestock(int $id, int $amount, StockMovementService $stockMovementService): void
    {
        $amount = max(1, $amount);
        $stock = Stock::findOrFail($id);

        $stockMovementService->increase(
            (int) $stock->id,
            $amount,
            'quick_restock',
            [
                'user_id' => auth()->id(),
                'notes' => 'Quick restock from inventory board.',
            ]
        );

        session()->flash('message', "{$stock->name} was restocked by {$amount} unit(s).");
    }

    public function restockToReorderLevel(int $id, StockMovementService $stockMovementService): void
    {
        $stock = Stock::findOrFail($id);
        $needed = max(1, (int) $stock->reorder_level - (int) $stock->quantity);

        $stockMovementService->increase(
            (int) $stock->id,
            $needed,
            'quick_restock',
            [
                'user_id' => auth()->id(),
                'notes' => 'Quick restock to reorder level from inventory board.',
            ]
        );

        session()->flash('message', "{$stock->name} was topped up by {$needed} unit(s) to reach the reorder level.");
    }

    public function printSelectedLabels(): void
    {
        if (empty($this->selectedStockIds)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Select at least one stock item before printing labels.']);
            return;
        }

        $this->dispatch('print-stock-label-sheet');
    }

    public function printRowLabel(int $id): void
    {
        $this->selectedStockIds = [$id];
        $this->dispatch('print-stock-label-sheet');
    }

    public function setEntryMode(string $mode): void
    {
        $this->entryMode = in_array($mode, ['quick', 'advanced'], true) ? $mode : 'quick';
    }

    public function setStockFormStep(string $step): void
    {
        $allowed = ['catalog', 'inventory', 'media', 'review'];
        $this->stockFormStep = in_array($step, $allowed, true) ? $step : 'catalog';
    }

    public function nextStockFormStep(): void
    {
        $steps = ['catalog', 'inventory', 'media', 'review'];
        $currentIndex = array_search($this->stockFormStep, $steps, true);
        $nextIndex = $currentIndex === false ? 0 : min($currentIndex + 1, count($steps) - 1);

        $this->stockFormStep = $steps[$nextIndex];
    }

    public function previousStockFormStep(): void
    {
        $steps = ['catalog', 'inventory', 'media', 'review'];
        $currentIndex = array_search($this->stockFormStep, $steps, true);
        $previousIndex = $currentIndex === false ? 0 : max($currentIndex - 1, 0);

        $this->stockFormStep = $steps[$previousIndex];
    }

    public function toggleQuickSetup(): void
    {
        $this->showQuickSetup = !$this->showQuickSetup;
    }

    public function runAiIntakeAssist(): void
    {
        if (!$this->name) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Enter a product name first so AI can help with intake.']);
            return;
        }

        $this->isLoading = true;

        try {
            if (!$this->description) {
                $this->generateAiDescription();
            }

            if (!$this->tags) {
                $this->generateSeoKeywords();
            }

            if ($this->brand_id && $this->model_name && empty($this->suggestedModelNumbers)) {
                $this->getModelSuggestions();
            }

            if ($this->unit_price && !$this->aiSuggestion) {
                $this->getAiPricingSuggestion();
            }

            $categoryName = Category::find($this->category_id)?->name ?? 'General';
            $this->aiDemandInsight = $this->openAIService->predictDemand($this->name, $categoryName, [], $this->getCurrentSeason());

            $this->dispatch('notify', ['type' => 'success', 'message' => 'AI intake assist prepared description, pricing, tags, and demand guidance.']);
        } catch (\Throwable $e) {
            Log::warning('AI intake assist failed: '.$e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'AI intake assist could not finish completely.']);
        }

        $this->isLoading = false;
    }

    public function quickCreateCategory(): void
    {
        $this->category_id = $this->quickCreateLookup(Category::class, $this->quickCategoryName, 'quickCategoryName');
    }

    public function quickCreateMake(): void
    {
        $this->make_id = $this->quickCreateLookup(Make::class, $this->quickMakeName, 'quickMakeName');
    }

    public function quickCreateBrand(): void
    {
        $this->brand_id = $this->quickCreateLookup(Brand::class, $this->quickBrandName, 'quickBrandName');
    }

    public function quickCreateSupplier(): void
    {
        $this->supplier_id = $this->quickCreateLookup(Supplier::class, $this->quickSupplierName, 'quickSupplierName');
    }

    public function quickCreateItemType(): void
    {
        $this->item_type_id = $this->quickCreateLookup(ItemType::class, $this->quickItemTypeName, 'quickItemTypeName');
    }

    protected function quickCreateLookup(string $modelClass, string $name, string $property): ?int
    {
        $name = trim($name);

        if ($name === '') {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Enter a name first before creating a quick setup item.']);
            return null;
        }

        try {
            $record = $modelClass::firstOrCreate(
                ['name' => $name],
                $this->buildQuickCreateDefaults($modelClass, $name)
            );

            $this->{$property} = '';
            $this->dispatch('notify', ['type' => 'success', 'message' => $record->name.' is ready to use.']);

            return (int) $record->id;
        } catch (\Throwable $e) {
            Log::warning('Quick stock setup creation failed.', [
                'model' => $modelClass,
                'name' => $name,
                'message' => $e->getMessage(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'That setup item could not be created right now. Please try again or complete it from the full management page.',
            ]);

            return null;
        }
    }

    protected function buildQuickCreateDefaults(string $modelClass, string $name): array
    {
        return match ($modelClass) {
            Category::class => ['slug' => Str::slug($name)],
            Brand::class => ['slug' => Str::slug($name), 'status' => 'active'],
            Make::class => ['code' => strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 4)), 'is_active' => true],
            ItemType::class => ['slug' => Str::slug($name), 'status' => 'active'],
            Supplier::class => [
                'email' => $this->generateQuickSupplierEmail($name),
                'company' => $name,
                'contact_person' => $name,
                'status' => 'active',
            ],
            default => [],
        };
    }

    protected function generateQuickSupplierEmail(string $name): string
    {
        $slug = Str::slug($name);
        $slug = $slug !== '' ? $slug : 'supplier';
        $base = Str::limit($slug, 40, '');
        $email = $base.'@placeholder.stock-ai.local';
        $counter = 1;

        while (Supplier::where('email', $email)->exists()) {
            $email = $base.'-'.$counter.'@placeholder.stock-ai.local';
            $counter++;
        }

        return $email;
    }

    public function printBarcode(): void
    {
        if (!$this->barcode) {
            $this->generateBarcode();
        }

        if (!$this->barcode) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Generate a barcode first.']);
            return;
        }

        $this->dispatch('print-stock-barcode');
    }

    public function store(StockMovementService $stockMovementService)
    {
        // FIX: validate() now calls rules() method — no manual mutation needed
        $this->validate();

        $imagePaths = array_values($this->currentImages);
        foreach ($this->tempImages as $image) {
            $imagePaths[] = $this->storeOptimizedImage($image);
        }

        $videoPaths = array_values($this->currentVideos);
        foreach ($this->tempVideos as $video) {
            $videoPaths[] = $video->store('stock-videos', 'public');
        }

        $targetQuantity = max(0, (int) $this->quantity);
        $isUpdate = filled($this->stock_id);

        DB::transaction(function () use ($stockMovementService, $imagePaths, $videoPaths, $targetQuantity, $isUpdate) {
            if ($isUpdate) {
                $stock = Stock::query()->lockForUpdate()->findOrFail($this->stock_id);
                $originalQuantity = (int) $stock->quantity;

                $stock->update($this->buildStockPayload($imagePaths, $videoPaths, $originalQuantity));

                $difference = $targetQuantity - $originalQuantity;

                if ($difference > 0) {
                    $stockMovementService->increase((int) $stock->id, $difference, 'manual_adjustment', [
                        'user_id' => auth()->id(),
                        'notes' => 'Quantity increased from stock management workspace.',
                    ]);
                } elseif ($difference < 0) {
                    $stockMovementService->decrease((int) $stock->id, abs($difference), 'manual_adjustment', [
                        'user_id' => auth()->id(),
                        'notes' => 'Quantity reduced from stock management workspace.',
                    ]);
                }
            } else {
                $stock = Stock::create($this->buildStockPayload($imagePaths, $videoPaths, 0));

                if ($targetQuantity > 0) {
                    $stockMovementService->increase((int) $stock->id, $targetQuantity, 'opening_balance', [
                        'user_id' => auth()->id(),
                        'notes' => 'Opening stock recorded during item creation.',
                    ]);
                }
            }
        });

        session()->flash('message', $isUpdate ? 'Stock updated with quantity changes tracked in the stock ledger.' : 'Stock item created and opening balance recorded.');

        if ($this->saveAndAddAnother && !$isUpdate) {
            $this->resetInputFields();
            $this->isOpen = true;
            $this->loadQualityLevels();
            return;
        }

        $this->closeModal();
    }

    protected function storeOptimizedImage($image): string
    {
        $sourcePath = $image->getRealPath();
        $mime = strtolower((string) $image->getMimeType());

        $resource = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/gif' => @imagecreatefromgif($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
            default => null,
        };

        if (!$resource) {
            return $image->store('stock-images', 'public');
        }

        $width = imagesx($resource);
        $height = imagesy($resource);
        $ratio = min(1800 / max($width, 1), 1800 / max($height, 1), 1);
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagecopyresampled($canvas, $resource, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $path = 'stock-images/' . Str::uuid() . '.webp';

        ob_start();
        if (function_exists('imagewebp')) {
            imagewebp($canvas, null, 82);
        } else {
            imagejpeg($canvas, null, 84);
            $path = 'stock-images/' . Str::uuid() . '.jpg';
        }
        $binary = ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($resource);

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    protected function buildStockPayload(array $imagePaths, array $videoPaths, int $quantity): array
    {
        return [
            'sku' => $this->sku,
            'item_code' => $this->item_code,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'make_id' => $this->make_id,
            'brand_id' => $this->brand_id,
            'item_type_id' => $this->item_type_id ?: null,
            'supplier_id' => $this->supplier_id,
            'warranty_id' => $this->warranty_id ?: null,
            'quantity' => $quantity,
            'reorder_level' => $this->reorder_level,
            'unit_price' => $this->unit_price,
            'selling_price' => $this->selling_price,
            'location' => $this->location,
            'barcode' => $this->barcode ?: null,
            'status' => $this->status,
            'model_name' => $this->model_name,
            'model_number' => $this->model_number,
            'color' => $this->color,
            'size' => $this->size,
            'weight' => $this->weight ?: null,
            'specifications' => $this->specifications,
            'images' => $imagePaths,
            'videos' => $videoPaths,
            'tags' => $this->tags,
            'notes' => $this->notes,
            'quality_level' => $this->quality_level ?: null,
            'target_category_id' => $this->enableTargetCategory ? ($this->target_category_id ?: null) : null,
            'target_item_type_id' => $this->enableTargetCategory ? ($this->target_item_type_id ?: null) : null,
            'target_make_id' => $this->enableTargetCategory ? ($this->target_make_id ?: null) : null,
            'target_brand_id' => $this->enableTargetCategory ? ($this->target_brand_id ?: null) : null,
            'target_model' => $this->enableTargetCategory ? ($this->target_model ?: null) : null,
            'target_model_number' => $this->enableTargetCategory ? ($this->target_model_number ?: null) : null,
            'wholesale_price' => $this->wholesale_price ?: null,
        ];
    }

    public function edit($id)
    {
        $stock = Stock::findOrFail($id);

        $this->stock_id      = $id;
        $this->sku           = $stock->sku;
        $this->item_code     = $stock->item_code;
        $this->name          = $stock->name;
        $this->description   = $stock->description;
        $this->category_id   = $stock->category_id;
        $this->make_id       = $stock->make_id;
        $this->brand_id      = $stock->brand_id;
        $this->item_type_id  = $stock->item_type_id;
        $this->supplier_id   = $stock->supplier_id;
        $this->warranty_id   = $stock->warranty_id;
        $this->quantity      = $stock->quantity;
        $this->reorder_level = $stock->reorder_level ?? 10;
        $this->unit_price    = $stock->unit_price;
        $this->selling_price = $stock->selling_price;
        $this->location      = $stock->location ?? 'Main Warehouse';
        $this->barcode       = $stock->barcode;
        $this->status        = $stock->status ?? 'active';
        $this->model_name    = $stock->model_name;
        $this->model_number  = $stock->model_number;
        $this->color         = $stock->color;
        $this->size          = $stock->size;
        $this->weight        = $stock->weight;
        $this->specifications = $stock->specifications ?? [];
        $this->tags          = $stock->tags;
        $this->notes         = $stock->notes;
        $this->currentImages = $stock->images ?? [];
        $this->currentVideos = $stock->videos ?? [];

        // New fields
        $this->quality_level         = $stock->quality_level;
        $this->enableTargetCategory  = !is_null($stock->target_category_id);
        $this->target_category_id    = $stock->target_category_id;
        $this->target_item_type_id   = $stock->target_item_type_id;
        $this->target_make_id        = $stock->target_make_id;
        $this->target_brand_id       = $stock->target_brand_id;
        $this->target_model          = $stock->target_model;
        $this->target_model_number   = $stock->target_model_number;
        $this->wholesale_price       = $stock->wholesale_price;
        $this->stockWorkflowMode     = 'edit';
        $this->stockFormStep         = 'catalog';
        $this->showQuickSetup        = true;

        $this->loadQualityLevels();
        $this->openModal();
    }

    public function delete($id)
    {
        $stock = Stock::findOrFail($id);

        foreach (($stock->images ?? []) as $imagePath) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        foreach (($stock->videos ?? []) as $videoPath) {
            if ($videoPath && Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
        }

        $stock->delete();
        session()->flash('message', 'Stock deleted successfully.');
    }

    public function processRestock(StockMovementService $stockMovementService)
    {
        $validated = $this->validate([
            'restockStockId' => 'required|exists:stocks,id',
            'restockQuantity' => 'required|integer|min:1',
            'restockUnitCost' => 'nullable|numeric|min:0',
            'restockNotes' => 'nullable|string|max:500',
        ]);

        $stock = Stock::findOrFail($validated['restockStockId']);

        if (!is_null($validated['restockUnitCost']) && $validated['restockUnitCost'] !== '') {
            $stock->update([
                'unit_price' => $validated['restockUnitCost'],
            ]);
        }

        $stockMovementService->increase(
            (int) $stock->id,
            (int) $validated['restockQuantity'],
            'manual_restock',
            [
                'user_id' => auth()->id(),
                'notes' => $validated['restockNotes'] ?: 'Manual restock from stock workspace.',
            ]
        );

        session()->flash('message', "Restocked {$stock->name} with {$validated['restockQuantity']} unit(s).");
        $this->closeRestockModal();
    }

    public function removeCurrentImage($index)
    {
        if (!isset($this->currentImages[$index])) {
            return;
        }

        $path = $this->currentImages[$index];

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        unset($this->currentImages[$index]);
        $this->currentImages = array_values($this->currentImages);

        if ($this->stock_id) {
            $stock = Stock::find($this->stock_id);

            if ($stock) {
                $stock->update([
                    'images' => $this->currentImages,
                ]);
            }
        }

        session()->flash('message', 'Image removed successfully.');
    }

    public function moveCurrentImageUp($index): void
    {
        $this->currentImages = $this->moveMediaItem($this->currentImages, (int) $index, -1);
    }

    public function moveCurrentImageDown($index): void
    {
        $this->currentImages = $this->moveMediaItem($this->currentImages, (int) $index, 1);
    }

    public function moveTempImageUp($index): void
    {
        $this->tempImages = $this->moveMediaItem($this->tempImages, (int) $index, -1);
    }

    public function moveTempImageDown($index): void
    {
        $this->tempImages = $this->moveMediaItem($this->tempImages, (int) $index, 1);
    }

    public function removeTempImage($index): void
    {
        if (!isset($this->tempImages[$index])) {
            return;
        }

        unset($this->tempImages[$index]);
        $this->tempImages = array_values($this->tempImages);
    }

    public function removeCurrentVideo($index)
    {
        if (!isset($this->currentVideos[$index])) {
            return;
        }

        $path = $this->currentVideos[$index];

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        unset($this->currentVideos[$index]);
        $this->currentVideos = array_values($this->currentVideos);

        if ($this->stock_id) {
            $stock = Stock::find($this->stock_id);

            if ($stock) {
                $stock->update([
                    'videos' => $this->currentVideos,
                ]);
            }
        }

        session()->flash('message', 'Video removed successfully.');
    }

    public function moveCurrentVideoUp($index): void
    {
        $this->currentVideos = $this->moveMediaItem($this->currentVideos, (int) $index, -1);
    }

    public function moveCurrentVideoDown($index): void
    {
        $this->currentVideos = $this->moveMediaItem($this->currentVideos, (int) $index, 1);
    }

    public function moveTempVideoUp($index): void
    {
        $this->tempVideos = $this->moveMediaItem($this->tempVideos, (int) $index, -1);
    }

    public function moveTempVideoDown($index): void
    {
        $this->tempVideos = $this->moveMediaItem($this->tempVideos, (int) $index, 1);
    }

    public function removeTempVideo($index): void
    {
        if (!isset($this->tempVideos[$index])) {
            return;
        }

        unset($this->tempVideos[$index]);
        $this->tempVideos = array_values($this->tempVideos);
    }

    protected function moveMediaItem(array $items, int $index, int $direction): array
    {
        $target = $index + $direction;

        if (!isset($items[$index]) || !isset($items[$target])) {
            return array_values($items);
        }

        $current = Arr::pull($items, $index);
        $items = array_values($items);
        array_splice($items, $target, 0, [$current]);

        return array_values($items);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    // -------------------------------------------------------------------------
    // Export Helpers
    // -------------------------------------------------------------------------

    private function getFilteredQuery()
    {
        return Stock::query()
            ->with(['category', 'make', 'brand', 'itemType', 'supplier', 'warranty', 'qualityLevel'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name',         'like', '%' . $this->search . '%')
                       ->orWhere('sku',         'like', '%' . $this->search . '%')
                       ->orWhere('item_code',   'like', '%' . $this->search . '%')
                       ->orWhere('model_name',  'like', '%' . $this->search . '%')
                       ->orWhere('model_number','like', '%' . $this->search . '%')
                       ->orWhere('barcode',     'like', '%' . $this->search . '%')
                       ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->when($this->selectedMake,     fn($q) => $q->where('make_id',     $this->selectedMake))
            ->when($this->selectedSupplier, fn($q) => $q->where('supplier_id', $this->selectedSupplier))
            ->when($this->showLowStockOnly, fn($q) => $q->whereColumn('quantity', '<=', 'reorder_level'))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function exportCsv()
    {
        $stocks = $this->getFilteredQuery()->get();

        $filename = 'stocks-export-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'SKU', 'Item Code', 'Name', 'Make', 'Brand', 'Model Name', 'Model Number',
            'Category', 'Supplier', 'Quantity', 'Unit Price', 'Selling Price',
            'Wholesale Price', 'Status', 'Color', 'Size', 'Weight', 'Tags', 'Quality Level',
        ];

        $callback = function () use ($stocks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->sku,
                    $stock->item_code,
                    $stock->name,
                    $stock->make->name          ?? 'N/A',
                    $stock->brand->name         ?? 'N/A',
                    $stock->model_name          ?? 'N/A',
                    $stock->model_number        ?? 'N/A',
                    $stock->category->name      ?? 'N/A',
                    $stock->supplier->name      ?? 'N/A',
                    $stock->quantity,
                    $stock->unit_price,
                    $stock->selling_price,
                    $stock->wholesale_price     ?? 'N/A',
                    $stock->status,
                    $stock->color               ?? 'N/A',
                    $stock->size                ?? 'N/A',
                    $stock->weight              ?? 'N/A',
                    $stock->tags                ?? 'N/A',
                    $stock->qualityLevel->name  ?? $stock->quality_level ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $stocks = $this->getFilteredQuery()->get();

        $data = [
            'stocks'          => $stocks,
            'date'            => now()->format('Y-m-d H:i:s'),
            'total_value'     => $stocks->sum(fn($s) => $s->unit_price * $s->quantity),
            'low_stock_count' => $stocks->filter(fn($s) => $s->isLowStock())->count(),
        ];

        $pdf = Pdf::loadView('exports.stocks-pdf', $data);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'stocks-export-' . date('Y-m-d-His') . '.pdf'
        );
    }

    public function exportAllPdf()
    {
        $stocks = Stock::with(['category', 'make', 'brand', 'supplier', 'qualityLevel'])->get();

        $data = [
            'stocks'          => $stocks,
            'date'            => now()->format('Y-m-d H:i:s'),
            'total_value'     => $stocks->sum(fn($s) => $s->unit_price * $s->quantity),
            'total_items'     => $stocks->count(),
            'total_quantity'  => $stocks->sum('quantity'),
            'low_stock_count' => $stocks->filter(fn($s) => $s->isLowStock())->count(),
        ];

        $pdf = Pdf::loadView('exports.stocks-all-pdf', $data)->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'all-stocks-export-' . date('Y-m-d-His') . '.pdf'
        );
    }
}
