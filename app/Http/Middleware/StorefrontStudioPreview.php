<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StorefrontStudioPreview
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasPreviewToken = $request->has('studio_preview') || $request->has('signature');

        if ($hasPreviewToken && $request->hasValidSignature()) {
            $request->attributes->set('is_studio_preview', true);
            $response = $next($request);
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            return $response;
        }

        $request->attributes->set('is_studio_preview', false);
        return $next($request);
    }
}
