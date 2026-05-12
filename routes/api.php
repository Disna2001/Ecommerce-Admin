<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\SiteSettingController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product:slug}', [ProductController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('settings', [SiteSettingController::class, 'index']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // Add protected routes here (profile, cart, orders)
    });
});
