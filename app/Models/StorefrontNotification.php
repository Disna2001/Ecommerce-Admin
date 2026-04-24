<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StorefrontNotification extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'notification_key',
        'type',
        'label',
        'accent',
        'title',
        'body',
        'action_url',
        'read_at',
        'notified_at',
        'payload',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'notified_at' => 'datetime',
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
