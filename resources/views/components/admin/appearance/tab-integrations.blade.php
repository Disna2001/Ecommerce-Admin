<x-admin.ui.panel title="Client Setup Hub" description="Keep gateway and sign-in credentials in one admin workspace so each client can configure their own storefront safely.">
    <div class="grid gap-5">
        <div class="grid gap-3 md:grid-cols-3">
            <a href="https://www.payhere.lk/" target="_blank" rel="noopener" class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm font-semibold text-amber-800 transition hover:border-amber-300 hover:bg-amber-100 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                <i class="fas fa-bolt mr-2"></i>Get PayHere account
                <span class="mt-1 block text-xs font-normal leading-5">Create/login to PayHere, then copy Merchant ID and Secret.</span>
            </a>
            <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener" class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm font-semibold text-sky-800 transition hover:border-sky-300 hover:bg-sky-100 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-200">
                <i class="fab fa-google mr-2"></i>Get Google OAuth keys
                <span class="mt-1 block text-xs font-normal leading-5">Create OAuth client ID and paste the redirect URI below.</span>
            </a>
            <a href="https://developers.facebook.com/apps/" target="_blank" rel="noopener" class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-4 text-sm font-semibold text-indigo-800 transition hover:border-indigo-300 hover:bg-indigo-100 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-200">
                <i class="fab fa-facebook mr-2"></i>Get Meta app keys
                <span class="mt-1 block text-xs font-normal leading-5">Open Meta app settings, then copy App ID and App Secret.</span>
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Public Callback URL</h4>
                    <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">Use your real domain or tunnel URL for PayHere callbacks and social login redirects.</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-950 dark:text-slate-300">{{ filled($app_public_url) ? 'Configured' : 'Needs URL' }}</span>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Public App URL</label>
                <input type="url" wire:model="app_public_url" placeholder="https://yourdomain.com" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                @error('app_public_url')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div class="rounded-2xl bg-white px-4 py-3 text-xs text-slate-500 dark:bg-slate-950 dark:text-slate-300">PayHere notify: <span class="font-mono">{{ rtrim($app_public_url ?: config('app.url'), '/') }}/checkout/payhere/notify</span></div>
                <div class="rounded-2xl bg-white px-4 py-3 text-xs text-slate-500 dark:bg-slate-950 dark:text-slate-300">Google redirect: <span class="font-mono">{{ $google_redirect_uri ?: rtrim($app_public_url ?: config('app.url'), '/') . '/auth/google/callback' }}</span></div>
                <div class="rounded-2xl bg-white px-4 py-3 text-xs text-slate-500 dark:bg-slate-950 dark:text-slate-300">Facebook redirect: <span class="font-mono">{{ $facebook_redirect_uri ?: rtrim($app_public_url ?: config('app.url'), '/') . '/auth/facebook/callback' }}</span></div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Google Sign In</h4>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Let customers sign in with Google using this client store configuration.</p>
                    </div>
                    <label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300"><input type="checkbox" wire:model="enable_google_login" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable</label>
                </div>
                <div class="mt-4 grid gap-4">
                    <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Client ID</label><input type="text" wire:model="google_client_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                    <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Client Secret</label><input type="password" wire:model="google_client_secret" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                    <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Redirect URI override</label><input type="url" wire:model="google_redirect_uri" placeholder="{{ rtrim($app_public_url ?: config('app.url'), '/') }}/auth/google/callback" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">@error('google_redirect_uri')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror</div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Facebook Sign In</h4>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enable Meta/Facebook login without editing environment files.</p>
                    </div>
                    <label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300"><input type="checkbox" wire:model="enable_facebook_login" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable</label>
                </div>
                <div class="mt-4 grid gap-4">
                    <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">App ID</label><input type="text" wire:model="facebook_client_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                    <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">App Secret</label><input type="password" wire:model="facebook_client_secret" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></div>
                    <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Redirect URI override</label><input type="url" wire:model="facebook_redirect_uri" placeholder="{{ rtrim($app_public_url ?: config('app.url'), '/') }}/auth/facebook/callback" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">@error('facebook_redirect_uri')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
            Save this page after adding credentials. Use the exact redirect URLs shown above inside Google Cloud Console, Meta Developer Console, and PayHere merchant settings.
        </div>
    </div>
</x-admin.ui.panel>
