<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDetails extends Model
{
    protected $fillable = [
        'property_id',
        'title',
        'value',
        'description',
        'user_id',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
