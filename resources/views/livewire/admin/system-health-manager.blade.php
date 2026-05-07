<div class="space-y-8">
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200"><i class="fas fa-microchip text-2xl"></i></div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operations Control</p>
                    <h2 class="mt-1 text-3xl font-black text-slate-900 tracking-tight">System Health</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">Real-time diagnostic report on infrastructure readiness and service availability.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-1.5 shadow-inner">
                    <span class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-200">Stale Window</span>
                    <select wire:model.live="staleWindowMinutes" class="border-0 bg-transparent py-1 pl-3 pr-8 text-xs font-bold focus:ring-0">
                        <option value="5">5 Minutes</option>
                        <option value="15">15 Minutes</option>
                        <option value="30">30 Minutes</option>
                        <option value="60">1 Hour</option>
                    </select>
                </div>
                <button type="button" wire:click="$refresh" class="h-12 w-12 flex items-center justify-center rounded-2xl bg-white text-slate-400 border border-slate-200 hover:text-slate-900 hover:border-slate-900 transition-all shadow-sm">
                    <i class="fas fa-rotate text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <x-admin.health.hero :score="$score" :score-tone="$scoreTone" :metrics="$metrics" />

    <div class="grid gap-8 xl:grid-cols-[1fr_360px]">
        <div class="space-y-8">
            <x-admin.health.core-checks :checks="$checks" />
            <x-admin.health.attention :attention="$attention" />
        </div>

        <div class="space-y-8">
            <x-admin.health.operator-actions />
            <x-admin.health.checklist :checklist="$checklist" />
            <x-admin.health.commands :deploy-commands="$deployCommands" />
            <x-admin.health.signals :recent-signals="$recentSignals" />
        </div>
    </div>
</div>
