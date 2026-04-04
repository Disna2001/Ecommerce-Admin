<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Make;

class MakeManager extends Component
{
    use WithPagination;

    public $make_id;
    public $name;
    public $code;
    public $description;
    public $country_of_origin;
    public $website;
    public $is_active = true;

    public $isOpen = false;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:makes,code',
        'description' => 'nullable|string',
        'country_of_origin' => 'nullable|string|max:100',
        'website' => 'nullable|url|max:255',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $makes = Make::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('country_of_origin', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.make-manager', [
            'makes' => $makes
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
        $this->make_id = '';
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->country_of_origin = '';
        $this->website = '';
        $this->is_active = true;
    }

    public function store()
    {
        if ($this->make_id) {
            $this->rules['code'] = 'required|string|max:50|unique:makes,code,' . $this->make_id;
        }

        $this->validate();

        Make::updateOrCreate(['id' => $this->make_id], [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'country_of_origin' => $this->country_of_origin,
            'website' => $this->website,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->make_id ? 'Make updated successfully.' : 'Make created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $make = Make::findOrFail($id);
        $this->make_id = $id;
        $this->name = $make->name;
        $this->code = $make->code;
        $this->description = $make->description;
        $this->country_of_origin = $make->country_of_origin;
        $this->website = $make->website;
        $this->is_active = $make->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        Make::find($id)->delete();
        session()->flash('message', 'Make deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $make = Make::find($id);
        $make->is_active = !$make->is_active;
        $make->save();
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