<x-admin.ui.panel class="overflow-hidden" padding="p-0">
    <x-slot:header>
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
    </x-slot:header>

    <!-- Desktop Users Table -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200/70 text-sm dark:divide-slate-800">
            <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                <tr class="text-left text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">
                    <th class="px-5 py-4">User</th>
                    <th class="px-5 py-4 cursor-pointer" wire:click="sortBy('email')">Email</th>
                    <th class="px-5 py-4">Current access</th>
                    <th class="px-5 py-4">Teams</th>
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
                            <div class="flex max-w-xs flex-wrap gap-2">
                                @forelse($user->teams as $team)
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">{{ $team->name }}</span>
                                @empty
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">No team</span>
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
                        <td colspan="7" class="px-5 py-16 text-center">
                            <x-admin.ui.empty-state title="No users matched this view." description="Try clearing the filters or broaden the role and status selection." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="block lg:hidden space-y-4 px-4 py-2">
        @forelse($users as $user)
            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm space-y-4 dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-fuchsia-500 to-amber-400 text-sm font-black text-white shadow-lg shadow-blue-500/20">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white leading-none mb-1">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-none">User ID #{{ $user->id }}</p>
                    </div>
                </div>

                <div class="space-y-3 border-t border-slate-100 pt-4 dark:border-slate-800 text-xs">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Email Address</p>
                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-300">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Access Roles</p>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @forelse($user->roles as $role)
                                <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-200">{{ $role->name }}</span>
                            @empty
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">No role</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Status & Joined</p>
                        <div class="mt-1 flex items-center justify-between">
                            @if($user->email_verified_at)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">Verified</span>
                            @else
                                <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-200">Pending</span>
                            @endif
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ optional($user->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <button type="button" wire:click="openUser({{ $user->id }})" class="flex-1 h-9 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        View Profile
                    </button>
                    @if($user->id !== auth()->id())
                        <button type="button" wire:click="toggleUserStatus({{ $user->id }})" class="flex-1 h-9 rounded-xl bg-slate-900 text-white text-xs font-semibold dark:bg-white dark:text-slate-900">
                            {{ $user->email_verified_at ? 'Set Pending' : 'Verify' }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-[2rem] border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">No users found</p>
            </div>
        @endforelse
    </div>

    <div class="flex flex-col gap-3 border-t border-slate-200/70 px-5 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400 lg:flex-row lg:items-center lg:justify-between">
        <p>Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</p>
        {{ $users->links() }}
    </div>
</x-admin.ui.panel>
