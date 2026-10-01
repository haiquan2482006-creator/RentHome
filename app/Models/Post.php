<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';

    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'price_unit', 'area', 
        'province', 'district', 'ward', 'address', 'building_id','display_image',
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

    // Mối quan hệ tới bảng Image (Sử dụng array of IDs trong MongoDB)
    public function imageModels()
    {
        // Trong Laravel MongoDB, khi một model lưu mảng các ID của model khác, ta có thể dùng belongsToMany
        // Cột lưu trữ mảng ID trong bảng posts là 'images'
        return $this->belongsToMany(Image::class, null, 'post_ids', 'images');
    }

    // Accessor trả về URL ảnh hiển thị duy nhất (Base64 hoặc URL cũ)
    protected $appends = ['display_image', 'all_images'];

   public function getDisplayImageAttribute()
    {
        if (!empty($this->images) && is_array($this->images) && count($this->images) > 0) {
            $firstImage = (string) $this->images[0];

            // 1. Nhận diện ID MongoDB: 24 ký tự hex -> Trả về URL route ảnh tĩnh siêu tốc, có cache
            if (strlen($firstImage) === 24 && ctype_xdigit($firstImage)) {
                return url('/image/' . $firstImage);
            }

            // 2. Nhận diện URL web hoặc đã là chuỗi Base64
            if (\Illuminate\Support\Str::startsWith($firstImage, 'http') || \Illuminate\Support\Str::startsWith($firstImage, 'data:')) {
                return $firstImage;
            }

            // 3. Nhận diện tên file cũ trong ổ cứng
            return asset('storage/' . $firstImage);
        }

        // 4. Ảnh mặc định nếu bài đăng hoàn toàn không có mảng images
        return 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function getAllImagesAttribute()
    {
        $urls = [];
        if (!empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $item) {
                $imgStr = (string)$item;
                if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
                    $urls[] = url('/image/' . $imgStr);
                    continue;
                }
                if (\Illuminate\Support\Str::startsWith($imgStr, 'http') || \Illuminate\Support\Str::startsWith($imgStr, 'data:')) {
                    $urls[] = $imgStr;
                    continue;
                }
                $urls[] = asset('storage/' . $imgStr);
            }
        }
        if (empty($urls)) {
            $urls[] = $this->display_image;
        }
        return $urls;
    }
}
