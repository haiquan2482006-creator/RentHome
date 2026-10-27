<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TicketReply extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ticket_replies';

    protected $fillable = [
        'ticket_id',
        'ticket_type',      // 'complaint' hoặc 'contact'
        'sender_id',        // ID người gửi
        'sender_name',      // Tên hiển thị người gửi
        'sender_avatar',    // Avatar nếu có
        'is_cskh',          // true nếu là nhân viên CSKH, false nếu là khách hàng
        'message',          // Nội dung phản hồi / tin nhắn
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_cskh'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với người gửi (User)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Quan hệ với Khiếu nại (nếu type là complaint)
     */
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'ticket_id');
    }

    /**
     * Quan hệ với Liên hệ (nếu type là contact)
     */
    public function contactMessage()
    {
        return $this->belongsTo(ContactMessage::class, 'ticket_id');
    }
}
