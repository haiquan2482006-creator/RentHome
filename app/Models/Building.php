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
    ];

    // Mối quan hệ tới bảng Image
    public function imageModel()
    {
        return $this->belongsTo(Image::class, 'image');
    }

    // BỔ SUNG TỪ ĐÂY XUỐNG DƯỚI:

    // Yêu cầu Laravel luôn đính kèm thuộc tính 'display_image' khi trả về JSON
    protected $appends = ['display_image'];

    // Hàm tạo ra thuộc tính ảo 'display_image' bọc thép chống lỗi 403
    public function getDisplayImageAttribute()
    {
        $imgValue = $this->image; 

        if (empty($imgValue)) {
            return 'https://placehold.co/600x400?text=Building'; // Ảnh mặc định cho tòa nhà
        }

        // 1. Nhận diện ID MongoDB: 24 ký tự hex -> Trả về URL route ảnh tĩnh siêu tốc, có cache
        if (is_string($imgValue) && strlen($imgValue) === 24 && ctype_xdigit($imgValue)) {
            return url('/image/' . $imgValue);
        }

        // 2. Nhận diện URL web hoặc đã là chuỗi Base64
        if (\Illuminate\Support\Str::startsWith($imgValue, 'http') || \Illuminate\Support\Str::startsWith($imgValue, 'data:')) {
            return $imgValue;
        }

        // 3. Nhận diện tên file cũ trong ổ cứng
        return asset('storage/' . $imgValue);
    }
}