<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'type', 'base_url', 'api_key', 'markup_percent', 'active'];
    protected $casts = [
        'active' => 'boolean',
        'markup_percent' => 'decimal:2',
    ];
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
