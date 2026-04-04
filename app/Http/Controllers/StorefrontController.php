<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Storefront\StorefrontDataService;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function __construct(
        protected StorefrontDataService $storefrontDataService
    ) {
    }

    public function home()
    {
        return view('welcome', $this->storefrontDataService->getHomePageData());
    }

    public function helpCenter()
    {
        return view('frontend.pages.help-center');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'nullable|string|max:60',
            'email' => 'nullable|email',
        ]);

        $order = null;

        if ($request->filled('order_number')) {
            $order = Order::with(['items.stock', 'statusHistory'])
                ->where('order_number', $request->string('order_number')->toString())
                ->when(
                    $request->filled('email'),
                    fn($query) => $query->where('customer_email', $request->string('email')->toString())
                )
                ->first();
        }

        return view('frontend.pages.track-order', [
            'order' => $order,
            'searched' => $request->filled('order_number'),
        ]);
    }
}
