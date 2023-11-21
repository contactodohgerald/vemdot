<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InactiveUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id', 
        'name', 
        'email',
        'status',
        'country',
        'phone',
        'gender',
        'wallet_balance',
        'address',
        'reason',
    ];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
        'status' => 'active'
    ];
}
