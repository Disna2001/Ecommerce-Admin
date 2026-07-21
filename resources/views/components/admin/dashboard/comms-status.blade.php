@props(['items'])

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-3 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-semibold">
                <i class="fas fa-server text-xs"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">System Status</h3>
                <p class="text-xs text-slate-500 font-medium">Service health and configuration status</p>
            </div>
        </div>

        <!-- Status Legend -->
        <div class="flex items-center gap-3 text-[11px] font-medium text-slate-500 self-start sm:self-auto">
            <span class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span>Operational</span>
            </span>
            <span class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                <span>Attention</span>
            </span>
            <span class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                <span>Failing</span>
            </span>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($items as [$label, $status, $icon, $colorClass])
            @php
                $dotColor = str_contains($colorClass, 'emerald') ? 'bg-emerald-500' : (str_contains($colorClass, 'rose') ? 'bg-rose-500' : 'bg-amber-500');
            @endphp
            <div class="flex items-center justify-between p-3.5 rounded-lg border border-slate-200/80 bg-slate-50/50 hover:bg-white transition-colors duration-150">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-white border border-slate-200 text-slate-500 shadow-xs">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-900 leading-tight">{{ $label }}</p>
                        <p class="text-[11px] font-medium {{ $colorClass }} mt-0.5">{{ $status }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $dotColor }}"></span>
                </div>
            </div>
        @endforeach
    </div>
</div>
