<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->when($request->category_id, function($query, $catId) {
                return $query->where('category_id', $catId);
            })
            ->latest()
            ->paginate(20);

        return response()->json($products);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category', 'reviews'));
    }
}
