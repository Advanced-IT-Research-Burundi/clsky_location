<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property_services extends Model
{
    protected $table = 'property_services';

    protected $fillable = [
        'property_id',
        'service_id'
    ];
}
