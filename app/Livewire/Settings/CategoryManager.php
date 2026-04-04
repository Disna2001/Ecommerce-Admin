<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
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
        'name' => 'required|string|min:2',
        'description' => 'nullable|string',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::query()
            ->withCount('stocks')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $searchQuery) {
                    $searchQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.category-manager', [
            'categories' => $categories,
            'totalCategories' => Category::count(),
            'activeCategories' => Category::count(),
            'categoriesWithProducts' => Category::has('stocks')->count(),
            'emptyCategories' => Category::doesntHave('stocks')->count(),
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
        $this->category_id = '';
        $this->name = '';
        $this->description = '';
    }

    public function store()
    {
        if ($this->category_id) {
            $this->rules['name'] = 'required|string|min:2|unique:categories,name,' . $this->category_id;
        } else {
            $this->rules['name'] = 'required|string|min:2|unique:categories,name';
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
        $category = Category::find($id);
        
        // Check if category has products
        if ($category->stocks()->count() > 0) {
            session()->flash('error', 'Cannot delete category with associated products.');
            return;
        }
        
        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
    }
}
