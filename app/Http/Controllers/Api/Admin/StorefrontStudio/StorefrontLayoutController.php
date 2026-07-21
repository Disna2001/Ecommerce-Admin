<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Resources\StorefrontSectionResource;
use App\Models\StorefrontPage;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Http\JsonResponse;

class StorefrontLayoutController extends Controller
{
    public function __construct(
        protected StorefrontLayoutService $layoutService
    ) {
    }

    public function layout(StorefrontPage $page): JsonResponse
    {
        $sections = $this->layoutService->editorSectionsFor($page->key);

        return StorefrontSectionResource::collection($sections)->response();
    }
}
