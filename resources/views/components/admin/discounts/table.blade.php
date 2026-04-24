<x-admin.ui.panel title="Active Promotions" description="Search, review scope, and toggle each discount without opening a large form first.">
    <div class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 dark:border-emerald-400/20 dark:bg-emerald-400/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-300">Active</p>
            <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-200">{{ $discountStats['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-4 dark:border-indigo-400/20 dark:bg-indigo-400/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Coupons</p>
            <p class="mt-2 text-3xl font-black text-indigo-700 dark:text-indigo-200">{{ $discountStats['coupons'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Auto apply</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $discountStats['auto_apply'] }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 dark:border-amber-400/20 dark:bg-amber-400/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">Scheduled</p>
            <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-200">{{ $discountStats['scheduled'] }}</p>
        </div>
    </div>

    <div class="mb-5 grid gap-3 lg:grid-cols-3">
        <button type="button" wire:click="applyPreset('welcome')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-950">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Welcome coupon</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Quick-start a reusable code for first-time customers.</p>
        </button>
        <button type="button" wire:click="applyPreset('bundle')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-950">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Bundle saving</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Use a fixed-value incentive for larger carts.</p>
        </button>
        <button type="button" wire:click="applyPreset('weekend')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-950">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Weekend countdown</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Generate a timer-based coupon and tune it in the editor.</p>
        </button>
    </div>

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or code" class="min-w-[240px] flex-1 rounded-2xl border-slate-200 text-sm shadow-none focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            <thead class="bg-slate-50 dark:bg-slate-900/70">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Value</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Scope</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Timer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Usage</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                @forelse($discounts as $discount)
                    <tr class="bg-white transition hover:bg-slate-50/80 dark:bg-slate-950/60 dark:hover:bg-slate-900/50">
                        <td class="px-4 py-4"><p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $discount->name }}</p>@if($discount->description)<p class="mt-1 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($discount->description, 50) }}</p>@endif</td>
                        <td class="px-4 py-4">@if($discount->code)<span class="rounded-full bg-slate-100 px-3 py-1 font-mono text-xs font-semibold text-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $discount->code }}</span>@else<span class="text-xs text-slate-400">Auto-apply</span>@endif</td>
                        <td class="px-4 py-4"><p class="text-sm font-bold text-emerald-700 dark:text-emerald-300">@if($discount->type === 'percentage'){{ $discount->value }}%@else Rs {{ number_format($discount->value, 2) }}@endif</p>@if($discount->max_discount_amount)<p class="mt-1 text-xs text-slate-400">Cap Rs {{ number_format($discount->max_discount_amount, 2) }}</p>@endif</td>
                        <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ ucfirst($discount->scope) }}</td>
                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400">@if($discount->has_timer && $discount->ends_at)@if($discount->isExpired())<span class="font-semibold text-rose-600 dark:text-rose-300">Expired</span>@else<span class="font-semibold text-amber-600 dark:text-amber-300">Ends {{ $discount->ends_at->diffForHumans() }}</span>@if($discount->show_timer_on_site)<p class="mt-1">Shown on site</p>@endif@endif @else <span>No timer</span>@endif</td>
                        <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $discount->used_count }}{{ $discount->usage_limit ? ' / ' . $discount->usage_limit : ' / unlimited' }}</td>
                        <td class="px-4 py-4"><button wire:click="toggleActive({{ $discount->id }})" class="rounded-full px-3 py-1 text-xs font-semibold {{ $discount->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-900 dark:text-slate-300' }}">{{ $discount->is_active ? 'Active' : 'Inactive' }}</button></td>
                        <td class="px-4 py-4"><div class="flex items-center gap-2"><button wire:click="edit({{ $discount->id }})" class="rounded-2xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-400/10 dark:text-indigo-300">Edit</button><button wire:click="delete({{ $discount->id }})" onclick="confirm('Delete this discount?') || event.stopImmediatePropagation()" class="rounded-2xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-400/10 dark:text-rose-300">Delete</button></div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-16"><x-admin.ui.empty-state title="No discounts yet" description="Create your first campaign rule to control discounts from one clean workspace." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $discounts->links() }}</div>
</x-admin.ui.panel>
