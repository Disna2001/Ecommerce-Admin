<div class="space-y-6">
    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Production Readiness</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">System Health Center</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Track queue readiness, delivery health, storage availability, and service configuration from one production-focused screen.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border {{ $scoreTone === 'emerald' ? 'border-emerald-200 bg-emerald-50' : ($scoreTone === 'amber' ? 'border-amber-200 bg-amber-50' : 'border-rose-200 bg-rose-50') }} p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] {{ $scoreTone === 'emerald' ? 'text-emerald-500' : ($scoreTone === 'amber' ? 'text-amber-500' : 'text-rose-500') }}">Hosting Score</p>
                    <p class="mt-2 text-3xl font-black {{ $scoreTone === 'emerald' ? 'text-emerald-700' : ($scoreTone === 'amber' ? 'text-amber-700' : 'text-rose-700') }}">{{ $score }}%</p>
                    <p class="mt-1 text-sm {{ $scoreTone === 'emerald' ? 'text-emerald-700/80' : ($scoreTone === 'amber' ? 'text-amber-700/80' : 'text-rose-700/80') }}">Overall hosted readiness.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Queued</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $metrics['queued'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Outbox entries waiting.</p>
                </div>
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-500">Stale Queue</p>
                    <p class="mt-2 text-3xl font-black text-rose-700">{{ $metrics['stale_queued'] }}</p>
                    <p class="mt-1 text-sm text-rose-700/80">Older than {{ $this->staleWindowMinutes }} minutes.</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-500">Failed</p>
                    <p class="mt-2 text-3xl font-black text-amber-700">{{ $metrics['failed'] }}</p>
                    <p class="mt-1 text-sm text-amber-700/80">Need review or retry.</p>
                </div>
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Retried</p>
                    <p class="mt-2 text-3xl font-black text-indigo-700">{{ $metrics['retried'] }}</p>
                    <p class="mt-1 text-sm text-indigo-700/80">Entries with multiple attempts.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <i class="fas fa-heart-pulse"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Core Checks</h3>
                        <p class="mt-1 text-sm text-slate-500">The fastest way to see whether the app is ready for daily production usage.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($checks as $check)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl
                                    {{ $check['status'] === 'healthy' ? 'bg-emerald-100 text-emerald-600' : ($check['status'] === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-slate-200 text-slate-600') }}">
                                    <i class="fas {{ $check['icon'] }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $check['label'] }}</p>
                                    <p class="mt-1 text-sm font-medium {{ $check['status'] === 'healthy' ? 'text-emerald-700' : ($check['status'] === 'warning' ? 'text-amber-700' : 'text-slate-600') }}">{{ $check['value'] }}</p>
                                    <p class="mt-2 text-xs leading-6 text-slate-500">{{ $check['help'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                        <i class="fas fa-siren-on"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Immediate Attention</h3>
                        <p class="mt-1 text-sm text-slate-500">Operational issues that should be handled before routine admin work.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($attention as $item)
                        <a href="{{ $item['route'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl
                                    {{ $item['tone'] === 'rose' ? 'bg-rose-100 text-rose-600' : ($item['tone'] === 'amber' ? 'bg-amber-100 text-amber-600' : ($item['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600')) }}">
                                    <i class="fas {{ $item['icon'] }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $item['count'] }}</p>
                                    <p class="mt-2 text-xs leading-6 text-slate-500">{{ $item['note'] }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No urgent production issues are visible right now.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                        <i class="fas fa-arrow-up-right-dots"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Operator Actions</h3>
                        <p class="mt-1 text-sm text-slate-500">Quick jumps into the pages you’ll use most during incidents.</p>
                    </div>
                </div>
                <div class="mt-5 grid gap-3">
                    <a href="{{ route('admin.notification-outbox') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
                        <span class="flex items-center gap-3"><i class="fas fa-inbox text-indigo-500"></i><span>Open Notification Outbox</span></span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
                        <span class="flex items-center gap-3"><i class="fas fa-sliders text-violet-500"></i><span>Review System Settings</span></span>
                    </a>
                    <a href="{{ route('admin.stock-movements') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
                        <span class="flex items-center gap-3"><i class="fas fa-arrow-right-arrow-left text-emerald-500"></i><span>Inspect Stock Ledger</span></span>
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
                        <span class="flex items-center gap-3"><i class="fas fa-clock-rotate-left text-rose-500"></i><span>Open Audit Trail</span></span>
                    </a>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Deployment Checklist</h3>
                        <p class="mt-1 text-sm text-slate-500">The quickest hosted-go-live review for this current environment.</p>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @foreach($checklist as $item)
                        <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                                <p class="mt-2 text-xs leading-6 text-slate-500">{{ $item['help'] }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['ready'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $item['ready'] ? 'Ready' : 'Review' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

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

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                        <i class="fas fa-wave-square"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Recent Operational Signals</h3>
                        <p class="mt-1 text-sm text-slate-500">Latest admin events that help explain current system state.</p>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse($recentSignals as $signal)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $signal->action)) }}</p>
                            <p class="mt-2 text-xs leading-6 text-slate-500">
                                {{ $signal->description ?: 'An admin action was recorded.' }}
                                @if($signal->user)
                                    by {{ $signal->user->name }}
                                @endif
                            </p>
                            <p class="mt-2 text-xs font-medium text-slate-400">{{ $signal->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No recent signals are available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
