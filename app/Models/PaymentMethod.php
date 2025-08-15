<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'type',
        'name',
        'bank_name',
        'account_name',
        'account_number',
        'instructions',
        'media_path',
        'is_active',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'meta' => 'array',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->type === 'bank') {
            $parts = array_filter([
                $this->bank_name,
                $this->account_number ? '• ' . $this->account_number : null,
                $this->account_name ? 'a.n ' . $this->account_name : null,
            ]);
            $fallback = trim($this->name ?: 'Bank Transfer');
            return count($parts) ? implode(' ', $parts) : $fallback;
        }
        return $this->name ?: ucfirst($this->type);
    }
}
