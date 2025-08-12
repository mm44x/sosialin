<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_PARTIAL    = 'partial';
    public const STATUS_CANCELED   = 'canceled';
    public const STATUS_ERROR      = 'error';

    protected $fillable = [
        'user_id',
        'service_id',
        'link',
        'quantity',
        'status',
        'provider_order_id',
        'cost',
        'meta'
    ];
    protected $casts = ['cost' => 'decimal:2', 'meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
