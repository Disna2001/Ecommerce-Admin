<?php

use App\Mail\WelcomeAccountMail;
use App\Models\Merchant;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
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

    public function updatedUserType(string $value): void
    {
        if ($value !== 'merchant') {
            $this->resetMerchantFields();
        }
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

    protected function resetMerchantFields(): void
    {
        $this->reset([
            'nic_number',
            'br_number',
            'nic_image',
            'shop_image',
            'merchant_selfie',
            'shop_name',
            'shop_address',
            'phone_number',
        ]);

        $this->resetErrorBag([
            'nic_number',
            'br_number',
            'nic_image',
            'shop_image',
            'merchant_selfie',
            'shop_name',
            'shop_address',
            'phone_number',
        ]);
    }

    protected function normalizeWhitespace(string $value): string
    {
        return preg_replace('/\s+/', ' ', trim($value)) ?: '';
    }

    protected function normalizeIdentifier(string $value): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($value)) ?: '');
    }

    protected function normalizePhoneNumber(string $value): string
    {
        return preg_replace('/\s+/', ' ', trim($value)) ?: '';
    }

    public function register(): void
    {
        $validated = $this->validate();
        $isFirstUser = User::count() === 0;
        $userType = $isFirstUser ? 'admin' : $validated['user_type'];

        $userData = [
            'name' => $this->normalizeWhitespace($validated['name']),
            'email' => Str::lower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'user_type' => $userType,
        ];

        $isMerchantRegistration = $userType === 'merchant';

        $merchantData = $isMerchantRegistration
            ? [
                'nic_number' => $this->normalizeIdentifier($this->nic_number),
                'br_number' => $this->normalizeIdentifier($this->br_number),
                'shop_name' => $this->normalizeWhitespace($this->shop_name),
                'shop_address' => trim($this->shop_address),
                'phone_number' => $this->normalizePhoneNumber($this->phone_number),
                'verification_status' => 'pending',
            ]
            : [];

        $storedFiles = [];

        try {
            if ($isMerchantRegistration) {
                $storedFiles = [
                    'nic_image_path' => $this->nic_image->store('merchant-documents/nic', 'public'),
                    'shop_image_path' => $this->shop_image->store('merchant-documents/shop', 'public'),
                    'merchant_selfie_path' => $this->merchant_selfie->store('merchant-documents/selfies', 'public'),
                ];
            }

            $user = DB::transaction(function () use ($isFirstUser, $userType, $userData, $merchantData, $storedFiles) {
                $user = User::create($userData);

                if ($isFirstUser) {
                    $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
                    $user->assignRole($adminRole);

                    Log::info('First user registered as admin: ' . $user->email);

                    return $user;
                }

                if ($userType === 'merchant') {
                    Merchant::create([
                        'user_id' => $user->id,
                        ...$merchantData,
                        ...$storedFiles,
                    ]);

                    $merchantRole = Role::firstOrCreate(['name' => 'Merchant', 'guard_name' => 'web']);
                    $user->assignRole($merchantRole);

                    return $user;
                }

                $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
                $user->assignRole($userRole);

                return $user;
            });
        } catch (\Throwable $exception) {
            foreach ($storedFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            report($exception);

            $this->addError('user_type', 'We could not complete your registration right now. Please try again.');

            return;
        }

        event(new Registered($user));
        Auth::login($user);
        $this->sendWelcomeEmail($user, $isFirstUser, $isMerchantRegistration);

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
        <div class="absolute top-1/3 left-1/4 h-32 w-32 animate-float rounded-2xl bg-white/5 backdrop-blur-3xl border border-white/10 dark:bg-slate-800/10"></div>
        <div class="absolute bottom-1/3 right-1/4 h-24 w-24 animate-float [animation-delay:-3s] rounded-full bg-white/5 backdrop-blur-3xl border border-white/10 dark:bg-slate-800/10"></div>
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
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="Display Lanka" class="h-10 w-auto object-contain drop-shadow-md">
                            @else
                                <a href="/" wire:navigate class="inline-block text-2xl font-black lowercase tracking-tight text-white drop-shadow-md hover:opacity-90 transition-opacity">display lanka</a>
                            @endif

                            <h1 class="mt-12 max-w-md text-3xl font-bold leading-tight tracking-tight sm:text-4xl">
                                Create your account.
                            </h1>
                            <p class="mt-4 max-w-md text-base leading-relaxed text-white/80">
                                Choose a simple customer account or apply as a merchant. We will send a confirmation email as soon as your account is created.
                            </p>
                        </div>

                        <div class="mt-12 space-y-4">
                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-md transition-all hover:bg-white/15">
                                <div class="flex items-start gap-4">
                                    <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-white shadow-inner">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white">Fast for regular users</h2>
                                        <p class="mt-1 text-sm leading-relaxed text-white/70">
                                            Name, email, and password are all you need to get started.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-md transition-all hover:bg-white/15">
                                <div class="flex items-start gap-4">
                                    <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-white shadow-inner">
                                        <i class="fas fa-store text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white">Merchant onboarding</h2>
                                        <p class="mt-1 text-sm leading-relaxed text-white/70">
                                            Upload your documents once and we will review your account.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (App\Models\User::count() === 0)
                            <div class="mt-8 rounded-2xl border border-amber-300/25 bg-amber-400/20 p-4 text-sm leading-6 text-amber-50 backdrop-blur-sm">
                                <i class="fas fa-shield-alt mr-2"></i> The first account created on this system is promoted automatically to administrator access.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Side: Register Form -->
                <div class="px-6 py-8 sm:px-12 sm:py-16 bg-white/50 dark:bg-slate-900/50">
                    <div class="mb-8 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--primary)">New account</p>
                            <h2 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">Sign up</h2>
                        </div>
                        <a class="flex items-center justify-center rounded-full border border-slate-200/80 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-white/10 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white" href="{{ route('login') }}" wire:navigate>
                            Sign in
                        </a>
                    </div>

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
                            <span class="mx-4 shrink-0 text-xs font-semibold text-slate-400">OR REGISTER WITH EMAIL</span>
                            <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                        </div>
                    @endif

                    <form wire:submit="register" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <x-input-label for="user_type" :value="__('Account Type')" class="text-sm font-semibold text-slate-800 dark:text-slate-200" />
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <label class="cursor-pointer rounded-2xl border p-4 transition-all {{ $user_type === 'regular' ? 'border-violet-500 bg-violet-50/50 shadow-sm ring-1 ring-violet-500 dark:bg-violet-500/10 dark:border-violet-400 dark:ring-violet-400' : 'border-slate-200/80 bg-white/60 hover:border-violet-300 dark:border-white/10 dark:bg-slate-800/60 dark:hover:border-violet-400/50' }}">
                                    <input type="radio" wire:model.live="user_type" name="user_type" value="regular" class="sr-only">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-900 dark:text-white">Regular User</div>
                                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Quick signup for browsing and buying.</p>
                                        </div>
                                        <div class="mt-1 h-4 w-4 shrink-0 rounded-full border {{ $user_type === 'regular' ? 'border-violet-500 bg-violet-500 ring-4 ring-violet-100 dark:ring-violet-900' : 'border-slate-300 dark:border-slate-600' }}"></div>
                                    </div>
                                </label>

                                <label class="cursor-pointer rounded-2xl border p-4 transition-all {{ $user_type === 'merchant' ? 'border-violet-500 bg-violet-50/50 shadow-sm ring-1 ring-violet-500 dark:bg-violet-500/10 dark:border-violet-400 dark:ring-violet-400' : 'border-slate-200/80 bg-white/60 hover:border-violet-300 dark:border-white/10 dark:bg-slate-800/60 dark:hover:border-violet-400/50' }}">
                                    <input type="radio" wire:model.live="user_type" name="user_type" value="merchant" class="sr-only">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-900 dark:text-white">Merchant</div>
                                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Create a seller account and submit docs.</p>
                                        </div>
                                        <div class="mt-1 h-4 w-4 shrink-0 rounded-full border {{ $user_type === 'merchant' ? 'border-violet-500 bg-violet-500 ring-4 ring-violet-100 dark:ring-violet-900' : 'border-slate-300 dark:border-slate-600' }}"></div>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-input-label for="name" :value="__('Full Name')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><i class="fas fa-user"></i></div>
                                    <x-text-input wire:model.blur="name" id="name" class="block w-full rounded-2xl border border-slate-200/80 bg-white/80 py-3 pl-11 pr-4 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" type="text" placeholder="Enter your full name" required autofocus />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="email" :value="__('Email Address')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><i class="fas fa-envelope"></i></div>
                                    <x-text-input wire:model.blur="email" id="email" class="block w-full rounded-2xl border border-slate-200/80 bg-white/80 py-3 pl-11 pr-4 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" type="email" placeholder="name@example.com" required autocomplete="username" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                <div class="relative mt-2" x-data="{ show: false }">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><i class="fas fa-lock"></i></div>
                                    <x-text-input wire:model.blur="password" id="password" class="block w-full rounded-2xl border border-slate-200/80 bg-white/80 py-3 pl-11 pr-12 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" x-bind:type="show ? 'text' : 'password'" placeholder="Create a password" required autocomplete="new-password" />
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus:outline-none">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                <div class="relative mt-2" x-data="{ show: false }">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><i class="fas fa-check-double"></i></div>
                                    <x-text-input wire:model.blur="password_confirmation" id="password_confirmation" class="block w-full rounded-2xl border border-slate-200/80 bg-white/80 py-3 pl-11 pr-12 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" x-bind:type="show ? 'text' : 'password'" placeholder="Re-enter your password" required autocomplete="new-password" />
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus:outline-none">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        @if ($user_type === 'merchant')
                            <div class="rounded-3xl border border-white/40 bg-white/40 p-6 backdrop-blur-sm dark:border-white/10 dark:bg-slate-800/40">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Merchant details</h3>
                                        <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                            Provide your business details for verification.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <x-input-label for="shop_name" :value="__('Shop Name')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                        <x-text-input wire:model.blur="shop_name" id="shop_name" class="mt-2 block w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" type="text" placeholder="Business or shop name" required />
                                        <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="phone_number" :value="__('Phone Number')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                        <x-text-input wire:model.blur="phone_number" id="phone_number" class="mt-2 block w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" type="tel" placeholder="+94 77 123 4567" required />
                                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="nic_number" :value="__('NIC Number')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                        <x-text-input wire:model.blur="nic_number" id="nic_number" class="mt-2 block w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" type="text" placeholder="National ID number" required />
                                        <x-input-error :messages="$errors->get('nic_number')" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="br_number" :value="__('Business Registration Number')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                        <x-text-input wire:model.blur="br_number" id="br_number" class="mt-2 block w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" type="text" placeholder="BR registration number" required />
                                        <x-input-error :messages="$errors->get('br_number')" class="mt-2" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="shop_address" :value="__('Shop Address')" class="ml-1 text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                        <textarea wire:model.blur="shop_address" id="shop_address" rows="3" class="mt-2 block w-full rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition-all duration-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-500/10 dark:border-white/10 dark:bg-slate-900/50 dark:text-white dark:focus:bg-slate-900" placeholder="Enter your full business address" required></textarea>
                                        <x-input-error :messages="$errors->get('shop_address')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">Required Documents</h4>
                                    <div class="mt-4 grid gap-4">
                                        <div class="rounded-2xl border border-white/40 bg-white/50 p-4 dark:border-white/10 dark:bg-slate-800/50">
                                            <x-input-label for="nic_image" :value="__('NIC Image')" class="text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                            <input type="file" wire:model="nic_image" id="nic_image" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-violet-500/10 dark:file:text-violet-400 dark:hover:file:bg-violet-500/20 file:transition-all file:cursor-pointer cursor-pointer rounded-xl border border-dashed border-slate-300 bg-white/50 px-3 py-2.5 hover:border-violet-300 transition-all dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-300 dark:hover:border-violet-500/50" accept="image/jpeg,image/png,image/jpg" required>
                                            @if ($nic_image)
                                                <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
                                                    <img src="{{ $nic_image->temporaryUrl() }}" class="h-36 w-full object-cover" alt="NIC preview">
                                                </div>
                                            @endif
                                            <x-input-error :messages="$errors->get('nic_image')" class="mt-2" />
                                        </div>

                                        <div class="rounded-2xl border border-white/40 bg-white/50 p-4 dark:border-white/10 dark:bg-slate-800/50">
                                            <x-input-label for="shop_image" :value="__('Shop Image')" class="text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                            <input type="file" wire:model="shop_image" id="shop_image" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-violet-500/10 dark:file:text-violet-400 dark:hover:file:bg-violet-500/20 file:transition-all file:cursor-pointer cursor-pointer rounded-xl border border-dashed border-slate-300 bg-white/50 px-3 py-2.5 hover:border-violet-300 transition-all dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-300 dark:hover:border-violet-500/50" accept="image/jpeg,image/png,image/jpg" required>
                                            @if ($shop_image)
                                                <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
                                                    <img src="{{ $shop_image->temporaryUrl() }}" class="h-36 w-full object-cover" alt="Shop preview">
                                                </div>
                                            @endif
                                            <x-input-error :messages="$errors->get('shop_image')" class="mt-2" />
                                        </div>

                                        <div class="rounded-2xl border border-white/40 bg-white/50 p-4 dark:border-white/10 dark:bg-slate-800/50">
                                            <x-input-label for="merchant_selfie" :value="__('Selfie with Owner')" class="text-sm font-semibold text-slate-800 dark:text-slate-200" />
                                            <input type="file" wire:model="merchant_selfie" id="merchant_selfie" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-violet-500/10 dark:file:text-violet-400 dark:hover:file:bg-violet-500/20 file:transition-all file:cursor-pointer cursor-pointer rounded-xl border border-dashed border-slate-300 bg-white/50 px-3 py-2.5 hover:border-violet-300 transition-all dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-300 dark:hover:border-violet-500/50" accept="image/jpeg,image/png,image/jpg" required>
                                            @if ($merchant_selfie)
                                                <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
                                                    <img src="{{ $merchant_selfie->temporaryUrl() }}" class="h-36 w-full object-cover" alt="Merchant selfie preview">
                                                </div>
                                            @endif
                                            <x-input-error :messages="$errors->get('merchant_selfie')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-8">
                            <button type="submit" wire:loading.attr="disabled" class="group relative flex w-full items-center justify-center gap-2 rounded-full px-8 py-3.5 text-sm font-bold text-white shadow-lg transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-violet-500/30 disabled:cursor-not-allowed disabled:opacity-70" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                                <span wire:loading.remove wire:target="register,nic_image,shop_image,merchant_selfie">Create Account</span>
                                <span wire:loading wire:target="register">Creating...</span>
                                <span wire:loading wire:target="nic_image,shop_image,merchant_selfie">Uploading...</span>
                                <i wire:loading.remove wire:target="register,nic_image,shop_image,merchant_selfie" class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
