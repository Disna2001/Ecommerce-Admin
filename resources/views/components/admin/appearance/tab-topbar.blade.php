<div class="mt-12 pt-12 border-t border-slate-100 space-y-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 shadow-inner">
                <i class="fas fa-bullhorn text-lg"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Announcement Deck</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Global Marketing Pulse</p>
            </div>
        </div>

        <button 
            wire:click="$set('topbar_enabled', {{ !$topbar_enabled ? 'true' : 'false' }})"
            class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors focus:outline-none {{ $topbar_enabled ? 'bg-amber-500' : 'bg-slate-200' }}"
        >
            <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $topbar_enabled ? 'translate-x-9' : 'translate-x-1' }}"></span>
        </button>
    </div>

    @if($topbar_enabled)
        <div class="grid gap-6 lg:grid-cols-2 animate-in fade-in slide-in-from-top-4">
            <div class="lg:col-span-2 group">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-amber-500 transition-colors">Broadcast Message</label>
                <div class="mt-2 relative">
                    <input type="text" wire:model.live="topbar_text" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-4 text-sm font-bold text-slate-900 shadow-sm transition-all focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-amber-400">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Chromatic Signature</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ambient Start</label>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="color" wire:model.live="topbar_bg_from" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                            <input type="text" wire:model.live="topbar_bg_from" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[10px] font-black">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ambient End</label>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="color" wire:model.live="topbar_bg_to" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                            <input type="text" wire:model.live="topbar_bg_to" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[10px] font-black">
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 flex flex-col items-center justify-center text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Live Rail Preview</p>
                <div class="w-full rounded-full py-2 px-6 text-[10px] font-black text-white shadow-lg overflow-hidden whitespace-nowrap" style="background: linear-gradient(to right, {{ $topbar_bg_from }}, {{ $topbar_bg_to }})">
                    {{ $topbar_text ?: 'Previewing your broadcast message...' }}
                </div>
            </div>
        </div>
    @else
        <div class="rounded-[2rem] border border-dashed border-slate-200 bg-slate-50/50 p-12 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-slate-200 mb-4">
                <i class="fas fa-eye-slash text-xl"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">The announcement rail is currently offline.<br>Enable it to broadcast marketing signals.</p>
        </div>
    @endif
</div>
