<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'name', 'email', 'phone', 'address', 'company',
        'contact_person', 'tax_number', 'payment_terms', 'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
