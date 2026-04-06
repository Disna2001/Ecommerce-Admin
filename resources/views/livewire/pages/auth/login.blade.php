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

<div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.20),_transparent_36%),linear-gradient(180deg,_#f8fbff_0%,_#eef4ff_42%,_#f8fafc_100%)] px-4 py-8 sm:px-6 lg:py-12">
    <div class="mx-auto w-full max-w-5xl">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200/70 bg-white/90 shadow-[0_25px_80px_rgba(15,23,42,0.12)] backdrop-blur">
            <div class="grid lg:grid-cols-[1.02fr_0.98fr]">
                <div class="relative overflow-hidden bg-slate-950 px-6 py-8 text-white sm:px-8 lg:px-10">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.30),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(96,165,250,0.24),_transparent_35%)]"></div>
                    <div class="relative">
                        @if (!empty($logoPath))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain">
                        @else
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-200">{{ $siteName }}</p>
                        @endif

                        <h1 class="mt-6 max-w-md text-3xl font-semibold leading-tight sm:text-4xl">
                            Welcome back to {{ $siteName }}.
                        </h1>
                        <p class="mt-4 max-w-lg text-sm leading-7 text-slate-300 sm:text-base">
                            {{ $tagline }}
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-sky-400/15 text-sky-200">1</div>
                                    <div>
                                        <h2 class="font-semibold">Fast access</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">
                                            Sign in quickly with your email and password on any device.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-sky-400/15 text-sky-200">2</div>
                                    <div>
                                        <h2 class="font-semibold">Pick up where you left off</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">
                                            Manage products, orders, invoices, and profile settings from one place.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-emerald-50">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-300/20 text-emerald-100">3</div>
                                    <div>
                                        <h2 class="font-semibold">Account help built in</h2>
                                        <p class="mt-1 text-sm leading-6 text-emerald-100/90">
                                            Reset your password or create a new account if you are signing in for the first time.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-6 sm:px-8 sm:py-8">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-sky-700">Account access</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Sign in</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Enter your account details below to continue.
                            </p>
                        </div>

                        <a class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900" href="{{ route('register') }}" wire:navigate>
                            Sign up
                        </a>
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    @if($googleReady || $facebookReady)
                        <div class="mb-6 grid gap-3 sm:grid-cols-2">
                            @if($googleReady)
                                <a href="{{ route('auth.social.redirect', 'google') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                    <i class="fab fa-google text-red-500"></i> Continue with Google
                                </a>
                            @endif
                            @if($facebookReady)
                                <a href="{{ route('auth.social.redirect', 'facebook') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                    <i class="fab fa-facebook text-blue-600"></i> Continue with Facebook
                                </a>
                            @endif
                        </div>
                    @endif

                    <form wire:submit="login" class="space-y-6">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-slate-800" />
                                <x-text-input
                                    wire:model.blur="form.email"
                                    id="email"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500"
                                    type="email"
                                    name="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="name@example.com"
                                />
                                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-800" />

                                    @if (Route::has('password.request'))
                                        <a class="text-sm font-medium text-sky-700 underline-offset-4 transition hover:text-sky-900 hover:underline" href="{{ route('password.request') }}" wire:navigate>
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <x-text-input
                                    wire:model.blur="form.password"
                                    id="password"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                />
                                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                            </div>
                        </div>

                        <label for="remember" class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <input
                                wire:model="form.remember"
                                id="remember"
                                type="checkbox"
                                class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500"
                                name="remember"
                            >
                            <span>Keep me signed in on this device</span>
                        </label>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                            New here? Create an account to get started, or reset your password if you cannot remember it.
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <a class="text-sm font-medium text-slate-600 underline-offset-4 transition hover:text-slate-900 hover:underline" href="{{ route('register') }}" wire:navigate>
                                Need an account?
                            </a>

                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800">
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
