<!DOCTYPE html>
<html>
<head>
    <title>Stocks Export</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .date { color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; padding: 10px; text-align: left; font-size: 12px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .low-stock { background-color: #fff3cd; }
        .summary { margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
        .summary-item { margin-right: 20px; display: inline-block; }
    </style>
</head>
<body>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    @endphp
    <div class="header">
        <h2>{{ $siteName }}</h2>
        <div><strong>Stock Report</strong></div>
        <div class="date">Generated on: {{ $date }}</div>
    </div>

    <div class="summary">
        <span class="summary-item"><strong>Total Items:</strong> {{ $stocks->count() }}</span>
        <span class="summary-item"><strong>Total Value:</strong> Rs {{ number_format($total_value, 2) }}</span>
        <span class="summary-item"><strong>Low Stock Items:</strong> {{ $low_stock_count }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Quality Level</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Selling Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr class="{{ $stock->isLowStock() ? 'low-stock' : '' }}">
                <td>{{ $stock->sku }}</td>
                <td>{{ $stock->name }}</td>
                <td>{{ $stock->brand->name ?? 'N/A' }}</td>
                <td>{{ $stock->qualityLevel->name ?? 'N/A' }}</td>
                <td>{{ $stock->category->name ?? 'N/A' }}</td>
                <td>{{ $stock->supplier->name ?? 'N/A' }}</td>
                <td>{{ $stock->quantity }}</td>
                <td>Rs {{ number_format($stock->unit_price, 2) }}</td>
                <td>Rs {{ number_format($stock->selling_price, 2) }}</td>
                <td>{{ ucfirst($stock->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
