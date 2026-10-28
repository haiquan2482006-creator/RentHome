<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     *
     * @param mixed $ticket
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Delivery channels: database
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Dữ liệu lưu vào database notifications collection
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $post = $this->ticket->post ?? null;
        $postTitle = $post ? ($post->title ?? 'Bài đăng') : ($this->ticket->post_title ?? 'Bài đăng');
        $postId = $this->ticket->post_id ?? ($this->ticket->target_id ?? ($post ? ($post->_id ?? $post->id) : null));
        
        $actionUrl = !empty($postId) ? url('/chitietbaidang/' . $postId) : url('/thong-bao-ca-nhan');
        $code = $this->ticket->complaint_code ?? ('#KN-' . strtoupper(substr((string)($this->ticket->_id ?? $this->ticket->id ?? ''), -5)));

        return [
            'ticket_id'   => (string)($this->ticket->_id ?? $this->ticket->id ?? ''),
            'post_id'     => (string)$postId,
            'title'       => 'Thông báo xử lý khiếu nại',
            'message'     => "Cảm ơn bạn. Phản ánh của bạn về bài đăng [{$postTitle}] đã được Ban quản trị xác minh và xử lý hoàn tất.",
            'action_url'  => url('/thong-bao-ca-nhan'),
            'category'    => 'complaint_resolved',
            'type'        => 'success',
            'icon'        => 'fa-solid fa-circle-check',
        ];
    }
}
