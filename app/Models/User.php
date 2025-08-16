<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];


    /**
     * Hidden attributes for arrays / JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /**
     * Relasi: satu user punya satu wallet.
     */
    public function wallet()
    {
        return $this->hasOne(\App\Models\Wallet::class);
    }

    /**
     * Relasi: satu user punya banyak order.
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * Relasi: satu user punya banyak transaction.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    /**
     * Relasi: satu user punya banyak ticket.
     */
    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }

    /**
     * Helper: cek apakah user admin.
     */
    public function isAdmin(): bool
    {
        return (string) $this->role === 'admin';
    }
}
