<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @php
        $profile = $billProfile ?? [];
        $currencySymbol = $company['currency_symbol'] ?? 'Rs';
        $fontScale = (float) ($profile['font_scale'] ?? 1);
        $isThermal = in_array($profile['paper_size'] ?? 'a4', ['thermal_80', 'thermal_58'], true);
        $baseFont = $isThermal ? 10.5 : 12;
        $bodyFont = $baseFont * $fontScale;
        $titleFont = ($isThermal ? 16 : 26) * $fontScale;
        $mutedFont = ($isThermal ? 8.5 : 10) * $fontScale;
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
        .page {
            padding: {{ $isThermal ? 10 : 28 }}px;
        }
        .header {
            text-align: {{ $isThermal ? 'left' : 'center' }};
            margin-bottom: {{ $isThermal ? 14 : 28 }}px;
            position: relative;
        }
        .header-brand {
            margin-bottom: 12px;
        }
        .header-brand img {
            max-height: {{ $isThermal ? 42 : 72 }}px;
            max-width: {{ $isThermal ? 150 : 220 }}px;
            object-fit: contain;
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
        .meta-grid td:last-child {
            text-align: right;
        }
        .status {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: {{ max(8, $mutedFont - 1) }}px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-sent { background-color: #dbeafe; color: #1e40af; }
        .status-draft { background-color: #f1f5f9; color: #334155; }
        .status-overdue { background-color: #fee2e2; color: #991b1b; }
        .status-cancelled { background-color: #fef3c7; color: #92400e; }
        table.items,
        table.totals {
            width: 100%;
            border-collapse: collapse;
        }
        table.items {
            margin-bottom: {{ $isThermal ? 12 : 22 }}px;
        }
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
        .watermark {
            position: fixed;
            top: 42%;
            left: 10%;
            right: 10%;
            text-align: center;
            font-size: {{ $isThermal ? 34 : 92 }}px;
            font-weight: 800;
            letter-spacing: 0.18em;
            color: {{ $invoice->isPaid() ? 'rgba(16,185,129,0.10)' : 'rgba(245,158,11,0.11)' }};
            transform: rotate(-24deg);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $invoice->isPaid() ? 'PAID' : 'UNPAID' }}</div>
    <div class="page">
        <div class="header">
            @if(filled($company['logo_data_uri'] ?? ''))
                <div class="header-brand">
                    <img src="{{ $company['logo_data_uri'] }}" alt="{{ $company['name'] }} logo">
                </div>
            @endif
            <h1>{{ $company['name'] }}</h1>
            <p>{{ $company['address'] }}</p>
            @if(($profile['show_company_phone'] ?? true) && filled($company['phone']))
                <p>Phone: {{ $company['phone'] }}</p>
            @endif
            <p>Email: {{ $company['email'] }}</p>
            @if(($profile['show_tax_id'] ?? true) && filled($company['tax_id']))
                <p>Business / Tax ID: {{ $company['tax_id'] }}</p>
            @endif
            @if(filled($profile['header_note'] ?? ''))
                <p class="meta-note">{{ $profile['header_note'] }}</p>
            @endif
        </div>

        <table class="meta-grid">
            <tr>
                <td style="width: {{ $isThermal ? '60%' : '50%' }};">
                    <strong>Bill To</strong><br>
                    {{ $invoice->customer_name }}<br>
                    @if(($profile['show_customer_email'] ?? true) && $invoice->customer_email)
                        {{ $invoice->customer_email }}<br>
                    @endif
                    @if(($profile['show_customer_phone'] ?? true) && $invoice->customer_phone)
                        {{ $invoice->customer_phone }}<br>
                    @endif
                    @if(($profile['show_customer_address'] ?? true) && $invoice->customer_address)
                        {{ $invoice->customer_address }}
                    @endif
                </td>
                <td>
                    <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}<br>
                    @if($invoice->due_date)
                        <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    @endif
                    @if(($profile['show_payment_method'] ?? true) && $invoice->payment_method)
                        <strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}<br>
                    @endif
                    <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    @if(!$isThermal)
                        <th>Description</th>
                    @endif
                    <th class="number">Qty</th>
                    <th class="number">Price</th>
                    @if(!$isThermal)
                        <th class="number">Discount</th>
                        <th class="number">Tax</th>
                    @endif
                    <th class="number">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            {{ $item->item_name }}
                            @if($isThermal && filled($item->description))
                                <div class="meta-note">{{ $item->description }}</div>
                            @endif
                        </td>
                        @if(!$isThermal)
                            <td>{{ $item->description ?? '-' }}</td>
                        @endif
                        <td class="number">{{ $item->quantity }}</td>
                        <td class="number">{{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}</td>
                        @if(!$isThermal)
                            <td class="number">{{ $item->discount }}%</td>
                            <td class="number">{{ $item->tax_rate }}%</td>
                        @endif
                        <td class="number">{{ $currencySymbol }} {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-wrap">
            <table class="totals">
                <tr>
                    <td>Subtotal</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($invoice->subtotal * ($invoice->discount / 100), 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($invoice->total, 2) }}</td>
                </tr>
                <tr>
                    <td>Amount Paid</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                <tr>
                    <td>Balance Due</td>
                    <td class="number">{{ $currencySymbol }} {{ number_format($invoice->balance_due, 2) }}</td>
                </tr>
            </table>
        </div>

        @if(($profile['show_notes'] ?? true) && ($invoice->notes || filled($profile['footer_note'] ?? '')))
            <div class="section">
                <h4>Notes</h4>
                @if($invoice->notes)
                    <p>{{ $invoice->notes }}</p>
                @endif
                @if(filled($profile['footer_note'] ?? ''))
                    <p>{{ $profile['footer_note'] }}</p>
                @endif
            </div>
        @endif

        @if(($profile['show_terms'] ?? true) && $invoice->terms_conditions)
            <div class="section">
                <h4>Terms & Conditions</h4>
                <p>{{ $invoice->terms_conditions }}</p>
            </div>
        @endif

        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
            <p>{{ $company['name'] }}</p>
        </div>
    </div>
</body>
</html>
