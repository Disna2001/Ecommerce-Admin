<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination;

    public $category_id;
    public $name;
    public $description;
    public $isOpen = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|min:2|unique:categories,name',
        'description' => 'nullable|string',
    ];

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.category-manager')
            ->layout('layouts.admin');
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
        $this->category_id = '';
        $this->name = '';
        $this->description = '';
    }

    public function store()
    {
        if ($this->category_id) {
            $this->rules['name'] = 'required|string|min:2|unique:categories,name,' . $this->category_id;
        }

        $this->validate();

        Category::updateOrCreate(['id' => $this->category_id], [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
        ]);

        session()->flash('message', $this->category_id ? 'Category updated successfully.' : 'Category created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->category_id = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->openModal();
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Category deleted successfully.');
    }
}