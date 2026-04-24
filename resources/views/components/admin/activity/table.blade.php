<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Activity Timeline</h3>
            <p class="mt-1 text-sm text-slate-500">Latest admin actions with actor, event type, and target context.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Action</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Description</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Related</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500"><button wire:click="sortBy('created_at')" class="inline-flex items-center gap-2"><span>When</span><i class="fas fa-sort text-xs"></i></button></th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($logs as $log)
                        <tr class="align-top">
                            <td class="px-4 py-4"><span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $log->action)) }}</span></td>
                            <td class="px-4 py-4 text-slate-600">
                                <div class="space-y-2">
                                    <p>{{ $log->description ?: 'No description was recorded for this action.' }}</p>
                                    @if(!empty($log->properties))
                                        <div class="rounded-2xl bg-slate-50 p-3 text-xs text-slate-500"><pre class="whitespace-pre-wrap break-words">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-slate-600">{{ $log->user?->name ?? 'System / Unknown' }}</td>
                            <td class="px-4 py-4 text-slate-600">
                                @if($log->subject_type || $log->subject_id)
                                    <div><p class="font-medium text-slate-800">{{ class_basename($log->subject_type ?? 'Unknown') }}</p><p class="text-xs text-slate-400">#{{ $log->subject_id ?? 'n/a' }}</p></div>
                                @else
                                    <span class="text-slate-400">General</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                @if($log->related_url)
                                    <a href="{{ $log->related_url }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white"><i class="fas fa-arrow-up-right-from-square"></i>{{ $log->related_label }}</a>
                                @else
                                    <span class="text-xs text-slate-400">No direct link</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-500"><p>{{ $log->created_at->format('Y-m-d H:i') }}</p><p class="mt-1 text-xs">{{ $log->created_at->diffForHumans() }}</p></td>
                            <td class="px-4 py-4"><button wire:click="openDetailModal({{ $log->id }})" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white"><i class="fas fa-eye"></i>Details</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">No admin activity logs match the current filters yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $logs->links() }}</div>
</div>
