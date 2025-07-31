<?php

namespace App\Models;


use MongoDB\Laravel\Eloquent\Model;

class Vehicle extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'vehicles';
    protected $primaryKey = '_id';

    protected $fillable = [
        '_id',
        'vehicle_number',
        'fuel_type',
        'firebase_uid',
        'customeremail',
        'user_id',
        'status',
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
