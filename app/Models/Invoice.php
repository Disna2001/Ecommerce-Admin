<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'user_id',
        'supplier_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'amount_paid',
        'balance_due',
        'status',
        'notes',
        'terms_conditions',
        'payment_method',
        'payment_reference',
        'paid_at',
        'email_sent_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'email_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => ['bg-gray-100 text-gray-800', 'Draft'],
            'sent' => ['bg-blue-100 text-blue-800', 'Sent'],
            'paid' => ['bg-green-100 text-green-800', 'Paid'],
            'overdue' => ['bg-red-100 text-red-800', 'Overdue'],
            'cancelled' => ['bg-yellow-100 text-yellow-800', 'Cancelled'],
            default => ['bg-gray-100 text-gray-800', ucfirst($this->status)],
        };
    }

    public function isPaid()
    {
        return $this->status === 'paid' || $this->balance_due <= 0;
    }

    public function isOverdue()
    {
        return $this->status !== 'paid' && 
               $this->status !== 'cancelled' && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    public function updateBalance()
    {
        $this->balance_due = $this->total - $this->amount_paid;
        
        if ($this->balance_due <= 0) {
            $this->status = 'paid';
            $this->paid_at = now();
        } elseif ($this->isOverdue()) {
            $this->status = 'overdue';
        }
        
        $this->save();
    }
}
