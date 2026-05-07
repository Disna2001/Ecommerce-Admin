<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DailyDiscount extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'stock_id',
        'discount_percent',
        'original_price',
        'discounted_price',
        'applied_date',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'applied_date' => 'date',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
