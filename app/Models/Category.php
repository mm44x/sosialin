<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'provider_id', 'active'];
    protected $casts = ['active' => 'boolean'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
