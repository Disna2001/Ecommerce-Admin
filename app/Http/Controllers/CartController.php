<?php
// ============================================================
// app/Http/Controllers/CartController.php
// ============================================================
namespace App\Http\Controllers;
 
use App\Models\Stock;
use App\Services\Storefront\ProductPricingService;
use Illuminate\Http\Request;
 
class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }
 
    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }
 
    private function buildSummary(array $cart): array
    {
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discount = session('cart_discount', 0);
        $shipping = $subtotal > 5000 ? 0 : 350; // free shipping over Rs 5000
        $total    = max(0, $subtotal - $discount + $shipping);
 
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total'    => $total,
            'count'    => collect($cart)->sum('quantity'),
        ];
    }
 
    public function index()
    {
        $cart    = $this->getCart();
        $summary = $this->buildSummary($cart);
 
        $cartItems = collect($cart)->values();
 
        return view('frontend.cart.index', [
            'cartItems' => $cartItems,
            'subtotal'  => $summary['subtotal'],
            'discount'  => $summary['discount'],
            'shipping'  => $summary['shipping'],
            'total'     => $summary['total'],
        ]);
    }
 
    public function add(Request $request, $id, ProductPricingService $productPricingService)
    {
        $product = Stock::with('brand')->findOrFail($id);
        $qty     = max(1, (int) $request->input('quantity', 1));
        $cart    = $this->getCart();
 
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min($cart[$id]['quantity'] + $qty, $product->quantity);
        } else {
            $cart[$id] = $productPricingService->toCartItem($product, $qty);
        }
 
        $this->saveCart($cart);
        $summary = $this->buildSummary($cart);
 
        return response()->json([
            'success'  => true,
            'count'    => $summary['count'],
            'subtotal' => number_format($summary['subtotal'], 2),
            'total'    => number_format($summary['total'], 2),
        ]);
    }
 
    public function update(Request $request, $id)
    {
        $cart = $this->getCart();
        $qty  = max(1, (int) $request->input('quantity', 1));
 
        if (isset($cart[$id])) {
            $product = Stock::find($id);
            $cart[$id]['quantity'] = $product ? min($qty, $product->quantity) : $qty;
            $this->saveCart($cart);
        }
 
        $summary = $this->buildSummary($cart);
 
        return response()->json([
            'success'  => true,
            'count'    => $summary['count'],
            'subtotal' => number_format($summary['subtotal'], 2),
            'total'    => number_format($summary['total'], 2),
        ]);
    }
 
    public function remove($id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
 
        $summary = $this->buildSummary($cart);
 
        return response()->json([
            'success'  => true,
            'count'    => $summary['count'],
            'subtotal' => number_format($summary['subtotal'], 2),
            'total'    => number_format($summary['total'], 2),
        ]);
    }
 
    public function applyCoupon(Request $request)
    {
        $code     = strtoupper(trim($request->input('code')));
        $cart     = $this->getCart();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
 
        $discount = Discount::active()
            ->where('code', $code)
            ->first();
 
        if (!$discount) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code.']);
        }
 
        if ($subtotal < $discount->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order of Rs '.number_format($discount->min_order_amount,2).' required.',
            ]);
        }
 
        $discountAmount = $discount->calculateDiscount($subtotal);
        session(['cart_discount' => $discountAmount]);
 
        $summary = $this->buildSummary($cart);
 
        return response()->json([
            'success' => true,
            'message' => 'Coupon applied! You saved Rs '.number_format($discountAmount,2),
            'total'   => number_format($summary['total'], 2),
        ]);
    }
}
