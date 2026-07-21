<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Models\StorefrontSection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class SectionFragmentController extends Controller
{
    public function __invoke(StorefrontSection $section): Response
    {
        $secType = $section->type;
        $secId = $section->id;
        $secConfig = $section->config ?? [];

        if (!View::exists("storefront.sections.{$secType}")) {
            return response('<div class="error">Section view not found</div>', 404);
        }

        $html = view('storefront.partials.page-sections', [
            'sections' => [$section],
            'slot' => $section->slot ?? 'before',
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
        ]);
    }
}
