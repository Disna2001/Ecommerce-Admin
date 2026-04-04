@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
@endphp

<div style="margin-bottom:1.5rem;padding:1.1rem;background:linear-gradient(135deg,rgba(79,70,229,0.95),rgba(124,58,237,0.88),rgba(14,165,233,0.82));border-radius:1.35rem;color:white;box-shadow:0 22px 42px -24px rgba(79,70,229,0.68);">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.75rem;">
        <div>
            <p style="font-size:0.72rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:rgba(255,255,255,0.72);">Control Center</p>
            <h3 style="margin-top:0.35rem;font-size:1.1rem;font-weight:800;line-height:1.2;">{{ $siteName }}</h3>
            <p style="margin-top:0.45rem;font-size:0.82rem;line-height:1.5;color:rgba(255,255,255,0.82);">Track store health, sales flow, and content operations from one workspace.</p>
        </div>
        <span style="display:inline-flex;align-items:center;justify-content:center;width:2.35rem;height:2.35rem;border-radius:0.95rem;background:rgba(255,255,255,0.18);">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13.125C3 12.503 3.503 12 4.125 12h4.75c.622 0 1.125.503 1.125 1.125v6.75C10 20.497 9.497 21 8.875 21h-4.75A1.125 1.125 0 013 19.875v-6.75zM14 4.125C14 3.503 14.503 3 15.125 3h4.75C20.497 3 21 3.503 21 4.125v15.75c0 .622-.503 1.125-1.125 1.125h-4.75A1.125 1.125 0 0114 19.875V4.125zM8.875 3C8.253 3 7.75 3.503 7.75 4.125v4.75c0 .622.503 1.125 1.125 1.125h4.75c.622 0 1.125-.503 1.125-1.125v-4.75C14.75 3.503 14.247 3 13.625 3h-4.75z"></path>
            </svg>
        </span>
    </div>

    <div style="margin-top:1rem;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.65rem;">
        <div style="padding:0.75rem;border-radius:1rem;background:rgba(255,255,255,0.14);text-align:center;">
            <span style="display:block;font-size:1.25rem;font-weight:800;">{{ \App\Models\Stock::count() }}</span>
            <span style="font-size:0.72rem;color:rgba(255,255,255,0.72);">Items</span>
        </div>
        <div style="padding:0.75rem;border-radius:1rem;background:rgba(255,255,255,0.14);text-align:center;">
            <span style="display:block;font-size:1.25rem;font-weight:800;">{{ \App\Models\Order::count() }}</span>
            <span style="font-size:0.72rem;color:rgba(255,255,255,0.72);">Orders</span>
        </div>
    </div>
</div>
