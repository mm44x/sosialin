<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topup extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'method',
        'amount',
        'status',
        'proof_path',
        'note',
        'meta',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'meta'        => 'array',
        'reviewed_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
