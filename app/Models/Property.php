<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'address',
        'city',
        'country',
        'postal_code',
        'price',
        'bedrooms',
        'bathrooms',
        'area',
        'floor',
        'type',
        'status',
        'furnished',
        'featured',
        'user_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'furnished' => 'boolean',
        'featured' => 'boolean'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    // Accesseurs
    public function getStatusColorAttribute()
    {
        return [
            'available' => 'success',
            'rented' => 'warning',
            'maintenance' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        return [
            'available' => 'Disponible',
            'rented' => 'Loué',
            'maintenance' => 'En maintenance'
        ][$this->status] ?? $this->status;
    }

    public function getTypeTextAttribute()
    {
        return [
            'apartment' => 'Appartement',
            'studio' => 'Studio',
            'duplex' => 'Duplex'
        ][$this->type] ?? $this->type;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Méthodes
    public function isAvailable($checkIn, $checkOut, $excludeReservationId = null)
    {
        $query = $this->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                          ->where('check_out', '>=', $checkOut);
                    });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->count() === 0;
    }

    public function amenities(){
      return $this->belongsToMany(Amenity::class,'property_amenities');
    }

    public function services(){
       return $this->belongsToMany(Service::class,'property_services');
    }
}
