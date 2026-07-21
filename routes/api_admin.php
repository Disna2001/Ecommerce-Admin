<?php

use App\Http\Controllers\Api\Admin\StorefrontStudio\MediaAssetController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\SectionFragmentController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\StorefrontLayoutController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\StorefrontPreviewTokenController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\StorefrontPublishController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\StorefrontRegistryController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\StorefrontReorderController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\StorefrontSectionController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\ThemeSettingsController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\BannerController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\DiscountController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\AutomatedDiscountController;
use App\Http\Controllers\Api\Admin\StorefrontStudio\ReviewController;
use App\Models\Banner;
use App\Models\Discount;
use App\Models\MediaAsset;
use App\Models\Review;
use App\Models\StorefrontLayoutVersion;
use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use Illuminate\Support\Facades\Route;

// Explicit route model bindings
Route::bind('page', function ($value) {
    if (is_numeric($value)) {
        return StorefrontPage::findOrFail($value);
    }

    return StorefrontPage::firstOrCreate(
        ['key' => $value],
        ['label' => ucfirst($value)]
    );
});

Route::bind('section', function ($value) {
    return StorefrontSection::findOrFail($value);
});

Route::bind('version', function ($value) {
    return StorefrontLayoutVersion::findOrFail($value);
});

Route::bind('media', function ($value) {
    return MediaAsset::findOrFail($value);
});

Route::bind('banner', function ($value) {
    return Banner::findOrFail($value);
});

Route::bind('discount', function ($value) {
    return Discount::findOrFail($value);
});

Route::bind('review', function ($value) {
    return Review::findOrFail($value);
});

Route::middleware(['auth', 'permission:view site management'])
    ->group(function () {
        Route::get('/storefront-studio/layout/{page}', [StorefrontLayoutController::class, 'layout'])
            ->name('storefront-studio.layout');

        Route::post('/storefront-studio/sections', [StorefrontSectionController::class, 'store'])
            ->name('storefront-studio.sections.store');

        Route::get('/storefront-studio/sections/{section}/fragment', SectionFragmentController::class)
            ->name('storefront-studio.sections.fragment');

        Route::patch('/storefront-studio/sections/{section}', [StorefrontSectionController::class, 'update'])
            ->name('storefront-studio.sections.update');

        Route::patch('/storefront-studio/sections/{section}/order', [StorefrontSectionController::class, 'updateOrder'])
            ->name('storefront-studio.sections.order');

        Route::patch('/storefront-studio/sections/{section}/toggle', [StorefrontSectionController::class, 'toggle'])
            ->name('storefront-studio.sections.toggle');

        Route::delete('/storefront-studio/sections/{section}', [StorefrontSectionController::class, 'destroy'])
            ->name('storefront-studio.sections.destroy');

        Route::post('/storefront-studio/reorder', StorefrontReorderController::class)
            ->name('storefront-studio.reorder');

        Route::get('/storefront-studio/registry', StorefrontRegistryController::class)
            ->name('storefront-studio.registry');

        Route::get('/storefront-studio/theme', [ThemeSettingsController::class, 'show'])
            ->name('storefront-studio.theme.show');

        Route::patch('/storefront-studio/theme', [ThemeSettingsController::class, 'update'])
            ->name('storefront-studio.theme.update');

        Route::get('/storefront-studio/preview-token/{page}', StorefrontPreviewTokenController::class)
            ->name('storefront-studio.preview-token');

        // Phase 2 — Publishing & Versioning
        Route::get('/storefront-studio/pages/{page}/versions', [StorefrontPublishController::class, 'versions'])
            ->name('storefront-studio.versions');

        Route::post('/storefront-studio/pages/{page}/publish', [StorefrontPublishController::class, 'publish'])
            ->name('storefront-studio.publish');

        Route::post('/storefront-studio/pages/{page}/discard-draft', [StorefrontPublishController::class, 'discardDraft'])
            ->name('storefront-studio.discard-draft');

        Route::post('/storefront-studio/pages/{page}/rollback/{version}', [StorefrontPublishController::class, 'rollback'])
            ->name('storefront-studio.rollback');

        Route::get('/storefront-studio/pages/{page}/has-unpublished-changes', [StorefrontPublishController::class, 'hasUnpublishedChanges'])
            ->name('storefront-studio.has-unpublished-changes');

        // Phase 3 — Media Library
        Route::get('/storefront-studio/media', [MediaAssetController::class, 'index'])
            ->name('storefront-studio.media.index');
        Route::post('/storefront-studio/media', [MediaAssetController::class, 'store'])
            ->name('storefront-studio.media.store');
        Route::delete('/storefront-studio/media/{media}', [MediaAssetController::class, 'destroy'])
            ->name('storefront-studio.media.destroy');

        // Phase 4 — Banners
        Route::get('/storefront-studio/banners', [BannerController::class, 'index'])
            ->name('storefront-studio.banners.index');
        Route::post('/storefront-studio/banners', [BannerController::class, 'store'])
            ->name('storefront-studio.banners.store');
        Route::post('/storefront-studio/banners/reorder', [BannerController::class, 'reorder'])
            ->name('storefront-studio.banners.reorder');
        Route::get('/storefront-studio/banners/{banner}', [BannerController::class, 'show'])
            ->name('storefront-studio.banners.show');
        Route::put('/storefront-studio/banners/{banner}', [BannerController::class, 'update'])
            ->name('storefront-studio.banners.update');
        Route::patch('/storefront-studio/banners/{banner}', [BannerController::class, 'update'])
            ->name('storefront-studio.banners.update-patch');
        Route::delete('/storefront-studio/banners/{banner}', [BannerController::class, 'destroy'])
            ->name('storefront-studio.banners.destroy');
        Route::patch('/storefront-studio/banners/{banner}/toggle', [BannerController::class, 'toggle'])
            ->name('storefront-studio.banners.toggle');

        // Phase 4 — Discounts
        Route::get('/storefront-studio/discounts', [DiscountController::class, 'index'])
            ->name('storefront-studio.discounts.index');
        Route::post('/storefront-studio/discounts', [DiscountController::class, 'store'])
            ->name('storefront-studio.discounts.store');
        Route::post('/storefront-studio/discounts/generate-code', [DiscountController::class, 'generateCode'])
            ->name('storefront-studio.discounts.generate-code');
        Route::get('/storefront-studio/discounts/{discount}', [DiscountController::class, 'show'])
            ->name('storefront-studio.discounts.show');
        Route::put('/storefront-studio/discounts/{discount}', [DiscountController::class, 'update'])
            ->name('storefront-studio.discounts.update');
        Route::patch('/storefront-studio/discounts/{discount}', [DiscountController::class, 'update'])
            ->name('storefront-studio.discounts.update-patch');
        Route::delete('/storefront-studio/discounts/{discount}', [DiscountController::class, 'destroy'])
            ->name('storefront-studio.discounts.destroy');
        Route::patch('/storefront-studio/discounts/{discount}/toggle', [DiscountController::class, 'toggle'])
            ->name('storefront-studio.discounts.toggle');

        // Automated Discounts Hub
        Route::get('/storefront-studio/automated-discounts', [AutomatedDiscountController::class, 'show'])
            ->name('storefront-studio.automated-discounts.show');
        Route::patch('/storefront-studio/automated-discounts', [AutomatedDiscountController::class, 'update'])
            ->name('storefront-studio.automated-discounts.update');
        Route::post('/storefront-studio/automated-discounts/orchestrate', [AutomatedDiscountController::class, 'orchestrate'])
            ->name('storefront-studio.automated-discounts.orchestrate');

        // Phase 4 — Reviews
        Route::get('/storefront-studio/reviews', [ReviewController::class, 'index'])
            ->name('storefront-studio.reviews.index');
        Route::post('/storefront-studio/reviews/bulk', [ReviewController::class, 'bulk'])
            ->name('storefront-studio.reviews.bulk');
        Route::get('/storefront-studio/reviews/{review}', [ReviewController::class, 'show'])
            ->name('storefront-studio.reviews.show');
        Route::put('/storefront-studio/reviews/{review}', [ReviewController::class, 'update'])
            ->name('storefront-studio.reviews.update');
        Route::patch('/storefront-studio/reviews/{review}', [ReviewController::class, 'update'])
            ->name('storefront-studio.reviews.update-patch');
        Route::post('/storefront-studio/reviews/{review}/approve', [ReviewController::class, 'approve'])
            ->name('storefront-studio.reviews.approve');
        Route::post('/storefront-studio/reviews/{review}/reject', [ReviewController::class, 'reject'])
            ->name('storefront-studio.reviews.reject');
        Route::patch('/storefront-studio/reviews/{review}/toggle-flag', [ReviewController::class, 'toggleFlag'])
            ->name('storefront-studio.reviews.toggle-flag');
        Route::delete('/storefront-studio/reviews/{review}', [ReviewController::class, 'destroy'])
            ->name('storefront-studio.reviews.destroy');
    });
