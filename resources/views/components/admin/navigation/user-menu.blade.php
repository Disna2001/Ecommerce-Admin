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

<div style="display:flex;align-items:center;gap:0.65rem;">
    <div style="position:relative;">
        <button @click="notificationDropdownOpen = !notificationDropdownOpen" class="admin-tool-button" aria-label="Notifications">
            <span style="position:relative;display:inline-flex;align-items:center;justify-content:center;">
                <svg style="width:1.15rem;height:1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if($notificationCount > 0)
                    <span style="position:absolute;top:-0.2rem;right:-0.28rem;min-width:1rem;height:1rem;padding:0 0.22rem;background:#fb7185;border-radius:999px;box-shadow:0 0 0 3px rgba(15,23,42,0.5);font-size:0.65rem;font-weight:800;line-height:1rem;text-align:center;color:white;">{{ min($notificationCount, 9) }}</span>
                @endif
            </span>
        </button>

        <div x-show="notificationDropdownOpen" @click.away="notificationDropdownOpen = false" x-cloak class="admin-notification-panel">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                <div>
                    <p style="font-size:0.78rem;font-weight:800;letter-spacing:0.16em;text-transform:uppercase;color:var(--admin-text-soft);">Notifications</p>
                    <h3 style="margin-top:0.3rem;font-size:1.05rem;font-weight:800;color:var(--admin-text);">Operational alerts</h3>
                </div>
                <span class="admin-chip">{{ $notificationCount }} open</span>
            </div>

            <div style="margin-top:0.9rem;display:grid;gap:0.6rem;">
                @foreach([
                    ['Pending orders', $pendingOrders, route('admin.orders'), '#f59e0b'],
                    ['Payment reviews', $pendingPaymentReviews, route('admin.orders'), '#10b981'],
                    ['Review approvals', $pendingReviews, route('admin.site-management.reviews'), '#6366f1'],
                ] as [$label, $count, $href, $color])
                    <a href="{{ $href }}" wire:navigate style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.85rem 0.9rem;border-radius:1rem;text-decoration:none;background:var(--admin-surface-soft);border:1px solid var(--admin-border);color:var(--admin-text);">
                        <span style="display:flex;align-items:center;gap:0.7rem;">
                            <span style="width:0.7rem;height:0.7rem;border-radius:999px;background:{{ $color }};"></span>
                            <span style="font-size:0.9rem;font-weight:700;">{{ $label }}</span>
                        </span>
                        <span style="font-size:0.82rem;color:var(--admin-text-muted);">{{ $count }}</span>
                    </a>
                @endforeach
            </div>

            @if($activityFeed->isNotEmpty())
                <div style="margin-top:1rem;padding-top:0.9rem;border-top:1px solid var(--admin-border);">
                    <p style="font-size:0.76rem;font-weight:800;letter-spacing:0.16em;text-transform:uppercase;color:var(--admin-text-soft);">Recent activity</p>
                    <div style="margin-top:0.7rem;display:grid;gap:0.75rem;">
                        @foreach($activityFeed as $item)
                            <div style="display:flex;gap:0.8rem;">
                                <span style="width:0.78rem;height:0.78rem;border-radius:999px;background:{{ $item['color'] }};margin-top:0.3rem;flex-shrink:0;"></span>
                                <div>
                                    <p style="font-size:0.88rem;font-weight:700;color:var(--admin-text);">{{ $item['label'] }}</p>
                                    <p style="margin-top:0.18rem;font-size:0.8rem;line-height:1.5;color:var(--admin-text-muted);">{{ $item['meta'] }}</p>
                                    <p style="margin-top:0.18rem;font-size:0.74rem;color:var(--admin-text-soft);">{{ optional($item['time'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div style="position:relative;">
        <button @click="profileDropdownOpen = !profileDropdownOpen" class="admin-user-button" aria-label="User menu">
            @if($user->profile_photo_path ?? null)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="admin-user-avatar" style="object-fit:cover;">
            @else
                <span class="admin-user-avatar">{{ \Illuminate\Support\Str::substr($user->name, 0, 2) }}</span>
            @endif
            <span class="desktop-only" style="display:flex;flex-direction:column;align-items:flex-start;line-height:1.1;">
                <span style="font-size:0.9rem;font-weight:700;white-space:nowrap;">{{ $user->name }}</span>
                <span style="font-size:0.72rem;color:rgba(226,232,240,0.66);white-space:nowrap;">{{ $user->hasAnyRole(['Admin', 'Super Admin']) ? 'Administrator' : 'Team Member' }}</span>
            </span>
            <svg style="width:1rem;height:1rem;color:rgba(226,232,240,0.82);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <x-admin.navigation.user-dropdown :activity-feed="$activityFeed" />
    </div>
</div>
