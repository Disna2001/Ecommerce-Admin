<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals td {
            border: none;
            padding: 5px;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
        }
        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-sent { background-color: #dbeafe; color: #1e40af; }
        .status-draft { background-color: #f3f4f6; color: #374151; }
        .status-overdue { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    @endphp
    <div class="header">
        <h1>{{ $company['name'] ?? $siteName }}</h1>
        <p>{{ $company['address'] }}</p>
        <p>Email: {{ $company['email'] }} | Phone: {{ $company['phone'] }}</p>
        <p>Tax ID: {{ $company['tax_id'] }}</p>
    </div>

    <div class="invoice-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none;">
                    <strong>Bill To:</strong><br>
                    {{ $invoice->customer_name }}<br>
                    @if($invoice->customer_email){{ $invoice->customer_email }}<br>@endif
                    @if($invoice->customer_phone){{ $invoice->customer_phone }}<br>@endif
                    @if($invoice->customer_address){{ $invoice->customer_address }}@endif
                </td>
                <td style="width: 50%; border: none; text-align: right;">
                    <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}<br>
                    @if($invoice->due_date)
                        <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    @endif
                    <strong>Status:</strong> 
                    <span class="status status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->description ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rs {{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->discount }}%</td>
                <td>{{ $item->tax_rate }}%</td>
                <td>Rs {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal:</td>
            <td style="text-align: right;">Rs {{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax Total:</td>
            <td style="text-align: right;">Rs {{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Discount:</td>
            <td style="text-align: right;">Rs {{ number_format($invoice->subtotal * ($invoice->discount / 100), 2) }} ({{ $invoice->discount }}%)</td>
        </tr>
        <tr class="total-row">
            <td>Total:</td>
            <td style="text-align: right;">Rs {{ number_format($invoice->total, 2) }}</td>
        </tr>
        <tr>
            <td>Amount Paid:</td>
            <td style="text-align: right;">Rs {{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        <tr>
            <td>Balance Due:</td>
            <td style="text-align: right;">Rs {{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
    </table>

    @if($invoice->notes)
    <div style="margin-top: 30px;">
        <strong>Notes:</strong>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    @if($invoice->terms_conditions)
    <div style="margin-top: 20px;">
        <strong>Terms & Conditions:</strong>
        <p>{{ $invoice->terms_conditions }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ $siteName }}</p>
        <p>This invoice was generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
