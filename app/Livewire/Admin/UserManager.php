<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Title('User Management')]
class UserManager extends Component
{
    use WithPagination;

    public string $userWorkspaceTab = 'staff';
    public string $search = '';
    public string $selectedRole = '';
    public string $selectedTeam = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public ?int $selectedUserId = null;
    public string $newTeamName = '';
    public string $newTeamDescription = '';
    public string $newTeamColor = 'sky';
    public string $newTeamDefaultRole = '';
    public bool $showTeamCreator = false;
    public bool $showRoleCreator = false;
    public string $newRoleName = '';
    public array $selectedPermissions = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'userWorkspaceTab' => ['except' => 'staff'],
        'selectedRole' => ['except' => ''],
        'selectedTeam' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'newTeamName' => ['required', 'string', 'max:255'],
            'newTeamDescription' => ['nullable', 'string', 'max:500'],
            'newTeamColor' => ['required', 'string', 'max:32'],
            'newTeamDefaultRole' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function render()
    {
        Team::ensureDefaultTeamsForCurrentTenant();

        $filteredQuery = $this->usersQuery();
        $summaryQuery = clone $filteredQuery;
        $adminRoleNames = $this->existingAdminRoleNames();

        // Specific handling for merchant requests
        if ($this->userWorkspaceTab === 'requests') {
            $merchantRequests = \App\Models\Merchant::with('user')
                ->where('verification_status', 'pending')
                ->when($this->search, function ($query) {
                    $query->whereHas('user', function ($u) {
                        $u->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->perPage);
        } else {
            $merchantRequests = collect();
        }

        $users = $filteredQuery
            ->with(['roles', 'teams', 'merchant'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $roles = Role::withCount('users')->orderBy('name')->get();
        $teams = Team::withCount('users')->orderBy('name')->get();

        $attentionQueues = [
            'admins' => empty($adminRoleNames)
                ? 0
                : User::whereHas('roles', fn (Builder $query) => $query->whereIn('name', $adminRoleNames))->count(),
            'without_roles' => User::doesntHave('roles')->count(),
            'without_teams' => User::doesntHave('teams')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'new_this_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $selectedUser = $this->selectedUserId
            ? User::with(['roles.permissions', 'teams'])->find($this->selectedUserId)
            : null;

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'merchantRequests' => $merchantRequests,
            'roles' => $roles,
            'teams' => $teams,
            'selectedUser' => $selectedUser,
            'totalUsers' => User::count(),
            'verifiedUsers' => User::whereNotNull('email_verified_at')->count(),
            'filteredUsers' => ($this->userWorkspaceTab === 'requests') ? $merchantRequests->total() : $summaryQuery->count(),
            'attentionQueues' => $attentionQueues,
            'recentAccessChanges' => User::with(['roles', 'teams'])
                ->latest('updated_at')
                ->take(5)
                ->get(),
            'allPermissions' => \Spatie\Permission\Models\Permission::orderBy('name')->get(),
        ]);
    }

    protected function usersQuery(): Builder
    {
        return User::query()
            ->when($this->userWorkspaceTab === 'staff', function (Builder $query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Admin', 'Super Admin', 'Staff', 'Editor', 'Manager']);
                });
            })
            ->when($this->userWorkspaceTab === 'merchants', function (Builder $query) {
                $query->whereHas('merchant', function ($q) {
                    $q->where('verification_status', 'verified');
                });
            })
            ->when($this->userWorkspaceTab === 'regular', function (Builder $query) {
                $query->whereDoesntHave('merchant')
                    ->where(function ($q) {
                        $q->whereDoesntHave('roles')
                          ->orWhereHas('roles', function ($rq) {
                              $rq->whereNotIn('name', ['Admin', 'Super Admin', 'Staff', 'Editor', 'Manager']);
                          });
                    });
            })
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
            ->when($this->selectedTeam, function (Builder $query) {
                if ($this->selectedTeam === '__no_team__') {
                    $query->doesntHave('teams');
                    return;
                }
                $query->whereHas('teams', function (Builder $teamQuery) {
                    $teamQuery->where('slug', $this->selectedTeam);
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

    public function setUserWorkspaceTab(string $tab): void
    {
        $this->userWorkspaceTab = $tab;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'selectedRole', 'selectedTeam', 'statusFilter']);
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function approveMerchant(int $merchantId): void
    {
        $merchant = \App\Models\Merchant::findOrFail($merchantId);
        $merchant->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        $merchant->user->assignRole('Merchant');
        session()->flash('message', "Merchant access granted for {$merchant->shop_name}.");
    }

    public function rejectMerchant(int $merchantId, string $reason): void
    {
        $merchant = \App\Models\Merchant::findOrFail($merchantId);
        $merchant->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
        session()->flash('message', "Merchant request rejected for {$merchant->shop_name}.");
    }

    public function createRole(): void
    {
        $this->validate([
            'newRoleName' => 'required|unique:roles,name',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        $role = Role::create(['name' => $this->newRoleName]);
        $role->syncPermissions($this->selectedPermissions);

        $this->reset(['newRoleName', 'selectedPermissions', 'showRoleCreator']);
        session()->flash('message', "Role '{$role->name}' created successfully.");
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

    public function toggleTeamAssignment(int $userId, int $teamId): void
    {
        $user = User::with('teams')->findOrFail($userId);
        $team = Team::findOrFail($teamId);

        if ($user->teams->contains('id', $teamId)) {
            $user->teams()->detach($teamId);
            session()->flash('message', "{$user->name} was removed from {$team->name}.");

            return;
        }

        $user->teams()->attach($teamId);

        if (filled($team->default_role_name) && !$user->hasRole($team->default_role_name)) {
            $user->assignRole($team->default_role_name);
        }

        session()->flash('message', "{$user->name} was added to {$team->name}.");
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

    public function createTeam(): void
    {
        $validated = $this->validate();

        Team::ensureDefaultTeamsForCurrentTenant();

        $tenantId = app(TenantManager::class)->currentId();
        $slug = Str::slug($validated['newTeamName']);

        $team = Team::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('slug', $slug)->first();

        if ($team) {
            $this->addError('newTeamName', 'A team with that name already exists.');

            return;
        }

        Team::create([
            'tenant_id' => $tenantId,
            'name' => $validated['newTeamName'],
            'slug' => $slug,
            'description' => $validated['newTeamDescription'],
            'color' => $validated['newTeamColor'],
            'default_role_name' => $validated['newTeamDefaultRole'] ?: null,
            'is_active' => true,
        ]);

        $this->reset(['newTeamName', 'newTeamDescription', 'newTeamColor', 'newTeamDefaultRole', 'showTeamCreator']);
        session()->flash('message', 'Team created successfully.');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedRole(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedTeam(): void
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
