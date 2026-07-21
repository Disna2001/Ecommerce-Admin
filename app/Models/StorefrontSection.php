<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorefrontSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'order',
        'is_active',
        'config',
        'style',
        'schema_version',
        'slot',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'style' => 'array',
        'order' => 'integer',
        'schema_version' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(StorefrontPage::class, 'page_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeSlot(Builder $query, string $slot): Builder
    {
        return $query->where('slot', $slot);
    }
}
