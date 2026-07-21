<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorefrontLayoutVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'status',
        'snapshot',
        'note',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'published_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(StorefrontPage::class, 'page_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
