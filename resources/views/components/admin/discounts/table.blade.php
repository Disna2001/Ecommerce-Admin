<div class="space-y-4">
    @forelse($discounts as $discount)
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group hover:border-slate-900 transition-all">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
                <!-- Promo Type & Identity -->
                <div class="flex items-center gap-4 min-w-[240px]">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl shadow-slate-200">
                        @if($discount->type === 'percentage')
                            <span class="text-sm font-black">{{ (int)$discount->value }}%</span>
                        @else
                            <span class="text-sm font-black">Rs.</span>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 tracking-tight">{{ $discount->name }}</h4>
                        @if($discount->code)
                            <div class="mt-1 flex items-center gap-2">
                                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-[9px] font-black text-indigo-600 uppercase tracking-widest border border-indigo-100">{{ $discount->code }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Coupon Code</span>
                            </div>
                        @else
                            <div class="mt-1 flex items-center gap-2">
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[9px] font-black text-emerald-600 uppercase tracking-widest border border-emerald-100">Automated</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Auto-Applied</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Strategic Context -->
                <div class="flex-1 min-w-0 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Scope & Threshold</p>
                        <div class="flex items-center gap-2">
                            <i class="fas {{ $discount->scope === 'all' ? 'fa-globe' : ($discount->scope === 'category' ? 'fa-folder' : 'fa-box') }} text-[10px] text-slate-300"></i>
                            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">
                                {{ ucfirst($discount->scope) }}
                                @if($discount->min_order_amount > 0)
                                    <span class="ml-1 text-slate-400">| Min Rs.{{ number_format($discount->min_order_amount) }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Pulse</p>
                        <div class="flex items-center gap-2">
                            @if($discount->starts_at || $discount->ends_at)
                                <i class="fas fa-clock text-[10px] text-sky-400"></i>
                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">
                                    {{ $discount->starts_at ? $discount->starts_at->format('M d') : 'Now' }} 
                                    → 
                                    {{ $discount->ends_at ? $discount->ends_at->format('M d') : '∞' }}
                                </span>
                            @else
                                <i class="fas fa-infinity text-[10px] text-slate-300"></i>
                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">Persistent Offer</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Usage Signal</p>
                        <div class="flex items-center gap-3">
                             <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $discount->usage_limit ? min(100, ($discount->orders_count / $discount->usage_limit) * 100) : 0 }}%"></div>
                             </div>
                             <span class="text-[9px] font-black text-slate-900">{{ $discount->orders_count }} / {{ $discount->usage_limit ?: '∞' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Hub -->
                <div class="flex items-center gap-3 lg:border-l lg:border-slate-100 lg:pl-6">
                    <button 
                        wire:click="toggleActive({{ $discount->id }})"
                        class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors {{ $discount->is_active ? 'bg-emerald-500' : 'bg-slate-200' }}"
                    >
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $discount->is_active ? 'translate-x-8' : 'translate-x-1' }}"></span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $discount->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                        <button 
                            onclick="confirm('Decommission this promotion?') || event.stopImmediatePropagation()"
                            wire:click="delete({{ $discount->id }})" 
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm"
                        >
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="py-20 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[2rem] bg-white text-slate-200 shadow-sm mb-6">
                <i class="fas fa-ticket-alt text-3xl"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">No promotions found.<br>Deploy your first incentive to drive conversions.</p>
        </div>
    @endforelse

    <div class="pt-6">
        {{ $discounts->links() }}
    </div>
</div>
