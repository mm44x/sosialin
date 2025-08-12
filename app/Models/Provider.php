<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'type', 'base_url', 'api_key', 'markup_percent', 'active', 'meta'];
    protected $casts = ['markup_percent' => 'float', 'active' => 'boolean', 'meta' => 'array'];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
