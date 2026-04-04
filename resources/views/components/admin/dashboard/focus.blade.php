<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
            <x-admin.icon name="fa-user-shield" />
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Admin Focus</h3>
            <p class="mt-1 text-sm text-slate-500">Profile-level access and shortcuts for the current administrator.</p>
        </div>
    </div>
    <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-5">
        <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach($user->getRoleNames() as $role)
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 shadow-sm">{{ $role }}</span>
            @endforeach
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <a href="{{ route('profile.index', ['tab' => 'settings']) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300"><span class="flex items-center gap-3"><x-admin.icon name="fa-user-gear" class="h-4 w-4 text-indigo-500" /><span>Profile Settings</span></span></a>
            <a href="{{ route('admin.settings') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300"><span class="flex items-center gap-3"><x-admin.icon name="fa-sliders" class="h-4 w-4 text-violet-500" /><span>Admin Controls</span></span></a>
            @can('view activity logs')
                <a href="{{ route('admin.activity-logs') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300"><span class="flex items-center gap-3"><x-admin.icon name="fa-clock-rotate-left" class="h-4 w-4 text-rose-500" /><span>Activity Logs</span></span></a>
            @endcan
        </div>
    </div>
</div>
