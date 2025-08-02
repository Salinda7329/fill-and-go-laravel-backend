<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $primaryKey = '_id';

    protected $fillable = [
        'email',
        'password',
        'role',
        'status',
        'firebase_uid',
        // + add for owners:
        'station_name',
        'station_address',
        'contact_number',
        'created_at',
        'updated_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];
}
