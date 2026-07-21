<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorefrontStudio\ReorderSectionRequest;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Http\JsonResponse;

class StorefrontReorderController extends Controller
{
    public function __construct(
        protected StorefrontLayoutService $layoutService
    ) {
    }

    public function __invoke(ReorderSectionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->layoutService->reorderSections(
            $validated['page_key'],
            $validated['ordered_ids']
        );

        return response()->json(['message' => 'Sections reordered successfully']);
    }
}
