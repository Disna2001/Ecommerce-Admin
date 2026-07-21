@php
    $user = auth()->user();
    $pendingPaymentReviews = \App\Models\Order::where('payment_review_status', 'pending_review')->count();
    $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
    $pendingReviews = class_exists(\App\Models\Review::class) ? \App\Models\Review::where('is_approved', false)->count() : 0;
    $notificationCount = $pendingPaymentReviews + $pendingOrders + $pendingReviews;

    $activityFeed = collect([
        \App\Models\Order::where('payment_review_status', 'approved')->latest('updated_at')->first()
            ? [
                'label' => 'Payment verified',
                'meta' => 'A payment proof was approved recently.',
                'time' => optional(\App\Models\Order::where('payment_review_status', 'approved')->latest('updated_at')->first())->updated_at,
                'color' => '#10b981',
              ] : null,
        \App\Models\Order::whereNotNull('return_approved_at')->latest('return_approved_at')->first()
            ? [
                'label' => 'Return decision updated',
                'meta' => 'A return workflow was processed.',
                'time' => optional(\App\Models\Order::whereNotNull('return_approved_at')->latest('return_approved_at')->first())->return_approved_at,
                'color' => '#f59e0b',
              ] : null,
        class_exists(\App\Models\Review::class) && \App\Models\Review::whereNotNull('approved_at')->latest('approved_at')->first()
            ? [
                'label' => 'Review moderated',
                'meta' => 'A storefront review was approved or updated.',
                'time' => optional(\App\Models\Review::whereNotNull('approved_at')->latest('approved_at')->first())->approved_at,
                'color' => '#6366f1',
              ] : null,
    ])->filter()->take(3)->values();
@endphp

<div class="flex items-center gap-2">
    <!-- Notifications Trigger -->
    <div class="relative">
        <button @click="notificationDropdownOpen = !notificationDropdownOpen" class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white/80 hover:text-white hover:bg-white/20 transition-all relative" aria-label="Notifications">
            <i class="fas fa-bell text-xs"></i>
            @if($notificationCount > 0)
                <span class="absolute -top-1 -right-1 flex h-3.5 min-w-[14px] items-center justify-center rounded-full bg-rose-500 text-[8px] font-bold text-white px-1">
                    {{ min($notificationCount, 9) }}
                </span>
            @endif
        </button>

        <div x-show="notificationDropdownOpen" @click.away="notificationDropdownOpen = false" x-cloak class="admin-notification-panel">
            <div class="flex items-center justify-between gap-4 pb-2.5 border-b border-slate-100">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Notifications</p>
                    <h3 class="text-xs font-bold text-slate-900 mt-0.5">Operational Alerts</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold">{{ $notificationCount }} open</span>
            </div>

            <div class="mt-2.5 space-y-1.5">
                @foreach([
                    ['Pending orders', $pendingOrders, route('admin.orders'), 'bg-amber-500'],
                    ['Payment reviews', $pendingPaymentReviews, route('admin.orders'), 'bg-emerald-500'],
                    ['Review approvals', $pendingReviews, route('admin.site-management.index', ['tab' => 'reviews']), 'bg-indigo-500'],
                ] as [$label, $count, $href, $dotBg])
                    <a href="{{ $href }}" wire:navigate class="flex items-center justify-between gap-3 p-2 rounded-md border border-slate-200/80 bg-slate-50/50 hover:bg-white transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full {{ $dotBg }}"></span>
                            <span class="text-xs font-semibold text-slate-900">{{ $label }}</span>
                        </span>
                        <span class="text-xs font-bold text-slate-500">{{ $count }}</span>
                    </a>
                @endforeach
            </div>

            @if($activityFeed->isNotEmpty())
                <div class="mt-3 pt-2.5 border-t border-slate-100">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Recent activity</p>
                    <div class="space-y-1.5">
                        @foreach($activityFeed as $item)
                            <div class="flex items-start gap-2 text-xs">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-500 mt-1 flex-shrink-0"></span>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $item['label'] }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $item['meta'] }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ optional($item['time'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- User Profile Dropdown Trigger -->
    <div class="relative">
        <button @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center gap-2 px-2 py-1 rounded-lg bg-white/10 border border-white/10 text-white hover:bg-white/20 transition-all cursor-pointer" aria-label="User menu">
            @if($user->profile_photo_path ?? null)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-7 w-7 rounded-md object-cover">
            @else
                <span class="h-7 w-7 rounded-md bg-indigo-600 text-white font-bold text-[11px] flex items-center justify-center uppercase shadow-xs flex-shrink-0">{{ \Illuminate\Support\Str::substr($user->name, 0, 2) }}</span>
            @endif
            <div class="hidden sm:flex flex-col text-left leading-tight">
                <span class="text-xs font-semibold text-white whitespace-nowrap leading-none mb-0.5">{{ $user->name }}</span>
                <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap leading-none">{{ $user->hasAnyRole(['Admin', 'Super Admin']) ? 'Admin' : 'Member' }}</span>
            </div>
            <i class="fas fa-chevron-down text-[9px] text-slate-400 ml-0.5"></i>
        </button>

        <x-admin.navigation.user-dropdown :activity-feed="$activityFeed" />
    </div>
</div>
