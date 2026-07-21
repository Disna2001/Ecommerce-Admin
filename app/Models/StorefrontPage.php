<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorefrontPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(StorefrontSection::class, 'page_id')->orderBy('order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(StorefrontLayoutVersion::class, 'page_id')->orderByDesc('created_at');
    }

    public function latestPublishedVersion()
    {
        return $this->hasOne(StorefrontLayoutVersion::class, 'page_id')
            ->where('status', 'published')
            ->latestOfMany('created_at');
    }
}
