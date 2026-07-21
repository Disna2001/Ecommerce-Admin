<?php

namespace App\Http\Controllers\Api\Admin\StorefrontStudio;

use App\Http\Controllers\Controller;
use App\Services\Storefront\SectionRegistry;
use Illuminate\Http\JsonResponse;

class StorefrontRegistryController extends Controller
{
    public function __construct(
        protected SectionRegistry $registry
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json($this->registry->toRegistryArray());
    }
}
