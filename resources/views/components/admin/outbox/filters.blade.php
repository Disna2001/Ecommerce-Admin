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
