<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class Topup extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'topups';
    protected $primaryKey = '_id';

    protected $fillable = [
        '_id',
        'user_id',
        'amount',           // amount entered by user
        'detected_amount',  // amount detected by OCR
        'reference_number',
        'proof_image',      // path to the uploaded screenshot
        'status',           // pending | approved | rejected
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
