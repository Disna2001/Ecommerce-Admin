<?php

namespace App\Livewire\Admin\SiteManagement;

use Livewire\Component;
use App\Models\Discount;
use App\Models\Category;
use App\Models\Stock;

class DiscountManager extends Component
{
    public $discount_id;
    public $name                = '';
    public $code                = '';
    public $type                = 'percentage';
    public $value               = '';
    public $min_order_amount    = 0;
    public $max_discount_amount = '';
    public $scope               = 'all';
    public $scope_id            = '';
    public $has_timer           = false;
    public $starts_at           = '';
    public $ends_at             = '';
    public $show_timer_on_site  = true;
    public $timer_label         = 'Sale ends in:';
    public $usage_limit         = '';
    public $is_active           = true;
    public $description         = '';
    public $isOpen              = false;
    public $search              = '';

    protected function rules()
    {
        return [
            'name'                => 'required|string|max:200',
            'code'                => 'nullable|string|max:50|unique:discounts,code,' . ($this->discount_id ?: 'NULL'),
            'type'                => 'required|in:percentage,fixed',
            'value'               => 'required|numeric|min:0.01',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'scope'               => 'required|in:all,category,product',
            'scope_id'            => 'nullable',
            'has_timer'           => 'boolean',
            'starts_at'           => 'nullable|date',
            'ends_at'             => 'nullable|date',
            'show_timer_on_site'  => 'boolean',
            'timer_label'         => 'nullable|string|max:100',
            'usage_limit'         => 'nullable|integer|min:1',
            'is_active'           => 'boolean',
            'description'         => 'nullable|string',
        ];
    }

    public function openModal()
    {
        $this->reset(['discount_id','name','code','type','value','min_order_amount',
                      'max_discount_amount','scope','scope_id','has_timer','starts_at',
                      'ends_at','show_timer_on_site','timer_label','usage_limit',
                      'is_active','description']);
        $this->type              = 'percentage';
        $this->scope             = 'all';
        $this->is_active         = true;
        $this->show_timer_on_site = true;
        $this->timer_label       = 'Sale ends in:';
        $this->min_order_amount  = 0;
        $this->isOpen            = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $d = Discount::findOrFail($id);
        $this->discount_id        = $id;
        $this->name               = $d->name;
        $this->code               = $d->code;
        $this->type               = $d->type;
        $this->value              = $d->value;
        $this->min_order_amount   = $d->min_order_amount;
        $this->max_discount_amount = $d->max_discount_amount;
        $this->scope              = $d->scope;
        $this->scope_id           = $d->scope_id;
        $this->has_timer          = $d->has_timer;
        $this->starts_at          = $d->starts_at?->format('Y-m-d\TH:i');
        $this->ends_at            = $d->ends_at?->format('Y-m-d\TH:i');
        $this->show_timer_on_site = $d->show_timer_on_site;
        $this->timer_label        = $d->timer_label;
        $this->usage_limit        = $d->usage_limit;
        $this->is_active          = $d->is_active;
        $this->description        = $d->description;
        $this->isOpen             = true;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        Discount::updateOrCreate(
            ['id' => $this->discount_id ?: null],
            [
                'name'                => $this->name,
                'code'                => $this->code ?: null,
                'type'                => $this->type,
                'value'               => $this->value,
                'min_order_amount'    => $this->min_order_amount ?: 0,
                'max_discount_amount' => $this->max_discount_amount ?: null,
                'scope'               => $this->scope,
                'scope_id'            => in_array($this->scope, ['category','product']) ? ($this->scope_id ?: null) : null,
                'has_timer'           => $this->has_timer,
                'starts_at'           => $this->starts_at ?: null,
                'ends_at'             => $this->ends_at ?: null,
                'show_timer_on_site'  => $this->show_timer_on_site,
                'timer_label'         => $this->timer_label,
                'usage_limit'         => $this->usage_limit ?: null,
                'is_active'           => $this->is_active,
                'description'         => $this->description,
            ]
        );

        session()->flash('message', $this->discount_id ? 'Discount updated.' : 'Discount created.');
        $this->isOpen = false;
    }

    public function toggleActive($id)
    {
        $d = Discount::findOrFail($id);
        $d->update(['is_active' => !$d->is_active]);
    }

    public function delete($id)
    {
        Discount::findOrFail($id)->delete();
        session()->flash('message', 'Discount deleted.');
    }

    public function generateCode()
    {
        $this->code = strtoupper(\Illuminate\Support\Str::random(8));
    }

    public function render()
    {
        $discounts = Discount::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                                              ->orWhere('code', 'like', '%'.$this->search.'%'))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.site-management.discount-manager', [
            'discounts'  => $discounts,
            'categories' => Category::all(),
            'products'   => Stock::select('id','name','sku')->orderBy('name')->get(),
        ]);
    }
}