<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
    @foreach([
        ['total', 'Total Proof', 'Cumulative signals', 'fa-database', 'slate'],
        ['approved', 'Published', 'Storefront live', 'fa-check-circle', 'emerald'],
        ['pending', 'Awaiting Review', 'Queue status', 'fa-clock', 'amber'],
        ['flagged', 'Security Alert', 'Suspicious content', 'fa-flag', 'rose'],
        ['avg', 'Trust Pulse', 'Avg. Score / 5.0', 'fa-star', 'indigo']
    ] as [$key, $label, $sub, $icon, $color])
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 group-hover:bg-{{ $color }}-500 group-hover:text-white transition-colors">
                    <i class="fas {{ $icon }} text-xs"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none mb-1">{{ $label }}</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-900 leading-none">{{ $this->stats[$key] }}</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $sub }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
