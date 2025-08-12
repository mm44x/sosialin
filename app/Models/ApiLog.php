<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'provider_id',
        'endpoint',
        'request',
        'status_code',
        'duration_ms',
        'response'
    ];
    protected $casts = [
        'request' => 'array',
        // biarkan response sebagai string (raw) agar mudah simpan apa adanya
    ];
}
