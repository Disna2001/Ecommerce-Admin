<?php

namespace App\Services\Storefront;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MediaLibraryService
{
    public function listAssets(): Collection
    {
        return MediaAsset::orderByDesc('created_at')->get();
    }

    public function upload(UploadedFile $file): MediaAsset
    {
        $path = $file->store('media-library', 'public');
        $url = Storage::url($path);

        $width = null;
        $height = null;
        if (str_starts_with($file->getClientMimeType(), 'image/')) {
            $dimensions = @getimagesize($file->getRealPath());
            if ($dimensions) {
                $width = $dimensions[0];
                $height = $dimensions[1];
            }
        }

        return MediaAsset::create([
            'name' => $file->getClientOriginalName(),
            'path' => $url,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function delete(int $id): bool
    {
        $asset = MediaAsset::findOrFail($id);
        return $asset->delete();
    }
}
