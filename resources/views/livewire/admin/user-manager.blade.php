<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">{{ session('error') }}</div>
    @endif

    <!-- Content Header -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">User Management</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">User & Access Control</h2>
                <p class="mt-1 text-xs text-slate-500">Manage staff accounts, roles, and permissions.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative w-64">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users..." class="w-full rounded-lg border-slate-200 pl-9 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
                </div>
            </div>
        </div>
    </div>

    <div class="grid min-w-0 gap-6 xl:grid-cols-[260px_minmax(0,1fr)]">
        <!-- Sidebar Navigation -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                <nav class="flex flex-col gap-1 text-xs font-semibold">
                    <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Management</p>
                    
                    <button type="button" wire:click="setUserWorkspaceTab('staff')" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all {{ $userWorkspaceTab === 'staff' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $userWorkspaceTab === 'staff' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-shield-halved text-xs"></i></div>
                            <span>Staff Directory</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('merchants')" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all {{ $userWorkspaceTab === 'merchants' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $userWorkspaceTab === 'merchants' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-store text-xs"></i></div>
                            <span>Merchants</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('regular')" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all {{ $userWorkspaceTab === 'regular' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $userWorkspaceTab === 'regular' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-users text-xs"></i></div>
                            <span>Regular Users</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                    </button>

                    <div class="my-3 border-t border-slate-100"></div>
                    <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Security & Access</p>

                    <button type="button" wire:click="setUserWorkspaceTab('roles')" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all {{ $userWorkspaceTab === 'roles' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $userWorkspaceTab === 'roles' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-user-gear text-xs"></i></div>
                            <span>Roles & Permissions</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('teams')" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all {{ $userWorkspaceTab === 'teams' ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $userWorkspaceTab === 'teams' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-people-group text-xs"></i></div>
                            <span>Teams</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('requests')" class="group flex items-center justify-between rounded-lg px-3 py-2.5 transition-all {{ $userWorkspaceTab === 'requests' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $userWorkspaceTab === 'requests' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-hand-holding-heart text-xs"></i></div>
                            <span>Access Requests</span>
                        </div>
                        @if($attentionQueues['unverified'] > 0)
                            <span class="flex h-5 min-w-[20px] items-center justify-center rounded-md bg-white/20 px-1 text-[10px] font-bold">{{ $attentionQueues['unverified'] }}</span>
                        @endif
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="min-w-0 space-y-6">
            @if(in_array($userWorkspaceTab, ['staff', 'merchants', 'regular']))
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 text-slate-900">
                                <i class="fas {{ $userWorkspaceTab === 'staff' ? 'fa-shield-halved' : ($userWorkspaceTab === 'merchants' ? 'fa-store' : 'fa-users') }} text-xs"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    {{ $userWorkspaceTab === 'staff' ? 'Internal Staff' : ($userWorkspaceTab === 'merchants' ? 'Merchants' : 'Customers') }}
                                </h3>
                                <p class="text-xs text-slate-500">Managing {{ number_format($filteredUsers) }} accounts</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                             <select wire:model.live="selectedRole" class="rounded-lg border-slate-200 px-3 py-1.5 font-semibold text-slate-700 shadow-xs focus:ring-0">
                                <option value="">All Roles</option>
                                <option value="__no_role__">No Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>

                            <select wire:model.live="selectedTeam" class="rounded-lg border-slate-200 px-3 py-1.5 font-semibold text-slate-700 shadow-xs focus:ring-0">
                                <option value="">All Teams</option>
                                <option value="__no_team__">No Team</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->slug }}">{{ $team->name }}</option>
                                @endforeach
                            </select>

                             <select wire:model.live="perPage" class="rounded-lg border-slate-200 px-3 py-1.5 font-semibold text-slate-700 shadow-xs focus:ring-0">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>

                            <button type="button" wire:click="clearFilters" class="h-[31px] w-[31px] flex items-center justify-center rounded-lg bg-white text-slate-400 border border-slate-200 hover:text-slate-900 hover:bg-slate-50 transition-colors shadow-xs" title="Reset Filters">
                                <i class="fas fa-rotate-left text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="px-6 py-3.5">User</th>
                                    <th class="px-6 py-3.5">Role</th>
                                    @if($userWorkspaceTab === 'merchants')
                                        <th class="px-6 py-3.5">Business Details</th>
                                    @else
                                        <th class="px-6 py-3.5">Team</th>
                                    @endif
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-xs">
                                @forelse($users as $user)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 shrink-0">
                                                    <img src="{{ $user->profile_photo_url }}" class="h-full w-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                                    <p class="text-[10px] text-slate-400 font-mono">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($user->roles as $role)
                                                    <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600 border border-slate-200">{{ $role->name }}</span>
                                                @empty
                                                    <span class="inline-flex rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-500 border border-rose-100">No Access</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($userWorkspaceTab === 'merchants' && $user->merchant)
                                                <div class="space-y-0.5">
                                                    <p class="font-bold text-slate-800">{{ $user->merchant->shop_name }}</p>
                                                    <p class="text-[10px] text-slate-400 truncate max-w-[150px]">{{ $user->merchant->shop_address }}</p>
                                                </div>
                                            @else
                                                <div class="flex flex-wrap gap-1">
                                                    @forelse($user->teams as $team)
                                                        <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-700 border border-slate-200">{{ $team->name }}</span>
                                                    @empty
                                                        <span class="text-[10px] font-medium text-slate-400 italic">Personal</span>
                                                    @endforelse
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->email_verified_at)
                                                <span class="inline-flex rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700 border border-emerald-100">Active</span>
                                            @else
                                                <span class="inline-flex rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-700 border border-amber-100">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button wire:click="openUser({{ $user->id }})" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white h-8 w-8 text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors shadow-xs" title="Manage User">
                                                <i class="fas fa-gear text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">No users found matching your search.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>

            @elseif($userWorkspaceTab === 'roles')
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-xs"><i class="fas fa-user-shield text-xs"></i></div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Roles & Permissions</h3>
                                <p class="text-xs text-slate-500">Define permissions and access levels for system roles.</p>
                            </div>
                        </div>

                        <form wire:submit.prevent="createRole" class="space-y-4 text-xs">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Role Name</label>
                                <input type="text" wire:model="newRoleName" placeholder="e.g. Content Editor" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                                @error('newRoleName') <span class="text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block font-bold text-slate-700">Permissions</label>
                                <div class="grid gap-1.5 sm:grid-cols-2 max-h-[260px] overflow-y-auto pr-2">
                                    @foreach($allPermissions as $permission)
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 p-2.5 hover:bg-white hover:border-slate-200">
                                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-0">
                                            <span class="text-xs font-semibold text-slate-700">{{ str_replace('_', ' ', $permission->name) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedPermissions') <span class="text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">Create Role</button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Existing Roles</label>
                        <div class="grid gap-2">
                            @foreach($roles as $role)
                                <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500"><i class="fas fa-fingerprint text-xs"></i></div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">{{ $role->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $role->users_count }} active users</p>
                                        </div>
                                    </div>
                                    <button class="h-8 w-8 rounded-lg text-slate-300 hover:text-rose-500 transition-colors"><i class="fas fa-trash-can text-xs"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($userWorkspaceTab === 'teams')
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-600 text-white shadow-xs"><i class="fas fa-people-line text-xs"></i></div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Teams</h3>
                                <p class="text-xs text-slate-500">Organize users into teams and assign default roles.</p>
                            </div>
                        </div>

                        <form wire:submit.prevent="createTeam" class="space-y-4 text-xs">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-700">Team Name</label>
                                    <input type="text" wire:model="newTeamName" placeholder="e.g. Sales Division" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                                    @error('newTeamName') <span class="text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-700">Default Role</label>
                                    <select wire:model="newTeamDefaultRole" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                                        <option value="">No Default</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700">Description</label>
                                <textarea wire:model="newTeamDescription" rows="2" placeholder="Define purpose of this team..." class="w-full rounded-lg border-slate-200 px-3 py-2 font-medium text-slate-900 focus:ring-0"></textarea>
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">Create Team</button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Existing Teams</label>
                        <div class="grid gap-3">
                            @foreach($teams as $team)
                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                                                <i class="fas fa-people-group text-xs"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-xs">{{ $team->name }}</h4>
                                                <p class="text-[10px] text-slate-400">{{ $team->users_count }} members</p>
                                            </div>
                                        </div>
                                        @if($team->default_role_name)
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Default: {{ $team->default_role_name }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $team->description ?: 'No description added for this team.' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($userWorkspaceTab === 'requests')
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500 text-white shadow-xs"><i class="fas fa-handshake text-xs"></i></div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Access Requests</h3>
                            <p class="text-xs text-slate-500">Review and approve merchant account applications.</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        @forelse($merchantRequests as $req)
                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs space-y-3">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-lg border border-slate-200 bg-slate-50 overflow-hidden shrink-0">
                                            @if($req->shop_image_path)
                                                <img src="{{ asset('storage/' . $req->shop_image_path) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-slate-300"><i class="fas fa-shop text-xs"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-slate-900 text-sm">{{ $req->shop_name }}</h4>
                                                <span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-700 border border-amber-100">Pending Review</span>
                                            </div>
                                            <p class="text-xs font-medium text-slate-600 mt-0.5">{{ $req->user->name }} • <span class="text-slate-400">{{ $req->user->email }}</span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="approveMerchant({{ $req->id }})" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs">Approve</button>
                                        <button x-data @click="const reason = prompt('Rejection Reason:'); if(reason) $wire.rejectMerchant({{ $req->id }}, reason)" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Reject</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-400 font-medium">
                                <i class="fas fa-inbox text-3xl mb-3 text-slate-300"></i>
                                <p class="text-sm font-bold text-slate-900">No Pending Requests</p>
                                <p class="text-xs text-slate-500 mt-0.5">There are no merchant applications waiting for review.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($merchantRequests->hasPages())
                        <div class="pt-4 border-t border-slate-100">
                            {{ $merchantRequests->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($selectedUserId)
        <x-admin.users.user-modal :selected-user="$selectedUser" :roles="$roles" :teams="$teams" />
    @endif

    <div wire:loading class="fixed bottom-6 right-6 z-50">
        <div class="flex items-center gap-2 rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-lg">
            <div class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span>Loading...</span>
        </div>
    </div>
</div>
