<div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Pipeline Filters</p>
    
    <div class="space-y-4">
        <div class="space-y-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Channel</label>
            <select wire:model.live="channelFilter" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-bold focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                <option value="">All Mediums</option>
                <option value="whatsapp">WhatsApp Cloud</option>
                <option value="email">SMTP / Mail</option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Delivery Status</label>
            <select wire:model.live="statusFilter" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-bold focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                <option value="">All States</option>
                <option value="sent">Delivered</option>
                <option value="queued">Queued</option>
                <option value="failed">Failed</option>
                <option value="skipped">Skipped</option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Temporal Range</label>
            <div class="grid grid-cols-2 gap-2">
                <input type="date" wire:model.live="dateFrom" class="rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                <input type="date" wire:model.live="dateTo" class="rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
            </div>
        </div>

        <button type="button" wire:click="clearFilters" class="mt-4 w-full rounded-2xl border border-slate-100 bg-slate-50 py-3 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all">
            Purge Filters
        </button>
    </div>
</div>
