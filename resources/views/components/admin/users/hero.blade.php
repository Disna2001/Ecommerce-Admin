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
                        Manage verification, role assignments, and access health from one focused workspace.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <button type="button" wire:click="$set('statusFilter', 'admin')">
                    <x-admin.ui.metric label="Admins" :value="$attentionQueues['admins']" hint="Users with platform-wide access." tone="blue" :active="$statusFilter === 'admin'" />
                </button>
                <button type="button" wire:click="$set('selectedRole', '__no_role__')">
                    <x-admin.ui.metric label="No Role" :value="$attentionQueues['without_roles']" hint="Accounts that still need access rules." tone="amber" :active="$selectedRole === '__no_role__'" />
                </button>
                <button type="button" wire:click="$set('statusFilter', 'pending')">
                    <x-admin.ui.metric label="Pending" :value="$attentionQueues['unverified']" hint="Users waiting on verification." tone="emerald" :active="$statusFilter === 'pending'" />
                </button>
                <button type="button" wire:click="$set('statusFilter', 'new')">
                    <x-admin.ui.metric label="This Week" :value="$attentionQueues['new_this_week']" hint="Fresh signups that may need onboarding." tone="accent" :active="$statusFilter === 'new'" />
                </button>
            </div>
        </div>

        <x-admin.ui.panel padding="p-5" body-class="space-y-5">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Access Snapshot</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $filteredUsers }} visible</span>
                </div>
            </x-slot:header>

            <div class="grid gap-3">
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

            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-4 dark:border-slate-700">
                <p class="text-sm font-semibold text-slate-800 dark:text-white">Operating guide</p>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                    <li>Review users without roles first so nobody stays outside your permission system.</li>
                    <li>Use pending verification as a holding state instead of deleting accounts too early.</li>
                    <li>Open a user card before changing access so you can confirm their current permissions.</li>
                </ul>
            </div>
        </x-admin.ui.panel>
    </div>
</section>
