@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
@endphp

<div style="width:100%;">
    <label class="admin-search admin-search--compact">
        <svg style="width:1rem;height:1rem;flex-shrink:0;color:rgba(226,232,240,0.74);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input type="text" placeholder="Search pages, tools, users, or orders..." aria-label="Search admin">
        <span class="admin-chip" style="padding:0.32rem 0.62rem;background:rgba(255,255,255,0.08);color:rgba(248,250,252,0.8);font-size:0.72rem;">{{ $siteName }}</span>
    </label>
</div>
