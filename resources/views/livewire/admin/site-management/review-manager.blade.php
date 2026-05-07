<div class="space-y-6">
    <!-- Social Proof Control Deck -->
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
        <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Trust Orchestration</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Review Moderator</h1>
                <p class="mt-2 max-w-xl text-sm font-medium leading-relaxed text-slate-500">Curate customer social proof, moderate feedback loops, and manage your storefront's trust signals to drive conversion confidence.</p>
            </div>
            @if(count($selected) > 0)
                <div class="flex items-center gap-2 rounded-2xl bg-slate-50 p-2 border border-slate-100 animate-in fade-in zoom-in-95">
                    <span class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ count($selected) }} Selected</span>
                    <button wire:click="bulkApprove" class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 transition-all hover:scale-105"><i class="fas fa-check text-xs"></i></button>
                    <button wire:click="bulkReject" class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg shadow-slate-900/20 transition-all hover:scale-105"><i class="fas fa-eye-slash text-xs"></i></button>
                    <button onclick="confirm('Permanently purge these signals?') || event.stopImmediatePropagation()" wire:click="bulkDelete" class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500 text-white shadow-lg shadow-rose-500/20 transition-all hover:scale-105"><i class="fas fa-trash-alt text-xs"></i></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Review Intelligence Summary -->
    @include('components.admin.reviews.summary')

    <!-- Discovery & Moderation Hub -->
    <div class="space-y-6">
        @include('components.admin.reviews.filters')
        
        <div class="rounded-[2.5rem] border border-slate-200 bg-white p-2 shadow-sm">
            <div class="rounded-[2.25rem] bg-slate-50/50 p-6 min-h-[400px]">
                @include('components.admin.reviews.table')
            </div>
        </div>
    </div>

    <!-- Feedback Workspace -->
    @include('components.admin.reviews.view-modal')
    @include('components.admin.reviews.edit-modal')

    <div wire:loading class="fixed bottom-8 right-8 z-[60]">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-5 py-3 text-white shadow-2xl shadow-slate-400">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Moderating Proof...</span>
        </div>
    </div>
</div>
