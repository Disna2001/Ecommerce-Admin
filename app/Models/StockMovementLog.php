<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovementLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'stock_id',
        'user_id',
        'direction',
        'quantity',
        'before_quantity',
        'after_quantity',
        'context',
        'reference_type',
        'reference_id',
        'notes',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
