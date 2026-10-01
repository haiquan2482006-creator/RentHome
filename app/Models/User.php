<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'account_type',
        'account_name',
        'company_name',
        'company_address',
        'tax_code',
        'company_email',
        'business_license',
        'role',
        'status',
        'is_deleted',
        'is_locked',
        'lock_reason',
        'locked_at',
        'deleted_at',
        'deleted_by',
        
        // Bổ sung các trường ở trang Profile
        'avatar',
        'id_front',
        'id_back',
        'bank_name',
        'bank_account',
        'bank_owner',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Lấy ảnh đại diện hiển thị đồng bộ.
     * Trả về ảnh người dùng tải lên, hoặc ảnh mặc định chữ cái đầu từ ui-avatars.
     */
    public function getDisplayAvatarAttribute()
    {
        if (!empty($this->avatar)) {
            // Nếu đã là URL hợp lệ hoặc chuỗi Base64
            if (filter_var($this->avatar, FILTER_VALIDATE_URL) || str_starts_with($this->avatar, 'data:image')) {
                return $this->avatar;
            }
            // Mặc định lưu trong storage
            return asset('storage/' . $this->avatar);
        }

        // Ưu tiên lấy account_name, sau đó là username
        $name = $this->account_name ?? $this->username ?? 'User';
        
        // Trả về ảnh mặc định với chữ cái đầu, tone nền màu xanh thương hiệu (#16a34a)
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=16a34a&color=ffffff&bold=true&rounded=true&size=150';
    }
}

