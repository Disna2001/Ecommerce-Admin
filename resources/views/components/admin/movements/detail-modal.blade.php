<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeDetailModal"></div>
        
        <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-2xl">
            <div class="border-b border-slate-100 bg-white px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1rem] bg-slate-900 text-white shadow-lg shadow-slate-200"><i class="fas fa-truck-ramp-box text-lg"></i></div>
                        <div>
                            <h3 class="text-xl font-black tracking-tight text-slate-900">Mutation Dossier</h3>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-widest">{{ $selectedMovement->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="h-10 w-10 flex items-center justify-center rounded-full border border-slate-100 bg-slate-50 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="max-h-[70vh] overflow-y-auto px-8 py-8 space-y-8">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Resource Identity</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedMovement->stock->name ?? 'Resource Unavailable' }}</p>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">{{ $selectedMovement->stock->sku ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mutation Direction</p>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full {{ $selectedMovement->direction === 'in' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                            <p class="text-sm font-bold text-slate-900 capitalize">{{ $selectedMovement->direction === 'in' ? 'Inbound Influx' : 'Outbound Depletion' }}</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Quantity Shift</p>
                        <p class="text-lg font-black {{ $selectedMovement->direction === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $selectedMovement->direction === 'in' ? '+' : '-' }}{{ $selectedMovement->quantity }} Units</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Balance State</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedMovement->pre_quantity }} <i class="fas fa-arrow-right text-[10px] mx-1 text-slate-300"></i> {{ $selectedMovement->post_quantity }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Operational Narrative</p>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm font-medium text-slate-700 leading-relaxed italic">
                        "{{ $selectedMovement->notes ?: 'No narrative provided for this mutation protocol.' }}"
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrative Agent</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedMovement->user->name ?? 'System Automated' }}</p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mutation Context</p>
                        <span class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-indigo-600 border border-indigo-100 shadow-sm">{{ str_replace('_', ' ', $selectedMovement->context) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-8 py-6">
                 @php
                    $relatedUrl = match($selectedMovement->context) {
                        'order_checkout', 'order_cancelled', 'order_refunded' => route('admin.orders', ['search' => $selectedMovement->stock->sku]),
                        'pos_sale' => route('admin.pos.logs'),
                        default => null
                    };
                @endphp

                @if($relatedUrl)
                    <a href="{{ $relatedUrl }}" wire:navigate class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-[10px] font-black text-white uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:bg-indigo-700 hover:scale-[1.02]">
                        <i class="fas fa-arrow-up-right-from-square"></i> Open Logistics Workspace
                    </a>
                @else
                    <div></div>
                @endif
                <button type="button" wire:click="closeDetailModal" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900">Close Dossier</button>
            </div>
        </div>
    </div>
</div>
