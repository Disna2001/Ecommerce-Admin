<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandManager extends Component
{
    use WithPagination, WithFileUploads;

    public $brand_id;
    public $name;
    public $description;
    public $logo;
    public $website;
    public $status = 'active';
    public $isOpen = false;
    public $search = '';
    public $currentLogoPath = '';

    protected $rules = [
        'name' => 'required|string|min:2',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|max:1024',
        'website' => 'nullable|url',
        'status' => 'required|in:active,inactive',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $brands = Brand::query()
            ->withCount('stocks')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $searchQuery) {
                    $searchQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.brand-manager', [
            'brands' => $brands,
            'totalBrands' => Brand::count(),
            'activeBrands' => Brand::where('status', 'active')->count(),
            'brandsWithLogos' => Brand::whereNotNull('logo')->count(),
            'brandsWithProducts' => Brand::has('stocks')->count(),
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
        $this->brand_id = '';
        $this->name = '';
        $this->description = '';
        $this->logo = '';
        $this->website = '';
        $this->status = 'active';
        $this->currentLogoPath = '';
    }

    public function store()
    {
        if ($this->brand_id) {
            $this->rules['name'] = 'required|string|min:2|unique:brands,name,' . $this->brand_id;
        } else {
            $this->rules['name'] = 'required|string|min:2|unique:brands,name';
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'website' => $this->website,
            'status' => $this->status,
        ];

        if ($this->logo) {
            if ($this->brand_id && $this->currentLogoPath && Storage::disk('public')->exists($this->currentLogoPath)) {
                Storage::disk('public')->delete($this->currentLogoPath);
            }

            $path = $this->logo->store('brands', 'public');
            $data['logo'] = $path;
        }

        Brand::updateOrCreate(['id' => $this->brand_id], $data);

        session()->flash('message', $this->brand_id ? 'Brand updated successfully.' : 'Brand created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        $this->brand_id = $id;
        $this->name = $brand->name;
        $this->description = $brand->description;
        $this->website = $brand->website;
        $this->status = $brand->status;
        $this->currentLogoPath = $brand->logo ?? '';
        $this->openModal();
    }

    public function removeCurrentLogo(): void
    {
        if (!$this->brand_id) {
            $this->currentLogoPath = '';
            $this->logo = null;
            return;
        }

        $brand = Brand::find($this->brand_id);
        if (!$brand) {
            return;
        }

        if (!empty($brand->logo) && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->update(['logo' => null]);
        $this->currentLogoPath = '';
        $this->logo = null;

        session()->flash('message', 'Brand logo removed successfully.');
    }

    public function delete($id)
    {
        $brand = Brand::find($id);
        
        if ($brand->stocks()->count() > 0) {
            session()->flash('error', 'Cannot delete brand with associated products.');
            return;
        }

        if (!empty($brand->logo) && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }
        
        $brand->delete();
        session()->flash('message', 'Brand deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $brand = Brand::find($id);
        $brand->status = $brand->status === 'active' ? 'inactive' : 'active';
        $brand->save();
        
        session()->flash('message', 'Brand status updated.');
    }
}
