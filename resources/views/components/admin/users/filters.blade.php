<x-admin.ui.panel padding="p-5">
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
</x-admin.ui.panel>
