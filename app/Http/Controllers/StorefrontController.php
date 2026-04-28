<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\Billing\BillCustomizationService;
use App\Services\Storefront\StorefrontDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller
{
    public function __construct(
        protected StorefrontDataService $storefrontDataService
    ) {
    }

    public function home()
    {
        return view('welcome', $this->storefrontDataService->getHomePageData());
    }

    public function helpCenter()
    {
        return view('frontend.pages.help-center');
    }

    public function refundPolicy()
    {
        return $this->policyPage(
            'Refund Policy',
            'Returns & Refunds',
            'Explain how cancellations, delivery issues, and refund requests are handled before customers complete a purchase.',
            [
                [
                    'title' => 'Order cancellations',
                    'body' => [
                        'Orders may be cancelled before the item, subscription, or account access has been delivered or activated.',
                        'Once delivery, activation, account provisioning, or digital fulfilment has started, cancellation may no longer be possible.',
                    ],
                ],
                [
                    'title' => 'When refunds are considered',
                    'body' => [
                        'Refunds may be reviewed when an order cannot be fulfilled, the wrong item is delivered, duplicate payment is confirmed, or a verified technical issue prevents access.',
                        'Requests must include the order number, payment details, and a short explanation so the support team can investigate quickly.',
                    ],
                ],
                [
                    'title' => 'Digital products and delivered subscriptions',
                    'body' => [
                        'Because most products sold through this store are digital services, account credentials, software access, gift cards, and already-delivered subscriptions are generally non-returnable once successfully delivered.',
                        'Refunds are not guaranteed for change-of-mind purchases after successful delivery.',
                    ],
                ],
                [
                    'title' => 'Refund processing time',
                    'body' => [
                        'Approved refunds are processed back to the original payment method or by an agreed settlement method.',
                        'Banks and payment gateways may take additional time to reflect the refund after approval.',
                    ],
                ],
            ]
        );
    }

    public function privacyPolicy()
    {
        return $this->policyPage(
            'Privacy Policy',
            'Privacy & Data',
            'Let customers know what information is collected, why it is used, and how it is protected when they browse, register, and place orders.',
            [
                [
                    'title' => 'Information we collect',
                    'body' => [
                        'We may collect your name, email address, phone number, delivery details, account information, and payment-related references when you register, contact support, or place an order.',
                        'Basic technical data such as device type, browser, IP address, and site activity may also be collected for security, analytics, and service improvement.',
                    ],
                ],
                [
                    'title' => 'How we use your information',
                    'body' => [
                        'Your information is used to process orders, confirm payments, provide customer support, send order updates, prevent fraud, and improve the storefront experience.',
                        'We only use the data that is reasonably necessary to operate the website and fulfil customer requests.',
                    ],
                ],
                [
                    'title' => 'Payments and third-party services',
                    'body' => [
                        'Payments are processed through trusted third-party payment providers. We do not store full card details on this website.',
                        'Information may be shared with payment gateways, courier partners, analytics tools, and messaging providers only when required to complete services or comply with legal obligations.',
                    ],
                ],
                [
                    'title' => 'Data protection',
                    'body' => [
                        'We take reasonable administrative and technical measures to protect customer information from unauthorized access, misuse, or disclosure.',
                        'Customers should also protect their account password and contact us immediately if they suspect unauthorized activity.',
                    ],
                ],
            ]
        );
    }

    public function termsConditions()
    {
        return $this->policyPage(
            'Terms & Conditions',
            'Terms of Use',
            'Set the basic rules for browsing the storefront, placing orders, and using the services provided through this website.',
            [
                [
                    'title' => 'Using the website',
                    'body' => [
                        'By using this website, you agree to use it lawfully and provide accurate account, billing, and order information.',
                        'You are responsible for maintaining the confidentiality of your account and any activities performed under it.',
                    ],
                ],
                [
                    'title' => 'Products, pricing, and availability',
                    'body' => [
                        'Product descriptions, stock levels, delivery timing, and prices may change without prior notice.',
                        'Orders may be declined or cancelled if a product becomes unavailable, pricing is incorrect, or fraud or abuse is suspected.',
                    ],
                ],
                [
                    'title' => 'Payments and verification',
                    'body' => [
                        'Orders are only confirmed after successful payment or manual payment verification where applicable.',
                        'The store may request additional confirmation for suspicious or incomplete payment records before completing fulfilment.',
                    ],
                ],
                [
                    'title' => 'Delivery, returns, and support',
                    'body' => [
                        'Delivery timelines depend on the product type, order review requirements, and any information needed from the customer.',
                        'Returns and refund handling follow the published Refund Policy, and support is available through the listed contact channels.',
                    ],
                ],
            ]
        );
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'nullable|string|max:60',
            'email' => 'nullable|email',
        ]);

        $order = null;

        if ($request->filled('order_number')) {
            $order = Order::with(['items.stock', 'statusHistory'])
                ->where('order_number', $request->string('order_number')->toString())
                ->when(
                    $request->filled('email'),
                    fn($query) => $query->where('customer_email', $request->string('email')->toString())
                )
                ->first();
        }

        return view('frontend.pages.track-order', [
            'order' => $order,
            'searched' => $request->filled('order_number'),
        ]);
    }

    public function orderDetails(Order $order)
    {
        abort_unless(Auth::id() === $order->user_id, 404);

        $order->load([
            'items.stock',
            'statusHistory.changedBy',
        ]);

        return view('frontend.pages.order-details', [
            'order' => $order,
        ]);
    }

    public function requestReturn(Request $request, Order $order)
    {
        abort_unless(Auth::id() === $order->user_id, 404);

        if (! $order->canBeReturned() || $order->isReturnPending() || in_array($order->status, ['returned', 'refunded'], true)) {
            return back()->with('error', 'This order is not eligible for a new return request.');
        }

        $validated = $request->validate([
            'return_reason' => 'required|string|max:255',
            'return_notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'status' => 'return_requested',
                'return_reason' => $validated['return_reason'],
                'return_notes' => $validated['return_notes'] ?? null,
                'return_requested_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'return_requested',
                'note' => 'Customer requested a return: '.$validated['return_reason'].(filled($validated['return_notes'] ?? null) ? ' — '.$validated['return_notes'] : ''),
                'changed_by' => Auth::id(),
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('orders.show', $order->fresh())
            ->with('success', 'Your return request has been submitted. The team will review it shortly.');
    }

    public function downloadReceipt(Order $order, BillCustomizationService $billCustomizationService)
    {
        abort_unless(Auth::id() === $order->user_id, 404);

        $order->loadMissing(['items.stock']);

        $billProfile = $billCustomizationService->resolveProfile('invoice_pdf', [
            'device_type' => 'desktop',
            'input_mode' => 'any',
            'printer_hint' => 'customer receipt',
        ]);

        $pdf = Pdf::loadView('exports.order-receipt-pdf', [
            'order' => $order,
            'company' => $billCustomizationService->companyPayload(),
            'billProfile' => $billProfile,
        ])->setPaper(
            $billCustomizationService->paperConfig($billProfile),
            $billCustomizationService->paperOrientation($billProfile)
        );

        return $pdf->download('order-'.$order->order_number.'.pdf');
    }

    public function downloadInvoice(Order $order, BillCustomizationService $billCustomizationService)
    {
        abort_unless(Auth::id() === $order->user_id, 404);

        $order->loadMissing(['items.stock']);

        $invoice = new Invoice();
        $invoice->forceFill([
            'tenant_id' => $order->tenant_id,
            'invoice_number' => 'INV-'.$order->order_number,
            'user_id' => $order->user_id,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'customer_address' => trim(collect([
                $order->shipping_address,
                $order->shipping_city,
                $order->shipping_postal_code,
                $order->shipping_country,
            ])->filter()->implode(', ')),
            'invoice_date' => $order->created_at,
            'due_date' => null,
            'subtotal' => $order->subtotal,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => $order->total,
            'amount_paid' => $order->payment_status === 'paid' ? $order->total : 0,
            'balance_due' => $order->payment_status === 'paid' ? 0 : $order->total,
            'status' => $order->payment_status === 'paid' ? 'paid' : 'sent',
            'notes' => $order->notes,
            'terms_conditions' => 'Customer purchases are subject to the published Terms & Conditions and Refund Policy available on the website.',
            'payment_method' => $order->payment_method,
            'payment_reference' => $order->payment_reference,
            'paid_at' => $order->payment_status === 'paid' ? ($order->payment_verified_at ?? $order->updated_at) : null,
        ]);

        $invoiceItems = $order->items->map(function ($item) {
            $unitPrice = (float) ($item->sale_price ?? $item->unit_price ?? 0);
            $lineTotal = (float) ($item->subtotal ?? ($unitPrice * (int) $item->quantity));

            $invoiceItem = new InvoiceItem();
            $invoiceItem->forceFill([
                'tenant_id' => $item->tenant_id,
                'stock_id' => $item->stock_id,
                'item_name' => $item->product_name ?: ($item->stock?->name ?: 'Ordered item'),
                'item_code' => $item->product_sku,
                'description' => data_get($item->product_snapshot, 'description'),
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'discount' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'total' => $lineTotal,
            ]);

            return $invoiceItem;
        });

        $invoice->setRelation('items', $invoiceItems);

        $data = $billCustomizationService->invoiceViewData($invoice, [
            'device_type' => 'desktop',
            'input_mode' => 'any',
            'printer_hint' => 'customer invoice',
        ]);

        $pdf = Pdf::loadView('exports.invoice-pdf', $data)->setPaper(
            $billCustomizationService->paperConfig($data['billProfile']),
            $billCustomizationService->paperOrientation($data['billProfile'])
        );

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }

    protected function policyPage(string $title, string $eyebrow, string $intro, array $sections)
    {
        return view('frontend.pages.policy', [
            'title' => $title,
            'eyebrow' => $eyebrow,
            'intro' => $intro,
            'sections' => $sections,
        ]);
    }
}
