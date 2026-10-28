<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';

    protected $fillable = [
        'id',
        'data',
        'read_at',
        'user_id',
        'role',
        'target_id',
        'post_id',
        'building_id',
        'action_url',
        'title',
        'message',
        'type', // success, error, warning, info, building
        'is_read',
        'category',
        'changes'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'title',
        'message',
        'action_url',
        'type',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!empty($model->data) && is_array($model->data)) {
                if (empty($model->title) && isset($model->data['title'])) {
                    $model->title = $model->data['title'];
                }
                if (empty($model->message) && isset($model->data['message'])) {
                    $model->message = $model->data['message'];
                }
                if (empty($model->action_url) && isset($model->data['action_url'])) {
                    $model->action_url = $model->data['action_url'];
                }
                if (empty($model->post_id) && isset($model->data['post_id'])) {
                    $model->post_id = $model->data['post_id'];
                }
                if (empty($model->category) && isset($model->data['category'])) {
                    $model->category = $model->data['category'];
                }
                if (empty($model->type) && isset($model->data['type'])) {
                    $model->type = $model->data['type'];
                }
            }
            if ($model->is_read === null) {
                $model->is_read = !empty($model->read_at);
            }
        });
    }

    public function getTitleAttribute($value = null)
    {
        return $value ?: ($this->attributes['title'] ?? ($this->data['title'] ?? 'Thông báo hệ thống'));
    }

    public function getMessageAttribute($value = null)
    {
        return $value ?: ($this->attributes['message'] ?? ($this->data['message'] ?? ''));
    }

    public function getActionUrlAttribute($value = null)
    {
        return $value ?: ($this->attributes['action_url'] ?? ($this->data['action_url'] ?? url('/thong-bao-ca-nhan')));
    }

    public function getTypeAttribute($value = null)
    {
        $raw = $value ?: ($this->attributes['type'] ?? null);
        if (!empty($raw) && !str_contains((string)$raw, '\\')) {
            return $raw;
        }
        return $this->data['type'] ?? ($this->data['category'] ?? ($this->attributes['category'] ?? 'info'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    /**
     * Gửi thông báo khi Admin phân công 1 tòa nhà (vỏ) cho tài khoản nhân sự đang hoạt động
     *
     * @param User|string $staff Đối tượng User hoặc ID nhân sự
     * @param Building $building Tòa nhà được phân công
     * @return Notification|null
     */
    public static function sendBuildingAssignedNotification($staff, $building)
    {
        if (!$staff || !$building) {
            return null;
        }

        if (is_string($staff) || is_numeric($staff)) {
            $staff = User::find($staff);
        }

        if (!$staff) {
            return null;
        }

        // Kiểm tra nhân sự có đang hoạt động hay không (chưa bị khóa, chưa bị xóa)
        $isLocked = !empty($staff->is_locked) || in_array(strtolower((string)($staff->status ?? '')), ['locked', 'inactive', 'khoa', 'khoá', 'bị khóa', 'bi_khoa']);
        $isDeleted = !empty($staff->is_deleted);
        if ($isLocked || $isDeleted) {
            return null;
        }

        // Không gửi cho kiểm duyệt viên hoặc CSKH (chỉ áp dụng cho nhân sự quản lý tòa nhà)
        $staffRole = strtolower($staff->role ?? '');
        if (in_array($staffRole, ['kiemduyet', 'moderator', 'kiem_duyet', 'cskh', 'cham_soc_kh', 'chamsockh'])) {
            return null;
        }

        $bId = (string)($building->_id ?? $building->id);
        $staffId = (string)($staff->_id ?? $staff->id);
        $buildingName = $building->name ?? 'Tòa nhà / BĐS';

        // Chống gửi trùng lặp thông báo phân công cùng 1 tòa nhà trong vòng 2 phút
        $existing = self::where('user_id', $staffId)
            ->where('building_id', $bId)
            ->where('category', 'building_assigned')
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($existing) {
            return $existing;
        }

        $addressParts = array_filter([$building->address_detail, $building->ward, $building->district, $building->province]);
        $addressDetail = trim(implode(', ', $addressParts));
        $addrText = $addressDetail ? " ({$addressDetail})" : "";

        $title = "Bạn vừa được phân công quản lý tòa nhà: {$buildingName}";
        $message = "Ban Quản Trị đã phân công cho bạn quản lý cơ sở / tòa nhà: {$buildingName}{$addrText}. Bạn có thể tiếp nhận và bắt đầu đăng bài, quản lý các căn phòng của tòa nhà ngay bây giờ.";
        $actionUrl = url('/quan-ly-toa-nha/' . $bId . '?ref=buildings');

        return self::create([
            'user_id'         => $staffId,
            'notifiable_id'   => $staffId,
            'notifiable_type' => User::class,
            'building_id'     => $bId,
            'target_id'       => $bId,
            'title'           => $title,
            'message'         => $message,
            'action_url'      => $actionUrl,
            'type'            => 'building',
            'category'        => 'building_assigned',
            'is_read'         => false,
            'read_at'         => null,
            'data'            => [
                'title'       => $title,
                'message'     => $message,
                'action_url'  => $actionUrl,
                'building_id' => $bId,
                'category'    => 'building_assigned',
                'type'        => 'building',
            ]
        ]);
    }
}
