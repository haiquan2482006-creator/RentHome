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
        'parent_id',
        'name', 
        'project', 
        'type', 
        'business_type',
        'property_group',
        'province', 
        'district', 
        'ward', 
        'address_detail', 
        'total_floors',
        'total_rooms', 
        'description', 
        'image',
        'images',
        'electricity_price',
        'water_price',
        'water_calc_type',
        'service_fee',
        'internet_fee',
        'parking_fee',
        'amenities',
        'manager_id',
        'manager_ids',
        'user_ids',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
        'manager_ids' => 'array',
        'user_ids' => 'array',
    ];

    /**
     * Quan hệ Nhiều - Nhiều (Many-to-Many) với User (Nhân viên quản lý phụ trách)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, null, 'building_ids', 'user_ids');
    }

    // Mối quan hệ tới tài khoản nhân viên phụ trách quản lý tòa nhà (Tương thích bản ghi cũ)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', '_id');
    }

    // Mối quan hệ tới bảng Image
    public function imageModel()
    {
        return $this->belongsTo(Image::class, 'image');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'building_id', '_id');
    }

    // Địa chỉ đầy đủ
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_detail,
            $this->ward,
            $this->district,
            $this->province
        ]);
        return count($parts) > 0 ? implode(', ', $parts) : 'Chưa cập nhật địa chỉ';
    }

    // Yêu cầu Laravel luôn đính kèm thuộc tính 'display_image' và 'all_images' khi trả về JSON
    protected $appends = ['display_image', 'all_images'];

    // Hàm tạo ra thuộc tính ảo 'display_image'
    public function getDisplayImageAttribute()
    {
        $imgValue = $this->image; 
        if (empty($imgValue) && !empty($this->images) && is_array($this->images)) {
            $imgValue = $this->images[0] ?? null;
        }

        if (empty($imgValue)) {
            return 'https://placehold.co/600x400?text=Building'; // Ảnh mặc định cho tòa nhà
        }

        // 1. Nhận diện ID MongoDB: 24 ký tự hex -> Trả về URL route ảnh tĩnh
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

    // Danh sách tất cả các ảnh của tòa nhà kèm ID và URL
    public function getAllImagesAttribute()
    {
        $urls = [];
        $list = $this->images;
        if (empty($list) || !is_array($list)) {
            if (!empty($this->image)) {
                $list = [$this->image];
            } else {
                return [];
            }
        }

        foreach ($list as $item) {
            $imgStr = (string)$item;
            if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
                $urls[] = [
                    'id' => $imgStr,
                    'url' => url('/image/' . $imgStr)
                ];
                continue;
            }
            if (\Illuminate\Support\Str::startsWith($imgStr, 'http') || \Illuminate\Support\Str::startsWith($imgStr, 'data:')) {
                $urls[] = [
                    'id' => $imgStr,
                    'url' => $imgStr
                ];
                continue;
            }
            $urls[] = [
                'id' => $imgStr,
                'url' => asset('storage/' . $imgStr)
            ];
        }
        return $urls;
    }
}