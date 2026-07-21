<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Services\Storefront\MediaLibraryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaAssetController extends Controller
{
    public function __construct(
        protected MediaLibraryService $mediaService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->mediaService->listAssets());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|max:5120',
        ]);

        $asset = $this->mediaService->upload($request->file('file'));

        return response()->json($asset, 201);
    }

    public function destroy(MediaAsset $media): JsonResponse
    {
        $this->mediaService->delete($media->id);

        return response()->json([
            'message' => 'Media asset deleted successfully',
            'warning' => 'Note: Deleted media assets may still be referenced in existing section configurations.',
        ]);
    }
}
