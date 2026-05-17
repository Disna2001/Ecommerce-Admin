<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Retrieve the authenticated user's order history natively.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['items.stock'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference_number' => $order->reference_number,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->stock ? $item->stock->name : 'Unknown Product',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->subtotal,
                            'image' => $item->stock && $item->stock->images ? $item->stock->images[0] ?? null : null,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }
}
