@props([
    'selectedUser',
    'roles',
    'teams',
])

@php($permissions = $selectedUser->roles->flatMap->permissions->pluck('name')->unique()->values())

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="absolute inset-0" wire:click="closeUser"></div>
    <div class="relative z-10 flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-900 text-base font-bold text-white shadow-xs">
                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ $selectedUser->name }}</h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $selectedUser->email }}</p>
                </div>
            </div>
            <button type="button" wire:click="closeUser" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <div class="grid flex-1 gap-6 overflow-y-auto p-6 lg:grid-cols-[1.2fr_0.8fr] text-xs">
            <div class="space-y-6">
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Joined</p>
                        <p class="mt-1 font-bold text-slate-900">{{ optional($selectedUser->created_at)->format('M d, Y') }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $selectedUser->email_verified_at ? 'Active' : 'Pending' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Roles</p>
                        <p class="mt-1 font-bold text-slate-900">{{ max($selectedUser->roles->count(), 0) }}</p>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 p-4 space-y-2">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Assigned Roles</h4>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($selectedUser->roles as $role)
                            <span class="rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 border border-indigo-100">{{ $role->name }}</span>
                        @empty
                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">No role assigned</span>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 p-4 space-y-2">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Teams</h4>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($selectedUser->teams as $team)
                            <span class="rounded-md bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700 border border-sky-100">{{ $team->name }}</span>
                        @empty
                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">No team assigned</span>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 p-4 space-y-2">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Permissions</h4>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($permissions as $permission)
                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 border border-slate-200">{{ str_replace('_', ' ', $permission) }}</span>
                        @empty
                            <span class="text-xs text-slate-500">This user does not inherit any permissions yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-lg border border-slate-200 p-4 space-y-2">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Change Role</h4>
                    <div class="space-y-2">
                        <select wire:change="assignRole({{ $selectedUser->id }}, $event.target.value)" class="w-full rounded-lg border-slate-200 text-xs font-semibold text-slate-700 shadow-xs focus:ring-0" {{ $selectedUser->id === auth()->id() ? 'disabled' : '' }}>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $selectedUser->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @if($selectedUser->id === auth()->id())
                            <p class="text-[10px] text-slate-400">Your own role cannot be changed here.</p>
                        @endif
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 p-4 space-y-2">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Assign Teams</h4>
                    <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                        @foreach($teams as $team)
                            <label class="flex items-start gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-2.5 hover:bg-white transition-colors cursor-pointer">
                                <input
                                    type="checkbox"
                                    class="mt-0.5 h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-0"
                                    wire:click="toggleTeamAssignment({{ $selectedUser->id }}, {{ $team->id }})"
                                    {{ $selectedUser->teams->contains('id', $team->id) ? 'checked' : '' }}
                                >
                                <div>
                                    <span class="block font-bold text-slate-900">{{ $team->name }}</span>
                                    <span class="block text-[10px] text-slate-500">
                                        {{ $team->description ?: 'No team description.' }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 p-4 space-y-2">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Actions</h4>
                    <div class="flex flex-col gap-2">
                        <button type="button" wire:click="toggleUserStatus({{ $selectedUser->id }})" class="rounded-lg bg-slate-900 py-2 font-semibold text-white transition hover:bg-slate-800 shadow-xs" {{ $selectedUser->id === auth()->id() ? 'disabled' : '' }}>
                            {{ $selectedUser->email_verified_at ? 'Mark as Pending' : 'Verify User' }}
                        </button>
                        <button type="button" wire:click="deleteUser({{ $selectedUser->id }})" wire:confirm="Delete this user account? This cannot be undone." class="rounded-lg border border-rose-200 bg-rose-50 py-2 font-semibold text-rose-600 transition hover:bg-rose-100" {{ $selectedUser->id === auth()->id() ? 'disabled' : '' }}>
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
