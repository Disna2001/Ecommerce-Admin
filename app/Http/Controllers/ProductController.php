<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('frontend.pages.products-index');
    }

    public function show(Stock $product)
    {
        abort_if($product->status !== 'active', 404);

        $product->load(['category','brand','make','itemType','warranty','qualityLevel']);

        return view('frontend.pages.products-show', compact('product'));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:200',
            'body'   => 'required|string|min:10',
        ]);

        // Only store if Review model exists
        if (class_exists(\App\Models\Review::class)) {
            // Check if user already reviewed
            $existing = \App\Models\Review::where('stock_id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existing) {
                return back()->with('error', 'You have already reviewed this product.');
            }

            \App\Models\Review::create([
                'stock_id'    => $id,
                'user_id'     => auth()->id(),
                'rating'      => $request->rating,
                'title'       => $request->title,
                'body'        => $request->body,
                'is_approved' => true, // set false if you want moderation
            ]);
        }

        return back()->with('success', 'Review submitted successfully!');
    }
}
