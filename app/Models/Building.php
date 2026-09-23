<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; // Bắt buộc dùng thư viện này cho MongoDB

class Building extends Model
{
    // Chỉ định chính xác tên collection bạn vừa tạo trên MongoDB Atlas
    protected $collection = 'buildings';

    // Cho phép Laravel ghi dữ liệu hàng loạt vào các cột này
    protected $fillable = [
        'user_id', 
        'name', 
        'project', 
        'type', 
        'province', 
        'district', 
        'ward', 
        'address_detail', 
        'total_rooms', 
        'description', 
        'image', 
        'status'
    ];
}