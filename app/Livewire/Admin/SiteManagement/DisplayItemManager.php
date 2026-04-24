<?php

namespace App\Livewire\Admin\SiteManagement;

use Livewire\Component;
use App\Models\Stock;
use App\Models\SiteSetting;
use App\Models\Category;
use App\Services\Inventory\StockMovementService;

class DisplayItemManager extends Component
{
    public $search            = '';
    public $selectedCategory  = '';
    public $inventoryFilter   = 'all';
    public $featuredIds       = [];
    public $newArrivalsIds    = [];
    public $dealIds           = [];
    public $featuredSectionTitle   = 'Featured Products';
    public $newArrivalsSectionTitle = 'New Arrivals';
    public $dealsSectionTitle      = 'Best Deals';
    public $featuredSectionSubtitle = 'High-conviction picks for the homepage.';
    public $newArrivalsSectionSubtitle = 'Fresh products customers should notice first.';
    public $dealsSectionSubtitle = 'Price-led products with the strongest promo story.';
    public $railLayout = 'immersive';
    public $showRailQuantity = true;
    public $showRailStockStatus = true;
    public $productsPerRail = 8;

    public function mount()
    {
        $this->featuredIds            = (array) (SiteSetting::get('featured_product_ids', []) ?? []);
        $this->newArrivalsIds         = (array) (SiteSetting::get('new_arrivals_ids',     []) ?? []);
        $this->dealIds                = (array) (SiteSetting::get('deal_product_ids',     []) ?? []);
        $this->featuredSectionTitle   = SiteSetting::get('featured_section_title', 'Featured Products');
        $this->newArrivalsSectionTitle = SiteSetting::get('new_arrivals_section_title', 'New Arrivals');
        $this->dealsSectionTitle      = SiteSetting::get('deals_section_title', 'Best Deals');
        $this->featuredSectionSubtitle = SiteSetting::get('featured_section_subtitle', 'High-conviction picks for the homepage.');
        $this->newArrivalsSectionSubtitle = SiteSetting::get('new_arrivals_section_subtitle', 'Fresh products customers should notice first.');
        $this->dealsSectionSubtitle = SiteSetting::get('deals_section_subtitle', 'Price-led products with the strongest promo story.');
        $this->railLayout = SiteSetting::get('rail_layout', 'immersive');
        $this->showRailQuantity = SiteSetting::get('show_rail_quantity', true);
        $this->showRailStockStatus = SiteSetting::get('show_rail_stock_status', true);
        $this->productsPerRail = (int) SiteSetting::get('products_per_rail', 8);
    }

    public function toggleFeatured($id)
    {
        if (in_array($id, $this->featuredIds)) {
            $this->featuredIds = array_values(array_diff($this->featuredIds, [$id]));
        } else {
            $this->featuredIds[] = $id;
        }
    }

    public function toggleNewArrival($id)
    {
        if (in_array($id, $this->newArrivalsIds)) {
            $this->newArrivalsIds = array_values(array_diff($this->newArrivalsIds, [$id]));
        } else {
            $this->newArrivalsIds[] = $id;
        }
    }

    public function toggleDeal($id)
    {
        if (in_array($id, $this->dealIds)) {
            $this->dealIds = array_values(array_diff($this->dealIds, [$id]));
        } else {
            $this->dealIds[] = $id;
        }
    }

    public function save()
    {
        SiteSetting::set('featured_product_ids',       json_encode($this->featuredIds),    'json', 'display');
        SiteSetting::set('new_arrivals_ids',            json_encode($this->newArrivalsIds), 'json', 'display');
        SiteSetting::set('deal_product_ids',            json_encode($this->dealIds),        'json', 'display');
        SiteSetting::set('featured_section_title',      $this->featuredSectionTitle,        'text', 'display');
        SiteSetting::set('new_arrivals_section_title',  $this->newArrivalsSectionTitle,     'text', 'display');
        SiteSetting::set('deals_section_title',         $this->dealsSectionTitle,           'text', 'display');
        SiteSetting::set('featured_section_subtitle',      $this->featuredSectionSubtitle,        'text', 'display');
        SiteSetting::set('new_arrivals_section_subtitle',  $this->newArrivalsSectionSubtitle,     'text', 'display');
        SiteSetting::set('deals_section_subtitle',         $this->dealsSectionSubtitle,           'text', 'display');
        SiteSetting::set('rail_layout', $this->railLayout, 'text', 'display');
        SiteSetting::set('show_rail_quantity', $this->showRailQuantity ? '1' : '0', 'boolean', 'display');
        SiteSetting::set('show_rail_stock_status', $this->showRailStockStatus ? '1' : '0', 'boolean', 'display');
        SiteSetting::set('products_per_rail', max(4, min(12, (int) $this->productsPerRail)), 'text', 'display');

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Display items saved!']);
    }

    public function adjustQuantity(int $stockId, int $amount, StockMovementService $stockMovementService): void
    {
        $stock = Stock::findOrFail($stockId);

        if ($amount === 0) {
            return;
        }

        if ($amount > 0) {
            $stockMovementService->increase($stockId, $amount, 'display_manager_adjustment', [
                'user_id' => auth()->id(),
                'notes' => 'Quantity increased from storefront display manager.',
            ]);
        } else {
            if ($stock->quantity < abs($amount)) {
                $this->dispatch('notify', ['type' => 'warning', 'message' => 'Cannot reduce below zero for '.$stock->name.'.']);
                return;
            }

            $stockMovementService->decrease($stockId, abs($amount), 'display_manager_adjustment', [
                'user_id' => auth()->id(),
                'notes' => 'Quantity reduced from storefront display manager.',
            ]);
        }
    }

    public function render()
    {
        $stocks = Stock::query()
            ->with(['category', 'brand'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                                              ->orWhere('sku',  'like', '%'.$this->search.'%'))
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->when($this->inventoryFilter === 'low_stock', fn($q) => $q->whereColumn('quantity', '<=', 'reorder_level'))
            ->when($this->inventoryFilter === 'out_of_stock', fn($q) => $q->where('quantity', '<=', 0))
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.site-management.display-item-manager', [
            'stocks'     => $stocks,
            'categories' => Category::all(),
            'displayStats' => [
                'featured' => count($this->featuredIds),
                'new' => count($this->newArrivalsIds),
                'deals' => count($this->dealIds),
                'low_stock' => Stock::query()->where('status', 'active')->whereColumn('quantity', '<=', 'reorder_level')->count(),
            ],
        ]);
    }
}
