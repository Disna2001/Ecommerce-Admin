<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
            <x-admin.icon name="fa-clock-rotate-left" />
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Recent Admin Activity</h3>
            <p class="mt-1 text-sm text-slate-500">A quick read on the latest workflow changes across the system.</p>
        </div>
    </div>
    <div class="mt-5 space-y-4">
        @forelse($recentActivityLogs as $activity)
            <div class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mt-1 h-3 w-3 rounded-full {{ str_contains($activity->action, 'deleted') || str_contains($activity->action, 'cancelled') ? 'bg-rose-500' : (str_contains($activity->action, 'payment') ? 'bg-emerald-500' : 'bg-indigo-500') }}"></div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $activity->action)) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $activity->description ?: 'An admin activity was recorded.' }} @if($activity->user)<span class="font-medium text-slate-600">by {{ $activity->user->name }}</span>@endif</p>
                    <p class="mt-2 text-xs font-medium text-slate-400">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No recent admin actions are available yet.</div>
        @endforelse
    </div>
    <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-sm font-medium text-slate-900">Access Model</p>
        <p class="mt-2 text-sm leading-7 text-slate-500">Page access is controlled by permissions, not just the Admin role. Use Roles & Permissions to decide exactly which modules each staff member can open.</p>
    </div>
</div>
