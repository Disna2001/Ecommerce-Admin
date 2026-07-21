<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'provider',
        'state',
        'phone_number',
        'connected_at',
        'last_seen_at',
    ];

    protected $casts = [
        'connected_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the singleton session row (creates one if it doesn't exist).
     */
    public static function singleton(): static
    {
        return static::firstOrCreate(
            [],
            ['provider' => 'baileys', 'state' => 'disconnected']
        );
    }

    public function isConnected(): bool
    {
        return $this->state === 'connected';
    }

    public function markConnected(string $phoneNumber): void
    {
        $this->update([
            'state'        => 'connected',
            'phone_number' => $phoneNumber,
            'connected_at' => now(),
            'last_seen_at' => now(),
        ]);
    }

    public function markDisconnected(): void
    {
        $this->update([
            'state'        => 'disconnected',
            'phone_number' => null,
            'last_seen_at' => now(),
        ]);
    }

    public function markConnecting(): void
    {
        $this->update([
            'state'        => 'connecting',
            'last_seen_at' => now(),
        ]);
    }
}
