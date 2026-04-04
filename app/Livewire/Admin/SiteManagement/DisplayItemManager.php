<?php

namespace App\Livewire\Admin\SiteManagement;

use Livewire\Component;
use App\Models\Stock;
use App\Models\SiteSetting;
use App\Models\Category;

class DisplayItemManager extends Component
{
    public $search            = '';
    public $selectedCategory  = '';
    public $featuredIds       = [];
    public $newArrivalsIds    = [];
    public $dealIds           = [];
    public $featuredSectionTitle   = 'Featured Products';
    public $newArrivalsSectionTitle = 'New Arrivals';
    public $dealsSectionTitle      = 'Best Deals';

    public function mount()
    {
        $this->featuredIds            = (array) (SiteSetting::get('featured_product_ids', []) ?? []);
        $this->newArrivalsIds         = (array) (SiteSetting::get('new_arrivals_ids',     []) ?? []);
        $this->dealIds                = (array) (SiteSetting::get('deal_product_ids',     []) ?? []);
        $this->featuredSectionTitle   = SiteSetting::get('featured_section_title', 'Featured Products');
        $this->newArrivalsSectionTitle = SiteSetting::get('new_arrivals_section_title', 'New Arrivals');
        $this->dealsSectionTitle      = SiteSetting::get('deals_section_title', 'Best Deals');
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

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Display items saved!']);
    }

    public function render()
    {
        $stocks = Stock::query()
            ->with(['category', 'brand'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                                              ->orWhere('sku',  'like', '%'.$this->search.'%'))
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.site-management.display-item-manager', [
            'stocks'     => $stocks,
            'categories' => Category::all(),
        ]);
    }
}