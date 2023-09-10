<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'name', 'city', 'state', 'location', 'coordinates', 'default', 'phone', 'placemark', 'geolocation'];

    protected $casts = [
        'coordinates' => 'array'
    ];

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

}
