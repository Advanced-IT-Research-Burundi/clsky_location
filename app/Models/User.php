<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;
    use   Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'address',
        'role',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'email_verified_at' => 'timestamp',
    ];
    public function getRoleTextAttribute()
    {
        return [
            'admin' => 'Administrateur',
            'agent' => 'Agent',
            'client' => 'Client'
        ][$this->role] ?? $this->role;
    }

    // Méthodes
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function reservations(){
        $this->hasMany(Reservation::class);
    }

    public function payments(){
        $this->hasMany(Payment::class);
    }

    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

}
