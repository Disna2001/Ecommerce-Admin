<?php

namespace App\Services\Storefront;

use Illuminate\Support\Facades\URL;

class StorefrontPreviewSigner
{
    public function generateToken(string $pageKey): string
    {
        return URL::temporarySignedRoute(
            'home',
            now()->addMinutes(30),
            ['preview_page' => $pageKey]
        );
    }

    public function validateToken(string $tokenUrl): bool
    {
        try {
            return URL::hasValidSignature(request());
        } catch (\Throwable $e) {
            return false;
        }
    }
}
