<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SupportTicket extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'support_tickets';

    protected $fillable = [
        'user_id',
        'type',                 // 'lien_he', 'khieu_nai', etc.
        'ticket_code',
        'complaint_code',
        'sender_name',
        'sender_phone',
        'sender_email',
        'subject',
        'message',
        'content',
        'reason',
        'target_id',
        'target_type',
        'post_id',
        'staff_id',
        'image',
        'images',
        'status',               // 'pending', 'processing', 'resolved'
        'note',
        'admin_note',
        'reply_message',
        'resolution_note',
        'resolution_action',
        'resolved_by',
        'resolved_at',
        'is_escalated',
        'escalated_at',
        'escalated_by',
        'escalation_reason',
        'cskh_note',
        'is_post_hidden',
        'is_staff_locked',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status'       => 'pending',
        'type'         => 'lien_he',
        'is_escalated' => false,
    ];

    protected $casts = [
        'images'          => 'array',
        'is_valid'        => 'boolean',
        'is_escalated'    => 'boolean',
        'is_post_hidden'  => 'boolean',
        'is_staff_locked' => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'resolved_at'     => 'datetime',
        'escalated_at'    => 'datetime',
    ];

    /**
     * Accessor đồng bộ trường name / sender_name
     */
    public function getNameAttribute()
    {
        return $this->attributes['sender_name'] ?? ($this->attributes['name'] ?? null);
    }

    /**
     * Accessor đồng bộ trường phone / sender_phone
     */
    public function getPhoneAttribute()
    {
        return $this->attributes['sender_phone'] ?? ($this->attributes['phone'] ?? null);
    }

    /**
     * Accessor đồng bộ trường email / sender_email
     */
    public function getEmailAttribute()
    {
        return $this->attributes['sender_email'] ?? ($this->attributes['email'] ?? null);
    }

    /**
     * Tự động sinh mã ticket định dạng chuẩn #TK-XXXXX
     */
    public function getFormattedTicketCodeAttribute(): string
    {
        if (!empty($this->ticket_code)) {
            return str_starts_with($this->ticket_code, '#') ? $this->ticket_code : ('#' . $this->ticket_code);
        }
        if (!empty($this->complaint_code)) {
            return str_starts_with($this->complaint_code, '#') ? $this->complaint_code : ('#' . $this->complaint_code);
        }
        $idStr = (string)($this->_id ?? $this->id);
        return '#TK-' . strtoupper(substr($idStr, -5));
    }

    /**
     * Tên nhãn trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'escalated'  => 'Đã chuyển Admin',
            'resolved'   => 'Đã giải quyết',
            default      => 'Chờ xử lý',
        };
    }

    /**
     * Accessor đồng bộ nội dung khiếu nại (content / message)
     */
    public function getContentAttribute()
    {
        return $this->attributes['content'] ?? ($this->attributes['message'] ?? null);
    }

    /**
     * Accessor lý do phản ánh
     */
    public function getReasonAttribute()
    {
        return $this->attributes['reason'] ?? ($this->attributes['subject'] ?? 'Phản ánh bài đăng');
    }

    /**
     * Accessor mã khiếu nại định dạng #KN-XXXXX
     */
    public function getComplaintCodeAttribute(): string
    {
        if (!empty($this->attributes['complaint_code'])) {
            return str_starts_with($this->attributes['complaint_code'], '#') ? $this->attributes['complaint_code'] : ('#' . $this->attributes['complaint_code']);
        }
        if (!empty($this->attributes['ticket_code'])) {
            return str_starts_with($this->attributes['ticket_code'], '#') ? $this->attributes['ticket_code'] : ('#' . $this->attributes['ticket_code']);
        }
        $idStr = (string)($this->_id ?? $this->id);
        return '#KN-' . strtoupper(substr($idStr, -5));
    }

    /**
     * Liên kết belongsTo tới User (Người gửi khiếu nại)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias người gửi
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Bài đăng bị khiếu nại
     */
    public function post()
    {
        return $this->belongsTo(Post::class, !empty($this->post_id) ? 'post_id' : 'target_id');
    }

    /**
     * Nhân viên phụ trách bài đăng
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Người xử lý ticket
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Lịch sử phản hồi / chat
     */
    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }
}
