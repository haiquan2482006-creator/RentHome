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
            if (strlen($this->avatar) === 24 && ctype_xdigit($this->avatar)) {
                return url('/image/' . $this->avatar);
            }
            if (filter_var($this->avatar, FILTER_VALIDATE_URL) || str_starts_with($this->avatar, 'data:image')) {
                return $this->avatar;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
                return asset('storage/' . $this->avatar);
            }
        }
        $name = $this->account_name ?? $this->username ?? 'User';
        $initial = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 150"><rect width="150" height="150" fill="#16a34a"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="75" font-weight="bold">'.$initial.'</text></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function getDocumentUrl($fieldValue)
    {
        if (empty($fieldValue)) return '';
        if (strlen($fieldValue) === 24 && ctype_xdigit($fieldValue)) {
            return url('/image/' . $fieldValue);
        }
        if (filter_var($fieldValue, FILTER_VALIDATE_URL) || str_starts_with($fieldValue, 'data:image')) {
            return $fieldValue;
        }
        return asset('storage/' . $fieldValue);
    }

    public function getIdFrontUrlAttribute() { return $this->getDocumentUrl($this->id_front); }
    public function getIdBackUrlAttribute() { return $this->getDocumentUrl($this->id_back); }
    public function getBusinessLicenseUrlAttribute() { return $this->getDocumentUrl($this->business_license); }
}

