<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationOutbox extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'channel',
        'recipient',
        'subject',
        'status',
        'provider',
        'related_type',
        'related_id',
        'payload',
        'attempt_count',
        'last_attempt_at',
        'failure_message',
        'queued_at',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempt_count' => 'integer',
        'last_attempt_at' => 'datetime',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
}
