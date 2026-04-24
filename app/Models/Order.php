<?php

namespace App\Models;

use App\Mail\OrderStatusMail;
use App\Models\Concerns\BelongsToTenant;
use App\Models\NotificationOutbox;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_number', 'user_id',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_postal_code', 'shipping_country',
        'status', 'subtotal', 'discount', 'shipping_fee', 'total',
        'payment_method', 'payment_gateway', 'payment_status', 'payment_reference',
        'payment_gateway_transaction_id', 'payment_gateway_payload',
        'payment_review_status', 'payment_review_note', 'payment_proof_path',
        'payment_submitted_at', 'payment_verified_at', 'payment_verified_by',
        'tracking_number', 'courier', 'tracking_url',
        'return_reason', 'return_notes', 'return_requested_at', 'return_approved_at',
        'coupon_code', 'notes',
    ];

    protected $casts = [
        'subtotal'             => 'decimal:2',
        'discount'             => 'decimal:2',
        'shipping_fee'         => 'decimal:2',
        'total'                => 'decimal:2',
        'payment_submitted_at' => 'datetime',
        'payment_verified_at'  => 'datetime',
        'return_requested_at'  => 'datetime',
        'return_approved_at'   => 'datetime',
        'payment_gateway_payload' => 'array',
    ];

    // ── Status labels & colors ────────────────────────────────
    public const STATUSES = [
        'pending'          => ['label' => 'Pending',          'color' => '#f59e0b', 'bg' => '#fef3c7'],
        'confirmed'        => ['label' => 'Confirmed',        'color' => '#3b82f6', 'bg' => '#dbeafe'],
        'processing'       => ['label' => 'Processing',       'color' => '#8b5cf6', 'bg' => '#ede9fe'],
        'shipped'          => ['label' => 'Shipped',          'color' => '#06b6d4', 'bg' => '#cffafe'],
        'delivered'        => ['label' => 'Delivered',        'color' => '#10b981', 'bg' => '#d1fae5'],
        'completed'        => ['label' => 'Completed',        'color' => '#059669', 'bg' => '#dcfce7'],
        'cancelled'        => ['label' => 'Cancelled',        'color' => '#ef4444', 'bg' => '#fee2e2'],
        'return_requested' => ['label' => 'Return Requested', 'color' => '#f97316', 'bg' => '#ffedd5'],
        'return_approved'  => ['label' => 'Return Approved',  'color' => '#d97706', 'bg' => '#fef3c7'],
        'returned'         => ['label' => 'Returned',         'color' => '#6b7280', 'bg' => '#f3f4f6'],
        'refunded'         => ['label' => 'Refunded',         'color' => '#7c3aed', 'bg' => '#ede9fe'],
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? '#6b7280';
    }

    public function getStatusBgAttribute(): string
    {
        return self::STATUSES[$this->status]['bg'] ?? '#f3f4f6';
    }

    // ── Relationships ─────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    // ── Helpers ───────────────────────────────────────────────
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeReturned(): bool
    {
        return in_array($this->status, ['completed', 'delivered']);
    }

    public function isReturnPending(): bool
    {
        return in_array($this->status, ['return_requested', 'return_approved']);
    }

    public function needsPaymentVerification(): bool
    {
        return in_array($this->payment_method, ['bank', 'card']);
    }

    public function usesGateway(): bool
    {
        return filled($this->payment_gateway) || in_array($this->payment_method, ['payhere'], true);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function sendCustomerProgressEmail(string $stage, ?string $message = null, ?int $outboxId = null): void
    {
        if (!$this->customer_email) {
            return;
        }

        $outbox = $outboxId ? NotificationOutbox::find($outboxId) : null;

        try {
            Mail::to($this->customer_email)->send(new OrderStatusMail($this->loadMissing('items'), $stage, $message));

            if ($outbox) {
                $outbox->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'last_attempt_at' => now(),
                    'failed_at' => null,
                    'failure_message' => null,
                ]);
            }
        } catch (\Throwable $e) {
            if ($outbox) {
                $outbox->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'last_attempt_at' => now(),
                    'failure_message' => $e->getMessage(),
                ]);
            }

            Log::warning('Failed to send order progress email.', [
                'order_id' => $this->id,
                'stage' => $stage,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
