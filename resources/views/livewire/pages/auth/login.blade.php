<?php

use App\Livewire\Forms\LoginForm;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

@php
    $siteName = SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    $tagline = SiteSetting::get('site_tagline', 'Sign in to continue managing your products, orders, and account.');
    $logoPath = SiteSetting::get('logo_path', '');
    $googleReady = SiteSetting::get('enable_google_login', false) && filled(SiteSetting::get('google_client_id')) && filled(SiteSetting::get('google_client_secret'));
    $facebookReady = SiteSetting::get('enable_facebook_login', false) && filled(SiteSetting::get('facebook_client_id')) && filled(SiteSetting::get('facebook_client_secret'));
@endphp

<div class="min-h-screen relative flex items-center justify-center overflow-hidden bg-slate-50 p-4 sm:p-6 lg:p-8 dark:bg-slate-950">
    <!-- Immersive Animated Background -->
    <div class="absolute inset-0 z-0">
        <!-- Mesh Gradient -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(139,92,246,0.1),transparent_50%)] dark:bg-[radial-gradient(circle_at_50%_50%,rgba(139,92,246,0.2),transparent_50%)]"></div>
        
        <!-- Animated Blobs -->
        <div class="absolute -right-20 -top-20 h-[600px] w-[600px] animate-drift rounded-full bg-violet-500/20 blur-[120px] dark:bg-violet-600/30"></div>
        <div class="absolute -bottom-40 -left-20 h-[600px] w-[600px] animate-drift [animation-delay:-5s] rounded-full bg-fuchsia-500/20 blur-[120px] dark:bg-fuchsia-600/30"></div>
        <div class="absolute left-1/2 top-1/2 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 animate-pulse-slow rounded-full bg-blue-500/10 blur-[100px] dark:bg-blue-600/10"></div>
        
        <!-- Animated Floating Shapes -->
        <div class="absolute top-1/4 left-1/4 h-32 w-32 animate-float rounded-2xl bg-white/5 backdrop-blur-3xl border border-white/10 dark:bg-slate-800/10"></div>
        <div class="absolute bottom-1/4 right-1/4 h-24 w-24 animate-float [animation-delay:-2s] rounded-full bg-white/5 backdrop-blur-3xl border border-white/10 dark:bg-slate-800/10"></div>
    </div>

    <div class="relative mx-auto w-full max-w-5xl z-10">
        <div class="overflow-hidden rounded-[2.5rem] border border-white/40 bg-white/60 shadow-[0_24px_80px_rgba(0,0,0,0.07)] backdrop-blur-xl dark:border-white/10 dark:bg-slate-900/60 dark:shadow-[0_24px_80px_rgba(0,0,0,0.4)]">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr]">
                
                <!-- Left Side: Brand Panel -->
                <div class="relative overflow-hidden text-white px-8 py-10 sm:px-12 sm:py-16" style="background:linear-gradient(135deg, var(--primary), var(--secondary))">
                    <!-- overlay for contrast -->
                    <div class="absolute inset-0 bg-black/10 mix-blend-multiply"></div>
                    <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                    
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div>
                            @if (!empty($logoPath))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain drop-shadow-md brightness-0 invert">
                            @else
                                <a href="/" wire:navigate class="inline-block text-2xl font-black lowercase tracking-tight text-white drop-shadow-md hover:opacity-90 transition-opacity">{{ strtolower($siteName) }}</a>
                            @endif

                            <h1 class="mt-12 max-w-md text-3xl font-bold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                                Welcome back to <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-white/70">{{ $siteName }}</span>.
                            </h1>
                            <p class="mt-6 max-w-md text-base leading-relaxed text-white/80">
                                {{ $tagline }}
                            </p>
                        </div>

                        <div class="mt-12 space-y-4">
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-md transition-all hover:bg-white/15">
                                <div class="flex items-start gap-4">
                                    <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-white shadow-inner">
                                        <i class="fas fa-bolt text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white">Fast access</h2>
                                        <p class="mt-1 text-sm leading-relaxed text-white/70">
                                            Sign in quickly with your email and password on any device.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-md transition-all hover:bg-white/15">
                                <div class="flex items-start gap-4">
                                    <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-white shadow-inner">
                                        <i class="fas fa-layer-group text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white">Pick up where you left off</h2>
                                        <p class="mt-1 text-sm leading-relaxed text-white/70">
                                            Manage products, orders, invoices, and profile settings from one place.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Login Form -->
                <div class="px-6 py-8 sm:px-12 sm:py-16 bg-white/50 dark:bg-slate-900/50">
                    <div class="mb-8 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--primary)">Account access</p>
                            <h2 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">Sign in</h2>
                        </div>
                        <a class="flex items-center justify-center rounded-full border border-slate-200/80 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-white/10 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white" href="{{ route('register') }}" wire:navigate>
                            Sign up
                        </a>
                    </div>

                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    @if($googleReady || $facebookReady)
                        <div class="mb-8 grid gap-3 sm:grid-cols-2">
                            @if($googleReady)
                                <a href="{{ route('auth.social.redirect', 'google') }}" class="group inline-flex items-center justify-center gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                    <i class="fab fa-google text-red-500 transition-transform group-hover:scale-110"></i> Google
                                </a>
                            @endif
                            @if($facebookReady)
                                <a href="{{ route('auth.social.redirect', 'facebook') }}" class="group inline-flex items-center justify-center gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                    <i class="fab fa-facebook text-blue-600 transition-transform group-hover:scale-110"></i> Facebook
                                </a>
                            @endif
                        </div>
                        
                        <div class="relative mb-8 flex items-center py-2">
                            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                            <span class="mx-4 shrink-0 text-xs font-semibold text-slate-400">OR CONTINUE WITH EMAIL</span>
                            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                        </div>
                    @endif

                    <form wire:submit="login" class="space-y-6">
                        <div class="space-y-5">
                            <div>
                                <x-input-label for="email" :value="__('Email Address')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <x-text-input
                                        wire:model.blur="form.email"
                                        id="email"
                                        class="block w-full rounded-2xl border border-slate-200/80 bg-white/80 py-3 pl-11 pr-4 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900"
                                        type="email"
                                        name="email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        placeholder="name@example.com"
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-3 mb-2 ml-1">
                                    <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                    @if (Route::has('password.request'))
                                        <a class="text-xs font-medium text-slate-500 transition hover:text-violet-600 dark:hover:text-violet-400" href="{{ route('password.request') }}" wire:navigate>
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>
                                <div class="relative" x-data="{ show: false }">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <x-text-input
                                        wire:model.blur="form.password"
                                        id="password"
                                        class="block w-full rounded-2xl border border-slate-200/80 bg-white/80 py-3 pl-11 pr-12 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900"
                                        x-bind:type="show ? 'text' : 'password'"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                    />
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus:outline-none">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                            </div>
                        </div>

                        <label for="remember" class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/60 px-4 py-3 text-sm text-slate-600 transition hover:bg-white dark:border-white/10 dark:bg-slate-800/60 dark:text-slate-300 dark:hover:bg-slate-800">
                            <input
                                wire:model="form.remember"
                                id="remember"
                                type="checkbox"
                                class="rounded border-slate-300 text-violet-600 shadow-sm focus:ring-violet-500 dark:border-slate-600 dark:bg-slate-700"
                                name="remember"
                            >
                            <span class="font-medium">Keep me signed in on this device</span>
                        </label>

                        <div class="mt-8">
                            <button type="submit" class="group relative flex w-full items-center justify-center gap-2 rounded-full px-8 py-3.5 text-sm font-bold text-white shadow-lg transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-violet-500/30" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                                <span>Sign In Securely</span>
                                <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
