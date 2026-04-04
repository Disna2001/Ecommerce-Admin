<?php

namespace App\Services\Storefront;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class StorefrontImageService
{
    protected const PRESETS = [
        'thumb' => ['width' => 220, 'height' => 220],
        'card' => ['width' => 640, 'height' => 640],
        'detail' => ['width' => 1200, 'height' => 1200],
    ];

    public function urlForPath(?string $path, string $preset = 'card', string $format = 'jpg'): ?string
    {
        if (!$path) {
            return null;
        }

        if (!isset(self::PRESETS[$preset])) {
            return $this->assetUrl($path);
        }

        if (!$this->gdAvailable()) {
            return $this->assetUrl($path);
        }

        $derivedPath = $this->ensureDerivedImage($path, $preset, $format);

        return $this->assetUrl($derivedPath ?: $path);
    }

    public function pictureSourcesForPath(?string $path, string $preset = 'card'): array
    {
        if (!$path) {
            return [
                'fallback' => null,
                'webp' => null,
                'jpeg' => null,
            ];
        }

        $jpeg = $this->urlForPath($path, $preset, 'jpg');
        $webp = $this->webpAvailable() ? $this->urlForPath($path, $preset, 'webp') : null;

        return [
            'fallback' => $webp ?: $jpeg,
            'webp' => $webp,
            'jpeg' => $jpeg,
        ];
    }

    public function buildAllForPath(?string $path, bool $force = false): void
    {
        if (!$path || !$this->gdAvailable()) {
            return;
        }

        foreach (array_keys(self::PRESETS) as $preset) {
            $this->ensureDerivedImage($path, $preset, 'jpg', $force);

            if ($this->webpAvailable()) {
                $this->ensureDerivedImage($path, $preset, 'webp', $force);
            }
        }
    }

    protected function ensureDerivedImage(string $path, string $preset, string $format = 'jpg', bool $force = false): ?string
    {
        $sourceAbsolute = storage_path('app/public/'.$path);

        if (!is_file($sourceAbsolute)) {
            return null;
        }

        $derivedExtension = $format === 'webp' ? 'webp' : 'jpg';
        $derivedPath = 'derived/'.$preset.'/'.sha1($path).'.'.$derivedExtension;
        $derivedAbsolute = storage_path('app/public/'.$derivedPath);

        if (!$force && is_file($derivedAbsolute) && filemtime($derivedAbsolute) >= filemtime($sourceAbsolute)) {
            return $derivedPath;
        }

        if (!is_dir(dirname($derivedAbsolute))) {
            mkdir(dirname($derivedAbsolute), 0775, true);
        }

        $sourceImage = $this->createImageFromSource($sourceAbsolute);

        if (!$sourceImage) {
            return null;
        }

        [$targetWidth, $targetHeight] = [self::PRESETS[$preset]['width'], self::PRESETS[$preset]['height']];
        [$sourceWidth, $sourceHeight] = [imagesx($sourceImage), imagesy($sourceImage)];

        $scale = max($targetWidth / max(1, $sourceWidth), $targetHeight / max(1, $sourceHeight));
        $resizedWidth = (int) ceil($sourceWidth * $scale);
        $resizedHeight = (int) ceil($sourceHeight * $scale);
        $cropX = (int) floor(($resizedWidth - $targetWidth) / 2);
        $cropY = (int) floor(($resizedHeight - $targetHeight) / 2);

        $resized = imagecreatetruecolor($resizedWidth, $resizedHeight);
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        $background = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $background);
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));

        imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $sourceWidth, $sourceHeight);
        imagecopy($target, $resized, 0, 0, $cropX, $cropY, $targetWidth, $targetHeight);
        $saved = $format === 'webp' && $this->webpAvailable()
            ? imagewebp($target, $derivedAbsolute, 82)
            : imagejpeg($target, $derivedAbsolute, 84);

        imagedestroy($sourceImage);
        imagedestroy($resized);
        imagedestroy($target);

        return $saved ? $derivedPath : null;
    }

    protected function createImageFromSource(string $absolutePath)
    {
        $imageInfo = @getimagesize($absolutePath);
        $mime = $imageInfo['mime'] ?? null;

        return match ($mime) {
            'image/jpeg', 'image/jpg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($absolutePath) : null,
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($absolutePath) : null,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : null,
            'image/gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($absolutePath) : null,
            default => null,
        };
    }

    protected function gdAvailable(): bool
    {
        return function_exists('gd_info') && function_exists('imagecreatetruecolor') && function_exists('imagejpeg');
    }

    protected function webpAvailable(): bool
    {
        return function_exists('imagewebp');
    }

    protected function assetUrl(string $path): string
    {
        $storageUrl = Storage::url($path);
        $assetHost = rtrim((string) SiteSetting::get('asset_cdn_url', ''), '/');

        if (!$assetHost) {
            return $storageUrl;
        }

        return $assetHost.'/'.ltrim($storageUrl, '/');
    }
}
