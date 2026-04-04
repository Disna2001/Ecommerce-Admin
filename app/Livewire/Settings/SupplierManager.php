<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;

class SupplierManager extends Component
{
    use WithPagination;

    public $supplier_id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $company;
    public $contact_person;
    public $tax_number;
    public $payment_terms;
    public $status = 'active';
    public $isOpen = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|min:2',
        'email' => 'required|email|unique:suppliers,email',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'company' => 'nullable|string',
        'contact_person' => 'nullable|string',
        'tax_number' => 'nullable|string',
        'payment_terms' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->withCount('stocks')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $searchQuery) {
                    $searchQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('company', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('contact_person', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.supplier-manager', [
            'suppliers' => $suppliers,
            'totalSuppliers' => Supplier::count(),
            'activeSuppliers' => Supplier::where('status', 'active')->count(),
            'suppliersWithStock' => Supplier::has('stocks')->count(),
            'inactiveSuppliers' => Supplier::where('status', 'inactive')->count(),
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
        $this->supplier_id = '';
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->company = '';
        $this->contact_person = '';
        $this->tax_number = '';
        $this->payment_terms = '';
        $this->status = 'active';
    }

    public function store()
    {
        if ($this->supplier_id) {
            $this->rules['email'] = 'required|email|unique:suppliers,email,' . $this->supplier_id;
        }

        $this->validate();

        Supplier::updateOrCreate(['id' => $this->supplier_id], [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'company' => $this->company,
            'contact_person' => $this->contact_person,
            'tax_number' => $this->tax_number,
            'payment_terms' => $this->payment_terms,
            'status' => $this->status,
        ]);

        session()->flash('message', $this->supplier_id ? 'Supplier updated successfully.' : 'Supplier created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplier_id = $id;
        $this->name = $supplier->name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->company = $supplier->company;
        $this->contact_person = $supplier->contact_person;
        $this->tax_number = $supplier->tax_number;
        $this->payment_terms = $supplier->payment_terms;
        $this->status = $supplier->status;
        $this->openModal();
    }

    public function delete($id)
    {
        $supplier = Supplier::find($id);
        
        if ($supplier->stocks()->count() > 0) {
            session()->flash('error', 'Cannot delete supplier with associated products.');
            return;
        }
        
        $supplier->delete();
        session()->flash('message', 'Supplier deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $supplier = Supplier::find($id);
        $supplier->status = $supplier->status === 'active' ? 'inactive' : 'active';
        $supplier->save();
        
        session()->flash('message', 'Supplier status updated.');
    }
}
