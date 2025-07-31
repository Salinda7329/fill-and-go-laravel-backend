<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Account extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'accounts';
    protected $primaryKey = '_id';

    protected $fillable = [
        'user_id',
        'balance',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function getAccountNumberAttribute()
    {
        return $this->_id;
    }
}
