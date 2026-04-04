<?php
// ============================================================
// app/Models/OrderItem.php
// ============================================================
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id', 'stock_id', 'product_name', 'product_sku',
        'unit_price', 'sale_price', 'quantity', 'subtotal', 'product_snapshot',
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'unit_price'       => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'subtotal'         => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
