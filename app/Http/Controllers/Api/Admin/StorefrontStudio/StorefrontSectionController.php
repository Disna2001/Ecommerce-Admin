<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorefrontStudio\AddSectionRequest;
use App\Http\Requests\Admin\StorefrontStudio\ToggleSectionRequest;
use App\Http\Requests\Admin\StorefrontStudio\UpdateSectionRequest;
use App\Http\Resources\StorefrontSectionResource;
use App\Models\StorefrontSection;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Http\JsonResponse;

class StorefrontSectionController extends Controller
{
    public function __construct(
        protected StorefrontLayoutService $layoutService
    ) {
    }

    public function store(AddSectionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $section = $this->layoutService->addSection(
            $validated['page_key'],
            $validated['type'],
            $validated['after_order'] ?? null,
            $validated['slot'] ?? 'before'
        );

        return (new StorefrontSectionResource($section))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateSectionRequest $request, StorefrontSection $section): JsonResponse
    {
        $validated = $request->validated();

        $updated = $this->layoutService->updateSection(
            $section->id,
            $validated['config'],
            $validated['style'] ?? null
        );

        return (new StorefrontSectionResource($updated))->response();
    }

    public function toggle(ToggleSectionRequest $request, StorefrontSection $section): JsonResponse
    {
        $validated = $request->validated();

        $updated = $this->layoutService->toggleSection(
            $section->id,
            $validated['is_active']
        );

        return (new StorefrontSectionResource($updated))->response();
    }

    public function destroy(StorefrontSection $section): JsonResponse
    {
        $this->layoutService->deleteSection($section->id);

        return response()->json(['message' => 'Section deleted successfully']);
    }
}
