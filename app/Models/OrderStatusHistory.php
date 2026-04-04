<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use BelongsToTenant;

    public $timestamps = false;

    protected $fillable = ['tenant_id', 'order_id', 'status', 'note', 'changed_by', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function order()    { return $this->belongsTo(Order::class); }
    public function changedBy(){ return $this->belongsTo(User::class, 'changed_by'); }
}
