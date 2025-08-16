<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    public const STATUS_OPEN   = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id', 'subject', 'order_id', 'status', 'last_message_at', 'meta',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'meta'            => 'array',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function messages(): HasMany {
        return $this->hasMany(TicketMessage::class)->orderBy('id');
    }

    public function scopeMine($q, int $userId) {
        return $q->where('user_id', $userId);
    }

    public function isOpen(): bool {
        return $this->status === self::STATUS_OPEN;
    }
}
