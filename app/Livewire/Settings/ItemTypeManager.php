<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ItemType;
use Illuminate\Support\Str;

class ItemTypeManager extends Component
{
    use WithPagination;

    public $item_type_id;
    public $name;
    public $description;
    public $status = 'active';
    public $isOpen = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|min:2',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ];

    public function render()
    {
        $itemTypes = ItemType::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.item-type-manager', [
            'itemTypes' => $itemTypes,
            'totalTypes' => ItemType::count(),
            'activeTypes' => ItemType::where('status', 'active')->count(),
        ]);
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
        $this->item_type_id = '';
        $this->name = '';
        $this->description = '';
        $this->status = 'active';
    }

    public function store()
    {
        if ($this->item_type_id) {
            $this->rules['name'] = 'required|string|min:2|unique:item_types,name,' . $this->item_type_id;
        } else {
            $this->rules['name'] = 'required|string|min:2|unique:item_types,name';
        }

        $this->validate();

        ItemType::updateOrCreate(['id' => $this->item_type_id], [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('message', $this->item_type_id ? 'Item Type updated successfully.' : 'Item Type created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $itemType = ItemType::findOrFail($id);
        $this->item_type_id = $id;
        $this->name = $itemType->name;
        $this->description = $itemType->description;
        $this->status = $itemType->status;
        $this->openModal();
    }

    public function delete($id)
    {
        $itemType = ItemType::find($id);
        
        if ($itemType->stocks()->count() > 0) {
            session()->flash('error', 'Cannot delete item type with associated products.');
            return;
        }
        
        $itemType->delete();
        session()->flash('message', 'Item Type deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $itemType = ItemType::find($id);
        $itemType->status = $itemType->status === 'active' ? 'inactive' : 'active';
        $itemType->save();
        
        session()->flash('message', 'Item Type status updated.');
    }
}