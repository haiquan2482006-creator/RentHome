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
        'account_name',
        'company_name',
        'representative',
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
        'address',
        'business_license_date',
        'representative_id_card',
        'representative_id_date',
        'representative_id_place',
        'website',
        'hotline',
        'company_description',

        // Tài khoản con & phân quyền tòa nhà & quyền hạn chi tiết
        'parent_id',
        'assigned_buildings',
        'building_ids',
        'staff_role',
        'permissions',
        'plain_password',
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

    public function isAdmin(): bool
    {
        return ($this->role === 'admin');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin() || $this->isEnterprise()) {
            return true;
        }
        $perms = (array) ($this->permissions ?? []);
        if (empty($perms)) {
            return true; // Mặc định không giới hạn nếu chưa cấu hình
        }
        return in_array($permission, $perms, true) || in_array('*', $perms, true) || in_array('all', $perms, true);
    }

    public function canManagePosts(): bool
    {
        return $this->hasPermission('manage_posts');
    }

    public function canManageContacts(): bool
    {
        return $this->hasPermission('manage_contacts');
    }

    public function canManageContracts(): bool
    {
        return $this->hasPermission('manage_contracts');
    }

    public function canManageInvoices(): bool
    {
        return $this->hasPermission('manage_invoices');
    }

    public function isModerator(): bool
    {
        return false;
    }

    public function isEnterprise(): bool
    {
        return ($this->role === 'doanhnghiep' || $this->role === 'enterprise');
    }

    public function isSubAccount(): bool
    {
        return ($this->role === 'sub_account' || $this->role === 'staff' || !empty($this->parent_id));
    }

    public function isStaff(): bool
    {
        return $this->isSubAccount();
    }

    public function isIndividual(): bool
    {
        return ($this->role === 'user' || $this->role === 'canhan' || (!$this->isAdmin() && !$this->isEnterprise() && !$this->isSubAccount()));
    }

    public function canManageBuilding($buildingId = null): bool
    {
        if ($this->isAdmin() || $this->isEnterprise()) {
            return true;
        }
        if ($this->isSubAccount()) {
            $assigned = (array) ($this->assigned_buildings ?? []);
            // Nếu chưa chỉ định danh sách hoặc chứa 'all' / '*' thì nhân viên được quản lý tất cả tòa nhà
            if (empty($assigned) || in_array('all', $assigned, true) || in_array('*', $assigned, true)) {
                return true;
            }
            if ($buildingId === null || $buildingId === 'all') {
                return true;
            }
            return in_array((string)$buildingId, array_map('strval', $assigned), true);
        }
        return false;
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function subAccounts()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Quan hệ Nhiều - Nhiều (Many-to-Many) với Building
     */
    public function buildings()
    {
        return $this->belongsToMany(Building::class, null, 'user_ids', 'building_ids');
    }

    public static function getEnterpriseUser()
    {
        return self::where('role', 'admin')
            ->orWhere('role', 'doanhnghiep')
            ->first();
    }

    public function getDisplayNameAttribute()
    {
        if ($this->isAdmin()) {
            return $this->account_name ?: ($this->username ?: 'Ban Quản Trị Admin');
        }
        if ($this->isEnterprise()) {
            return $this->company_name ?: ($this->account_name ?: 'RentHome');
        }
        if ($this->isSubAccount()) {
            return ($this->account_name ?: $this->username ?: 'Nhân viên') . ' (RentHome)';
        }
        return $this->account_name ?: ($this->username ?: 'Người dùng');
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
        $name = $this->display_name;
        $initial = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
        $color = $this->isEnterprise() ? '#7c3aed' : ($this->isSubAccount() ? '#2563eb' : '#16a34a');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 150"><rect width="150" height="150" fill="'.$color.'"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="75" font-weight="bold">'.$initial.'</text></svg>';
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

    /**
     * Get the entity's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id')->latest();
    }

    /**
     * Get the entity's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id')
                    ->where('is_read', false)
                    ->latest();
    }
}

