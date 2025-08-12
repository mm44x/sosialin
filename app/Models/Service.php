<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'provider_id',
        'external_service_id',
        'name',
        'rate',
        'min',
        'max',
        'description',
        'active',
        'markup_percent_override',
        'meta',
    ];
    protected $casts = [
        'active' => 'boolean',
        'meta'   => 'array',
        'rate'   => 'decimal:4',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
