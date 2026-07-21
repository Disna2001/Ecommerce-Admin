<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $banners = Banner::query()
            ->orderBy('position')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return BannerResource::collection($banners);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->has('image_path')) {
            $validated['image_path'] = $request->input('image_path');
        }

        $banner = Banner::create($validated);

        return (new BannerResource($banner))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Banner $banner): BannerResource
    {
        return new BannerResource($banner);
    }

    public function update(Request $request, Banner $banner): BannerResource
    {
        $validated = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->has('image_path')) {
            $validated['image_path'] = $request->input('image_path');
        }

        $banner->update($validated);

        return new BannerResource($banner);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();

        return response()->json([
            'message' => 'Banner deleted successfully.',
        ]);
    }

    public function toggle(Banner $banner): BannerResource
    {
        $banner->update([
            'is_active' => !$banner->is_active,
        ]);

        return new BannerResource($banner);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:banners,id',
        ]);

        foreach ($request->input('order') as $index => $id) {
            Banner::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json([
            'message' => 'Banners reordered successfully.',
        ]);
    }

    protected function validateBanner(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'caption'     => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:500',
            'position'    => 'required|in:hero,promo,sidebar,top_bar',
            'bg_color'    => 'required|string',
            'text_color'  => 'required|string',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'image_path'  => 'nullable|string|max:1000',
        ]);
    }
}
