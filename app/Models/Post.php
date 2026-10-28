<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';

    protected $fillable = [
        'user_id', 'parent_id', 'staff_id', 'author_id', 'title', 'description', 'price', 'price_unit', 'area', 
        'floor', 'floor_number', 'bedroom', 'bathroom',
        'province', 'district', 'ward', 'address', 'building_id', 'display_image',
        'property_type', 'account_role', 'amenities', 'images', 'status',
        'payment_requirement',
        'approved_at', 'approved_by', 'rejected_at', 'rejected_by', 'reject_reason', 'admin_directive',
        'views', 'view_count', 'favorites_count', 'likes',
        'is_featured', 'featured_at'
    ];

    protected $attributes = [
        'views' => 0,
        'favorites_count' => 0,
        'is_featured' => false,
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'views' => 'integer',
        'favorites_count' => 'integer',
        'is_featured' => 'boolean',
        'featured_at' => 'datetime',
    ];

    public function getIsFeaturedAttribute($value)
    {
        return (bool)($value ?? false);
    }

    public function getViewsAttribute($value)
    {
        return (int)($value ?? 0);
    }

    public function getFavoritesCountAttribute($value)
    {
        return max(0, (int)($value ?? ($this->attributes['likes'] ?? 0)));
    }

    public function getPropertyTypeAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        if ($this->building && !empty($this->building->type)) {
            return $this->building->type;
        }
        return 'nha_rieng';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Định nghĩa mối quan hệ: 1 Bài đăng thuộc về 1 Tòa nhà
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    // Accessor trả về URL ảnh hiển thị duy nhất
    protected $appends = ['display_image', 'all_images', 'favorites_count', 'formatted_price_unit'];

    public function getDisplayImageAttribute()
    {
        if (!empty($this->images) && is_array($this->images) && count($this->images) > 0) {
            $firstImage = trim((string) $this->images[0]);

            // 1. Nhận diện ID MongoDB: 24 ký tự hex -> Trả về URL route ảnh tĩnh
            if (strlen($firstImage) === 24 && ctype_xdigit($firstImage)) {
                return url('/images/' . $firstImage);
            }

            // 2. Nhận diện đường dẫn /images/ hoặc /image/
            if (\Illuminate\Support\Str::startsWith($firstImage, '/images/') || \Illuminate\Support\Str::startsWith($firstImage, 'images/')) {
                return url('/' . ltrim($firstImage, '/'));
            }
            if (\Illuminate\Support\Str::startsWith($firstImage, '/image/') || \Illuminate\Support\Str::startsWith($firstImage, 'image/')) {
                return url('/' . ltrim($firstImage, '/'));
            }

            // 3. Nhận diện URL web hoặc đã là chuỗi Base64
            if (\Illuminate\Support\Str::startsWith($firstImage, 'http') || \Illuminate\Support\Str::startsWith($firstImage, 'data:')) {
                return $firstImage;
            }

            // 4. Nếu đường dẫn đã chứa /storage/ hoặc storage/
            if (\Illuminate\Support\Str::startsWith($firstImage, '/storage/') || \Illuminate\Support\Str::startsWith($firstImage, 'storage/')) {
                return asset(ltrim($firstImage, '/'));
            }

            // 5. Nếu là đường dẫn bắt đầu bằng '/'
            if (\Illuminate\Support\Str::startsWith($firstImage, '/')) {
                return asset(ltrim($firstImage, '/'));
            }

            // 6. Tên file lưu trong storage
            return asset('storage/' . $firstImage);
        }

        // Ảnh mặc định nếu bài đăng hoàn toàn không có mảng images
        return 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'post_id', '_id');
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'post_id');
    }

    public function getTicketsAttribute()
    {
        $id = (string)($this->_id ?? $this->id);
        return SupportTicket::where(function($q) use ($id) {
            $q->where('post_id', $id)
              ->orWhere('target_id', $id);
        })->get();
    }

    public function getAllImagesAttribute()
    {
        $urls = [];
        if (!empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $item) {
                $imgStr = trim((string)$item);
                if (empty($imgStr)) continue;

                if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
                    $urls[] = url('/images/' . $imgStr);
                    continue;
                }
                if (\Illuminate\Support\Str::startsWith($imgStr, '/images/') || \Illuminate\Support\Str::startsWith($imgStr, 'images/')) {
                    $urls[] = url('/' . ltrim($imgStr, '/'));
                    continue;
                }
                if (\Illuminate\Support\Str::startsWith($imgStr, '/image/') || \Illuminate\Support\Str::startsWith($imgStr, 'image/')) {
                    $urls[] = url('/' . ltrim($imgStr, '/'));
                    continue;
                }
                if (\Illuminate\Support\Str::startsWith($imgStr, 'http') || \Illuminate\Support\Str::startsWith($imgStr, 'data:')) {
                    $urls[] = $imgStr;
                    continue;
                }
                if (\Illuminate\Support\Str::startsWith($imgStr, '/storage/') || \Illuminate\Support\Str::startsWith($imgStr, 'storage/')) {
                    $urls[] = asset(ltrim($imgStr, '/'));
                    continue;
                }
                if (\Illuminate\Support\Str::startsWith($imgStr, '/')) {
                    $urls[] = asset(ltrim($imgStr, '/'));
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

    public function getFormattedPriceUnitAttribute()
    {
        $u = strtolower(trim((string)($this->price_unit ?? 'thang')));
        $map = [
            'thang'      => '/ tháng',
            'tháng'      => '/ tháng',
            '2_nam'      => '/ 2 năm',
            '2 nam'      => '/ 2 năm',
            '3_nam'      => '/ 3 năm',
            '3 nam'      => '/ 3 năm',
            '5_nam'      => '/ 5 năm',
            '5 nam'      => '/ 5 năm',
            'tren_5_nam' => '/ trên 5 năm',
            'tren 5 nam' => '/ trên 5 năm',
            'nam'        => '/ năm',
            'năm'        => '/ năm',
            'm2'         => '/ m²',
            'tong'       => '',
        ];
        return $map[$u] ?? ($u ? '/' . $u : '/ tháng');
    }
}
