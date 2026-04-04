<div class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
    <div class="max-h-[85vh] w-full max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Audit Record</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $selectedLog->action)) }}</h3>
            </div>
            <button wire:click="closeDetailModal" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">Close</button>
        </div>

        <div class="grid max-h-[calc(85vh-88px)] gap-6 overflow-y-auto p-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Summary</p><p class="mt-3 text-sm leading-6 text-slate-600">{{ $selectedLog->description ?: 'No description was recorded for this action.' }}</p></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actor</p><p class="mt-3 text-sm font-semibold text-slate-900">{{ $selectedLog->user?->name ?? 'System / Unknown' }}</p><p class="mt-1 text-sm text-slate-500">{{ $selectedLog->created_at->format('Y-m-d H:i:s') }}</p><p class="mt-1 text-xs text-slate-400">{{ $selectedLog->created_at->diffForHumans() }}</p></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Target</p><p class="mt-3 text-sm font-semibold text-slate-900">{{ class_basename($selectedLog->subject_type ?? 'General') }}</p><p class="mt-1 text-sm text-slate-500">Record ID: {{ $selectedLog->subject_id ?? 'n/a' }}</p>@if($selectedLog->related_url)<a href="{{ $selectedLog->related_url }}" class="mt-4 inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"><i class="fas fa-arrow-up-right-from-square"></i>{{ $selectedLog->related_label }}</a>@endif</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stored Payload</p>
                <pre class="mt-3 whitespace-pre-wrap break-words text-xs leading-6 text-slate-600">{{ json_encode($selectedLog->properties ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</div>
