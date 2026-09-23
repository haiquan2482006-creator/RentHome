<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';

    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'price_unit', 'area', 
        'province', 'district', 'ward', 'address', 'building_id',
        'property_type', 'account_role', 'amenities', 'images', 'status'
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Định nghĩa mối quan hệ: 1 Bài đăng thuộc về 1 Tòa nhà
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }
}
