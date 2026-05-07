@props(['recentActivityLogs'])

<div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-center gap-4 mb-8">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg">
            <i class="fas fa-fingerprint text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Administrative Audit</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Recent Registry Movements</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($recentActivityLogs as $log)
            <div class="relative flex items-start gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:border-slate-200 hover:shadow-md transition-all duration-300">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-white text-[10px] font-black uppercase flex-shrink-0 shadow-lg">
                    {{ substr($log->user->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest truncate">{{ $log->user->name ?? 'System' }}</span>
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[11px] font-bold text-slate-500 leading-relaxed">{{ $log->description }}</p>
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-50 text-slate-200 mb-4">
                    <i class="fas fa-box-archive text-xl"></i>
                </div>
                <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">No activity recorded</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 pt-6 border-t border-slate-50 text-center">
        <a href="{{ route('admin.activity-logs') }}" class="text-[9px] font-black text-indigo-600 uppercase tracking-[0.25em] hover:text-indigo-900 transition-colors">
            View Full Audit Trail
            <i class="fas fa-chevron-right ml-2 text-[7px]"></i>
        </a>
    </div>
</div>
