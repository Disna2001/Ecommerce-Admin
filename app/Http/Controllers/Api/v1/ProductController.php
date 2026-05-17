<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Services\Storefront\ProductPricingService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Stock::with('category')
            ->visibleOnStorefront()
            ->when($request->category_id, function($query, $catId) {
                return $query->where('category_id', $catId);
            })
            ->latest()
            ->paginate(20);

        $pricingService = app(ProductPricingService::class);

        $products->getCollection()->transform(function (Stock $stock) use ($pricingService) {
            return [
                'id' => $stock->id,
                'name' => $stock->name,
                'description' => $stock->description,
                'price' => $pricingService->finalPriceForProduct($stock),
                'primary_image_url' => $pricingService->imageUrlForProduct($stock),
                'category' => $stock->category ? [
                    'id' => $stock->category->id,
                    'name' => $stock->category->name,
                ] : null,
            ];
        });

        return response()->json($products);
    }

    public function show(Stock $product)
    {
        $pricingService = app(ProductPricingService::class);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $pricingService->finalPriceForProduct($product),
            'primary_image_url' => $pricingService->imageUrlForProduct($product),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
        ]);
    }
}

