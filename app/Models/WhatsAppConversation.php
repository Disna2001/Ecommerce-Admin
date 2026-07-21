<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone_number',
        'customer_name',
        'status',
        'bot_reply_count',
        'awaiting_handoff_confirmation',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'awaiting_handoff_confirmation' => 'boolean',
        'bot_reply_count' => 'integer',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id')->orderBy('id', 'asc');
    }

    public function scopeAwaitingHuman($query)
    {
        return $query->where('status', 'awaiting_human');
    }

    public function scopeBotActive($query)
    {
        return $query->where('status', 'bot_active');
    }

    public function markAsAwaitingHuman(): void
    {
        $this->update([
            'status' => 'awaiting_human',
            'awaiting_handoff_confirmation' => false,
        ]);
    }

    public function markAsHumanActive(): void
    {
        $this->update([
            'status' => 'human_active',
            'bot_reply_count' => 0,
            'awaiting_handoff_confirmation' => false,
        ]);
    }

    public function returnToBot(): void
    {
        $this->update([
            'status' => 'bot_active',
            'bot_reply_count' => 0,
            'awaiting_handoff_confirmation' => false,
        ]);
    }
}
