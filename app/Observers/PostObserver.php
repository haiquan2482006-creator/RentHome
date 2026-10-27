<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    /**
     * Lắng nghe sự kiện trước khi lưu Post (saving)
     * Bất cứ khi nào bài đăng chuyển sang trạng thái đã thuê (rented),
     * tự động ngầm gỡ bỏ cờ is_featured = false để loại khỏi Slider Trang chủ.
     */
    public function saving(Post $post): void
    {
        $this->handleAutoUnfeatureIfRented($post);
    }

    /**
     * Lắng nghe sự kiện trước khi cập nhật Post (updating)
     */
    public function updating(Post $post): void
    {
        $this->handleAutoUnfeatureIfRented($post);
    }

    /**
     * Logic tự động ngầm gỡ bỏ cờ is_featured khi phòng không còn trống
     */
    private function handleAutoUnfeatureIfRented(Post $post): void
    {
        $rawStatus = strtolower(trim((string)($post->status ?? '')));
        $isRentedStatus = in_array($rawStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue']);
        $isRentedFlag = ($post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1');

        // Khi phòng được đánh dấu đã thuê
        if ($isRentedStatus || $isRentedFlag) {
            if ($post->is_featured) {
                $post->is_featured = false;
                $post->featured_at = null;
                Log::info("PostObserver: Tự động gỡ cờ is_featured bài đăng ID [{$post->_id}] ('{$post->title}') vì trạng thái phòng đã chuyển sang đã thuê (rented).");
            }
        }
    }
}
