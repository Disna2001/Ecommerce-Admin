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
            <button wire:click="clearFilters" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">Clear</button>
            <button wire:click="exportCsv" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"><i class="fas fa-download mr-2"></i>Export CSV</button>
            <button wire:click="exportPdf" class="rounded-2xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700"><i class="fas fa-file-pdf mr-2"></i>Export PDF</button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Order Actions</p><p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['order_actions'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Invoice Actions</p><p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['invoice_actions'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Settings Changes</p><p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['settings_changes'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">POS Actions</p><p class="mt-2 text-2xl font-black text-slate-900">{{ $stats['pos_actions'] }}</p></div>
    </div>
</div>
