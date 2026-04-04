<section class="admin-surface rounded-[2rem] border border-white/60 bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.18),_transparent_38%),linear-gradient(135deg,rgba(255,255,255,0.96),rgba(248,250,252,0.92))] p-6 shadow-[0_25px_80px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.24),_transparent_30%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(17,24,39,0.92))]">
    <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
        <div class="space-y-5">
            <div class="space-y-3"><span class="inline-flex items-center rounded-full border border-fuchsia-200/70 bg-fuchsia-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-fuchsia-700 dark:border-fuchsia-400/20 dark:bg-fuchsia-400/10 dark:text-fuchsia-200">Customer-Facing Experience</span><div class="space-y-2"><h3 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Control the full storefront without jumping between tools.</h3><p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">Update visual identity, homepage copy, category icons, and checkout payment options from one structured admin flow.</p></div></div>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <x-admin.ui.metric label="Branding" :value="$storefrontSummary['branding_ready'] ? 'Ready' : 'Review'" :hint="$logo_path ? 'Logo uploaded | favicon set' : 'Logo or favicon missing'" tone="slate" />
                <x-admin.ui.metric label="Hero Block" :value="$storefrontSummary['hero_ready'] ? 'Live' : 'Edit'" :hint="$hero_image_path ? 'Image loaded' : 'Text-only now'" tone="blue" />
                <x-admin.ui.metric label="Payment Methods" :value="$storefrontSummary['payments_enabled']" hint="Checkout options enabled" tone="emerald" />
                <x-admin.ui.metric label="Featured Products" :value="$storefrontSummary['featured_items']" hint="Products chosen for homepage rails" tone="accent" />
            </div>
        </div>
        <aside class="rounded-[1.75rem] border border-white/70 bg-white/85 p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/75"><div class="flex items-center justify-between"><h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Working Rhythm</h3><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ ucfirst($activeTab) }}</span></div><div class="mt-4 space-y-3">@foreach([['1', 'Branding', 'Upload logo, favicon, and site identity first.'], ['2', 'Homepage', 'Tune trust copy, CTA text, and featured experience.'], ['3', 'Payments', 'Enable only the checkout methods you truly operate.']] as [$step, $title, $text])<div class="flex gap-3 rounded-2xl bg-slate-100/80 p-4 dark:bg-slate-800/80"><div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-900 text-xs font-black text-white dark:bg-white dark:text-slate-900">{{ $step }}</div><div><p class="font-semibold text-slate-900 dark:text-white">{{ $title }}</p><p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $text }}</p></div></div>@endforeach</div></aside>
    </div>
</section>
<section class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Live Preview</p>
            <h3 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Homepage composition preview</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">This preview follows your current appearance settings so you can tune layout, sections, and promo rhythm before saving.</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">{{ ucfirst($hero_layout) }} hero | {{ $promo_strip_enabled ? 'Promo rail on' : 'Promo rail off' }}</div>
    </div>
    <div class="mt-6 rounded-[2rem] border border-slate-200 bg-[linear-gradient(180deg,#eef2ff_0%,#f8fafc_100%)] p-5 dark:border-slate-800 dark:bg-[linear-gradient(180deg,#0f172a_0%,#111827_100%)]">
        <div class="rounded-[1.75rem] p-6 text-white {{ $hero_surface === 'minimal' ? '' : 'shadow-[0_18px_50px_rgba(15,23,42,0.15)]' }}" style="background:{{ $hero_surface === 'minimal' ? 'transparent' : 'linear-gradient(135deg, ' . $hero_bg_from . ', ' . $hero_bg_to . ')' }}; border:1px solid rgba(255,255,255,0.16);">
            <div class="{{ $hero_layout === 'centered' ? 'mx-auto max-w-3xl text-center' : ($hero_layout === 'stacked' ? 'max-w-4xl' : 'grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center') }}">
                <div class="{{ $hero_alignment === 'center' ? 'text-center' : 'text-left' }}">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/70">{{ $site_tagline }}</p>
                    <h4 class="mt-4 text-3xl font-black leading-tight">{{ $hero_title }} <span class="block text-white/80">{{ $hero_highlight_text }}</span></h4>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80 {{ $hero_alignment === 'center' ? 'mx-auto' : '' }}">{{ $hero_subtitle }} <span class="font-semibold text-white">{{ $hero_microcopy }}</span></p>
                    <div class="mt-5 inline-flex rounded-full bg-white px-5 py-3 text-sm font-bold text-slate-900">{{ $hero_button_text }}</div>
                </div>
                @if($hero_layout === 'split')
                    <div class="rounded-[1.5rem] border border-white/15 bg-white/10 p-4">
                        <div class="h-48 rounded-[1.25rem] bg-white/20"></div>
                    </div>
                @endif
            </div>
        </div>
        @if($promo_strip_enabled)
            <div class="mt-5 rounded-[1.5rem] p-5 text-white shadow-[0_14px_40px_rgba(15,23,42,0.14)]" style="background:linear-gradient(135deg, {{ $promo_strip_from }}, {{ $promo_strip_to }})">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">{{ $promo_strip_badge }}</span>
                        <div class="mt-3 text-xl font-bold">{{ $promo_strip_title }}</div>
                        <p class="mt-2 text-sm text-white/80">{{ $promo_strip_text }}</p>
                    </div>
                    <div class="inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">{{ $promo_strip_button_text }}</div>
                </div>
            </div>
        @endif
        <div class="mt-5 grid gap-4 lg:grid-cols-3">
            @foreach([$deals_section_title, $featured_section_title, $new_arrivals_section_title] as $sectionTitle)
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $sectionTitle }}</p>
                    <div class="mt-4 h-28 rounded-2xl bg-slate-100 dark:bg-slate-800"></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
