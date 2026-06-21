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

    public function mount()
    {
        // Auto-apply claimed coupon if not already applied
        if (session()->has('claimed_coupon') && !session()->has('applied_coupon_code')) {
            $this->couponCode = session('claimed_coupon');
            $this->applyCoupon();
        } elseif (session()->has('applied_coupon_code')) {
            $this->couponCode = session('applied_coupon_code');
            $this->recalculateDiscount();
        }
    }

    public function getCartProperty(): array
    {
        $cart = session('cart', []);
        $refreshed = app(\App\Services\Storefront\ProductPricingService::class)->refreshCartPrices($cart);
        if ($cart !== $refreshed) {
            session(['cart' => $refreshed]);
        }
        return $refreshed;
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
        $this->recalculateDiscount();
        $this->dispatch('cart-updated', count: collect($cart)->sum('quantity'));
    }

    public function removeItem(int $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);
        $this->recalculateDiscount();
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
            $this->couponApplied = false;
            session()->forget(['cart_discount', 'applied_coupon_code']);
            return;
        }

        if ($subtotal < $discount->min_order_amount) {
            $this->couponMsg   = 'Minimum order of Rs '.number_format($discount->min_order_amount,2).' required.';
            $this->couponError = true;
            $this->couponApplied = false;
            session()->forget(['cart_discount', 'applied_coupon_code']);
            return;
        }

        $amount = $discount->calculateDiscount($subtotal);
        session([
            'cart_discount' => $amount,
            'applied_coupon_code' => $code
        ]);

        $this->couponMsg     = '✓ Saved Rs '.number_format($amount,2).'!';
        $this->couponError   = false;
        $this->couponApplied = true;
        $this->dispatch('notify', type: 'success', message: 'Coupon applied! Saved Rs '.number_format($amount,2));
    }

    public function removeCoupon()
    {
        session()->forget(['cart_discount', 'applied_coupon_code', 'claimed_coupon']);
        $this->couponCode    = '';
        $this->couponMsg     = '';
        $this->couponApplied = false;
        $this->couponError   = false;
    }

    protected function recalculateDiscount()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            session()->forget(['cart_discount', 'applied_coupon_code']);
            $this->couponApplied = false;
            $this->couponMsg = '';
            return;
        }

        if (!session()->has('applied_coupon_code')) {
            return;
        }

        $code = session('applied_coupon_code');
        $discount = Discount::active()->where('code', $code)->first();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        if (!$discount) {
            session()->forget(['cart_discount', 'applied_coupon_code']);
            $this->couponApplied = false;
            $this->couponMsg = '';
            return;
        }

        if ($subtotal < $discount->min_order_amount) {
            session()->forget('cart_discount');
            $this->couponMsg   = 'Minimum order of Rs '.number_format($discount->min_order_amount,2).' required.';
            $this->couponError = true;
            $this->couponApplied = false;
            return;
        }

        $amount = $discount->calculateDiscount($subtotal);
        session(['cart_discount' => $amount]);

        $this->couponMsg     = '✓ Saved Rs '.number_format($amount,2).'!';
        $this->couponError   = false;
        $this->couponApplied = true;
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
