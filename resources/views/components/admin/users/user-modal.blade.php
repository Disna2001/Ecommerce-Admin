@props([
    'selectedUser',
    'roles',
])

@php($permissions = $selectedUser->roles->flatMap->permissions->pluck('name')->unique()->values())

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
