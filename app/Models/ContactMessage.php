<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ContactMessage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contact_messages';

    protected $fillable = [
        'ticket_code',
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'status',          // 'pending' (Chờ xử lý), 'processing' (Đang xử lý), 'resolved' (Đã giải quyết)
        'user_id',
        'admin_note',
        'responded_at',
        'responded_by',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Tự động sinh mã ticket nếu chưa có (Ví dụ: #TK-001)
     */
    public function getFormattedTicketCodeAttribute(): string
    {
        if (!empty($this->ticket_code)) {
            return str_starts_with($this->ticket_code, '#') ? $this->ticket_code : ('#' . $this->ticket_code);
        }
        $idStr = (string)($this->_id ?? $this->id);
        return '#TK-' . strtoupper(substr($idStr, -4));
    }

    /**
     * Tên nhãn trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'resolved'   => 'Đã giải quyết',
            default      => 'Chờ xử lý',
        };
    }

    /**
     * Màu sắc badge Tailwind theo trạng thái
     */
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-amber-50 text-amber-700 border-amber-300 focus:ring-amber-400',
            'processing' => 'bg-sky-50 text-sky-700 border-sky-300 focus:ring-sky-400',
            'resolved'   => 'bg-emerald-50 text-emerald-700 border-emerald-300 focus:ring-emerald-400',
            default      => 'bg-slate-50 text-slate-700 border-slate-300 focus:ring-slate-400',
        };
    }

    /**
     * Quan hệ với User nếu người gửi đã đăng nhập
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }
}
