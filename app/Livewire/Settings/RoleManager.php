<?php

namespace App\Livewire\Settings;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    use WithPagination;

    public $role_id;
    public $name;
    public $guard_name = 'web';
    public $selectedPermissions = [];
    public $isOpen = false;
    public $search = '';
    public $permissionSearch = '';
    public $focus = '';
    public $selectedRoleId = null;

    protected $rules = [
        'name' => 'required|string|min:2|unique:roles,name',
        'guard_name' => 'required|string',
        'selectedPermissions' => 'array',
    ];

    public function render()
    {
        $rolesQuery = Role::query()
            ->with(['permissions', 'users'])
            ->withCount(['permissions', 'users'])
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->focus, function (Builder $query) {
                match ($this->focus) {
                    'system' => $query->whereIn('name', ['Admin', 'Super Admin']),
                    'empty' => $query->doesntHave('users'),
                    'limited' => $query->has('permissions', '<', 3),
                    'busy' => $query->has('users', '>=', 3),
                    default => $query,
                };
            })
            ->orderBy('name');

        $roles = $rolesQuery->paginate(10);

        $permissions = Permission::query()
            ->when($this->permissionSearch, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->permissionSearch . '%');
            })
            ->orderBy('name')
            ->get();

        $selectedRole = $this->selectedRoleId
            ? Role::with(['permissions', 'users'])->withCount(['permissions', 'users'])->find($this->selectedRoleId)
            : null;

        return view('livewire.settings.role-manager', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRole' => $selectedRole,
            'totalRoles' => Role::count(),
            'totalPermissions' => Permission::count(),
            'systemRoles' => Role::whereIn('name', ['Admin', 'Super Admin'])->count(),
            'rolesWithoutUsers' => Role::doesntHave('users')->count(),
            'recentRoles' => Role::withCount(['permissions', 'users'])->latest('updated_at')->take(5)->get(),
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
        $this->permissionSearch = '';
    }

    public function openRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
    }

    public function closeRole(): void
    {
        $this->selectedRoleId = null;
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'permissionSearch', 'focus']);
        $this->resetPage();
    }

    private function resetInputFields()
    {
        $this->role_id = '';
        $this->name = '';
        $this->guard_name = 'web';
        $this->selectedPermissions = [];
    }

    public function store()
    {
        if ($this->role_id) {
            $this->rules['name'] = 'required|string|min:2|unique:roles,name,' . $this->role_id;
        }

        $this->validate();

        $role = Role::updateOrCreate(['id' => $this->role_id], [
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ]);

        $role->syncPermissions($this->selectedPermissions);
        $this->selectedRoleId = $role->id;

        session()->flash('message', $this->role_id ? 'Role updated successfully.' : 'Role created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->role_id = $id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->openModal();
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);

        if (in_array(strtolower($role->name), ['admin', 'super admin', 'super-admin'])) {
            session()->flash('error', 'Cannot delete system roles.');

            return;
        }

        if ($role->users()->count() > 0) {
            session()->flash('error', 'Cannot delete role with assigned users.');

            return;
        }

        if ((int) $this->selectedRoleId === (int) $id) {
            $this->selectedRoleId = null;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function createDefaultPermissions()
    {
        $permissions = [
            'view dashboard',
            'view orders', 'manage orders', 'verify payments',
            'view inventory', 'manage inventory',
            'view supply chain', 'manage supply chain',
            'view invoices', 'view pos',
            'view users', 'create users', 'edit users', 'delete users',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view settings', 'edit settings',
            'view activity logs',
            'view notification outbox',
            'view stock movements',
            'view system health',
            'view site management', 'manage site management',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        session()->flash('message', 'Default permissions created successfully.');
    }

    public function createAdminRole()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());
        $this->selectedRoleId = $role->id;

        session()->flash('message', 'Admin role created with all permissions.');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPermissionSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFocus(): void
    {
        $this->resetPage();
    }
}
