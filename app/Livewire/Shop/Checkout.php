<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Services\Notifications\CustomerNotificationService;
use App\Services\Orders\OrderWorkflowService;

class Checkout extends Component
{
    use WithFileUploads;

    // Shipping
    public string $first_name      = '';
    public string $last_name       = '';
    public string $email           = '';
    public string $phone           = '';
    public string $address         = '';
    public string $city            = '';
    public string $postal_code     = '';
    public string $notes           = '';

    // Payment
    public string $payment_method  = 'cod';
    public string $payment_reference = '';
    public string $payment_note      = '';
    public $payment_proof;

    public function placeOrder(
        OrderWorkflowService $orderWorkflowService,
        CustomerNotificationService $customerNotificationService
    )
    {
        $this->validate();

        $cart = session('cart', []);

        if (empty($cart)) {
            $this->dispatch('notify', type: 'error', message: 'Your cart is empty.');
            return;
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discountAmount = session('cart_discount', 0);
        $shipping = $subtotal > 5000 ? 0 : 350;
        $total = max(0, $subtotal - $discountAmount + $shipping);
        $paymentProofPath = null;

        if ($this->payment_proof) {
            $paymentProofPath = $this->payment_proof->store('orders/payment-proofs', 'public');
        }

        $requiresVerification = in_array($this->payment_method, ['bank', 'card']);

        $order = $orderWorkflowService->createOrder(
            [
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'customer_name' => trim($this->first_name . ' ' . $this->last_name),
                'customer_email' => $this->email,
                'customer_phone' => $this->phone,
                'shipping_address' => $this->address,
                'shipping_city' => $this->city,
                'shipping_postal_code' => $this->postal_code,
                'shipping_country' => 'Sri Lanka',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'shipping_fee' => $shipping,
                'total' => $total,
                'payment_method' => $this->payment_method,
                'payment_status' => 'unpaid',
                'payment_reference' => $this->payment_reference ?: null,
                'payment_review_status' => $requiresVerification ? 'pending_review' : 'not_required',
                'payment_review_note' => $this->payment_note ?: null,
                'payment_proof_path' => $paymentProofPath,
                'payment_submitted_at' => $requiresVerification ? now() : null,
                'notes' => $this->notes,
            ],
            $cart,
            $requiresVerification
                ? 'Order placed by customer. Payment proof submitted for review.'
                : 'Order placed by customer.',
            auth()->id()
        );

        $customerNotificationService->sendOrderUpdate(
            $order,
            $requiresVerification ? 'payment_submitted' : 'created',
            $requiresVerification
                ? 'We received your order and your payment proof. Our team will verify it before moving your order forward.'
                : 'We received your order successfully and will update you at each next step.'
        );

        session()->forget(['cart', 'cart_discount', 'coupon_code']);

        return redirect()->route('checkout.success', ['order' => $order->order_number]);
    }

    protected function rules(): array
    {
        $enabledMethods = $this->getEnabledPaymentMethods();

        $rules = [
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
            'notes'          => 'nullable|string',
            'payment_method' => 'required|in:'.implode(',', $enabledMethods),
            'payment_reference' => 'nullable|string|max:120',
            'payment_note'      => 'nullable|string|max:1000',
            'payment_proof'     => 'nullable|image|max:4096',
        ];

        if (in_array($this->payment_method, ['bank', 'card'])) {
            $rules['payment_reference'] = 'required|string|max:120';
            $rules['payment_proof'] = 'required|image|max:4096';
        }

        return $rules;
    }

    public function mount()
    {
        $enabledMethods = $this->getEnabledPaymentMethods();
        $this->payment_method = $enabledMethods[0] ?? 'cod';

        $user = Auth::user();
        if ($user) {
            $name = explode(' ', $user->name, 2);
            $this->first_name = $name[0];
            $this->last_name  = $name[1] ?? '';
            $this->email      = $user->email;
            $this->phone      = $user->phone ?? '';
            $this->address    = $user->address ?? '';
        }
    }

    public function getCartProperty(): array
    {
        return session('cart', []);
    }

    protected function getEnabledPaymentMethods(): array
    {
        $methods = [];

        if (SiteSetting::get('enable_cod', true)) {
            $methods[] = 'cod';
        }

        if (SiteSetting::get('enable_bank_transfer', true)) {
            $methods[] = 'bank';
        }

        if (SiteSetting::get('enable_card_payment', true)) {
            $methods[] = 'card';
        }

        return $methods ?: ['cod'];
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

    public function render()
    {
        $cart           = session('cart', []);
        $subtotal       = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discountAmount = session('cart_discount', 0);
        $shipping       = $subtotal > 5000 ? 0 : 350;
        $total          = max(0, $subtotal - $discountAmount + $shipping);
        $paymentOptions = collect([
            [
                'value' => 'cod',
                'enabled' => SiteSetting::get('enable_cod', true),
                'label' => SiteSetting::get('cod_label', 'Cash on Delivery'),
                'description' => SiteSetting::get('cod_description', 'Pay when your order arrives'),
                'icon' => 'fa-money-bill-wave',
                'text' => 'text-green-600',
                'bg' => 'bg-green-100',
            ],
            [
                'value' => 'bank',
                'enabled' => SiteSetting::get('enable_bank_transfer', true),
                'label' => SiteSetting::get('bank_label', 'Bank Transfer'),
                'description' => SiteSetting::get('bank_description', 'Upload your transfer slip for admin verification'),
                'instruction_title' => SiteSetting::get('bank_instruction_title', 'Bank transfer verification'),
                'instruction_body' => SiteSetting::get('bank_instruction_body', 'Complete your transfer, then upload the payment slip with the bank reference so our team can verify it quickly.'),
                'account_name' => SiteSetting::get('bank_account_name', ''),
                'account_number' => SiteSetting::get('bank_account_number', ''),
                'bank_name' => SiteSetting::get('bank_name', ''),
                'bank_branch' => SiteSetting::get('bank_branch', ''),
                'icon' => 'fa-university',
                'text' => 'text-blue-600',
                'bg' => 'bg-blue-100',
            ],
            [
                'value' => 'card',
                'enabled' => SiteSetting::get('enable_card_payment', true),
                'label' => SiteSetting::get('card_label', 'Online / Card Payment'),
                'description' => SiteSetting::get('card_description', 'Submit your transaction proof for approval'),
                'instruction_title' => SiteSetting::get('card_instruction_title', 'Online payment verification'),
                'instruction_body' => SiteSetting::get('card_instruction_body', 'Complete your online or card payment outside the site, then upload the confirmation screenshot or receipt for review.'),
                'icon' => 'fa-credit-card',
                'text' => 'text-purple-600',
                'bg' => 'bg-purple-100',
            ],
        ])->where('enabled', true)->values();

        if (!$paymentOptions->contains(fn($option) => $option['value'] === $this->payment_method)) {
            $this->payment_method = $paymentOptions->first()['value'] ?? 'cod';
        }

        $selectedPaymentOption = $paymentOptions->firstWhere('value', $this->payment_method);

        return view('livewire.shop.checkout', compact(
            'cart', 'subtotal', 'discountAmount', 'shipping', 'total', 'paymentOptions', 'selectedPaymentOption'
        ));
    }
}
