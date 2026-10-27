<?php

namespace App\Observers;

use App\Models\Contract;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class ContractObserver
{
    /**
     * Lắng nghe sự kiện khi hợp đồng mới được tạo (created)
     * Khi khách ký / chốt hợp đồng thuê, lập tức gỡ bài đăng khỏi Slider VIP Nổi Bật Trang chủ
     */
    public function created(Contract $contract): void
    {
        $this->autoUnfeaturePostFromContract($contract);
    }

    /**
     * Lắng nghe sự kiện khi hợp đồng được cập nhật (saved)
     */
    public function saved(Contract $contract): void
    {
        $this->autoUnfeaturePostFromContract($contract);
    }

    /**
     * Tự động tìm bài đăng liên kết và chuyển is_featured = false
     */
    private function autoUnfeaturePostFromContract(Contract $contract): void
    {
        $inactiveStatuses = ['cancelled', 'terminated', 'da_thanh_ly', 'expired', 'da_huy'];
        $contractStatus = strtolower(trim((string)($contract->status ?? '')));

        // Hợp đồng đang có hiệu lực (đã thuê)
        if (!in_array($contractStatus, $inactiveStatuses)) {
            $postId = (string)($contract->post_id ?: $contract->room_id);
            if (!empty($postId)) {
                $post = Post::where('_id', $postId)->orWhere('id', $postId)->first();
                if ($post) {
                    $needSave = false;

                    // 1. Tự động gỡ khỏi Slider Nổi Bật
                    if ($post->is_featured) {
                        $post->is_featured = false;
                        $post->featured_at = null;
                        $needSave = true;
                    }

                    // 2. Đảm bảo trạng thái đã thuê
                    if (!$post->is_rented) {
                        $post->is_rented = true;
                        $needSave = true;
                    }

                    if (!in_array(strtolower(trim((string)($post->status ?? ''))), ['đã cho thuê', 'rented', 'dang_thue'])) {
                        $post->status = 'đã cho thuê';
                        $needSave = true;
                    }

                    if ($needSave) {
                        $post->save();
                        Log::info("ContractObserver: Tự động gỡ cờ is_featured bài đăng ID [{$postId}] ('{$post->title}') do Hợp đồng [{$contract->contract_code}] được ký kết/kích hoạt.");
                    }
                }
            }
        }
    }
}
