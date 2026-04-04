<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-3xl border border-rose-200 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="admin-surface rounded-[2rem] border border-white/60 bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.18),_transparent_40%),linear-gradient(135deg,rgba(255,255,255,0.96),rgba(248,250,252,0.92))] p-6 shadow-[0_25px_80px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.25),_transparent_32%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(17,24,39,0.92))]">
        <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
            <div class="space-y-5">
                <div class="space-y-3">
                    <span class="inline-flex items-center rounded-full border border-fuchsia-200/70 bg-fuchsia-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-fuchsia-700 dark:border-fuchsia-400/20 dark:bg-fuchsia-400/10 dark:text-fuchsia-200">
                        Roles And Permissions
                    </span>
                    <div class="space-y-2">
                        <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                            Build clean permission layers the team can actually operate.
                        </h2>
                        <p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Manage system roles, permission coverage, and empty assignments without losing track of who owns access across the admin panel.
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <button type="button" wire:click="$set('focus', 'system')" class="rounded-3xl border border-white/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">System Roles</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $systemRoles }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Protected platform roles.</p>
                    </button>
                    <button type="button" wire:click="$set('focus', 'empty')" class="rounded-3xl border border-white/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Unused Roles</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $rolesWithoutUsers }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Roles with nobody assigned.</p>
                    </button>
                    <button type="button" wire:click="$set('focus', 'limited')" class="rounded-3xl border border-white/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Lean Roles</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $roles->where('permissions_count', '<', 3)->count() }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Small permission sets to review.</p>
                    </button>
                    <button type="button" wire:click="openModal" class="rounded-3xl border border-white/70 bg-gradient-to-br from-indigo-500 via-fuchsia-500 to-amber-400 p-4 text-left shadow-lg shadow-indigo-500/20 transition hover:-translate-y-0.5 dark:border-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">Create</p>
                        <p class="mt-3 text-3xl font-black text-white">+</p>
                        <p class="mt-2 text-sm text-white/80">Add a new role layer now.</p>
                    </button>
                </div>
            </div>

            <aside class="rounded-[1.75rem] border border-white/70 bg-white/85 p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Coverage Snapshot</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $totalPermissions }} permissions</span>
                </div>

                <div class="mt-4 grid gap-3">
                    <div class="rounded-2xl bg-slate-100/80 px-4 py-3 dark:bg-slate-800/80">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Total Roles</p>
                        <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $totalRoles }}</p>
                    </div>
                    <div class="rounded-2xl bg-blue-50 px-4 py-3 dark:bg-blue-500/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600 dark:text-blue-300">Permission Library</p>
                        <p class="mt-2 text-2xl font-black text-blue-700 dark:text-blue-200">{{ $totalPermissions }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-dashed border-slate-200 px-4 py-4 dark:border-slate-700">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Admin guide</p>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                        <li>Keep system roles protected and reserve them for platform-level operations.</li>
                        <li>Review unused roles regularly so access policy stays clean.</li>
                        <li>Prefer role-based access instead of assigning many direct exceptions.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    <section class="grid gap-6 2xl:grid-cols-[1.8fr_0.8fr]">
        <div class="space-y-6">
            <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="grid gap-4 xl:grid-cols-[1.1fr_0.8fr_0.45fr_0.45fr]">
                    <label class="space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Search Roles</span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Find roles by name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-fuchsia-400 focus:ring-2 focus:ring-fuchsia-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-fuchsia-500 dark:focus:ring-fuchsia-500/20">
                    </label>
                    <label class="space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Focus</span>
                        <select wire:model.live="focus" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-fuchsia-400 focus:ring-2 focus:ring-fuchsia-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-fuchsia-500 dark:focus:ring-fuchsia-500/20">
                            <option value="">All roles</option>
                            <option value="system">System roles</option>
                            <option value="empty">Without users</option>
                            <option value="limited">Less than 3 permissions</option>
                            <option value="busy">3+ assigned users</option>
                        </select>
                    </label>
                    <div class="flex items-end">
                        <button type="button" wire:click="createDefaultPermissions" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                            Seed Defaults
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button type="button" wire:click="clearFilters" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="admin-surface overflow-hidden rounded-[2rem] border border-white/60 bg-white/90 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="flex flex-col gap-3 border-b border-slate-200/70 px-5 py-4 dark:border-slate-800 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Role directory</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Review permission weight and user adoption before changing access layers.</p>
                    </div>
                    <button type="button" wire:click="openModal" class="rounded-2xl bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-amber-400 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:opacity-95">
                        New Role
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200/70 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">
                                <th class="px-5 py-4">Role</th>
                                <th class="px-5 py-4">Permissions</th>
                                <th class="px-5 py-4">Users</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800">
                            @forelse($roles as $role)
                                <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-900/60">
                                    <td class="px-5 py-4">
                                        <button type="button" wire:click="openRole({{ $role->id }})" class="text-left">
                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $role->name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                @if(in_array(strtolower($role->name), ['admin', 'super admin', 'super-admin']))
                                                    Protected system role
                                                @else
                                                    Custom access role
                                                @endif
                                            </p>
                                        </button>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($role->permissions->take(4) as $permission)
                                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-200">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions_count > 4)
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">+{{ $role->permissions_count - 4 }} more</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $role->users_count }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" wire:click="openRole({{ $role->id }})" class="rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                                View
                                            </button>
                                            <button type="button" wire:click="edit({{ $role->id }})" class="rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                                Edit
                                            </button>
                                            @if(!in_array(strtolower($role->name), ['admin', 'super admin', 'super-admin']))
                                                <button type="button" wire:click="delete({{ $role->id }})" wire:confirm="Delete this role? Only empty roles should be removed." class="rounded-2xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-16 text-center">
                                        <div class="mx-auto max-w-md space-y-2">
                                            <p class="text-lg font-semibold text-slate-900 dark:text-white">No roles matched this view.</p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Try clearing the focus filter or seed defaults if the permission library is empty.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200/70 px-5 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400 lg:flex-row lg:items-center lg:justify-between">
                    <p>Showing {{ $roles->firstItem() ?? 0 }}-{{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }} roles</p>
                    {{ $roles->links() }}
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Recent Role Updates</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">Latest</span>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($recentRoles as $role)
                        <button type="button" wire:click="openRole({{ $role->id }})" class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-left transition hover:border-fuchsia-200 hover:bg-fuchsia-50/70 dark:border-slate-800 dark:hover:border-fuchsia-500/30 dark:hover:bg-fuchsia-500/10">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $role->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $role->permissions_count }} permissions · {{ $role->users_count }} users</p>
                                </div>
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ optional($role->updated_at)->diffForHumans() }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Quick Recovery</h3>
                </div>
                <div class="mt-4 space-y-3">
                    <button type="button" wire:click="createAdminRole" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                        Rebuild Admin Role
                    </button>
                    <p class="text-sm leading-6 text-slate-500 dark:text-slate-400">
                        Use this only when you need to restore a full-access admin role quickly after permission drift.
                    </p>
                </div>
            </div>
        </aside>
    </section>

    @if($selectedRole)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="absolute inset-0" wire:click="closeRole"></div>
            <div class="relative z-10 flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-2xl dark:bg-slate-950">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $selectedRole->name }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Role detail and assignment overview</p>
                    </div>
                    <button type="button" wire:click="closeRole" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Close
                    </button>
                </div>

                <div class="grid flex-1 gap-6 overflow-y-auto px-6 py-6 lg:grid-cols-[1fr_1fr]">
                    <div class="space-y-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-3xl bg-slate-100/80 p-4 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Users Assigned</p>
                                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $selectedRole->users_count }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-100/80 p-4 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Permissions</p>
                                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $selectedRole->permissions_count }}</p>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Permission Set</h4>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($selectedRole->permissions as $permission)
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-200">{{ $permission->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Assigned Users</h4>
                            <div class="mt-4 space-y-3">
                                @forelse($selectedRole->users as $user)
                                    <div class="rounded-2xl bg-slate-100/80 px-4 py-3 dark:bg-slate-900">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500 dark:text-slate-400">No users are assigned to this role yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] bg-slate-100/80 p-5 dark:bg-slate-900">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Role hygiene tip</p>
                            <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                When a role gains too many unrelated permissions, split it into clearer operational roles so access remains easier to audit.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="absolute inset-0" wire:click="closeModal"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-2xl dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $role_id ? 'Update Role' : 'Create New Role' }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Control the permission layer for a specific job function.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Close
                    </button>
                </div>

                <form wire:submit="store" class="flex flex-1 flex-col overflow-hidden">
                    <div class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Role Name</label>
                            <input type="text" wire:model="name" placeholder="e.g. Fulfillment Manager" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-fuchsia-400 focus:ring-2 focus:ring-fuchsia-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-fuchsia-500 dark:focus:ring-fuchsia-500/20">
                            @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Filter Permissions</label>
                            <input type="text" wire:model.live.debounce.300ms="permissionSearch" placeholder="Search permissions" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-fuchsia-400 focus:ring-2 focus:ring-fuchsia-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-fuchsia-500 dark:focus:ring-fuchsia-500/20">
                        </div>

                        <div class="rounded-[1.75rem] border border-slate-200 p-4 dark:border-slate-800">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Permission Library</p>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $permissions->count() }} visible</span>
                            </div>
                            <div class="grid gap-3 md:grid-cols-2">
                                @forelse($permissions as $permission)
                                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">
                                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="mt-1 rounded border-slate-300 text-fuchsia-600 focus:ring-fuchsia-500">
                                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $permission->name }}</span>
                                    </label>
                                @empty
                                    <p class="md:col-span-2 text-sm text-slate-500 dark:text-slate-400">No permissions matched your search.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                        <button type="button" wire:click="closeModal" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-amber-400 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:opacity-95">
                            Save Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
