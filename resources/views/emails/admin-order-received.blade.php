<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f8fafc; color:#1e293b; margin:0; padding:24px; }
        .mail-wrap { max-width: 680px; margin: 0 auto; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 18px 45px rgba(15,23,42,0.08); }
        .hero { padding:32px; color:#fff; background:linear-gradient(135deg, #4f46e5, #111827); }
        .hero h1 { margin:0 0 10px; font-size:28px; line-height:1.2; }
        .hero p { margin:0; opacity:0.92; font-size:15px; }
        .body { padding:28px 32px; }
        .card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:18px 20px; margin:18px 0; }
        .row { display:flex; justify-content:space-between; gap:16px; padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:14px; }
        .row:last-child { border-bottom:none; }
        .label { color:#64748b; font-weight:600; }
        .value { color:#0f172a; font-weight:700; text-align:right; }
        .footer { padding:22px 32px 30px; color:#64748b; font-size:13px; }
        .btn { display:inline-block; padding:14px 28px; border-radius:16px; background:#4f46e5; color:#ffffff; font-size:14px; font-weight:700; text-decoration:none; text-align:center; margin-top:10px; }
        .btn:hover { background:#4338ca; }
        .items-table { width:100%; border-collapse:collapse; }
        .items-table td { padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:14px; }
        .items-table tr:last-child td { border-bottom:none; }
        .muted { color:#64748b; font-size:13px; }
    </style>
</head>
<body>
    <div class="mail-wrap">
        <div class="hero">
            <div style="font-size:12px;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;opacity:0.8;">{{ $siteName }} Admin Alert</div>
            <h1>New Order Received</h1>
            <p>A new order has been placed on the storefront and is awaiting action.</p>
        </div>

        <div class="body">
            <p>Hello Admin,</p>
            <p>A new order <strong>{{ $order->order_number }}</strong> has been placed by a customer.</p>

            <div style="text-align:center; margin-bottom: 20px;">
                <a href="{{ url('/admin/orders') }}" class="btn" style="color: #ffffff;">View & Manage Order</a>
            </div>

            <div class="card">
                <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:10px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">Customer Information</div>
                <div class="row">
                    <span class="label">Name</span>
                    <span class="value">{{ $order->customer_name }}</span>
                </div>
                <div class="row">
                    <span class="label">Email</span>
                    <span class="value">{{ $order->customer_email }}</span>
                </div>
                <div class="row">
                    <span class="label">Phone</span>
                    <span class="value">{{ $order->customer_phone }}</span>
                </div>
                <div class="row">
                    <span class="label">Shipping Address</span>
                    <span class="value">
                        {{ $order->shipping_address }},<br>
                        {{ $order->shipping_city }} {{ $order->shipping_postal_code }},<br>
                        {{ $order->shipping_country }}
                    </span>
                </div>
            </div>

            <div class="card">
                <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:10px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">Order Details</div>
                <div class="row">
                    <span class="label">Order Number</span>
                    <span class="value">{{ $order->order_number }}</span>
                </div>
                <div class="row">
                    <span class="label">Order Status</span>
                    <span class="value">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="row">
                    <span class="label">Payment Method</span>
                    <span class="value">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="row">
                    <span class="label">Subtotal</span>
                    <span class="value">Rs {{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="row">
                        <span class="label">Discount</span>
                        <span class="value">- Rs {{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif
                <div class="row">
                    <span class="label">Shipping Fee</span>
                    <span class="value">Rs {{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="row" style="border-top:2px solid #e2e8f0;padding-top:12px;margin-top:4px;">
                    <span class="label" style="font-size:16px;color:#0f172a;">Total</span>
                    <span class="value" style="font-size:18px;color:#4f46e5;">Rs {{ number_format($order->total, 2) }}</span>
                </div>
                @if($order->notes)
                    <div class="row" style="flex-direction:column;align-items:flex-start;">
                        <span class="label" style="margin-bottom:4px;">Customer Notes:</span>
                        <span style="color:#0f172a;font-style:italic;font-size:13px;">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <div class="card">
                <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:10px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">Order Items</div>
                <table class="items-table">
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="font-weight:700;color:#0f172a;">{{ $item->product_name }}</div>
                                <div class="muted">
                                    SKU: {{ $item->product_sku ?: 'N/A' }} | 
                                    Qty {{ $item->quantity }} x Rs {{ number_format($item->sale_price, 2) }}
                                </div>
                            </td>
                            <td style="text-align:right;font-weight:700;color:#0f172a;">Rs {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="footer">
            <div>{{ $siteName }} Management</div>
            <div style="margin-top:6px;">This notification was generated automatically on {{ now()->format('F j, Y \a\t h:i A') }}.</div>
        </div>
    </div>
</body>
</html>
