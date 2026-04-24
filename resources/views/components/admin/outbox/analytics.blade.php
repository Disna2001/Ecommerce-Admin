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
