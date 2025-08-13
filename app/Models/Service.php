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
        'public_active',
        'public_name',
        'public_description',
        'markup_percent_override',
    ];
    protected $casts = [
        'active' => 'boolean',
        'meta'   => 'array',
        'rate'   => 'decimal:4',
        'public_active' => 'boolean',
        'markup_percent_override' => 'decimal:2',
    ];

    // Nama & deskripsi yang aman untuk publik
    public function getDisplayNameAttribute(): string
    {
        return $this->public_name ?: $this->name;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        return $this->public_description ?: $this->description;
    }

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
