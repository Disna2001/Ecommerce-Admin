<div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Channel</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Recipient</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Subject</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Related</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">When</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($notifications as $notification)
                    <tr>
                        <td class="px-4 py-4"><span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">{{ strtoupper($notification->channel) }}</span></td>
                        <td class="px-4 py-4 text-slate-600">{{ $notification->recipient ?: 'n/a' }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $notification->subject ?: 'No subject' }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $notification->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($notification->status === 'failed' ? 'bg-rose-100 text-rose-700' : ($notification->status === 'skipped' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')) }}">
                                {{ ucfirst($notification->status) }}
                            </span>
                            @if(($notification->attempt_count ?? 1) > 1)
                                <p class="mt-2 text-xs font-medium text-indigo-600">{{ $notification->attempt_count }} attempts</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-slate-600">{{ class_basename($notification->related_type ?? 'General') }} @if($notification->related_id)#{{ $notification->related_id }}@endif</td>
                        <td class="px-4 py-4 text-slate-500">{{ optional($notification->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-2">
                                <button wire:click="openDetailModal({{ $notification->id }})" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                                @if(in_array($notification->status, ['failed', 'skipped']))
                                    <button wire:click="retryOutbox({{ $notification->id }})" class="inline-flex items-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                        <i class="fas fa-rotate-right"></i> Retry
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No notification outbox records match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $notifications->links() }}</div>
</div>
