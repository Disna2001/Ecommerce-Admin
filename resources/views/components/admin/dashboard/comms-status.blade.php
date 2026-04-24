<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <x-admin.icon name="fa-satellite-dish" />
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Comms & AI Status</h3>
            <p class="mt-1 text-sm text-slate-500">Monitor automation systems and assistant readiness.</p>
        </div>
    </div>
    <div class="mt-5 space-y-4">
        @foreach($items as [$label, $value, $icon, $color])
            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white">
                    <x-admin.icon :name="$icon" class="h-5 w-5 {{ $color }}" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $label }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $value }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
