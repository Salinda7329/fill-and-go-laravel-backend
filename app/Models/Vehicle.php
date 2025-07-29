<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;


class Vehicle extends Model implements AuthenticatableContract
{
     use Authenticatable;

    protected $connection = 'mongodb';
    protected $collection = 'vehicles';

    protected $fillable = [
        'first_name',
        'status',
    ];

}
