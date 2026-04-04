<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
 
class WishlistController extends Controller
{
    private function getWishlist(): array
    {
        return session('wishlist', []);
    }
 
    public function index()
    {
        $ids     = $this->getWishlist();
        $items   = empty($ids) ? collect() : \App\Models\Stock::with(['brand','category'])
                    ->whereIn('id', $ids)->where('status','active')->get();
 
        return view('frontend.wishlist.index', ['wishlistItems' => $items]);
    }
 
    public function toggle($id)
    {
        $wishlist = $this->getWishlist();
 
        $added = false;
        if (in_array($id, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$id]));
        } else {
            $wishlist[] = $id;
            $added = true;
        }
 
        session(['wishlist' => $wishlist]);
 
        return response()->json(['added' => $added, 'count' => count($wishlist)]);
    }
}
