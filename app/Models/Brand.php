<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = ['tenant_id', 'name', 'slug', 'description', 'logo', 'website', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
