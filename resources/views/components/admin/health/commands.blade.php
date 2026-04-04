<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
            <i class="fas fa-terminal"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Recommended Commands</h3>
            <p class="mt-1 text-sm text-slate-500">Run these on the server after configuration changes or before going live.</p>
        </div>
    </div>
    <div class="mt-5 space-y-3">
        @foreach($deployCommands as $command)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <code class="text-sm font-semibold text-slate-800">{{ $command }}</code>
            </div>
        @endforeach
    </div>
</div>
