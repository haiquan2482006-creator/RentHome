<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Contact extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contacts';

    protected $fillable = [
        'post_id',
        'sender_name',
        'phone',
        'email',
        'note',
        'message',
        'status', // pending, communicating, approved, closed
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
