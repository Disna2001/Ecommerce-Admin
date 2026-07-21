@props(['recentActivityLogs'])

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-semibold">
            <i class="fas fa-clock-rotate-left text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900">Recent Activity</h3>
            <p class="text-xs text-slate-500 font-medium">Latest system and admin events</p>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($recentActivityLogs as $log)
            <div class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200/80 bg-slate-50/50 hover:bg-white transition-colors duration-150">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($log->user->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-slate-900 truncate">{{ $log->user->name ?? 'System' }}</span>
                        <span class="text-[11px] font-medium text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-600 font-normal leading-relaxed mt-0.5">{{ $log->description }}</p>
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-xs text-slate-500 font-medium">
                No recent activity recorded.
            </div>
        @endforelse
    </div>

    <div class="mt-5 pt-4 border-t border-slate-100 text-center">
        <a href="{{ route('admin.activity-logs') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
            <span>View Full Activity Log</span>
            <i class="fas fa-chevron-right text-[10px]"></i>
        </a>
    </div>
</div>
