<?php

use App\Mail\WelcomeAccountMail;
use App\Models\Merchant;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.guest')] class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $user_type = 'regular';

    public string $nic_number = '';
    public string $br_number = '';
    public $nic_image;
    public $shop_image;
    public $merchant_selfie;
    public string $shop_name = '';
    public string $shop_address = '';
    public string $phone_number = '';

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:regular,merchant'],
        ];

        if ($this->user_type === 'merchant') {
            $rules = array_merge($rules, [
                'nic_number' => ['required', 'string', 'max:20', 'unique:merchants,nic_number'],
                'br_number' => ['required', 'string', 'max:50', 'unique:merchants,br_number'],
                'nic_image' => ['required', 'image', 'max:5120', 'mimes:jpeg,png,jpg'],
                'shop_image' => ['required', 'image', 'max:5120', 'mimes:jpeg,png,jpg'],
                'merchant_selfie' => ['required', 'image', 'max:5120', 'mimes:jpeg,png,jpg'],
                'shop_name' => ['required', 'string', 'max:255'],
                'shop_address' => ['required', 'string', 'max:500'],
                'phone_number' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            ]);
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'phone_number.regex' => 'Use a valid phone number with digits and standard symbols only.',
            'nic_image.required' => 'Please upload the NIC image to continue.',
            'shop_image.required' => 'Please upload a shop image to continue.',
            'merchant_selfie.required' => 'Please upload a selfie for identity verification.',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    protected function sendWelcomeEmail(User $user, bool $isFirstUser, bool $isMerchant): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeAccountMail($user, $isFirstUser, $isMerchant));
        } catch (\Throwable $exception) {
            Log::warning('Welcome email could not be sent after registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function register(): void
    {
        $validated = $this->validate();
        $isFirstUser = User::count() === 0;
        $userType = $isFirstUser ? 'admin' : $validated['user_type'];

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'user_type' => $userType,
        ]);

        if ($isFirstUser) {
            $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $user->assignRole($adminRole);

            Log::info('First user registered as admin: '.$user->email);
        } elseif ($this->user_type === 'merchant') {
            $nicImagePath = $this->nic_image->store('merchant-documents/nic', 'public');
            $shopImagePath = $this->shop_image->store('merchant-documents/shop', 'public');
            $selfiePath = $this->merchant_selfie->store('merchant-documents/selfies', 'public');

            Merchant::create([
                'user_id' => $user->id,
                'nic_number' => trim($this->nic_number),
                'br_number' => trim($this->br_number),
                'nic_image_path' => $nicImagePath,
                'shop_image_path' => $shopImagePath,
                'merchant_selfie_path' => $selfiePath,
                'shop_name' => trim($this->shop_name),
                'shop_address' => trim($this->shop_address),
                'phone_number' => trim($this->phone_number),
                'verification_status' => 'pending',
            ]);

            $merchantRole = Role::firstOrCreate(['name' => 'Merchant', 'guard_name' => 'web']);
            $user->assignRole($merchantRole);
        } else {
            $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
            $user->assignRole($userRole);
        }

        event(new Registered($user));
        Auth::login($user);
        $this->sendWelcomeEmail($user, $isFirstUser, $this->user_type === 'merchant');

        if ($isFirstUser) {
            $this->redirect(route('admin.dashboard', absolute: false), navigate: true);

            return;
        }

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

@php
    $googleReady = SiteSetting::get('enable_google_login', false) && filled(SiteSetting::get('google_client_id')) && filled(SiteSetting::get('google_client_secret'));
    $facebookReady = SiteSetting::get('enable_facebook_login', false) && filled(SiteSetting::get('facebook_client_id')) && filled(SiteSetting::get('facebook_client_secret'));
@endphp

<div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.20),_transparent_38%),linear-gradient(180deg,_#f8fbff_0%,_#eef4ff_42%,_#f8fafc_100%)] px-4 py-8 sm:px-6 lg:py-12">
    <div class="mx-auto w-full max-w-5xl">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200/70 bg-white/90 shadow-[0_25px_80px_rgba(15,23,42,0.12)] backdrop-blur">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr]">
                <div class="relative overflow-hidden bg-slate-950 px-6 py-8 text-white sm:px-8 lg:px-10">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.28),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(96,165,250,0.22),_transparent_35%)]"></div>
                    <div class="relative">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-200">Display Lanka</p>
                        <h1 class="mt-4 max-w-md text-3xl font-semibold leading-tight sm:text-4xl">
                            Create your account in a few easy steps.
                        </h1>
                        <p class="mt-4 max-w-lg text-sm leading-7 text-slate-300 sm:text-base">
                            Choose a simple customer account or apply as a merchant. We will send a confirmation email as soon as your account is created.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-sky-400/15 text-sky-200">1</div>
                                    <div>
                                        <h2 class="font-semibold">Fast for regular users</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">
                                            Name, email, and password are all you need to get started.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-sky-400/15 text-sky-200">2</div>
                                    <div>
                                        <h2 class="font-semibold">Merchant onboarding included</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">
                                            Upload your documents once and we will review your account within 24 to 48 hours.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-emerald-50">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-300/20 text-emerald-100">3</div>
                                    <div>
                                        <h2 class="font-semibold">Email sent after signup</h2>
                                        <p class="mt-1 text-sm leading-6 text-emerald-100/90">
                                            You will receive a welcome email immediately after the account is created.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (App\Models\User::count() === 0)
                            <div class="mt-8 rounded-2xl border border-amber-300/25 bg-amber-400/10 p-4 text-sm leading-6 text-amber-50">
                                The first account created on this system is promoted automatically to administrator access.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-5 py-6 sm:px-8 sm:py-8">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-sky-700">New account</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Sign up</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Fill in the form below. Merchant fields only appear when you choose a merchant account.
                            </p>
                        </div>

                        <a class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900" href="{{ route('login') }}" wire:navigate>
                            Sign in
                        </a>
                    </div>

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

                    <form wire:submit="register" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <x-input-label for="user_type" :value="__('Account Type')" class="text-sm font-semibold text-slate-800" />
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <label class="cursor-pointer rounded-2xl border p-4 transition {{ $user_type === 'regular' ? 'border-sky-500 bg-sky-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="user_type" name="user_type" value="regular" class="sr-only">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-900">Regular User</div>
                                            <p class="mt-1 text-sm leading-6 text-slate-500">Quick signup for browsing, buying, and managing your profile.</p>
                                        </div>
                                        <div class="mt-1 h-4 w-4 rounded-full border {{ $user_type === 'regular' ? 'border-sky-500 bg-sky-500 ring-4 ring-sky-100' : 'border-slate-300' }}"></div>
                                    </div>
                                </label>

                                <label class="cursor-pointer rounded-2xl border p-4 transition {{ $user_type === 'merchant' ? 'border-sky-500 bg-sky-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                    <input type="radio" wire:model.live="user_type" name="user_type" value="merchant" class="sr-only">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-900">Merchant</div>
                                            <p class="mt-1 text-sm leading-6 text-slate-500">Create a seller account and submit your verification documents once.</p>
                                        </div>
                                        <div class="mt-1 h-4 w-4 rounded-full border {{ $user_type === 'merchant' ? 'border-sky-500 bg-sky-500 ring-4 ring-sky-100' : 'border-slate-300' }}"></div>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-input-label for="name" :value="__('Full Name')" class="text-sm font-semibold text-slate-800" />
                                <x-text-input wire:model.blur="name" id="name" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="text" placeholder="Enter your full name" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-slate-800" />
                                <x-text-input wire:model.blur="email" id="email" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="email" placeholder="name@example.com" required autocomplete="username" />
                                <p class="mt-2 text-xs text-slate-500">We will send account details and important updates to this address.</p>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-800" />
                                <x-text-input wire:model.blur="password" id="password" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="password" placeholder="Create a password" required autocomplete="new-password" />
                                <p class="mt-2 text-xs text-slate-500">Use a strong password with at least 8 characters.</p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-slate-800" />
                                <x-text-input wire:model.blur="password_confirmation" id="password_confirmation" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="password" placeholder="Re-enter your password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        @if ($user_type === 'merchant')
                            <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">Merchant details</h3>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            Provide your business and identity details once. We will review them after signup.
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">Verification required</span>
                                </div>

                                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <x-input-label for="shop_name" :value="__('Shop Name')" class="text-sm font-semibold text-slate-800" />
                                        <x-text-input wire:model.blur="shop_name" id="shop_name" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="text" placeholder="Business or shop name" required />
                                        <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="phone_number" :value="__('Phone Number')" class="text-sm font-semibold text-slate-800" />
                                        <x-text-input wire:model.blur="phone_number" id="phone_number" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="tel" placeholder="+94 77 123 4567" required />
                                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="nic_number" :value="__('NIC Number')" class="text-sm font-semibold text-slate-800" />
                                        <x-text-input wire:model.blur="nic_number" id="nic_number" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="text" placeholder="National ID number" required />
                                        <x-input-error :messages="$errors->get('nic_number')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="br_number" :value="__('Business Registration Number')" class="text-sm font-semibold text-slate-800" />
                                        <x-text-input wire:model.blur="br_number" id="br_number" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" type="text" placeholder="BR registration number" required />
                                        <x-input-error :messages="$errors->get('br_number')" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="shop_address" :value="__('Shop Address')" class="text-sm font-semibold text-slate-800" />
                                        <textarea wire:model.blur="shop_address" id="shop_address" rows="3" class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 shadow-none focus:border-sky-500 focus:ring-sky-500" placeholder="Enter your full business address" required></textarea>
                                        <x-input-error :messages="$errors->get('shop_address')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Documents</h4>
                                    <div class="mt-3 grid gap-4">
                                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <x-input-label for="nic_image" :value="__('NIC Image')" class="text-sm font-semibold text-slate-800" />
                                            <input type="file" wire:model="nic_image" id="nic_image" class="mt-2 block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-600" accept="image/jpeg,image/png,image/jpg" required>
                                            <p class="mt-2 text-xs text-slate-500">Upload a clear image in JPG or PNG format up to 5MB.</p>
                                            @if ($nic_image)
                                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200">
                                                    <img src="{{ $nic_image->temporaryUrl() }}" class="h-36 w-full object-cover" alt="NIC preview">
                                                </div>
                                            @endif
                                            <x-input-error :messages="$errors->get('nic_image')" class="mt-2" />
                                        </div>

                                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <x-input-label for="shop_image" :value="__('Shop Image')" class="text-sm font-semibold text-slate-800" />
                                            <input type="file" wire:model="shop_image" id="shop_image" class="mt-2 block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-600" accept="image/jpeg,image/png,image/jpg" required>
                                            <p class="mt-2 text-xs text-slate-500">Show the storefront or inside of the shop clearly.</p>
                                            @if ($shop_image)
                                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200">
                                                    <img src="{{ $shop_image->temporaryUrl() }}" class="h-36 w-full object-cover" alt="Shop preview">
                                                </div>
                                            @endif
                                            <x-input-error :messages="$errors->get('shop_image')" class="mt-2" />
                                        </div>

                                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <x-input-label for="merchant_selfie" :value="__('Selfie with Owner')" class="text-sm font-semibold text-slate-800" />
                                            <input type="file" wire:model="merchant_selfie" id="merchant_selfie" class="mt-2 block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-600" accept="image/jpeg,image/png,image/jpg" required>
                                            <p class="mt-2 text-xs text-slate-500">A recent selfie helps us complete identity verification faster.</p>
                                            @if ($merchant_selfie)
                                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200">
                                                    <img src="{{ $merchant_selfie->temporaryUrl() }}" class="h-36 w-full object-cover" alt="Merchant selfie preview">
                                                </div>
                                            @endif
                                            <x-input-error :messages="$errors->get('merchant_selfie')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                            By creating an account, you confirm that your information is accurate and that we may email you about your account and verification status.
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <a class="text-sm font-medium text-slate-600 underline-offset-4 transition hover:text-slate-900 hover:underline" href="{{ route('login') }}" wire:navigate>
                                Already registered?
                            </a>

                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="register,nic_image,shop_image,merchant_selfie">Create account</span>
                                <span wire:loading wire:target="register">Creating account...</span>
                                <span wire:loading wire:target="nic_image,shop_image,merchant_selfie">Uploading files...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
