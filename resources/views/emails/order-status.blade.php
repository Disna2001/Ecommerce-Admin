<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta['title'] }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f8fafc; color:#1e293b; margin:0; padding:24px; }
        .mail-wrap { max-width: 680px; margin: 0 auto; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 18px 45px rgba(15,23,42,0.08); }
        .hero { padding:32px; color:#fff; background:linear-gradient(135deg, {{ $meta['accent'] }}, #111827); }
        .hero h1 { margin:0 0 10px; font-size:28px; line-height:1.2; }
        .hero p { margin:0; opacity:0.92; font-size:15px; }
        .body { padding:28px 32px; }
        .card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:18px 20px; margin:18px 0; }
        .row { display:flex; justify-content:space-between; gap:16px; padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:14px; }
        .row:last-child { border-bottom:none; }
        .label { color:#64748b; font-weight:600; }
        .value { color:#0f172a; font-weight:700; text-align:right; }
        .note { margin-top:18px; padding:14px 16px; border-radius:16px; background:#eff6ff; color:#1d4ed8; font-size:14px; }
        .footer { padding:22px 32px 30px; color:#64748b; font-size:13px; }
        .status-pill { display:inline-block; margin-top:10px; padding:8px 14px; border-radius:999px; background:rgba(255,255,255,0.16); font-size:12px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; }
        .items-table { width:100%; border-collapse:collapse; }
        .items-table td { padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:14px; }
        .items-table tr:last-child td { border-bottom:none; }
        .muted { color:#64748b; font-size:13px; }
    </style>
</head>
<body>
    <div class="mail-wrap">
        <div class="hero">
            <div style="font-size:12px;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;opacity:0.8;">{{ $siteName }}</div>
            <h1>{{ $meta['title'] }}</h1>
            <p>{{ $meta['summary'] }}</p>
            <span class="status-pill">{{ $order->status_label }}</span>
        </div>

        <div class="body">
            <p>Hello <strong>{{ $order->customer_name }}</strong>,</p>
            <p>Here is the latest update for your order <strong>{{ $order->order_number }}</strong>.</p>

            @if($customMessage)
                <div class="note">{{ $customMessage }}</div>
            @endif

            <div class="card">
                <div class="row">
                    <span class="label">Order Number</span>
                    <span class="value">{{ $order->order_number }}</span>
                </div>
                <div class="row">
                    <span class="label">Order Status</span>
                    <span class="value">{{ $order->status_label }}</span>
                </div>
                <div class="row">
                    <span class="label">Payment Status</span>
                    <span class="value">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div class="row">
                    <span class="label">Payment Method</span>
                    <span class="value">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="row">
                    <span class="label">Total</span>
                    <span class="value">Rs {{ number_format($order->total, 2) }}</span>
                </div>
                @if($order->tracking_number)
                    <div class="row">
                        <span class="label">Tracking Number</span>
                        <span class="value">{{ $order->tracking_number }}</span>
                    </div>
                @endif
                @if($order->courier)
                    <div class="row">
                        <span class="label">Courier</span>
                        <span class="value">{{ $order->courier }}</span>
                    </div>
                @endif
                @if($order->payment_reference)
                    <div class="row">
                        <span class="label">Payment Reference</span>
                        <span class="value">{{ $order->payment_reference }}</span>
                    </div>
                @endif
            </div>

            <div class="card">
                <div style="font-size:15px;font-weight:800;color:#0f172a;margin-bottom:10px;">Order Items</div>
                <table class="items-table">
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="font-weight:700;color:#0f172a;">{{ $item->product_name }}</div>
                                <div class="muted">Qty {{ $item->quantity }} x Rs {{ number_format($item->sale_price, 2) }}</div>
                            </td>
                            <td style="text-align:right;font-weight:700;color:#0f172a;">Rs {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <p>If you need help with this order, you can reply to this email and our team will assist you.</p>
        </div>

        <div class="footer">
            <div>{{ $siteName }}</div>
            <div style="margin-top:6px;">This update was sent on {{ now()->format('F j, Y \a\t h:i A') }}.</div>
        </div>
    </div>
</body>
</html>
