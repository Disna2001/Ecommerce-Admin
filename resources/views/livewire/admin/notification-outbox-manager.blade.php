<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total Notifications</p>
            <p class="mt-3 text-3xl font-black text-slate-900">{{ $stats['total'] }}</p>
            <p class="mt-2 text-sm text-slate-500">Queued, sent, failed, and skipped delivery records.</p>
        </div>
        <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Delivered</p>
            <p class="mt-3 text-3xl font-black text-emerald-700">{{ $stats['sent'] }}</p>
            <p class="mt-2 text-sm text-emerald-700">Notifications marked as sent successfully.</p>
        </div>
        <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600">Failed</p>
            <p class="mt-3 text-3xl font-black text-rose-700">{{ $stats['failed'] }}</p>
            <p class="mt-2 text-sm text-rose-700">Records needing operator attention or retry logic.</p>
        </div>
        <div class="rounded-[1.75rem] border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600">Retries & Rate</p>
            <p class="mt-3 text-3xl font-black text-indigo-700">{{ $stats['retried'] }}</p>
            <p class="mt-2 text-sm text-indigo-700">{{ $stats['failure_rate'] }}% overall failure rate.</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <i class="fas fa-server"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Provider Health</h3>
                    <p class="mt-1 text-sm text-slate-500">Delivery performance across configured providers.</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($analytics['providerHealth'] as $provider)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $provider->provider }}</p>
                            <span class="text-xs font-semibold {{ $provider->failed_count > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $provider->failed_count > 0 ? $provider->failed_count . ' failed' : 'healthy' }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $provider->sent_count }} sent out of {{ $provider->total_count }} total records.</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">No provider data is available yet.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                    <i class="fas fa-user-xmark"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Repeat Failure Targets</h3>
                    <p class="mt-1 text-sm text-slate-500">Recipients with the most failed or skipped deliveries.</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($analytics['failingRecipients'] as $recipient)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $recipient->recipient }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $recipient->failure_count }} failed attempts recorded.</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">No repeated failures are recorded right now.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Message Mix</h3>
                    <p class="mt-1 text-sm text-slate-500">Top message subjects and related notification types.</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($analytics['messageTypes'] as $type)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Str::limit($type->label, 40) }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $type->total_count }} records in the outbox.</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">No message distribution data is available yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 xl:grid-cols-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search recipient or subject..." class="rounded-2xl border-slate-200 text-sm shadow-none xl:col-span-2">
            <select wire:model.live="channelFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
                <option value="">All channels</option>
                <option value="email">Email</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
            <select wire:model.live="statusFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
                <option value="">All statuses</option>
                <option value="queued">Queued</option>
                <option value="sent">Sent</option>
                <option value="failed">Failed</option>
                <option value="skipped">Skipped</option>
            </select>
            <input type="date" wire:model.live="dateFrom" class="rounded-2xl border-slate-200 text-sm shadow-none">
            <input type="date" wire:model.live="dateTo" class="rounded-2xl border-slate-200 text-sm shadow-none">
        </div>

        <div class="mt-4 flex flex-wrap gap-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Email: <span class="font-semibold text-slate-900">{{ $stats['email'] }}</span></div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">WhatsApp: <span class="font-semibold text-slate-900">{{ $stats['whatsapp'] }}</span></div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Queued: <span class="font-semibold text-slate-900">{{ $stats['queued'] }}</span></div>
            <button wire:click="clearFilters" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Clear Filters</button>
        </div>
    </div>

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

    @if($showDetailModal && $selectedOutbox)
        <div class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div class="max-h-[85vh] w-full max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Notification Detail</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ strtoupper($selectedOutbox->channel) }} Delivery</h3>
                    </div>
                    <button wire:click="closeDetailModal" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">Close</button>
                </div>
                <div class="grid max-h-[calc(85vh-88px)] gap-6 overflow-y-auto p-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Summary</p>
                            <div class="mt-3 space-y-2 text-sm text-slate-600">
                                <p><span class="font-semibold text-slate-900">Recipient:</span> {{ $selectedOutbox->recipient ?: 'n/a' }}</p>
                                <p><span class="font-semibold text-slate-900">Subject:</span> {{ $selectedOutbox->subject ?: 'n/a' }}</p>
                                <p><span class="font-semibold text-slate-900">Status:</span> {{ ucfirst($selectedOutbox->status) }}</p>
                                <p><span class="font-semibold text-slate-900">Provider:</span> {{ $selectedOutbox->provider ?: 'n/a' }}</p>
                                <p><span class="font-semibold text-slate-900">Attempts:</span> {{ $selectedOutbox->attempt_count ?? 1 }}</p>
                                <p><span class="font-semibold text-slate-900">Last Attempt:</span> {{ optional($selectedOutbox->last_attempt_at)->format('Y-m-d H:i:s') ?: 'n/a' }}</p>
                                <p><span class="font-semibold text-slate-900">Queued:</span> {{ optional($selectedOutbox->queued_at)->format('Y-m-d H:i:s') ?: 'n/a' }}</p>
                                <p><span class="font-semibold text-slate-900">Sent:</span> {{ optional($selectedOutbox->sent_at)->format('Y-m-d H:i:s') ?: 'n/a' }}</p>
                            </div>
                        </div>
                        @if($selectedOutbox->failure_message)
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-500">Failure Message</p>
                                <p class="mt-3 text-sm leading-6 text-rose-700">{{ $selectedOutbox->failure_message }}</p>
                            </div>
                        @endif
                        @if(in_array($selectedOutbox->status, ['failed', 'skipped']))
                            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-500">Operator Action</p>
                                <p class="mt-3 text-sm leading-6 text-indigo-700">You can retry this delivery after correcting provider settings, recipient details, or temporary transport issues.</p>
                                <button wire:click="retryOutbox({{ $selectedOutbox->id }})" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                    <i class="fas fa-rotate-right"></i>
                                    <span>Retry Delivery</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Payload</p>
                        <pre class="mt-3 whitespace-pre-wrap break-words text-xs leading-6 text-slate-600">{{ json_encode($selectedOutbox->payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
