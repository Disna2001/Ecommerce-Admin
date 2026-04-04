<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                <x-admin.icon name="fa-layer-group" />
            </div>
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Functional Modules</h3>
                <p class="mt-1 text-sm text-slate-500">Only modules you have permission to access are shown here.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse($enabledModules as $module)
            <a href="{{ $module['route'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <x-admin.icon :name="$module['icon']" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900">{{ $module['title'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $module['desc'] }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">No admin modules are assigned to your current permission set.</div>
        @endforelse
    </div>
</div>
