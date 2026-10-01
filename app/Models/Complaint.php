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
        'post_id', 
        'content', 
        'image', 
        'status', 
        'admin_response',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', '_id')->with('imageModels');
    }

    // Accessor trả về URL ảnh hiển thị (Base64 hoặc URL cũ)
    protected $appends = ['display_image'];

    public function getDisplayImageAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        // Kiểm tra nếu image là 1 ObjectId hợp lệ của MongoDB (24 ký tự hex) -> Trả về URL route ảnh
        if (preg_match('/^[a-f\d]{24}$/i', $this->image)) {
            return url('/image/' . $this->image);
        }

        // Nếu không phải ID (tức là path cũ)
        if (\Illuminate\Support\Str::startsWith($this->image, 'http') || \Illuminate\Support\Str::startsWith($this->image, 'data:')) {
            return $this->image;
        }
        return asset('storage/' . str_replace('/storage/', '', $this->image));
    }
}
