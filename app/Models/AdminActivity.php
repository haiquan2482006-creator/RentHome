<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminActivity extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'admin_activities';

    protected $fillable = [
        'admin_id',
        'admin_name',
        'admin_email',
        'action_type', // create, update, delete, lock, unlock, restore, approve, system, config, notify
        'category',    // account, building, post, contract, invoice, system, theme
        'title',       // Tiêu đề ngắn gọn hành động
        'description', // Mô tả chi tiết hành động
        'target_id',   // ID đối tượng bị tác động
        'target_name', // Tên đối tượng tác động
        'ip_address',  // Địa chỉ IP
        'user_agent',  // Thiết bị / Trình duyệt
        'status',      // success, warning, danger
        'metadata',    // Dữ liệu bổ sung
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ghi lại một hoạt động của Admin vào hệ thống
     */
    public static function log(string $actionType, string $category, string $title, ?string $description = null, $target = null, array $metadata = [])
    {
        try {
            $user = Auth::user();
            $adminId = $user ? (string)($user->_id ?? $user->id) : null;
            $adminName = $user ? ($user->account_name ?? $user->username ?? 'Quản trị viên') : 'Ban Quản Trị';
            $adminEmail = $user ? ($user->email ?? 'admin@renthome.vn') : 'admin@renthome.vn';

            $targetId = null;
            $targetName = null;
            if (is_object($target)) {
                $targetId = (string)($target->_id ?? $target->id ?? '');
                $targetName = $target->name ?? $target->title ?? $target->account_name ?? $target->username ?? $target->contract_code ?? $target->invoice_code ?? null;
            } elseif (is_string($target) || is_numeric($target)) {
                $targetId = (string)$target;
            }

            $request = request();
            $ip = $request ? $request->ip() : '127.0.0.1';
            $rawUa = $request ? ($request->userAgent() ?? '') : '';
            $ua = 'Chrome trên Windows';
            if (str_contains($rawUa, 'Firefox')) $ua = 'Firefox trên Windows';
            elseif (str_contains($rawUa, 'CocCoc')) $ua = 'Cốc Cốc trên Windows';
            elseif (str_contains($rawUa, 'Edg')) $ua = 'Edge trên Windows';
            elseif (str_contains($rawUa, 'Safari') && !str_contains($rawUa, 'Chrome')) $ua = 'Safari trên macOS';

            return self::create([
                'admin_id'    => $adminId,
                'admin_name'  => $adminName,
                'admin_email' => $adminEmail,
                'action_type' => $actionType,
                'category'    => $category,
                'title'       => $title,
                'description' => $description,
                'target_id'   => $targetId,
                'target_name' => $targetName,
                'ip_address'  => $ip,
                'user_agent'  => $ua,
                'status'      => in_array($actionType, ['delete', 'force_delete']) ? 'danger' : (in_array($actionType, ['lock', 'warning']) ? 'warning' : 'success'),
                'metadata'    => $metadata,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Không thể ghi log AdminActivity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách hoạt động thực tế của Admin từ database
     */
    public static function getCompiledActivities()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
}
