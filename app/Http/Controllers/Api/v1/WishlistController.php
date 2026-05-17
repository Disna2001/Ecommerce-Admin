<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Stock;

class WishlistController extends Controller
{
    /**
     * Retrieve the authenticated user's wishlist.
     */
    public function index(Request $request)
    {
        $wishlists = $request->user()->wishlists()
            ->with(['stock.itemQualityLevel', 'stock.images'])
            ->get()
            ->map(function ($wishlist) {
                $stock = $wishlist->stock;
                if (!$stock) return null;
                return [
                    'id' => $stock->id,
                    'name' => $stock->name,
                    'sku' => $stock->sku,
                    'price' => $stock->sale_price ?? $stock->unit_price ?? 0,
                    'image' => $stock->images ? $stock->images[0] ?? null : null,
                    'category_name' => $stock->itemQualityLevel ? $stock->itemQualityLevel->name : 'General',
                ];
            })->filter();

        return response()->json(array_values($wishlists->toArray()));
    }

    /**
     * Toggle a stock item in the wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
        ]);

        $user = $request->user();
        $stockId = $request->stock_id;

        $wishlist = Wishlist::where('user_id', $user->id)
                            ->where('stock_id', $stockId)
                            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['message' => 'Removed from wishlist', 'is_wished' => false]);
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'stock_id' => $stockId,
            ]);
            return response()->json(['message' => 'Added to wishlist', 'is_wished' => true]);
        }
    }
}
