<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeSettingsController extends Controller
{
    private const ALLOWED_FONTS = [
        'Plus Jakarta Sans',
        'Outfit',
        'Figtree',
    ];

    private const COLOR_KEYS = [
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'bg_color',
        'nav_bg_color',
    ];

    private const FONT_KEYS = [
        'heading_font',
        'body_font',
    ];

    private const BRANDING_KEYS = [
        'site_name',
        'site_tagline',
        'logo_path',
        'favicon_path',
    ];

    public function show(): JsonResponse
    {
        $data = [];

        foreach (self::BRANDING_KEYS as $key) {
            $data[$key] = SiteSetting::get($key, '');
        }

        foreach (self::COLOR_KEYS as $key) {
            $data[$key] = SiteSetting::get($key, '');
        }

        foreach (self::FONT_KEYS as $key) {
            $data[$key] = SiteSetting::get($key, '');
        }

        return response()->json($data);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_name'        => 'nullable|string|max:100',
            'site_tagline'     => 'nullable|string|max:255',
            'logo_path'        => 'nullable|string|max:255',
            'favicon_path'     => 'nullable|string|max:255',
            'primary_color'    => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) {
                    $fail("The {$attribute} must be a valid hex color.");
                }
            }],
            'secondary_color'  => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) {
                    $fail("The {$attribute} must be a valid hex color.");
                }
            }],
            'accent_color'     => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) {
                    $fail("The {$attribute} must be a valid hex color.");
                }
            }],
            'text_color'       => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) {
                    $fail("The {$attribute} must be a valid hex color.");
                }
            }],
            'bg_color'         => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) {
                    $fail("The {$attribute} must be a valid hex color.");
                }
            }],
            'nav_bg_color'     => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) {
                    $fail("The {$attribute} must be a valid hex color.");
                }
            }],
            'heading_font'     => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !in_array((string) $value, self::ALLOWED_FONTS, true)) {
                    $fail("The {$attribute} must be one of the allowed fonts.");
                }
            }],
            'body_font'        => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !in_array((string) $value, self::ALLOWED_FONTS, true)) {
                    $fail("The {$attribute} must be one of the allowed fonts.");
                }
            }],
        ]);

        foreach (self::BRANDING_KEYS as $key) {
            if (array_key_exists($key, $validated)) {
                SiteSetting::set($key, (string) $validated[$key], 'text', 'branding');
            }
        }

        foreach (self::COLOR_KEYS as $key) {
            if (array_key_exists($key, $validated)) {
                SiteSetting::set($key, (string) $validated[$key], 'color', 'appearance');
            }
        }

        foreach (self::FONT_KEYS as $key) {
            if (array_key_exists($key, $validated)) {
                SiteSetting::set($key, (string) $validated[$key], 'text', 'appearance');
            }
        }

        return response()->json(['message' => 'Theme settings updated']);
    }
}
