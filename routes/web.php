<?php

use App\Http\Controllers\Admin\SiteManagementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Livewire\Settings\RoleManager;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/help-center', [StorefrontController::class, 'helpCenter'])->name('help-center');
Route::get('/track-order', [StorefrontController::class, 'trackOrder'])->name('track-order');
Route::get('/refund-policy', [StorefrontController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/privacy-policy', [StorefrontController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [StorefrontController::class, 'termsConditions'])->name('terms-and-conditions');
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'receive'])->name('whatsapp.webhook.receive');

// Auth pages
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user && $user->can('view dashboard')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::view('/profile/edit', 'profile.edit')->name('profile.edit');
});

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');
Route::delete('/auth/{provider}/disconnect', [SocialAuthController::class, 'disconnect'])
    ->middleware('auth')
    ->name('auth.social.disconnect');

// Shop - public
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::view('/cart', 'frontend.pages.cart')->name('cart.index');

Route::post('/cart/add/{id}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [\App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/payhere/notify', [PaymentGatewayController::class, 'payhereNotify'])->name('checkout.payhere.notify');
Route::get('/checkout/payhere/{order}/return', [PaymentGatewayController::class, 'payhereReturn'])->name('checkout.payhere.return');
Route::get('/checkout/payhere/{order}/cancel', [PaymentGatewayController::class, 'payhereCancel'])->name('checkout.payhere.cancel');

// Shop - auth required
Route::middleware('auth')->group(function () {
    Route::view('/wishlist', 'frontend.pages.wishlist')->name('wishlist.index');
    Route::post('/wishlist/toggle/{id}', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/payhere/{order}/redirect', [PaymentGatewayController::class, 'payhereRedirect'])->name('checkout.payhere.redirect');
    Route::get('/orders/{order}', [StorefrontController::class, 'orderDetails'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [StorefrontController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/orders/{order}/receipt', [StorefrontController::class, 'downloadReceipt'])->name('orders.receipt');
    Route::post('/orders/{order}/return-request', [StorefrontController::class, 'requestReturn'])->name('orders.return-request');

    Route::view('/profile', 'frontend.pages.profile')->name('profile.index');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::post('/profile/logout-other-devices', [ProfileController::class, 'logoutOtherDevices'])->name('profile.logout-others');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    Route::view('/users', 'admin.users')
        ->middleware('permission:view users')
        ->name('users');

    Route::get('/roles', RoleManager::class)
        ->middleware('permission:view roles')
        ->name('roles');

    Route::get('/stocks', [AdminController::class, 'stocks'])
        ->middleware('permission:view inventory')
        ->name('stocks');
    Route::get('/categories', [AdminController::class, 'categories'])
        ->middleware('permission:view inventory')
        ->name('categories');
    Route::get('/makes', [AdminController::class, 'makes'])
        ->middleware('permission:view inventory')
        ->name('makes');
    Route::get('/brands', [AdminController::class, 'brands'])
        ->middleware('permission:view inventory')
        ->name('brands');
    Route::get('/item-types', [AdminController::class, 'itemTypes'])
        ->middleware('permission:view inventory')
        ->name('item-types');
    Route::get('/item-quality-levels', [AdminController::class, 'itemQualityLevels'])
        ->middleware('permission:view inventory')
        ->name('item-quality-levels');

    Route::get('/suppliers', [AdminController::class, 'suppliers'])
        ->middleware('permission:view supply chain')
        ->name('suppliers');
    Route::get('/warranties', [AdminController::class, 'warranties'])
        ->middleware('permission:view supply chain')
        ->name('warranties');

    Route::get('/invoices', [AdminController::class, 'invoices'])
        ->middleware('permission:view invoices')
        ->name('invoices');
    Route::get('/pos', [AdminController::class, 'pos'])
        ->middleware('permission:view pos')
        ->name('pos');
    Route::get('/settings', [AdminController::class, 'settings'])
        ->middleware('permission:view settings')
        ->name('settings');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])
        ->middleware('permission:view activity logs')
        ->name('activity-logs');
    Route::get('/notification-outbox', [AdminController::class, 'notificationOutbox'])
        ->middleware('permission:view notification outbox')
        ->name('notification-outbox');
    Route::get('/stock-movements', [AdminController::class, 'stockMovements'])
        ->middleware('permission:view stock movements')
        ->name('stock-movements');
    Route::get('/system-health', [AdminController::class, 'systemHealth'])
        ->middleware('permission:view system health')
        ->name('system-health');
    Route::get('/orders', [AdminController::class, 'orders'])
        ->middleware('permission:view orders')
        ->name('orders');

    Route::prefix('site-management')->name('site-management.')->group(function () {
        Route::get('/', [SiteManagementController::class, 'index'])
            ->middleware('permission:view site management')
            ->name('index');
        Route::get('/appearance', [SiteManagementController::class, 'appearance'])
            ->middleware('permission:view site management')
            ->name('appearance');
        Route::get('/banners', [SiteManagementController::class, 'banners'])
            ->middleware('permission:view site management')
            ->name('banners');
        Route::get('/discounts', [SiteManagementController::class, 'discounts'])
            ->middleware('permission:view site management')
            ->name('discounts');
        Route::get('/display-items', [SiteManagementController::class, 'displayItems'])
            ->middleware('permission:view site management')
            ->name('display-items');
        Route::get('/reviews', [SiteManagementController::class, 'reviews'])
            ->middleware('permission:view site management')
            ->name('reviews');
    });
});

require __DIR__.'/auth.php';
