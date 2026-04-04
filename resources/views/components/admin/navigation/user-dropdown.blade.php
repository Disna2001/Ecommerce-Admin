@props(['activityFeed' => collect()])

<div x-show="profileDropdownOpen" @click.away="profileDropdownOpen = false" x-cloak class="admin-dropdown" style="width:min(25rem,calc(100vw - 2rem));padding:0;">
    <div style="padding:1rem 1rem 0.9rem;border-bottom:1px solid var(--admin-border);">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
            <div>
                <p style="font-size:0.76rem;font-weight:800;letter-spacing:0.16em;text-transform:uppercase;color:var(--admin-text-soft);">Admin Profile</p>
                <p style="margin-top:0.25rem;font-size:1rem;font-weight:800;color:var(--admin-text);">{{ auth()->user()->name }}</p>
                <p style="margin-top:0.15rem;font-size:0.8rem;color:var(--admin-text-muted);">{{ auth()->user()->email }}</p>
            </div>
            <span class="admin-chip">Admin</span>
        </div>
    </div>

    <div style="padding:0.9rem 1rem;">
        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.65rem;">
            <a href="{{ route('profile.index', ['tab' => 'overview']) }}" wire:navigate class="admin-dropdown-link" style="border-radius:1rem;background:var(--admin-surface-soft);border:1px solid var(--admin-border);padding:0.85rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile
            </a>
            <a href="{{ route('profile.index', ['tab' => 'settings']) }}" wire:navigate class="admin-dropdown-link" style="border-radius:1rem;background:var(--admin-surface-soft);border:1px solid var(--admin-border);padding:0.85rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.25 4.5 8.25 6v3L5.25 10.5v3L8.25 15v3l3 1.5L14.25 18v-3l3-1.5v-3L14.25 9V6L11.25 4.5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14.25a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                </svg>
                Settings
            </a>
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="admin-dropdown-link" style="border-radius:1rem;background:var(--admin-surface-soft);border:1px solid var(--admin-border);padding:0.85rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 3.75h6.75v6.75H3.75V3.75zM13.5 3.75h6.75v4.5H13.5v-4.5zM13.5 11.25h6.75v9H13.5v-9zM3.75 13.5h6.75v6.75H3.75V13.5z"></path>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.settings') }}" wire:navigate class="admin-dropdown-link" style="border-radius:1rem;background:var(--admin-surface-soft);border:1px solid var(--admin-border);padding:0.85rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Controls
            </a>
        </div>

        <div style="margin-top:1rem;padding-top:0.9rem;border-top:1px solid var(--admin-border);">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
                <p style="display:flex;align-items:center;gap:0.45rem;font-size:0.76rem;font-weight:800;letter-spacing:0.16em;text-transform:uppercase;color:var(--admin-text-soft);">
                    <svg style="width:0.9rem;height:0.9rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 12h3l2.25-6 4.5 12 2.25-6h4.5"></path>
                    </svg>
                    Activity Center
                </p>
                <a href="{{ route('admin.orders') }}" wire:navigate style="font-size:0.76rem;font-weight:700;color:var(--admin-primary);text-decoration:none;">Open orders</a>
            </div>

            <div style="margin-top:0.75rem;display:grid;gap:0.8rem;">
                @forelse($activityFeed as $item)
                    <div style="display:flex;gap:0.8rem;align-items:flex-start;">
                        <span style="width:0.82rem;height:0.82rem;border-radius:999px;background:{{ $item['color'] }};margin-top:0.28rem;flex-shrink:0;"></span>
                        <div>
                            <p style="font-size:0.86rem;font-weight:700;color:var(--admin-text);">{{ $item['label'] }}</p>
                            <p style="margin-top:0.18rem;font-size:0.79rem;line-height:1.45;color:var(--admin-text-muted);">{{ $item['meta'] }}</p>
                            <p style="margin-top:0.15rem;font-size:0.73rem;color:var(--admin-text-soft);">{{ optional($item['time'])->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p style="font-size:0.82rem;color:var(--admin-text-muted);">No recent admin actions are available yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div style="border-top:1px solid var(--admin-border);padding:0.8rem 1rem 1rem;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="admin-dropdown-button" style="justify-content:center;border-radius:1rem;background:rgba(239,68,68,0.08);color:var(--admin-danger);font-weight:700;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
