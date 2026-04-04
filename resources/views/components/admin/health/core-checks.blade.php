<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
            <i class="fas fa-heart-pulse"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Core Checks</h3>
            <p class="mt-1 text-sm text-slate-500">The fastest way to see whether the app is ready for daily production usage.</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach($checks as $check)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $check['status'] === 'healthy' ? 'bg-emerald-100 text-emerald-600' : ($check['status'] === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-slate-200 text-slate-600') }}">
                        <i class="fas {{ $check['icon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $check['label'] }}</p>
                        <p class="mt-1 text-sm font-medium {{ $check['status'] === 'healthy' ? 'text-emerald-700' : ($check['status'] === 'warning' ? 'text-amber-700' : 'text-slate-600') }}">{{ $check['value'] }}</p>
                        <p class="mt-2 text-xs leading-6 text-slate-500">{{ $check['help'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
