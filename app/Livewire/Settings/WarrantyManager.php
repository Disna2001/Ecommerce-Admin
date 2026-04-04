<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Warranty;

class WarrantyManager extends Component
{
    use WithPagination;

    public $warranty_id;
    public $name;
    public $type = 'manufacturer';
    public $duration;
    public $terms;
    public $coverage;
    public $status = 'active';
    public $isOpen = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|min:2',
        'type' => 'required|in:manufacturer,extended,store',
        'duration' => 'required|integer|min:1|max:120',
        'terms' => 'nullable|string',
        'coverage' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ];

    public function render()
    {
        $warranties = Warranty::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('type', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.warranty-manager', [
            'warranties' => $warranties,
            'totalWarranties' => Warranty::count(),
            'activeWarranties' => Warranty::where('status', 'active')->count(),
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
        $this->warranty_id = '';
        $this->name = '';
        $this->type = 'manufacturer';
        $this->duration = '';
        $this->terms = '';
        $this->coverage = '';
        $this->status = 'active';
    }

    public function store()
    {
        if ($this->warranty_id) {
            $this->rules['name'] = 'required|string|min:2|unique:warranties,name,' . $this->warranty_id;
        } else {
            $this->rules['name'] = 'required|string|min:2|unique:warranties,name';
        }

        $this->validate();

        Warranty::updateOrCreate(['id' => $this->warranty_id], [
            'name' => $this->name,
            'type' => $this->type,
            'duration' => $this->duration,
            'terms' => $this->terms,
            'coverage' => $this->coverage,
            'status' => $this->status,
        ]);

        session()->flash('message', $this->warranty_id ? 'Warranty updated successfully.' : 'Warranty created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $warranty = Warranty::findOrFail($id);
        $this->warranty_id = $id;
        $this->name = $warranty->name;
        $this->type = $warranty->type;
        $this->duration = $warranty->duration;
        $this->terms = $warranty->terms;
        $this->coverage = $warranty->coverage;
        $this->status = $warranty->status;
        $this->openModal();
    }

    public function delete($id)
    {
        $warranty = Warranty::find($id);
        
        if ($warranty->stocks()->count() > 0) {
            session()->flash('error', 'Cannot delete warranty with associated products.');
            return;
        }
        
        $warranty->delete();
        session()->flash('message', 'Warranty deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $warranty = Warranty::find($id);
        $warranty->status = $warranty->status === 'active' ? 'inactive' : 'active';
        $warranty->save();
        
        session()->flash('message', 'Warranty status updated.');
    }
}