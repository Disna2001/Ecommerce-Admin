<div 
    x-data="{ show: @entangle('isOpen') }" 
    x-show="show" 
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto custom-scrollbar"
    x-cloak
>
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="relative w-full max-w-5xl rounded-[3rem] border border-slate-200 bg-white shadow-2xl overflow-hidden"
    >
        <!-- Modal Header -->
        <div class="border-b border-slate-100 bg-slate-50/50 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Promotion Architecture</p>
                    <h3 class="mt-2 text-2xl font-black tracking-tight text-slate-900">{{ $discount_id ? 'Refine Strategy' : 'Architect New Strategy' }}</h3>
                </div>
                <button @click="show = false" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm transition-all hover:bg-slate-900 hover:text-white">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Core Strategy Section -->
                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Core Incentive</p>
                        <div class="space-y-6">
                            <div class="group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Strategy Name</label>
                                <input type="text" wire:model="name" placeholder="e.g. Summer Clearance" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                                @error('name') <p class="mt-1 text-[9px] font-black text-rose-500 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="group/input">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Coupon Code</label>
                                    <div class="relative mt-2">
                                        <input type="text" wire:model="code" placeholder="Leave empty for auto" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 shadow-inner pr-12 uppercase">
                                        <button wire:click="generateCode" type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-indigo-500 transition-colors">
                                            <i class="fas fa-random text-xs"></i>
                                        </button>
                                    </div>
                                    @error('code') <p class="mt-1 text-[9px] font-black text-rose-500 uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="group/input">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Value Type</label>
                                    <select wire:model.live="type" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-3 shadow-inner">
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount (Rs.)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Discount Value</label>
                                <input type="number" step="0.01" wire:model="value" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-900 shadow-inner">
                                @error('value') <p class="mt-1 text-[9px] font-black text-rose-500 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Thresholds & Limits</p>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Min. Order Rs.</label>
                                <input type="number" wire:model="min_order_amount" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                            </div>
                            <div class="group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Usage Limit</label>
                                <input type="number" wire:model="usage_limit" placeholder="∞" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                            </div>
                        </div>
                        @if($type === 'percentage')
                            <div class="mt-6 group/input animate-in fade-in slide-in-from-top-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Max Discount Rs. (Cap)</label>
                                <input type="number" wire:model="max_discount_amount" placeholder="No cap" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Scope & Pulse Section -->
                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Strategic Scope</p>
                        <div class="space-y-6">
                            <div class="group/input">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Application Range</label>
                                <select wire:model.live="scope" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-3 shadow-inner">
                                    <option value="all">Global (All Products)</option>
                                    <option value="category">Specific Collection</option>
                                    <option value="product">Specific Product</option>
                                </select>
                            </div>

                            @if($scope !== 'all')
                                <div class="group/input animate-in fade-in slide-in-from-top-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Select Target {{ ucfirst($scope) }}</label>
                                    <select wire:model="scope_id" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-3 shadow-inner">
                                        <option value="">Choose...</option>
                                        @if($scope === 'category')
                                            @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                                        @else
                                            @foreach($products as $prod) <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }})</option> @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Time-Based Pulse</p>
                        <div class="flex items-center gap-4 mb-6">
                             <button 
                                wire:click="$set('has_timer', {{ !$has_timer ? 'true' : 'false' }})"
                                type="button"
                                class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors {{ $has_timer ? 'bg-sky-500' : 'bg-slate-200' }}"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $has_timer ? 'translate-x-7' : 'translate-x-1' }}"></span>
                            </button>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Enable Offer Timer</span>
                        </div>

                        @if($has_timer)
                            <div class="space-y-6 animate-in fade-in slide-in-from-top-4">
                                <div class="grid gap-6 md:grid-cols-2">
                                    <div class="group/input">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Launch Date</label>
                                        <input type="datetime-local" wire:model="starts_at" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                                    </div>
                                    <div class="group/input">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Expiry Date</label>
                                        <input type="datetime-local" wire:model="ends_at" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                    <button 
                                        wire:click="$set('show_timer_on_site', {{ !$show_timer_on_site ? 'true' : 'false' }})"
                                        type="button"
                                        class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors {{ $show_timer_on_site ? 'bg-amber-500' : 'bg-white' }}"
                                    >
                                        <span class="inline-block h-4 w-4 transform rounded-full {{ $show_timer_on_site ? 'bg-white' : 'bg-slate-200' }} transition-transform {{ $show_timer_on_site ? 'translate-x-7' : 'translate-x-1' }}"></span>
                                    </button>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">Public Countdown</p>
                                        <input type="text" wire:model="timer_label" class="mt-2 bg-transparent border-none p-0 text-[10px] font-bold text-slate-400 focus:ring-0 w-full">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-[2rem] bg-slate-900 text-white shadow-xl">
                        <button 
                            wire:click="$set('is_active', {{ !$is_active ? 'true' : 'false' }})"
                            type="button"
                            class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors {{ $is_active ? 'bg-emerald-500' : 'bg-white/10' }}"
                        >
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $is_active ? 'translate-x-9' : 'translate-x-1' }}"></span>
                        </button>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest leading-none">Strategy Activation</p>
                            <p class="text-[9px] font-bold text-white/40 uppercase tracking-tighter mt-1">{{ $is_active ? 'Live & Redeemable' : 'Draft Protocol' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-slate-100 bg-slate-50/50 p-8 flex items-center justify-end gap-3">
            <button @click="show = false" class="rounded-2xl px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">Discard Draft</button>
            <button wire:click="store" class="flex items-center gap-3 rounded-2xl bg-slate-900 px-10 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fas fa-check text-[10px] opacity-50"></i>
                Finalize Strategy
            </button>
        </div>
    </div>
</div>
