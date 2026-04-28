<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->order_number }}</title>
    @php
        $profile = $billProfile ?? [];
        $currencySymbol = $company['currency_symbol'] ?? 'Rs';
        $fontScale = (float) ($profile['font_scale'] ?? 1);
        $isThermal = in_array($profile['paper_size'] ?? 'a4', ['thermal_80', 'thermal_58'], true);
        $baseFont = $isThermal ? 10.5 : 12;
        $bodyFont = $baseFont * $fontScale;
        $titleFont = ($isThermal ? 16 : 24) * $fontScale;
        $mutedFont = ($isThermal ? 8.5 : 10) * $fontScale;
        $discountAmount = (float) ($order->discount ?? 0);
    @endphp
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: {{ $bodyFont }}px;
            line-height: 1.45;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .page { padding: {{ $isThermal ? 10 : 28 }}px; }
        .header {
            text-align: {{ $isThermal ? 'left' : 'center' }};
            margin-bottom: {{ $isThermal ? 14 : 28 }}px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: {{ $titleFont }}px;
            letter-spacing: 0.04em;
        }
        .header p,
        .meta-note,
        .footer {
            font-size: {{ $mutedFont }}px;
            color: #475569;
        }
        .meta-grid {
            width: 100%;
            margin-bottom: {{ $isThermal ? 12 : 24 }}px;
            border-collapse: collapse;
        }
        .meta-grid td {
            vertical-align: top;
            padding: 0;
            border: none;
        }
        .meta-grid td:last-child { text-align: right; }
        .status {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: {{ max(8, $mutedFont - 1) }}px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: {{ $order->status_bg }};
            color: {{ $order->status_color }};
        }
        table.items,
        table.totals {
            width: 100%;
            border-collapse: collapse;
        }
        table.items { margin-bottom: {{ $isThermal ? 12 : 22 }}px; }
        table.items th {
            text-align: left;
            font-size: {{ max(8, $mutedFont - 0.5) }}px;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
            padding: 8px 6px;
        }
        table.items td {
            padding: 8px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .number {
            text-align: right;
            white-space: nowrap;
        }
        .totals-wrap {
            width: {{ $isThermal ? '100%' : '320px' }};
            margin-left: auto;
        }
        table.totals td {
            padding: 5px 0;
            border: none;
        }
        table.totals tr.total-row td {
            border-top: 2px solid #0f172a;
            padding-top: 8px;
            font-weight: bold;
            font-size: {{ ($isThermal ? 11 : 14) * $fontScale }}px;
        }
        .section {
            margin-top: {{ $isThermal ? 10 : 18 }}px;
        }
        .section h4 {
            margin: 0 0 6px;
            font-size: {{ ($isThermal ? 9.5 : 11.5) * $fontScale }}px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
        }
        .footer {
            margin-top: {{ $isThermal ? 16 : 30 }}px;
            text-align: {{ $isThermal ? 'left' : 'center' }};
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>{{ $company['name'] }}</h1>
            <p>{{ $company['address'] }}</p>
            @if(($profile['show_company_phone'] ?? true) && filled($company['phone']))
                <p>Phone: {{ $company['phone'] }}</p>
            @endif
            <p>Email: {{ $company['email'] }}</p>
            @if(filled($profile['header_note'] ?? ''))
                <p class="meta-note">{{ $profile['header_note'] }}</p>
            @endif
        </div>

        <table class="meta-grid">
            <tr>
                <td style="width: {{ $isThermal ? '60%' : '50%' }};">
                    <strong>Customer</strong><br>
                    {{ $order->customer_name }}<br>
                    @if(($profile['show_customer_email'] ?? true) && $order->customer_email)
                        {{ $order->customer_email }}<br>
                    @endif
                    @if(($profile['show_customer_phone'] ?? true) && $order->customer_phone)
                        {{ $order->customer_phone }}<br>
                    @endif
                    @if(($profile['show_customer_address'] ?? true) && $order->shipping_address)
                        {{ $order->shipping_address }}, {{ $order->shipping_city }}@if($order->shipping_postal_code), {{ $order->shipping_postal_code }}@endif
                    @endif
                </td>
                <td>
                    <strong>Order Number:</strong> {{ $order->order_number }}<br>
                    <strong>Order Date:</strong> {{ $order->created_at?->format('M d, Y h:i A') }}<br>
                    @if(($profile['show_payment_method'] ?? true) && $order->payment_method)
                        <strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}<br>
                    @endif
                    <strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'unpaid') }}<br>
                    <span class="status">{{ $order->status_label }}</span>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="number">Qty</th>
                    <th class="number">Price</th>
                    <th class="number">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $itemName = $item->product_name ?: ($item->stock?->name ?: 'Ordered item');
                        $itemPrice = (float) ($item->sale_price ?? $item->unit_price ?? 0);
                        $itemSubtotal = (float) ($item->subtotal ?? ($itemPrice * (int) $item->quantity));
                    @endphp
                    <tr>
                        <td>
                            {{ $itemName }}
                            @if(!$isThermal && filled($item->product_sku))
                                <div class="meta-note">SKU: {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td class="number">{{ $item->quantity }}</td>
                        <td class="number">{{ $currencySymbol }} {{ number_format($itemPrice, 2) }}</td>
                        <td class="number">{{ $currencySymbol }} {{ number_format($itemSubtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-wrap">
            <table class="totals">
                <tr>
                    <td>Subtotal</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format((float) $order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($discountAmount, 2) }}</td>
                </tr>
                <tr>
                    <td>Shipping</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format((float) $order->shipping_fee, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format((float) $order->total, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($order->tracking_number || $order->courier || $order->notes)
            <div class="section">
                <h4>Delivery & Notes</h4>
                @if($order->courier)
                    <p><strong>Courier:</strong> {{ $order->courier }}</p>
                @endif
                @if($order->tracking_number)
                    <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
                @endif
                @if($order->notes)
                    <p>{{ $order->notes }}</p>
                @endif
            </div>
        @endif

        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
            <p>{{ $company['name'] }}</p>
        </div>
    </div>
</body>
</html>
