@php
    use App\Models\SiteSetting;

    $chatSiteName = SiteSetting::get('site_name', 'DISPLAY LANKA.LK');
    $chatPrimary = SiteSetting::get('primary_color', '#6d28d9');
    $chatSecondary = SiteSetting::get('secondary_color', '#7c3aed');
    $chatWhatsappEnabled = (bool) SiteSetting::get('whatsapp_enabled', false);
    $chatWhatsappNumber = preg_replace('/\D+/', '', SiteSetting::get('whatsapp_phone_number', ''));
    $chatSupportEmail = SiteSetting::get('support_email', SiteSetting::get('support_notification_email', ''));
    $chatSupportPhone = SiteSetting::get('support_phone', '');
    $chatMessage = rawurlencode("Hello {$chatSiteName}, I need help with a product/order.");
    $chatWhatsappLink = ($chatWhatsappEnabled && $chatWhatsappNumber)
        ? "https://wa.me/{$chatWhatsappNumber}?text={$chatMessage}"
        : null;
@endphp

<div
    x-data="{ open: false }"
    class="fixed bottom-5 right-5 z-[65]"
>
    <div
        x-show="open"
        x-transition.origin.bottom.right
        class="mb-3 w-[320px] max-w-[calc(100vw-2rem)] overflow-hidden rounded-[1.75rem] border border-white/50 bg-white/90 shadow-[0_24px_80px_rgba(15,23,42,0.18)] backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/90"
        style="display:none"
    >
        <div class="px-5 py-4 text-white" style="background:linear-gradient(135deg, {{ $chatPrimary }}, {{ $chatSecondary }})">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/70">Support</p>
                    <h3 class="mt-1 text-lg font-bold">{{ $chatSiteName }} Help Desk</h3>
                    <p class="mt-2 text-sm text-white/80">Fast help for products, checkout, and order updates.</p>
                </div>
                <button @click="open = false" type="button" class="rounded-full bg-white/15 p-2 text-white/90 transition hover:bg-white/25">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="space-y-4 px-5 py-5">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-600 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-300">
                Ask about product details, payment verification, or your current order status.
            </div>

            <div class="space-y-3">
                @if($chatWhatsappLink)
                    <a
                        href="{{ $chatWhatsappLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300"
                    >
                        <span class="flex items-center gap-3"><i class="fab fa-whatsapp text-base"></i>Chat on WhatsApp</span>
                        <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                @endif

                @if($chatSupportEmail)
                    <a
                        href="mailto:{{ $chatSupportEmail }}"
                        class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                    >
                        <span class="flex items-center gap-3"><i class="fas fa-envelope text-sm"></i>Email Support</span>
                        <span class="truncate text-xs text-slate-400 dark:text-slate-500">{{ $chatSupportEmail }}</span>
                    </a>
                @endif

                @if($chatSupportPhone)
                    <a
                        href="tel:{{ preg_replace('/\s+/', '', $chatSupportPhone) }}"
                        class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                    >
                        <span class="flex items-center gap-3"><i class="fas fa-phone text-sm"></i>Call Support</span>
                        <span class="truncate text-xs text-slate-400 dark:text-slate-500">{{ $chatSupportPhone }}</span>
                    </a>
                @endif

                <a
                    wire:navigate
                    href="{{ route('products.index') }}"
                    class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                >
                    <span class="flex items-center gap-3"><i class="fas fa-store text-sm"></i>Browse Products</span>
                    <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                </a>

                <a
                    wire:navigate
                    href="{{ route('track-order') }}"
                    class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                >
                    <span class="flex items-center gap-3"><i class="fas fa-location-crosshairs text-sm"></i>Track Order</span>
                    <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                </a>

                @auth
                    <a
                        wire:navigate
                        href="{{ route('profile.index') }}"
                        class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                    >
                        <span class="flex items-center gap-3"><i class="fas fa-user text-sm"></i>Open My Account</span>
                        <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                    </a>
                @else
                    <a
                        wire:navigate
                        href="{{ route('login') }}"
                        class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                    >
                        <span class="flex items-center gap-3"><i class="fas fa-right-to-bracket text-sm"></i>Login for Order Help</span>
                        <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <button
        @click="open = !open"
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full text-white shadow-[0_20px_50px_rgba(109,40,217,0.35)] transition hover:scale-105"
        style="background:linear-gradient(135deg, {{ $chatPrimary }}, {{ $chatSecondary }})"
        aria-label="Open support chat"
    >
        <i class="fas" :class="open ? 'fa-xmark' : 'fa-comments'"></i>
    </button>
</div>
