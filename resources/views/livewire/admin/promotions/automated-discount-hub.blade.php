<div class="min-h-screen bg-[#F8FAFC] p-4 lg:p-8">
    <div class="mx-auto max-w-[1400px] space-y-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white shadow-lg">
                        <i class="fas fa-robot text-[10px]"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Strategy Engine</p>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900">Automated Discount Hub</h1>
            </div>
            <div class="flex items-center gap-4">
                <button wire:click="runOrchestration" wire:loading.attr="disabled" class="group relative flex h-14 items-center gap-4 rounded-2xl bg-slate-900 px-10 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <div wire:loading wire:target="runOrchestration" class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
                    <i wire:loading.remove wire:target="runOrchestration" class="fas fa-bolt text-indigo-400 group-hover:rotate-12 transition-transform"></i>
                    <span>Execute Daily Sync</span>
                </button>
            </div>
        </div>

        <!-- Telemetry Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['active', 'Registry Pulse', $dailyStats['active_today'] . ' Items', 'fa-fire-flame-curved', 'indigo'],
                ['savings', 'Customer Edge', 'Rs ' . number_format($dailyStats['savings_value'], 0), 'fa-piggy-bank', 'emerald'],
                ['avg', 'Average Cut', number_format($dailyStats['avg_discount'], 1) . '% Off', 'fa-percent', 'amber'],
                ['value', 'Exposure Value', 'Rs ' . number_format($dailyStats['total_value'], 0), 'fa-chart-area', 'sky']
            ] as [$key, $label, $sub, $icon, $color])
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm group">
                    <div class="flex items-center gap-5">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 group-hover:bg-{{ $color }}-500 group-hover:text-white transition-colors shadow-inner">
                            <i class="fas {{ $icon }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 leading-none mb-2">{{ $label }}</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-xl font-black text-slate-900 tracking-tight">{{ explode(' ', $sub)[0] }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ implode(' ', array_slice(explode(' ', $sub), 1)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-8 lg:grid-cols-12">
            <!-- Strategic Configuration -->
            <div class="lg:col-span-4 space-y-6">
                <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-md">
                            <i class="fas fa-sliders text-xs"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Core Strategy</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Automation Parameters</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveRule" class="space-y-6">
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div>
                                <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">Status</p>
                                <p class="mt-1 text-[9px] font-bold text-slate-400 uppercase tracking-tight">Activate daily Deal Engine</p>
                            </div>
                            <button 
                                type="button"
                                wire:click="$set('is_active', {{ !$is_active ? 'true' : 'false' }})"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $is_active ? 'bg-indigo-600' : 'bg-slate-300' }}"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>

                        <div class="space-y-2">
                            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Safety Margin (Min %)</label>
                            <input type="number" wire:model="min_margin_percent" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        </div>

                        <div class="space-y-2">
                            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Max Discount Cap (%)</label>
                            <input type="number" wire:model="max_discount_percent" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        </div>

                        <div class="space-y-2">
                            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Daily Rotation Limit</label>
                            <input type="number" wire:model="daily_items_limit" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        </div>

                        <div class="space-y-2">
                            <label class="block px-1 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Selection Logic</label>
                            <select wire:model="rotation_strategy" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                <option value="random">Pure Randomize</option>
                                <option value="slow_moving">Slow Moving Assets</option>
                                <option value="overstocked">Deficit Liquidation (Overstocked)</option>
                            </select>
                        </div>

                        <div class="pt-4">
                             <button type="submit" class="w-full flex items-center justify-center gap-3 rounded-2xl bg-slate-900 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-check-circle text-[10px] opacity-50"></i>
                                Synchronize Strategy
                             </button>
                        </div>
                    </form>
                </div>

                <!-- Targeting Notice -->
                <div class="rounded-3xl border border-indigo-100 bg-indigo-50/50 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                            <i class="fas fa-shield-halved text-[10px]"></i>
                        </div>
                        <div>
                            <h5 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Safety Protocol Active</h5>
                            <p class="mt-2 text-[10px] font-medium text-slate-500 leading-relaxed">The engine automatically verifies unit costs before applying any cuts. No item will ever be listed below your defined safety margin.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registry Workspace -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Discovery Bar -->
                <div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative flex-1 group">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search today's discounted assets..." class="w-full rounded-xl border-none bg-slate-50 pl-11 py-2.5 text-xs font-bold text-slate-900 shadow-inner focus:ring-0 focus:bg-white transition-all">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Protocol Date:</span>
                        <div class="rounded-lg bg-slate-900 px-3 py-1.5 text-[10px] font-black text-white shadow-lg shadow-slate-200 uppercase tracking-widest">{{ now()->format('d M, Y') }}</div>
                    </div>
                </div>

                <!-- Ledger Table -->
                <div class="overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Product Identity</th>
                                <th class="px-8 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Registry Price</th>
                                <th class="px-8 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Calculated Deal</th>
                                <th class="px-8 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Cut Percent</th>
                                <th class="px-8 py-5 text-right text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($dailyDiscounts as $discount)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 flex-shrink-0">
                                                @php $img = collect($discount->stock->images)->first(); @endphp
                                                @if($img)
                                                    <img src="{{ asset('storage/' . ($img['path'] ?? '')) }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-slate-300"><i class="fas fa-box text-xs"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-slate-900 tracking-tight truncate max-w-[200px]">{{ $discount->stock->name }}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $discount->stock->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-black text-slate-400 line-through">Rs {{ number_format($discount->original_price, 0) }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-black text-slate-900 tracking-tight">Rs {{ number_format($discount->discounted_price, 0) }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 border border-emerald-100">
                                            <span class="text-[10px] font-black text-emerald-600">{{ number_format($discount->discount_percent, 0) }}%</span>
                                            <i class="fas fa-caret-down text-[8px] text-emerald-500"></i>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('admin.stocks', ['search' => $discount->stock->sku]) }}" class="text-[9px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">Registry Profile</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-24 text-center">
                                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[2.5rem] bg-slate-50 text-slate-200 shadow-sm mb-6">
                                            <i class="fas fa-wind text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-black text-slate-900 uppercase tracking-tight">No Dynamic Deals identified</p>
                                        <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Run the daily orchestration or check your strategy rules.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $dailyDiscounts->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('components.admin.notifications')
</div>
