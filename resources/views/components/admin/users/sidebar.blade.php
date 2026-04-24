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
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $recentUser->roles->pluck('name')->join(', ') ?: 'No role assigned' }}</p>
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
</div>
