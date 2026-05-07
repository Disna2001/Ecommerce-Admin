<div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Timestamp</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Agent</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Action Protocol</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Contextual Link</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($logs as $log)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-slate-900">{{ $log->created_at->format('M d, H:i') }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 overflow-hidden rounded-lg bg-slate-100 border border-slate-200">
                                    <img src="{{ $log->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($log->user->name ?? 'System').'&color=7F9CF5&background=EBF4FF' }}" class="h-full w-full object-cover">
                                </div>
                                <p class="text-xs font-bold text-slate-700">{{ $log->user->name ?? 'System Authority' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-widest text-slate-600">{{ $log->action }}</span>
                                </div>
                                <p class="text-xs text-slate-500 max-w-[300px] truncate">{{ $log->description }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->related_url)
                                <a href="{{ $log->related_url }}" wire:navigate class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-indigo-600 transition hover:bg-indigo-600 hover:text-white">
                                    <i class="fas fa-arrow-up-right-from-square"></i> {{ $log->related_label }}
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">No Link</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="openDetailModal({{ $log->id }})" class="h-8 w-8 rounded-lg text-slate-400 transition hover:bg-slate-900 hover:text-white shadow-sm">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-200">
                                    <i class="fas fa-terminal text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-500">No activity logs recorded in this segment</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $logs->links() }}
</div>
