<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                <x-admin.icon name="fa-bolt" />
            </div>
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Quick Actions</h3>
                <p class="mt-1 text-sm text-slate-500">Jump into the most common admin tasks without digging through the menu.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach($quickActions as $action)
            <a href="{{ $action['route'] }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <x-admin.icon :name="$action['icon']" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900">{{ $action['title'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $action['desc'] }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
