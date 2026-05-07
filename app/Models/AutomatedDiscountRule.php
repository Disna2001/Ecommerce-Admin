<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AutomatedDiscountRule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
        'min_margin_percent',
        'max_discount_percent',
        'daily_items_limit',
        'rotation_strategy',
        'target_categories',
        'target_brands',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_margin_percent' => 'decimal:2',
        'max_discount_percent' => 'decimal:2',
        'daily_items_limit' => 'integer',
        'target_categories' => 'array',
        'target_brands' => 'array',
    ];
}
