<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
 
class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) return redirect()->route('cart.index');

        return view('frontend.pages.checkout');
    }
 
    public function placeOrder(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'payment_method' => 'required|in:cod,bank,card',
        ]);
 
        // Implement your order creation logic here
        // e.g. create Order model, OrderItems, decrement stock, send email, etc.
 
        session()->forget(['cart','cart_discount']);
 
        return redirect()->route('checkout.success', ['order' => 'ORD-'.rand(10000,99999)]);
    }
 
    public function success($order)
    {
        return view('frontend.checkout.success', compact('order'));
    }
}
