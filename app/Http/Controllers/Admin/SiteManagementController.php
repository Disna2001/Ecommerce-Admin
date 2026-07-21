<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.site-management.studio');
    }

    public function appearance(): RedirectResponse
    {
        return redirect()->route('admin.site-management.index', ['tab' => 'theme'])
            ->with('warning', 'Notice: The Appearance tab has been consolidated into the unified Storefront Studio tool.');
    }

    public function banners(): RedirectResponse
    {
        return redirect()->route('admin.site-management.index', ['tab' => 'banners'])
            ->with('warning', 'Notice: Banner management has been consolidated into the unified Storefront Studio tool.');
    }

    public function discounts(): RedirectResponse
    {
        return redirect()->route('admin.site-management.index', ['tab' => 'discounts'])
            ->with('warning', 'Notice: Discount management has been consolidated into the unified Storefront Studio tool.');
    }

    public function automatedDiscounts(): RedirectResponse
    {
        return redirect()->route('admin.site-management.index', ['tab' => 'discounts'])
            ->with('warning', 'Notice: Automated Discount Hub has been consolidated into the unified Storefront Studio tool.');
    }

    public function displayItems(): RedirectResponse
    {
        return redirect()->route('admin.site-management.index', ['tab' => 'pages'])
            ->with('warning', 'Notice: Display items management has been consolidated into the unified Storefront Studio tool.');
    }

    public function reviews(): RedirectResponse
    {
        return redirect()->route('admin.site-management.index', ['tab' => 'reviews'])
            ->with('warning', 'Notice: Review moderation has been consolidated into the unified Storefront Studio tool.');
    }
}
