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

    <section class="admin-surface rounded-[2rem] border border-white/60 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.18),_transparent_42%),linear-gradient(135deg,rgba(255,255,255,0.96),rgba(248,250,252,0.92))] p-6 shadow-[0_25px_80px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.22),_transparent_32%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(17,24,39,0.92))]">
        <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
            <div class="space-y-5">
                <div class="space-y-3">
                    <span class="inline-flex items-center rounded-full border border-blue-200/70 bg-blue-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-blue-700 dark:border-blue-400/20 dark:bg-blue-400/10 dark:text-blue-200">
                        Access Control
                    </span>
                    <div class="space-y-2">
                        <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                            Keep admin access clean, safe, and easy to review.
                        </h2>
                        <p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Manage account verification, role assignments, and access health from one workspace instead of jumping between isolated user screens.
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <button type="button" wire:click="$set('statusFilter', 'admin')" class="rounded-3xl border border-white/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Admins</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $attentionQueues['admins'] }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Users with platform-wide access.</p>
                    </button>
                    <button type="button" wire:click="$set('selectedRole', '__no_role__')" class="rounded-3xl border border-white/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">No Role</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $attentionQueues['without_roles'] }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Accounts that still need access rules.</p>
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'pending')" class="rounded-3xl border border-white/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Pending</p>
                        <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $attentionQueues['unverified'] }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Users waiting on verification.</p>
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'new')" class="rounded-3xl border border-white/70 bg-gradient-to-br from-indigo-500 via-fuchsia-500 to-amber-400 p-4 text-left shadow-lg shadow-indigo-500/20 transition hover:-translate-y-0.5 dark:border-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">This Week</p>
                        <p class="mt-3 text-3xl font-black text-white">{{ $attentionQueues['new_this_week'] }}</p>
                        <p class="mt-2 text-sm text-white/80">Fresh signups that may need onboarding.</p>
                    </button>
                </div>
            </div>

            <aside class="rounded-[1.75rem] border border-white/70 bg-white/85 p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Access Snapshot</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $filteredUsers }} visible</span>
                </div>

                <div class="mt-4 grid gap-3">
                    <div class="rounded-2xl bg-slate-100/80 px-4 py-3 dark:bg-slate-800/80">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">All Users</p>
                        <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $totalUsers }}</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 dark:bg-emerald-500/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-600 dark:text-emerald-300">Verified</p>
                        <p class="mt-2 text-2xl font-black text-emerald-700 dark:text-emerald-200">{{ $verifiedUsers }}</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50 px-4 py-3 dark:bg-amber-500/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-600 dark:text-amber-300">Roles Available</p>
                        <p class="mt-2 text-2xl font-black text-amber-700 dark:text-amber-200">{{ $roles->count() }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-dashed border-slate-200 px-4 py-4 dark:border-slate-700">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Operating guide</p>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                        <li>Review users without roles first so nobody stays outside your permission system.</li>
                        <li>Use pending verification as a holding state instead of deleting accounts too early.</li>
                        <li>Open a user card before changing access so you can confirm their current permissions.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    <section class="grid gap-6 2xl:grid-cols-[1.8fr_0.8fr]">
        <div class="space-y-6">
            <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr_0.8fr_0.45fr]">
                    <label class="space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Search</span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-500/20">
                    </label>
                    <label class="space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Role</span>
                        <select wire:model.live="selectedRole" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-500/20">
                            <option value="">All roles</option>
                            <option value="__no_role__">No role assigned</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500">Status</span>
                        <select wire:model.live="statusFilter" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-500/20">
                            <option value="">All states</option>
                            <option value="verified">Verified</option>
                            <option value="pending">Pending verification</option>
                            <option value="admin">Admin access</option>
                            <option value="new">New this week</option>
                        </select>
                    </label>
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
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">User directory</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Review access, verification, and role coverage from one table.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Show</span>
                        <select wire:model.live="perPage" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-500/20">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200/70 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">
                                <th class="px-5 py-4">User</th>
                                <th class="px-5 py-4 cursor-pointer" wire:click="sortBy('email')">Email</th>
                                <th class="px-5 py-4">Current access</th>
                                <th class="px-5 py-4">State</th>
                                <th class="px-5 py-4 cursor-pointer" wire:click="sortBy('created_at')">Joined</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800">
                            @forelse($users as $user)
                                <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-900/60">
                                    <td class="px-5 py-4">
                                        <button type="button" wire:click="openUser({{ $user->id }})" class="flex items-center gap-3 text-left">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-fuchsia-500 to-amber-400 text-sm font-black text-white shadow-lg shadow-blue-500/20">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">User ID #{{ $user->id }}</p>
                                            </div>
                                        </button>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($user->roles as $role)
                                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-200">{{ $role->name }}</span>
                                            @empty
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">No role</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($user->email_verified_at)
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">Verified</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-200">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ optional($user->created_at)->diffForHumans() }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" wire:click="openUser({{ $user->id }})" class="rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                                View
                                            </button>
                                            @if($user->id !== auth()->id())
                                                <button type="button" wire:click="toggleUserStatus({{ $user->id }})" class="rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                                    {{ $user->email_verified_at ? 'Set Pending' : 'Verify' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="mx-auto max-w-md space-y-2">
                                            <p class="text-lg font-semibold text-slate-900 dark:text-white">No users matched this view.</p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Try clearing the filters or broaden the role and status selection.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200/70 px-5 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400 lg:flex-row lg:items-center lg:justify-between">
                    <p>Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</p>
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Recent Access Changes</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">Live</span>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($recentAccessChanges as $recentUser)
                        <button type="button" wire:click="openUser({{ $recentUser->id }})" class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-left transition hover:border-blue-200 hover:bg-blue-50/70 dark:border-slate-800 dark:hover:border-blue-500/30 dark:hover:bg-blue-500/10">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $recentUser->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $recentUser->roles->pluck('name')->join(', ') ?: 'No role assigned' }}</p>
                                </div>
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ optional($recentUser->updated_at)->diffForHumans() }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Role Coverage</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $roles->count() }} roles</span>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($roles->take(6) as $role)
                        <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $role->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $role->users_count }} users assigned</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $role->users_count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </section>

    @if($selectedUser)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="absolute inset-0" wire:click="closeUser"></div>
            <div class="relative z-10 flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-2xl dark:bg-slate-950">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-500 via-fuchsia-500 to-amber-400 text-lg font-black text-white">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $selectedUser->name }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $selectedUser->email }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeUser" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Close
                    </button>
                </div>

                <div class="grid flex-1 gap-6 overflow-y-auto px-6 py-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="rounded-3xl bg-slate-100/80 p-4 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Joined</p>
                                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{{ optional($selectedUser->created_at)->format('M d, Y') }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-100/80 p-4 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">State</p>
                                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{{ $selectedUser->email_verified_at ? 'Verified' : 'Pending' }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-100/80 p-4 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Roles</p>
                                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{{ max($selectedUser->roles->count(), 0) }}</p>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Assigned Roles</h4>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse($selectedUser->roles as $role)
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-200">{{ $role->name }}</span>
                                @empty
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">No role assigned</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Permission Reach</h4>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @php($permissions = $selectedUser->roles->flatMap->permissions->pluck('name')->unique()->values())
                                @forelse($permissions as $permission)
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-200">{{ $permission }}</span>
                                @empty
                                    <span class="text-sm text-slate-500 dark:text-slate-400">This user does not inherit any permissions yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Change Access</h4>
                            <div class="mt-4 space-y-3">
                                <select wire:change="assignRole({{ $selectedUser->id }}, $event.target.value)" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-500/20" {{ $selectedUser->id === auth()->id() ? 'disabled' : '' }}>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $selectedUser->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @if($selectedUser->id === auth()->id())
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Your own role is locked here for safety.</p>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Quick Actions</h4>
                            <div class="mt-4 flex flex-col gap-3">
                                <button type="button" wire:click="toggleUserStatus({{ $selectedUser->id }})" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200" {{ $selectedUser->id === auth()->id() ? 'disabled' : '' }}>
                                    {{ $selectedUser->email_verified_at ? 'Move To Pending Verification' : 'Verify User Access' }}
                                </button>
                                <button type="button" wire:click="deleteUser({{ $selectedUser->id }})" wire:confirm="Delete this user account? This cannot be undone." class="rounded-2xl border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10" {{ $selectedUser->id === auth()->id() ? 'disabled' : '' }}>
                                    Delete Account
                                </button>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] bg-slate-100/80 p-5 dark:bg-slate-900">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Admin Reminder</h4>
                            <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                Use role changes for access control and keep account deletion as a last resort. This keeps audit trails, orders, and payment history easier to preserve.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div wire:loading.delay class="fixed bottom-4 right-4 z-50 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-lg dark:bg-white dark:text-slate-900">
        Updating access workspace...
    </div>
</div>
