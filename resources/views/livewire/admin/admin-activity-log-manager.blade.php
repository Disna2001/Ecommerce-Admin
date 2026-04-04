<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total Activity</p>
            <p class="mt-3 text-3xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
            <p class="mt-2 text-sm text-slate-500">All recorded admin actions across the system.</p>
        </div>
        <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Today</p>
            <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($stats['today']) }}</p>
            <p class="mt-2 text-sm text-emerald-700">Actions captured since midnight.</p>
        </div>
        <div class="rounded-[1.75rem] border border-violet-200 bg-violet-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-violet-600">High Impact</p>
            <p class="mt-3 text-3xl font-black text-violet-700">{{ number_format($stats['order_actions'] + $stats['invoice_actions'] + $stats['settings_changes']) }}</p>
            <p class="mt-2 text-sm text-violet-700">Orders, invoices, and settings changes combined.</p>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Activity Filters</h3>
                <p class="mt-1 text-sm text-slate-500">Trace work by person, action area, or date range.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-8">
                <input type="text" wire:model.live.debounce.350ms="search" placeholder="Search actions..." class="rounded-2xl border-slate-200 text-sm shadow-none">
                <select wire:model.live="actionFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All action groups</option>
                    <option value="order.">Orders</option>
                    <option value="invoice.">Invoices</option>
                    <option value="settings.">Settings</option>
                    <option value="pos.">POS</option>
                </select>
                <select wire:model.live="userFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="">All users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <input type="date" wire:model.live="dateFrom" class="rounded-2xl border-slate-200 text-sm shadow-none">
                <input type="date" wire:model.live="dateTo" class="rounded-2xl border-slate-200 text-sm shadow-none">
                <button wire:click="clearFilters" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">
                    Clear
                </button>
                <button wire:click="exportCsv" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </button>
                <button wire:click="exportPdf" class="rounded-2xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-900">Order Actions</p>
                <p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['order_actions'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-900">Invoice Actions</p>
                <p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['invoice_actions'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-900">Settings Changes</p>
                <p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['settings_changes'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-900">POS Actions</p>
                <p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['pos_actions'] }}</p>
            </div>
        </div>
    </div>

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
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">
                                <button wire:click="sortBy('created_at')" class="inline-flex items-center gap-2">
                                    <span>When</span>
                                    <i class="fas fa-sort text-xs"></i>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($logs as $log)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">
                                        {{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    <div class="space-y-2">
                                        <p>{{ $log->description ?: 'No description was recorded for this action.' }}</p>
                                        @if(!empty($log->properties))
                                            <div class="rounded-2xl bg-slate-50 p-3 text-xs text-slate-500">
                                                <pre class="whitespace-pre-wrap break-words">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ $log->user?->name ?? 'System / Unknown' }}
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    @if($log->subject_type || $log->subject_id)
                                        <div>
                                            <p class="font-medium text-slate-800">{{ class_basename($log->subject_type ?? 'Unknown') }}</p>
                                            <p class="text-xs text-slate-400">#{{ $log->subject_id ?? 'n/a' }}</p>
                                        </div>
                                    @else
                                        <span class="text-slate-400">General</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    @if($log->related_url)
                                        <a href="{{ $log->related_url }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                                            <i class="fas fa-arrow-up-right-from-square"></i>
                                            {{ $log->related_label }}
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">No direct link</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-slate-500">
                                    <p>{{ $log->created_at->format('Y-m-d H:i') }}</p>
                                    <p class="mt-1 text-xs">{{ $log->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <button wire:click="openDetailModal({{ $log->id }})" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                                        <i class="fas fa-eye"></i>
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No admin activity logs match the current filters yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $logs->links() }}
        </div>
    </div>

    @if($showDetailModal && $selectedLog)
        <div class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div class="max-h-[85vh] w-full max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Audit Record</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $selectedLog->action)) }}</h3>
                    </div>
                    <button wire:click="closeDetailModal" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">
                        Close
                    </button>
                </div>

                <div class="grid max-h-[calc(85vh-88px)] gap-6 overflow-y-auto p-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Summary</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $selectedLog->description ?: 'No description was recorded for this action.' }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actor</p>
                            <p class="mt-3 text-sm font-semibold text-slate-900">{{ $selectedLog->user?->name ?? 'System / Unknown' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $selectedLog->created_at->format('Y-m-d H:i:s') }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $selectedLog->created_at->diffForHumans() }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Target</p>
                            <p class="mt-3 text-sm font-semibold text-slate-900">{{ class_basename($selectedLog->subject_type ?? 'General') }}</p>
                            <p class="mt-1 text-sm text-slate-500">Record ID: {{ $selectedLog->subject_id ?? 'n/a' }}</p>
                            @if($selectedLog->related_url)
                                <a href="{{ $selectedLog->related_url }}" class="mt-4 inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                    {{ $selectedLog->related_label }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stored Payload</p>
                        <pre class="mt-3 whitespace-pre-wrap break-words text-xs leading-6 text-slate-600">{{ json_encode($selectedLog->properties ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
