<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Title('User Management')]
class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedRole = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public ?int $selectedUserId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedRole' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function render()
    {
        $filteredQuery = $this->usersQuery();
        $summaryQuery = clone $filteredQuery;
        $adminRoleNames = $this->existingAdminRoleNames();

        $users = $filteredQuery
            ->with('roles')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $roles = Role::withCount('users')->orderBy('name')->get();

        $attentionQueues = [
            'admins' => empty($adminRoleNames)
                ? 0
                : User::whereHas('roles', fn (Builder $query) => $query->whereIn('name', $adminRoleNames))->count(),
            'without_roles' => User::doesntHave('roles')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'new_this_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $selectedUser = $this->selectedUserId
            ? User::with('roles.permissions')->find($this->selectedUserId)
            : null;

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'roles' => $roles,
            'selectedUser' => $selectedUser,
            'totalUsers' => User::count(),
            'verifiedUsers' => User::whereNotNull('email_verified_at')->count(),
            'filteredUsers' => $summaryQuery->count(),
            'attentionQueues' => $attentionQueues,
            'recentAccessChanges' => User::with('roles')
                ->latest('updated_at')
                ->take(5)
                ->get(),
        ]);
    }

    protected function usersQuery(): Builder
    {
        return User::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedRole, function (Builder $query) {
                if ($this->selectedRole === '__no_role__') {
                    $query->doesntHave('roles');

                    return;
                }

                $query->whereHas('roles', function (Builder $roleQuery) {
                    $roleQuery->where('name', $this->selectedRole);
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                match ($this->statusFilter) {
                    'verified' => $query->whereNotNull('email_verified_at'),
                    'pending' => $query->whereNull('email_verified_at'),
                    'admin' => $this->applyAdminRoleFilter($query),
                    'new' => $query->where('created_at', '>=', now()->subDays(7)),
                    default => $query,
                };
            });
    }

    protected function existingAdminRoleNames(): array
    {
        return Role::query()
            ->whereIn('name', ['Admin', 'Super Admin'])
            ->pluck('name')
            ->all();
    }

    protected function applyAdminRoleFilter(Builder $query): void
    {
        $adminRoleNames = $this->existingAdminRoleNames();

        if (empty($adminRoleNames)) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereHas('roles', function (Builder $roleQuery) use ($adminRoleNames) {
            $roleQuery->whereIn('name', $adminRoleNames);
        });
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openUser(int $userId): void
    {
        $this->selectedUserId = $userId;
    }

    public function closeUser(): void
    {
        $this->selectedUserId = null;
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'selectedRole', 'statusFilter']);
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function assignRole(int $userId, string $roleName): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot change your own role.');

            return;
        }

        $user->syncRoles([$roleName]);
        $this->selectedUserId = $userId;
        session()->flash('message', "Role access updated for {$user->name}.");
    }

    public function toggleUserStatus(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot change your own status.');

            return;
        }

        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            session()->flash('message', 'User access was marked as pending verification.');
        } else {
            $user->email_verified_at = now();
            session()->flash('message', 'User marked as verified and active.');
        }

        $user->save();
        $this->selectedUserId = $userId;
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');

            return;
        }

        $this->selectedUserId = $this->selectedUserId === $userId ? null : $this->selectedUserId;
        $user->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedRole(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
}
