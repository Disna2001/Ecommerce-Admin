<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .invoice-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #ddd;
        }
        .thank-you {
            text-align: center;
            font-size: 24px;
            color: #28a745;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Confirmation</h1>
        <p>Thank you for your purchase!</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $invoice->customer_name }}</strong>,</p>
        
        <p>Your payment has been successfully processed. Please find your invoice details below:</p>

        <div class="invoice-details">
            <div class="detail-row">
                <span class="label">Invoice Number:</span>
                <span class="value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice Date:</span>
                <span class="value">{{ $invoice->invoice_date->format('F j, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Date:</span>
                <span class="value">{{ now()->format('F j, Y \a\t h:i A') }}</span>
            </div>
        </div>

        <div class="invoice-details">
            <h3 style="margin-top: 0;">Order Summary</h3>
            @foreach($invoice->items as $item)
            <div class="detail-row">
                <span class="label">{{ $item->item_name }} x {{ $item->quantity }}</span>
                <span class="value">Rs {{ number_format($item->total, 2) }}</span>
            </div>
            @endforeach
            
            <div class="detail-row">
                <span class="label">Subtotal:</span>
                <span class="value">Rs {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            
            @if($invoice->discount > 0)
            <div class="detail-row">
                <span class="label">Discount:</span>
                <span class="value">- Rs {{ number_format($invoice->subtotal * ($invoice->discount / 100), 2) }}</span>
            </div>
            @endif
            
            <div class="detail-row total">
                <span class="label">Total Paid:</span>
                <span class="value">Rs {{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>

        <div class="thank-you">
            ✓ Payment Successful
        </div>

        <p>Your invoice PDF is attached to this email. Please keep it for your records.</p>

        <p>If you have any questions about your order, please don't hesitate to contact us.</p>

        <p>Best regards,<br>{{ $company['name'] }}</p>
    </div>

    <div class="footer">
        <p>{{ $company['name'] }} | {{ $company['address'] }}</p>
        <p>Email: {{ $company['email'] }} | Phone: {{ $company['phone'] }}</p>
        <p>&copy; {{ date('Y') }} {{ $company['name'] }}. All rights reserved.</p>
    </div>
</body>
</html>