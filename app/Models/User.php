<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
        'email_verified_at' => 'timestamp',
    ];

    /* =======================
        ROLES
    ======================= */

    const ROLE_ADMIN  = 'admin';
    const ROLE_AGENT  = 'agent';
    const ROLE_CLIENT = 'client';

    public function getRoleTextAttribute()
    {
        return [
            'admin'  => 'Administrateur',
            'agent'  => 'Agent',
            'client' => 'Client',
        ][$this->role] ?? $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function canAccessDashboard(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_AGENT]);
    }

    /* =======================
        RELATIONS
    ======================= */

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
