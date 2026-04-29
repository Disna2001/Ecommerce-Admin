@props(['recentAccessChanges', 'roles', 'teams'])

<div class="space-y-6">
    <x-admin.ui.panel padding="p-5">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Recent Access Changes</h3>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">Live</span>
            </div>
        </x-slot:header>

        <div class="space-y-3">
            @foreach($recentAccessChanges as $recentUser)
                <button type="button" wire:click="openUser({{ $recentUser->id }})" class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-left transition hover:border-blue-200 hover:bg-blue-50/70 dark:border-slate-800 dark:hover:border-blue-500/30 dark:hover:bg-blue-500/10">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $recentUser->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $recentUser->roles->pluck('name')->join(', ') ?: 'No role assigned' }}
                                @if($recentUser->teams->isNotEmpty())
                                    · {{ $recentUser->teams->pluck('name')->join(', ') }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">{{ optional($recentUser->updated_at)->diffForHumans() }}</span>
                    </div>
                </button>
            @endforeach
        </div>
    </x-admin.ui.panel>

    <x-admin.ui.panel padding="p-5">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Role Coverage</h3>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $roles->count() }} roles</span>
            </div>
        </x-slot:header>

        <div class="space-y-3">
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
    </x-admin.ui.panel>

    <x-admin.ui.panel padding="p-5">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Operations Teams</h3>
                <button type="button" wire:click="$toggle('showTeamCreator')" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                    {{ $showTeamCreator ? 'Hide' : 'Add Team' }}
                </button>
            </div>
        </x-slot:header>

        <div class="space-y-3">
            @foreach($teams->take(8) as $team)
                <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $team->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $team->description ?: 'No team brief yet.' }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $team->users_count }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($showTeamCreator)
            <form wire:submit="createTeam" class="mt-5 space-y-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                <label class="block space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Team Name</span>
                    <input type="text" wire:model.defer="newTeamName" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                    @error('newTeamName') <span class="text-xs font-medium text-rose-500">{{ $message }}</span> @enderror
                </label>
                <label class="block space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Description</span>
                    <textarea wire:model.defer="newTeamDescription" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
                </label>
                <div class="grid gap-3 md:grid-cols-2">
                    <label class="block space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Color</span>
                        <select wire:model.defer="newTeamColor" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            <option value="sky">Sky</option>
                            <option value="emerald">Emerald</option>
                            <option value="amber">Amber</option>
                            <option value="violet">Violet</option>
                            <option value="rose">Rose</option>
                            <option value="slate">Slate</option>
                        </select>
                    </label>
                    <label class="block space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Default Role</span>
                        <select wire:model.defer="newTeamDefaultRole" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            <option value="">No automatic role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                    Create Team
                </button>
            </form>
        @endif
    </x-admin.ui.panel>
</div>
