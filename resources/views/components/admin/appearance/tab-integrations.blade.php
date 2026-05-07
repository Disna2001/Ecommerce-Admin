<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 shadow-inner">
            <i class="fas fa-share-nodes text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Social Intelligence</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">OAuth Protocols & Connectivity Hub</p>
        </div>
    </div>

    <!-- Rapid Access Hub -->
    <div class="grid gap-4 md:grid-cols-3">
        @foreach([
            ['google', 'Google Cloud', 'OAuth Credentials', 'https://console.cloud.google.com/apis/credentials', 'sky'],
            ['facebook', 'Meta Developers', 'App ID & Secrets', 'https://developers.facebook.com/apps/', 'indigo'],
            ['bolt', 'PayHere Console', 'Merchant Identity', 'https://www.payhere.lk/', 'amber']
        ] as [$icon, $title, $desc, $link, $color])
            <a href="{{ $link }}" target="_blank" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-slate-900 hover:shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                        <i class="fa{{ $icon === 'google' || $icon === 'facebook' ? 'b' : 's' }} fa-{{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">{{ $title }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $desc }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Communication Pipeline -->
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Communication Pipeline</p>
                <h4 class="text-sm font-black text-slate-900 mt-1">Global Redirect & Callback Logic</h4>
            </div>
            <div class="flex items-center gap-3 rounded-full bg-slate-50 px-4 py-2 border border-slate-100">
                <div class="h-2 w-2 rounded-full {{ filled($app_public_url) ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ filled($app_public_url) ? 'Active Pipeline' : 'Dormant Pipeline' }}</span>
            </div>
        </div>

        <div class="grid gap-8">
            <div class="group">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Public Application URL</label>
                <input type="url" wire:model="app_public_url" placeholder="https://yourdomain.com" class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-900 shadow-inner focus:border-slate-900 focus:ring-0">
                @error('app_public_url')<p class="mt-2 text-[10px] font-black text-rose-500 uppercase">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach([
                    ['PayHere Notify', '/checkout/payhere/notify'],
                    ['Google Callback', '/auth/google/callback'],
                    ['Meta Callback', '/auth/facebook/callback']
                ] as [$label, $path])
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ $label }}</p>
                        <p class="text-[10px] font-mono font-bold text-slate-600 truncate select-all">{{ rtrim($app_public_url ?: config('app.url'), '/') }}{{ $path }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Provider Configuration -->
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Google Provider -->
        <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm group">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fab fa-google text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 tracking-tight">Google Identity</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">OAuth 2.0 Integration</p>
                    </div>
                </div>
                <button 
                    wire:click="$set('enable_google_login', {{ !$enable_google_login ? 'true' : 'false' }})"
                    class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $enable_google_login ? 'bg-sky-500' : 'bg-slate-100' }}"
                >
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $enable_google_login ? 'translate-x-8' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if($enable_google_login)
                <div class="space-y-6 animate-in fade-in slide-in-from-top-4">
                    <div class="group/input">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Client Identifier</label>
                        <input type="text" wire:model="google_client_id" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group/input" x-data="{ show: false }">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Client Secret Token</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'" wire:model="google_client_secret" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pr-12">
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-sky-500 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="group/input">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Redirect URI Override</label>
                        <input type="url" wire:model="google_redirect_uri" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                </div>
            @else
                <div class="py-8 text-center border-t border-slate-50">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Google Auth is Dormant</p>
                </div>
            @endif
        </div>

        <!-- Meta Provider -->
        <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm group">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fab fa-facebook text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 tracking-tight">Meta Identity</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Facebook Social Graph</p>
                    </div>
                </div>
                <button 
                    wire:click="$set('enable_facebook_login', {{ !$enable_facebook_login ? 'true' : 'false' }})"
                    class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $enable_facebook_login ? 'bg-indigo-500' : 'bg-slate-100' }}"
                >
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $enable_facebook_login ? 'translate-x-8' : 'translate-x-1' }}"></span>
                </button>
            </div>

            @if($enable_facebook_login)
                <div class="space-y-6 animate-in fade-in slide-in-from-top-4">
                    <div class="group/input">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Application ID</label>
                        <input type="text" wire:model="facebook_client_id" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group/input" x-data="{ show: false }">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Application Secret Token</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'" wire:model="facebook_client_secret" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pr-12">
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-indigo-500 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="group/input">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Redirect URI Override</label>
                        <input type="url" wire:model="facebook_redirect_uri" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                </div>
            @else
                <div class="py-8 text-center border-t border-slate-50">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Meta Auth is Dormant</p>
                </div>
            @endif
        </div>
    </div>
</div>
