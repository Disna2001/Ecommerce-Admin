<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Supported providers
    private array $providers = ['google', 'facebook', 'github'];

    /**
     * Redirect to the OAuth provider.
     * Route: GET /auth/{provider}
     */
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        // Store redirect intent — 'connect' if logged in, 'login' if guest
        session(['social_intent' => Auth::check() ? 'connect' : 'login']);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback.
     * Route: GET /auth/{provider}/callback
     */
    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);
        $tenantId = app(TenantManager::class)->currentId();

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('profile.index', ['tab' => 'connected'])
                ->with('error', 'Could not authenticate with ' . ucfirst($provider) . '. Please try again.');
        }

        $intent = session('social_intent', 'login');

        // ── CONNECT: link to existing logged-in account ───────
        if ($intent === 'connect' && Auth::check()) {
            $this->linkToUser(Auth::user(), $provider, $socialUser);

            return redirect()->route('profile.index', ['tab' => 'connected'])
                ->with('success', ucfirst($provider) . ' account connected successfully!');
        }

        // ── LOGIN / REGISTER ──────────────────────────────────
        $idColumn    = $provider . '_id';
        $tokenColumn = $provider . '_token';

        // Find existing user by social ID
        $user = User::query()
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->where($idColumn, $socialUser->getId())
            ->first();

        if (!$user) {
            // Try to find by email
            $user = User::query()
                ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                ->where('email', $socialUser->getEmail())
                ->first();

            if ($user) {
                // Link this social account to existing user
                $this->linkToUser($user, $provider, $socialUser);
            } else {
                // Create a brand new user
                $user = User::create([
                    'tenant_id'         => $tenantId,
                    'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email'             => $socialUser->getEmail(),
                    'password'          => bcrypt(\Illuminate\Support\Str::random(32)),
                    'email_verified_at' => now(),
                    $idColumn           => $socialUser->getId(),
                    $tokenColumn        => $socialUser->token,
                    'profile_photo_path'=> null, // could download from social provider
                ]);
            }
        } else {
            // Refresh token
            $user->update([$tokenColumn => $socialUser->token]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }

    /**
     * Disconnect a social provider.
     * Route: DELETE /auth/{provider}/disconnect
     */
    public function disconnect(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);
        abort_unless(Auth::check(), 401);

        $user = Auth::user();

        // Prevent disconnecting if it's the only login method and no password
        $idColumn        = $provider . '_id';
        $connectedCount  = collect($this->providers)->filter(fn($p) => !empty($user->{$p . '_id'}))->count();
        $hasPassword     = !empty($user->password);

        if ($connectedCount === 1 && !$hasPassword) {
            return back()->with('error', 'You cannot disconnect your only login method. Set a password first.');
        }

        $user->disconnectSocial($provider);

        return redirect()->route('profile.index', ['tab' => 'connected'])
            ->with('success', ucfirst($provider) . ' disconnected successfully.');
    }

    // ── Private helper ────────────────────────────────────────
    private function linkToUser(User $user, string $provider, $socialUser): void
    {
        $user->update([
            $provider . '_id'    => $socialUser->getId(),
            $provider . '_token' => $socialUser->token,
        ]);
    }
}
