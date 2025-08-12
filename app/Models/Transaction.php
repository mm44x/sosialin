<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'meta'];
    protected $casts = ['amount' => 'decimal:2', 'meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
