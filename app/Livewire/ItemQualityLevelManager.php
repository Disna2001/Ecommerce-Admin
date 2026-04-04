<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ItemQualityLevel;

class ItemQualityLevelManager extends Component
{
    use WithPagination;

    public $quality_id;
    public $name;
    public $code;
    public $description;
    public $level_order;
    public $color;
    public $icon;
    public $is_active = true;

    public $isOpen = false;
    public $search = '';
    public $sortField = 'level_order';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:item_quality_levels,code',
        'description' => 'nullable|string',
        'level_order' => 'required|integer|min:0',
        'color' => 'nullable|string|max:7',
        'icon' => 'nullable|string|max:50',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $qualities = ItemQualityLevel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.item-quality-level-manager', [
            'qualities' => $qualities
        ])->layout('layouts.admin');
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->quality_id = '';
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->level_order = '';
        $this->color = '#6B7280';
        $this->icon = '';
        $this->is_active = true;
    }

    public function store()
    {
        if ($this->quality_id) {
            $this->rules['code'] = 'required|string|max:50|unique:item_quality_levels,code,' . $this->quality_id;
        }

        $this->validate();

        ItemQualityLevel::updateOrCreate(['id' => $this->quality_id], [
            'name' => $this->name,
            'code' => strtolower($this->code),
            'description' => $this->description,
            'level_order' => $this->level_order,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->quality_id ? 'Quality level updated successfully.' : 'Quality level created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $quality = ItemQualityLevel::findOrFail($id);
        $this->quality_id = $id;
        $this->name = $quality->name;
        $this->code = $quality->code;
        $this->description = $quality->description;
        $this->level_order = $quality->level_order;
        $this->color = $quality->color;
        $this->icon = $quality->icon;
        $this->is_active = $quality->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        ItemQualityLevel::find($id)->delete();
        session()->flash('message', 'Quality level deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $quality = ItemQualityLevel::find($id);
        $quality->is_active = !$quality->is_active;
        $quality->save();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
}