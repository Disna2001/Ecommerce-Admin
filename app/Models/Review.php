<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id', 'stock_id', 'order_id',
        'rating', 'title', 'body',
        'is_approved', 'is_flagged', 'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_flagged'  => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function stock() { return $this->belongsTo(Stock::class); }
    public function order() { return $this->belongsTo(Order::class); }

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
