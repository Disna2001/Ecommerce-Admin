<!DOCTYPE html>
<html>
<head>
    <title>Complete Stock Report</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 15px;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            color: #34495e;
            margin: 5px 0;
            font-size: 18px;
        }
        .date { 
            color: #7f8c8d; 
            font-size: 11px;
            margin-top: 5px;
        }
        .summary { 
            margin-bottom: 20px; 
            padding: 15px; 
            background-color: #f8f9fa; 
            border-radius: 5px;
            border-left: 4px solid #3498db;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .summary-item { 
            flex: 1;
            min-width: 150px;
        }
        .summary-item strong {
            color: #2c3e50;
            display: block;
            font-size: 11px;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #27ae60;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            font-size: 10px;
        }
        th { 
            background-color: #34495e; 
            color: white; 
            padding: 8px; 
            text-align: left; 
            font-weight: bold;
        }
        td { 
            padding: 6px; 
            border-bottom: 1px solid #ddd; 
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .low-stock { 
            background-color: #fff3cd; 
        }
        .low-stock td:first-child {
            border-left: 3px solid #ffc107;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .status-discontinued {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .price {
            font-weight: bold;
            color: #27ae60;
        }
        .category-header {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .category-header td {
            padding: 8px;
            background-color: #e9ecef;
            color: #495057;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .total-row td {
            padding: 8px;
        }
    </style>
</head>
<body>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    @endphp
    <div class="header">
        <h1>{{ $siteName }}</h1>
        <h2>Complete Stock Report</h2>
        <div class="date">Generated on: {{ $date }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <strong>Total Items</strong>
            <span class="value">{{ number_format($total_items) }}</span>
        </div>
        <div class="summary-item">
            <strong>Total Quantity</strong>
            <span class="value">{{ number_format($total_quantity) }}</span>
        </div>
        <div class="summary-item">
            <strong>Total Value</strong>
            <span class="value">Rs {{ number_format($total_value, 2) }}</span>
        </div>
        <div class="summary-item">
            <strong>Low Stock Items</strong>
            <span class="value">{{ number_format($low_stock_count) }}</span>
        </div>
    </div>

    @php
        $groupedStocks = $stocks->groupBy(function($stock) {
            return $stock->category->name ?? 'Uncategorized';
        });
        $grandTotal = 0;
    @endphp

    @foreach($groupedStocks as $categoryName => $categoryStocks)
    <table>
        <tr class="category-header">
            <td colspan="9"><strong>{{ $categoryName }}</strong> ({{ $categoryStocks->count() }} items)</td>
        </tr>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Item Code</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Selling Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $categoryTotal = 0; @endphp
            @foreach($categoryStocks as $stock)
                @php 
                    $stockTotal = $stock->unit_price * $stock->quantity;
                    $categoryTotal += $stockTotal;
                @endphp
                <tr class="{{ $stock->isLowStock() ? 'low-stock' : '' }}">
                    <td>{{ $stock->sku }}</td>
                    <td>{{ $stock->item_code }}</td>
                    <td>
                        <strong>{{ $stock->name }}</strong>
                        @if($stock->model_number)
                            <br><small>Model: {{ $stock->model_number }}</small>
                        @endif
                        @if($stock->color || $stock->size)
                            <br><small>
                                @if($stock->color)Color: {{ $stock->color }}@endif
                                @if($stock->size) | Size: {{ $stock->size }}@endif
                            </small>
                        @endif
                    </td>
                    <td>{{ $stock->brand->name ?? 'N/A' }}</td>
                    <td>{{ $stock->supplier->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($stock->quantity) }}</td>
                    <td class="text-right">Rs {{ number_format($stock->unit_price, 2) }}</td>
                    <td class="text-right">Rs {{ number_format($stock->selling_price, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $stock->status }}">
                            {{ ucfirst($stock->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>Category Total:</strong></td>
                <td class="text-right"><strong>{{ number_format($categoryStocks->sum('quantity')) }}</strong></td>
                <td colspan="2" class="text-right"><strong>Rs {{ number_format($categoryTotal, 2) }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <br>
    @php $grandTotal += $categoryTotal; @endphp
    @endforeach

    <!-- Grand Total Table -->
    <table>
        <tr class="total-row">
            <td colspan="5" style="font-size: 14px;"><strong>GRAND TOTAL</strong></td>
            <td class="text-right" style="font-size: 14px;"><strong>{{ number_format($total_quantity) }}</strong></td>
            <td colspan="2" class="text-right" style="font-size: 14px;"><strong>Rs {{ number_format($grandTotal, 2) }}</strong></td>
            <td></td>
        </tr>
    </table>

    <div class="footer">
        <p>This report contains all stock items in the system. Generated on {{ $date }}</p>
        <p>{{ $siteName }} - Confidential</p>
    </div>
</body>
</html>
