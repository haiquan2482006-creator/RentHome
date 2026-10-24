<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Complaint extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'complaints';

    protected $fillable = [
        'user_id',
        'complaint_code',
        'content',
        'target_id',
        'target_type',
        'post_id',
        'staff_id',
        'image',
        'status',
        'note',
        'resolution_note',
        'resolution_action',
        'resolved_by',
        'resolved_at',
        'is_valid',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, !empty($this->post_id) ? 'post_id' : 'target_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
