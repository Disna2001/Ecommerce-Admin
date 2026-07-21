<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Resources\StorefrontSectionResource;
use App\Models\StorefrontLayoutVersion;
use App\Models\StorefrontPage;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StorefrontPublishController extends Controller
{
    public function __construct(
        protected StorefrontLayoutService $layoutService
    ) {
    }

    public function versions(StorefrontPage $page): JsonResponse
    {
        $versions = $page->versions()->get();

        return response()->json($versions);
    }

    public function publish(Request $request, StorefrontPage $page): JsonResponse
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $version = $this->layoutService->publishPage($page, $validated['note'] ?? null);

        return response()->json($version, 201);
    }

    public function discardDraft(StorefrontPage $page): JsonResponse
    {
        $restoredSections = $this->layoutService->discardDraft($page);

        return StorefrontSectionResource::collection($restoredSections)->response();
    }

    public function rollback(Request $request, StorefrontPage $page, StorefrontLayoutVersion $version): JsonResponse
    {
        $newVersion = $this->layoutService->rollbackToVersion($page, $version->id);

        return response()->json($newVersion);
    }

    public function hasUnpublishedChanges(StorefrontPage $page): JsonResponse
    {
        $hasChanges = $this->layoutService->hasUnpublishedChanges($page);

        return response()->json([
            'page_key' => $page->key,
            'has_unpublished_changes' => $hasChanges,
        ]);
    }
}
