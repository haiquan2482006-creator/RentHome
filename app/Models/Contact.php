<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Contact extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contacts';

    protected $fillable = [
        'post_id',
        'owner_id',
        'sender_id',
        'sender_name',
        'phone',
        'email',
        'appointment_date',
        'note',
        'message',
        'status',
        'contract_id',
        'is_hidden'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', '_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', '_id');
    }
}
