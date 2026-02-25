<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'user_id',
        'check_in',
        'check_out',
        'total_price',
        'guests',
        'status',
        'payment_status',
        'notes',
        'total_paid',
        'motif_annulation',
        'date_annulation'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_price' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'date_annulation' => 'datetime'
    ];

    protected static function booted()
    {
        static::deleting(function ($reservation) {
            $reservation->payments()->each(function ($payment) {
                $payment->delete();
            });
        });
    }


    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info'
        ][$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée'
        ][$this->status] ?? $this->status;
    }

    public function getPaymentStatusTextAttribute()
    {
        return [
            'pending' => 'En attente',
            'paid' => 'Payé',
            'refunded' => 'Remboursé'
        ][$this->payment_status] ?? $this->payment_status;
    }



    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>', now());
    }


    public function isCancellable(): bool
    {
        return $this->status !== 'cancelled'
            && $this->check_in->isFuture()
            && now()->diffInHours($this->check_in) >= 48;
    }

    public function isOngoing(): bool
    {
        return now()->between($this->check_in, $this->check_out);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total_price - $this->total_paid);
    }

    public function getNightsCountAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    public function isFullyPaid(): bool
    {
        return $this->total_paid >= $this->total_price;
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}