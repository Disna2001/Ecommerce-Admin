<?php

namespace App\Livewire\Admin\Promotions;

use App\Models\AutomatedDiscountRule;
use App\Models\DailyDiscount;
use App\Models\Category;
use App\Models\Brand;
use App\Services\Promotions\DiscountOrchestrator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AutomatedDiscountHub extends Component
{
    use WithPagination;

    // Rule Properties
    public $rule_id;
    public $is_active = true;
    public $min_margin_percent = 10;
    public $max_discount_percent = 30;
    public $daily_items_limit = 10;
    public $rotation_strategy = 'random';
    public $target_categories = [];
    public $target_brands = [];

    // UI State
    public $showConfigModal = false;
    public $isLoading = false;
    public $activeTab = 'insights'; // insights, registry, rules
    public $search = '';

    protected $rules = [
        'is_active' => 'boolean',
        'min_margin_percent' => 'required|numeric|min:0|max:100',
        'max_discount_percent' => 'required|numeric|min:0|max:100',
        'daily_items_limit' => 'required|integer|min:1|max:100',
        'rotation_strategy' => 'required|in:random,slow_moving,overstocked',
        'target_categories' => 'nullable|array',
        'target_brands' => 'nullable|array',
    ];

    public function mount()
    {
        $rule = AutomatedDiscountRule::first();
        if ($rule) {
            $this->rule_id = $rule->id;
            $this->is_active = $rule->is_active;
            $this->min_margin_percent = (float) $rule->min_margin_percent;
            $this->max_discount_percent = (float) $rule->max_discount_percent;
            $this->daily_items_limit = $rule->daily_items_limit;
            $this->rotation_strategy = $rule->rotation_strategy;
            $this->target_categories = $rule->target_categories ?? [];
            $this->target_brands = $rule->target_brands ?? [];
        }
    }

    public function saveRule()
    {
        $this->validate();

        $data = [
            'is_active' => $this->is_active,
            'min_margin_percent' => $this->min_margin_percent,
            'max_discount_percent' => $this->max_discount_percent,
            'daily_items_limit' => $this->daily_items_limit,
            'rotation_strategy' => $this->rotation_strategy,
            'target_categories' => $this->target_categories,
            'target_brands' => $this->target_brands,
        ];

        if ($this->rule_id) {
            AutomatedDiscountRule::find($this->rule_id)->update($data);
        } else {
            $rule = AutomatedDiscountRule::create($data);
            $this->rule_id = $rule->id;
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Automated discount rules synchronized successfully.']);
    }

    public function runOrchestration(DiscountOrchestrator $orchestrator)
    {
        $this->isLoading = true;
        
        try {
            $count = $orchestrator->generateDailyDiscounts();
            $this->dispatch('notify', [
                'type' => $count > 0 ? 'success' : 'info',
                'message' => $count > 0 
                    ? "Successfully synchronized {$count} items to today's discount registry." 
                    : "Orchestration complete. No qualifying items found based on active rules."
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Orchestration failed: ' . $e->getMessage()]);
        }

        $this->isLoading = false;
    }

    public function getDailyStatsProperty()
    {
        return [
            'active_today' => DailyDiscount::where('applied_date', now()->toDateString())->count(),
            'total_value' => DailyDiscount::where('applied_date', now()->toDateString())->sum('original_price'),
            'savings_value' => DailyDiscount::where('applied_date', now()->toDateString())->sum(DB::raw('original_price - discounted_price')),
            'avg_discount' => DailyDiscount::where('applied_date', now()->toDateString())->avg('discount_percent') ?? 0,
        ];
    }

    public function render()
    {
        $dailyDiscounts = DailyDiscount::with('stock')
            ->where('applied_date', now()->toDateString())
            ->when($this->search, function($q) {
                $q->whereHas('stock', function($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.promotions.automated-discount-hub', [
            'dailyDiscounts' => $dailyDiscounts,
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'dailyStats' => $this->dailyStats,
        ])->layout('layouts.admin');
    }
}
