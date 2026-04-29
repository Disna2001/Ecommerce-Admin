<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Stock;
use App\Models\Discount;

class Cart extends Component
{
    public string $couponCode   = '';
    public string $couponMsg    = '';
    public bool   $couponError  = false;
    public bool   $couponApplied = false;

    public function getCartProperty(): array
    {
        return session('cart', []);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    public function getDiscountAmountProperty(): float
    {
        return session('cart_discount', 0);
    }

    public function getShippingProperty(): float
    {
        return $this->subtotal > 5000 ? 0 : 350;
    }

    public function getTotalProperty(): float
    {
        return max(0, $this->subtotal - $this->discountAmount + $this->shipping);
    }

    public function getCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function updateQuantity(int $id, int $delta)
    {
        $cart    = session('cart', []);
        $product = Stock::find($id);

        if (!isset($cart[$id])) return;

        $newQty = $cart[$id]['quantity'] + $delta;
        $availableQuantity = $product?->storefront_available_quantity ?? $newQty;

        if ($newQty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $product ? min($newQty, $availableQuantity) : $newQty;
        }

        session(['cart' => $cart]);
        $this->dispatch('cart-updated', count: collect($cart)->sum('quantity'));
    }

    public function removeItem(int $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);
        $this->dispatch('cart-updated', count: collect($cart)->sum('quantity'));
        $this->dispatch('notify', type: 'info', message: 'Item removed.');
    }

    public function applyCoupon()
    {
        $code     = strtoupper(trim($this->couponCode));
        $discount = Discount::active()->where('code', $code)->first();
        $subtotal = collect(session('cart', []))->sum(fn($i) => $i['price'] * $i['quantity']);

        if (!$discount) {
            $this->couponMsg   = 'Invalid or expired coupon code.';
            $this->couponError = true;
            return;
        }

        if ($subtotal < $discount->min_order_amount) {
            $this->couponMsg   = 'Minimum order of Rs '.number_format($discount->min_order_amount,2).' required.';
            $this->couponError = true;
            return;
        }

        $amount = $discount->calculateDiscount($subtotal);
        session(['cart_discount' => $amount]);

        $this->couponMsg     = '✓ Saved Rs '.number_format($amount,2).'!';
        $this->couponError   = false;
        $this->couponApplied = true;
        $this->dispatch('notify', type: 'success', message: 'Coupon applied! Saved Rs '.number_format($amount,2));
    }

    public function removeCoupon()
    {
        session()->forget('cart_discount');
        $this->couponCode    = '';
        $this->couponMsg     = '';
        $this->couponApplied = false;
    }

    public function render()
    {
        $cart           = session('cart', []);
        $subtotal       = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discountAmount = session('cart_discount', 0);
        $shipping       = $subtotal > 5000 ? 0 : 350;
        $total          = max(0, $subtotal - $discountAmount + $shipping);
        $count          = collect($cart)->sum('quantity');

        return view('livewire.shop.cart', compact(
            'cart', 'subtotal', 'discountAmount', 'shipping', 'total', 'count'
        ));
    }
}
