<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Governance Workspace</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">User & Access Control</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Manage organizational hierarchies, merchant partnerships, and granular security protocols.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-1.5 shadow-inner">
                    <span class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200">Global Search</span>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter identities..." class="border-0 bg-transparent py-1 pl-8 pr-3 text-xs font-bold focus:ring-0 w-48">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid min-w-0 gap-6 xl:grid-cols-[280px_minmax(0,1fr)]">
        <!-- Sidebar Navigation -->
        <div class="space-y-6">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                <nav class="flex flex-col gap-1.5">
                    <p class="mb-3 px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Management</p>
                    
                    <button type="button" wire:click="setUserWorkspaceTab('staff')" class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $userWorkspaceTab === 'staff' ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl {{ $userWorkspaceTab === 'staff' ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white' }}"><i class="fas fa-shield-halved text-xs"></i></div>
                            <span class="text-sm font-bold">Staff Directory</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('merchants')" class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $userWorkspaceTab === 'merchants' ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl {{ $userWorkspaceTab === 'merchants' ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white' }}"><i class="fas fa-store text-xs"></i></div>
                            <span class="text-sm font-bold">Merchant Panel</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('regular')" class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $userWorkspaceTab === 'regular' ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl {{ $userWorkspaceTab === 'regular' ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white' }}"><i class="fas fa-users text-xs"></i></div>
                            <span class="text-sm font-bold">Regular Users</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </button>

                    <div class="my-4 border-t border-slate-100"></div>
                    <p class="mb-3 px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Security & Sourcing</p>

                    <button type="button" wire:click="setUserWorkspaceTab('roles')" class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $userWorkspaceTab === 'roles' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl {{ $userWorkspaceTab === 'roles' ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white' }}"><i class="fas fa-user-gear text-xs"></i></div>
                            <span class="text-sm font-bold">Role Architect</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('teams')" class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $userWorkspaceTab === 'teams' ? 'bg-sky-600 text-white shadow-xl shadow-sky-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl {{ $userWorkspaceTab === 'teams' ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white' }}"><i class="fas fa-people-group text-xs"></i></div>
                            <span class="text-sm font-bold">Team Registry</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </button>

                    <button type="button" wire:click="setUserWorkspaceTab('requests')" class="group flex items-center justify-between rounded-2xl px-4 py-3.5 transition-all {{ $userWorkspaceTab === 'requests' ? 'bg-amber-500 text-white shadow-xl shadow-amber-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl {{ $userWorkspaceTab === 'requests' ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white' }}"><i class="fas fa-hand-holding-heart text-xs"></i></div>
                            <span class="text-sm font-bold">Request Access Hub</span>
                        </div>
                        @if($attentionQueues['unverified'] > 0)
                            <span class="flex h-5 min-w-[20px] items-center justify-center rounded-lg bg-white/20 px-1 text-[10px] font-black leading-none">{{ $attentionQueues['unverified'] }}</span>
                        @endif
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="min-w-0 space-y-6">
            @if(in_array($userWorkspaceTab, ['staff', 'merchants', 'regular']))
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-slate-100 text-slate-900 shadow-inner">
                                <i class="fas {{ $userWorkspaceTab === 'staff' ? 'fa-shield-halved' : ($userWorkspaceTab === 'merchants' ? 'fa-store' : 'fa-users') }} text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">
                                    {{ $userWorkspaceTab === 'staff' ? 'Internal Staff' : ($userWorkspaceTab === 'merchants' ? 'Active Merchants' : 'Customer Registry') }}
                                </h3>
                                <p class="text-xs text-slate-500 font-medium">Managing {{ number_format($filteredUsers) }} identified profiles</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                             <select wire:model.live="selectedRole" class="rounded-xl border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold shadow-none focus:ring-0 transition-all focus:bg-white">
                                <option value="">All Roles</option>
                                <option value="__no_role__">No Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>

                            <select wire:model.live="selectedTeam" class="rounded-xl border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold shadow-none focus:ring-0 transition-all focus:bg-white">
                                <option value="">All Teams</option>
                                <option value="__no_team__">No Team</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->slug }}">{{ $team->name }}</option>
                                @endforeach
                            </select>

                             <select wire:model.live="perPage" class="rounded-xl border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold shadow-none focus:ring-0 transition-all focus:bg-white">
                                <option value="10">10 Rows</option>
                                <option value="25">25 Rows</option>
                                <option value="50">50 Rows</option>
                            </select>

                            <button type="button" wire:click="clearFilters" class="h-9 w-9 flex items-center justify-center rounded-xl bg-white text-rose-400 border border-slate-200 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Reset Filters">
                                <i class="fas fa-rotate-left text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">User Identity</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Access Level</th>
                                    @if($userWorkspaceTab === 'merchants')
                                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Commerce Profile</th>
                                    @else
                                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Deployment</th>
                                    @endif
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($users as $user)
                                    <tr class="group transition-colors hover:bg-slate-50/50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="h-10 w-10 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 ring-2 ring-transparent transition-all group-hover:ring-slate-100">
                                                    <img src="{{ $user->profile_photo_url }}" class="h-full w-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</p>
                                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-tighter">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse($user->roles as $role)
                                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-1 text-[9px] font-black uppercase tracking-widest text-slate-600">{{ $role->name }}</span>
                                                @empty
                                                    <span class="inline-flex items-center rounded-lg bg-rose-50 px-2 py-1 text-[9px] font-black uppercase tracking-widest text-rose-500">No Access</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($userWorkspaceTab === 'merchants' && $user->merchant)
                                                <div class="space-y-1">
                                                    <p class="text-xs font-bold text-slate-700">{{ $user->merchant->shop_name }}</p>
                                                    <p class="text-[10px] font-medium text-slate-400 truncate max-w-[150px]">{{ $user->merchant->shop_address }}</p>
                                                </div>
                                            @else
                                                <div class="flex flex-wrap gap-1.5">
                                                    @forelse($user->teams as $team)
                                                        <span class="inline-flex items-center rounded-lg bg-{{ $team->color ?? 'slate' }}-50 px-2 py-1 text-[9px] font-black uppercase tracking-widest text-{{ $team->color ?? 'slate' }}-600">{{ $team->name }}</span>
                                                    @empty
                                                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Personal</span>
                                                    @endforelse
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->email_verified_at)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-600">
                                                    <span class="h-1 w-1 rounded-full bg-emerald-600"></span> Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-amber-600">
                                                    <span class="h-1 w-1 rounded-full bg-amber-600 animate-pulse"></span> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button wire:click="openUser({{ $user->id }})" class="h-8 w-8 rounded-lg text-slate-400 transition hover:bg-slate-900 hover:text-white shadow-sm">
                                                <i class="fas fa-gear text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-200">
                                                    <i class="fas fa-user-slash text-2xl"></i>
                                                </div>
                                                <p class="text-sm font-bold text-slate-500">No identities match your criteria</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">
                    {{ $users->links() }}
                </div>

            @elseif($userWorkspaceTab === 'roles')
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-indigo-600 text-white shadow-xl shadow-indigo-100"><i class="fas fa-user-shield text-xl"></i></div>
                            <div>
                                <h3 class="text-2xl font-black tracking-tight text-slate-900">Role Architect</h3>
                                <p class="text-sm font-medium text-slate-500">Engineer granular permissions and access levels</p>
                            </div>
                        </div>

                        <form wire:submit.prevent="createRole" class="space-y-6">
                            <div class="space-y-2">
                                <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Unique Role Name</label>
                                <input type="text" wire:model="newRoleName" placeholder="e.g. Content Editor" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all">
                                @error('newRoleName') <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-4">
                                <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Entitlements Coverage</label>
                                <div class="grid gap-2 sm:grid-cols-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($allPermissions as $permission)
                                        <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3 transition-all hover:bg-white hover:border-indigo-600">
                                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-0">
                                            <span class="text-[11px] font-bold text-slate-600 group-hover:text-indigo-600 uppercase tracking-tighter">{{ str_replace('_', ' ', $permission->name) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedPermissions') <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-slate-900 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-xl shadow-slate-200 transition hover:scale-[1.02] active:scale-[0.98]">Deploy Access Role</button>
                        </form>
                    </div>

                    <div class="space-y-6">
                        <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Deployed Policy Matrix</p>
                        <div class="grid gap-3">
                            @foreach($roles as $role)
                                <div class="group flex items-center justify-between rounded-3xl border border-slate-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-slate-100">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors"><i class="fas fa-fingerprint text-lg"></i></div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $role->name }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $role->users_count }} active users</p>
                                        </div>
                                    </div>
                                    <button class="h-8 w-8 rounded-lg text-slate-300 transition hover:bg-rose-50 hover:text-rose-500"><i class="fas fa-trash-can text-xs"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($userWorkspaceTab === 'teams')
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-sky-600 text-white shadow-xl shadow-sky-100"><i class="fas fa-people-line text-xl"></i></div>
                            <div>
                                <h3 class="text-2xl font-black tracking-tight text-slate-900">Team Registry</h3>
                                <p class="text-sm font-medium text-slate-500">Define operational groups and default onboarding paths</p>
                            </div>
                        </div>

                        <form wire:submit.prevent="createTeam" class="space-y-6">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Team Name</label>
                                    <input type="text" wire:model="newTeamName" placeholder="e.g. Sales Division" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-600 focus:ring-0 transition-all">
                                    @error('newTeamName') <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Default Role</label>
                                    <select wire:model="newTeamDefaultRole" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-600 focus:ring-0 transition-all">
                                        <option value="">No Default</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Description & Mission</label>
                                <textarea wire:model="newTeamDescription" rows="3" placeholder="Define the core purpose of this group..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-600 focus:ring-0 transition-all resize-none"></textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Brand Identity</label>
                                <div class="flex flex-wrap gap-3 p-4 rounded-2xl border border-slate-100 bg-slate-50/50">
                                    @foreach(['sky', 'indigo', 'rose', 'emerald', 'amber', 'violet', 'slate'] as $color)
                                        <label class="relative flex cursor-pointer items-center justify-center">
                                            <input type="radio" wire:model="newTeamColor" value="{{ $color }}" class="peer sr-only">
                                            <div class="h-8 w-8 rounded-full bg-{{ $color }}-500 ring-offset-2 ring-{{ $color }}-500 transition-all peer-checked:ring-2"></div>
                                            <i class="fas fa-check absolute text-[10px] text-white opacity-0 transition-opacity peer-checked:opacity-100"></i>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-slate-900 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-xl shadow-slate-200 transition hover:scale-[1.02] active:scale-[0.98]">Authorize New Team</button>
                        </form>
                    </div>

                    <div class="space-y-6">
                        <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operational Groups</p>
                        <div class="grid gap-4">
                            @foreach($teams as $team)
                                <div class="group relative overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white p-6 transition-all hover:border-{{ $team->color ?? 'sky' }}-400 hover:shadow-2xl hover:shadow-{{ $team->color ?? 'sky' }}-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-5">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-{{ $team->color ?? 'sky' }}-50 text-{{ $team->color ?? 'sky' }}-600 shadow-inner">
                                                <i class="fas fa-people-group text-xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black text-slate-900">{{ $team->name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $team->users_count }} members enrolled</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($team->default_role_name)
                                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-[8px] font-black uppercase tracking-widest text-slate-500">Auto-Role: {{ $team->default_role_name }}</span>
                                            @endif
                                            <button class="h-8 w-8 rounded-lg text-slate-300 transition hover:bg-rose-50 hover:text-rose-500"><i class="fas fa-trash-can text-xs"></i></button>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-xs font-medium leading-relaxed text-slate-500">{{ $team->description ?: 'No operational mission defined for this registry entry.' }}</p>
                                    <div class="absolute bottom-0 right-0 h-24 w-24 translate-x-8 translate-y-8 rounded-full bg-{{ $team->color ?? 'sky' }}-50 opacity-20 transition-transform group-hover:scale-150"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($userWorkspaceTab === 'requests')
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-amber-500 text-white shadow-xl shadow-amber-100"><i class="fas fa-handshake text-xl"></i></div>
                        <div>
                            <h3 class="text-2xl font-black tracking-tight text-slate-900">Request Access Hub</h3>
                            <p class="text-sm font-medium text-slate-500">Audit and authorize incoming merchant partnership applications</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($merchantRequests as $req)
                            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition-all hover:border-amber-400 hover:shadow-xl hover:shadow-amber-50">
                                <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center">
                                    <div class="flex flex-1 items-center gap-6">
                                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-[1.5rem] border-4 border-slate-50 bg-slate-100 shadow-inner">
                                            @if($req->shop_image_path)
                                                <img src="{{ asset('storage/' . $req->shop_image_path) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-slate-300"><i class="fas fa-shop text-xl"></i></div>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-lg font-black text-slate-900">{{ $req->shop_name }}</h4>
                                                <span class="rounded-lg bg-amber-50 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-amber-600">Pending Review</span>
                                            </div>
                                            <p class="text-sm font-bold text-slate-600">{{ $req->user->name }} <span class="mx-2 text-slate-300">•</span> <span class="text-slate-400 font-medium">{{ $req->user->email }}</span></p>
                                            <div class="flex items-center gap-4 pt-1">
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><i class="fas fa-phone mr-1.5"></i> {{ $req->phone_number }}</span>
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><i class="fas fa-id-card mr-1.5"></i> NIC: {{ $req->nic_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 border-t border-slate-50 pt-6 lg:border-0 lg:pt-0">
                                        <button wire:click="approveMerchant({{ $req->id }})" class="flex-1 rounded-2xl bg-emerald-600 px-6 py-3 text-[10px] font-black text-white uppercase tracking-widest transition hover:bg-emerald-700 lg:flex-none">Authorize Access</button>
                                        <button x-data @click="const reason = prompt('Rejection Reason:'); if(reason) $wire.rejectMerchant({{ $req->id }}, reason)" class="rounded-2xl border border-slate-100 bg-slate-50 px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest transition hover:bg-rose-50 hover:text-rose-500 hover:border-rose-100 lg:flex-none">Reject</button>
                                    </div>
                                </div>
                                <div class="bg-slate-50/50 px-8 py-3 flex items-center justify-between">
                                     <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Submitted {{ $req->created_at->diffForHumans() }}</p>
                                     <button class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">View Verification Dossier</button>
                                </div>
                            </div>
                        @empty
                            <div class="py-20 text-center border-2 border-dashed border-slate-100 rounded-[2.5rem]">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-slate-200 mx-auto">
                                    <i class="fas fa-inbox text-3xl"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-900">Registry is Clear</h4>
                                <p class="text-sm font-medium text-slate-400 mt-1">No merchant partnership requests are currently awaiting triage.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $merchantRequests->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($selectedUserId)
        <x-admin.users.user-modal :selected-user="$selectedUser" :roles="$roles" :teams="$teams" />
    @endif

    <div wire:loading class="fixed bottom-8 right-8 z-[60]">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-5 py-3 text-white shadow-2xl shadow-slate-400 animate-in fade-in slide-in-from-bottom-4">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Processing Intelligence...</span>
        </div>
    </div>
</div>
