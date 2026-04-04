<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name', 'code', 'type', 'value', 'min_order_amount',
        'max_discount_amount', 'scope', 'scope_id', 'has_timer',
        'starts_at', 'ends_at', 'show_timer_on_site', 'timer_label',
        'usage_limit', 'used_count', 'is_active', 'description',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'has_timer'          => 'boolean',
        'show_timer_on_site' => 'boolean',
        'starts_at'          => 'datetime',
        'ends_at'            => 'datetime',
        'value'              => 'decimal:2',
        'min_order_amount'   => 'decimal:2',
        'max_discount_amount'=> 'decimal:2',
    ];

    public function isActive(): bool
    {
        if (!$this->is_active) return false;
        $now = Carbon::now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at   && $now->gt($this->ends_at))   return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function isExpired(): bool
    {
        return $this->ends_at && Carbon::now()->gt($this->ends_at);
    }

    public function timeRemainingSeconds(): int
    {
        if (!$this->ends_at) return 0;
        return max(0, (int) Carbon::now()->diffInSeconds($this->ends_at, false));
    }

    /**
     * Calculate discount amount for a given price.
     */
    public function calculateDiscount(float $price): float
    {
        if ($this->type === 'percentage') {
            $discount = $price * ($this->value / 100);
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
            return $discount;
        }
        return min($this->value, $price);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                     ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
