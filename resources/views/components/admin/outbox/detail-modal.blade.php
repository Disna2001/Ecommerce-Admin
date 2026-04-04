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
