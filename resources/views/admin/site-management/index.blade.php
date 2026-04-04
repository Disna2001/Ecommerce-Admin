@extends('layouts.admin')

@section('header', 'Site Management')
@section('breadcrumb', 'Storefront Control')

@section('content')
<div class="space-y-8">
    <div class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-950 via-violet-900 to-indigo-700 p-8 text-white shadow-[0_25px_80px_rgba(15,23,42,0.18)]">
        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr] lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-violet-200">Storefront Admin</p>
                <h1 class="mt-4 text-3xl font-semibold leading-tight">Control the entire customer-facing site from one workspace.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-violet-100/85">
                    Update homepage content, banners, discounts, featured products, and review moderation without digging through multiple menus.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-violet-200">Active Banners</p>
                    <p class="mt-2 text-3xl font-bold">{{ \App\Models\Banner::where('is_active', true)->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-violet-200">Discounts Live</p>
                    <p class="mt-2 text-3xl font-bold">{{ \App\Models\Discount::where('is_active', true)->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    @php $ids = \App\Models\SiteSetting::get('featured_product_ids', []); @endphp
                    <p class="text-xs uppercase tracking-[0.2em] text-violet-200">Featured Items</p>
                    <p class="mt-2 text-3xl font-bold">{{ is_array($ids) ? count($ids) : 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.site-management.appearance') }}" class="group rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-lg">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 transition group-hover:bg-violet-600 group-hover:text-white">
                <i class="fas fa-palette text-xl"></i>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-slate-900">Appearance & Homepage</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Branding, hero text, color palette, top bar, footer, search copy, and homepage CTA blocks.</p>
            <div class="mt-4 text-sm font-semibold text-violet-700">Open controls →</div>
        </a>

        <a href="{{ route('admin.site-management.banners') }}" class="group rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-fuchsia-200 hover:shadow-lg">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-fuchsia-100 text-fuchsia-600 transition group-hover:bg-fuchsia-600 group-hover:text-white">
                <i class="fas fa-images text-xl"></i>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-slate-900">Banners</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Manage hero sliders, announcement strips, and promotional visual blocks across the storefront.</p>
            <div class="mt-4 text-sm font-semibold text-fuchsia-700">Manage banners →</div>
        </a>

        <a href="{{ route('admin.site-management.discounts') }}" class="group rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white">
                <i class="fas fa-tags text-xl"></i>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-slate-900">Discounts</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Control sales, coupon logic, timers, and promotional pricing used throughout product cards and sections.</p>
            <div class="mt-4 text-sm font-semibold text-emerald-700">Adjust discounts →</div>
        </a>

        <a href="{{ route('admin.site-management.display-items') }}" class="group rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 transition group-hover:bg-amber-600 group-hover:text-white">
                <i class="fas fa-store text-xl"></i>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-slate-900">Homepage Products</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Choose the products shown in Best Sellers, Featured, New Arrivals, and the homepage product rails.</p>
            <div class="mt-4 text-sm font-semibold text-amber-700">Pick products →</div>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Recommended Workflow</h2>
            <div class="mt-5 space-y-4">
                @foreach([
                    ['1', 'Start with Appearance & Homepage', 'Set the new welcome-page layout, theme colors, hero copy, trust labels, and footer CTA first.'],
                    ['2', 'Update banners and promotions', 'Add campaign visuals and announcement strips to support the homepage structure.'],
                    ['3', 'Curate visible products', 'Choose which items belong in each section so the homepage always feels intentional and current.'],
                ] as [$step, $title, $text])
                    <div class="flex gap-4 rounded-2xl bg-slate-50 p-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white">{{ $step }}</div>
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ $title }}</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Quick Access</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('admin.site-management.appearance') }}" class="rounded-2xl border border-slate-200 px-4 py-4 text-sm font-medium text-slate-700 transition hover:border-violet-300 hover:bg-violet-50">Homepage text and colors</a>
                <a href="{{ route('admin.site-management.display-items') }}" class="rounded-2xl border border-slate-200 px-4 py-4 text-sm font-medium text-slate-700 transition hover:border-amber-300 hover:bg-amber-50">Section products</a>
                <a href="{{ route('admin.site-management.banners') }}" class="rounded-2xl border border-slate-200 px-4 py-4 text-sm font-medium text-slate-700 transition hover:border-fuchsia-300 hover:bg-fuchsia-50">Hero and promo banners</a>
                <a href="{{ route('admin.site-management.reviews') }}" class="rounded-2xl border border-slate-200 px-4 py-4 text-sm font-medium text-slate-700 transition hover:border-sky-300 hover:bg-sky-50">Customer reviews</a>
            </div>
        </div>
    </div>
</div>
@endsection
