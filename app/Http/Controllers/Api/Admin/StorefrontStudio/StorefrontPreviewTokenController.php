<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Models\StorefrontPage;
use App\Services\Storefront\StorefrontPreviewSigner;
use Illuminate\Http\JsonResponse;

class StorefrontPreviewTokenController extends Controller
{
    public function __construct(
        protected StorefrontPreviewSigner $signer
    ) {
    }

    public function __invoke(StorefrontPage $page): JsonResponse
    {
        $token = $this->signer->generateToken($page->key);

        return response()->json([
            'page_key' => $page->key,
            'token' => $token,
        ]);
    }
}
