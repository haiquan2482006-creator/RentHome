<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Favorite;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Lấy danh sách các bài đăng đã được yêu thích của người dùng hiện tại
     */
    public function getFavorites(Request $request)
    {
        if (!Auth::check() || Auth::user()->isEnterprise() || Auth::user()->role === 'doanhnghiep') {
            return response()->json([
                'success' => true,
                'favorites' => [],
                'favorite_ids' => []
            ]);
        }

        $userId = (string) Auth::id();
        $favorites = Favorite::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $postIds = $favorites->pluck('post_id')->filter()->unique()->values()->toArray();

        if (empty($postIds)) {
            return response()->json([
                'success' => true,
                'favorites' => [],
                'favorite_ids' => []
            ]);
        }

        // BƯỚC 1: Lấy toàn bộ bài đăng user đã lưu bất kể trạng thái hiện tại là gì.
        // TUYỆT ĐỐI KHÔNG dùng điều kiện lọc bỏ các bài đã thuê (không where('status', 'available')).
        $posts = Post::with(['user', 'building'])
            ->where(function ($q) use ($postIds) {
                $q->whereIn('_id', $postIds)
                  ->orWhereIn('id', $postIds);
            })
            ->get();

        // Lấy danh sách post_id hoặc room_id đang có hợp đồng thuê hoạt động
        $activeContractPostIds = [];
        try {
            $activeContracts = \App\Models\Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                ->where(function ($q) use ($postIds) {
                    $q->whereIn('post_id', $postIds)
                      ->orWhereIn('room_id', $postIds);
                })
                ->get(['post_id', 'room_id']);

            $activeContractPostIds = $activeContracts->pluck('post_id')
                ->merge($activeContracts->pluck('room_id'))
                ->filter()
                ->map('strval')
                ->unique()
                ->toArray();
        } catch (\Exception $e) {
            $activeContractPostIds = [];
        }

        $mappedPosts = $posts->map(function ($post) use ($activeContractPostIds) {
            $unitText = 'đ/tháng';
            if ($post->price_unit === 'nam') {
                $unitText = 'đ/năm';
            } elseif ($post->price_unit === 'm2') {
                $unitText = 'đ/m²';
            } elseif ($post->price_unit === 'tong') {
                $unitText = 'VNĐ';
            }

            $address = implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province]));
            $postIdStr = (string) ($post->_id ?? $post->id);
            $rawStatus = strtolower(trim((string) ($post->status ?? '')));

            $isRented = (bool) (
                $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
                in_array($rawStatus, ['rented', 'đã cho thuê', 'dang_thue', 'đang thuê', 'da_cho_thue', 'inactive']) ||
                in_array($postIdStr, $activeContractPostIds, true)
            );

            return [
                'id' => $postIdStr,
                'title' => $post->title,
                'price' => number_format((float) ($post->price ?? 0), 0, ',', '.') . ' ' . $unitText,
                'raw_price' => $post->price,
                'area' => $post->area,
                'address' => $address,
                'display_image' => $post->display_image,
                'status' => $post->status,
                'is_rented' => $isRented,
            ];
        });

        return response()->json([
            'success' => true,
            'favorites' => $mappedPosts,
            'favorite_ids' => $postIds
        ]);
    }

    /**
     * Thêm / Bỏ yêu thích bài đăng
     */
    public function toggleFavorite(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Vui lòng đăng nhập để lưu bài viết yêu thích!'
            ], 401);
        }

        $user = Auth::user();

        // 1. Tài khoản Doanh nghiệp không thể thích bài đăng
        if ($user->isEnterprise() || $user->role === 'doanhnghiep') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản Doanh nghiệp không thể thực hiện chức năng yêu thích bài đăng.'
            ], 403);
        }

        $postId = (string) $request->input('post_id');
        if (!$postId) {
            return response()->json([
                'success' => false,
                'message' => 'Mã bài đăng không hợp lệ.'
            ], 400);
        }

        $post = Post::where('_id', $postId)->orWhere('id', $postId)->first();
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Bài đăng không tồn tại hoặc đã bị xóa.'
            ], 404);
        }

        $userId = (string) Auth::id();
        $realPostId = (string) ($post->_id ?? $post->id);

        $existing = Favorite::where('user_id', $userId)
            ->where(function ($q) use ($realPostId, $postId) {
                $q->where('post_id', $realPostId)->orWhere('post_id', $postId);
            })->first();

        if ($existing) {
            $existing->delete();

            // Giảm favorites_count của bài đăng (Đảm bảo dữ liệu thật, không âm)
            $cur = max(0, (int)($post->favorites_count ?? ($post->likes ?? 0)));
            $newFav = max(0, $cur - 1);
            $post->favorites_count = $newFav;
            $post->likes = $newFav;
            $post->save();

            \Illuminate\Support\Facades\Cache::forget('home_approved_posts_default');
            \Illuminate\Support\Facades\Cache::forget('home_featured_posts');

            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'favorites_count' => (int)$newFav,
                'message' => 'Đã xóa bài viết khỏi danh sách yêu thích.',
                'post_id' => $realPostId
            ]);
        }

        // 2. Người đăng bài không thể tự thích bài đăng của chính bản thân
        if ((string) $post->user_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự yêu thích bài đăng của chính mình!'
            ], 400);
        }

        // Tạo bản ghi yêu thích mới
        Favorite::create([
            'user_id' => $userId,
            'post_id' => $realPostId,
        ]);

        // Tăng favorites_count của bài đăng (Đảm bảo dữ liệu thật)
        $cur = max(0, (int)($post->favorites_count ?? ($post->likes ?? 0)));
        $newFav = $cur + 1;
        $post->favorites_count = $newFav;
        $post->likes = $newFav;
        $post->save();

        \Illuminate\Support\Facades\Cache::forget('home_approved_posts_default');
        \Illuminate\Support\Facades\Cache::forget('home_featured_posts');

        // Tạo thông báo cho chủ sở hữu bài đăng (quản lý bài)
        $postOwnerId = (string) $post->user_id;
        if ($postOwnerId) {
            $liker = Auth::user();
            $likerName = $liker->account_name ?: ($liker->username ?: 'Khách hàng');
            $likerPhone = $liker->phone ?: 'Chưa cập nhật SĐT';
            $likerEmail = $liker->email ?: 'Chưa cập nhật Email';
            $likerType = ($liker->isEnterprise() || $liker->role === 'doanhnghiep') ? 'Tài khoản Doanh nghiệp' : 'Tài khoản Cá nhân';
            $postTitle = $post->title;

            $notificationMessage = "Người dùng {$likerName} ({$likerType}) vừa nhấn yêu thích bài viết \"{$postTitle}\" của bạn.\n"
                . "Thông tin liên hệ:\n"
                . "• Họ và tên: {$likerName}\n"
                . "• Số điện thoại: {$likerPhone}\n"
                . "• Email: {$likerEmail}";

            Notification::create([
                'user_id' => $postOwnerId,
                'post_id' => $realPostId,
                'title' => 'Có người thích bài đăng của bạn',
                'message' => $notificationMessage,
                'type' => 'favorite',
                'category' => 'favorite',
                'is_read' => false,
            ]);
        }

        $unitText = 'đ/tháng';
        if ($post->price_unit === 'nam') {
            $unitText = 'đ/năm';
        } elseif ($post->price_unit === 'm2') {
            $unitText = 'đ/m²';
        } elseif ($post->price_unit === 'tong') {
            $unitText = 'VNĐ';
        }

        $rawStatus = strtolower(trim((string) ($post->status ?? '')));
        $isRented = (bool) (
            $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
            in_array($rawStatus, ['rented', 'đã cho thuê', 'dang_thue', 'đang thuê', 'da_cho_thue', 'inactive'])
        );
        if (!$isRented) {
            try {
                $isRented = \App\Models\Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                    ->where(function ($q) use ($realPostId) {
                        $q->where('post_id', $realPostId)
                          ->orWhere('room_id', $realPostId);
                    })
                    ->exists();
            } catch (\Exception $e) {}
        }

        return response()->json([
            'success' => true,
            'is_favorite' => true,
            'favorites_count' => (int)($post->favorites_count ?? 0),
            'message' => 'Đã thêm bài viết vào danh sách yêu thích!',
            'post' => [
                'id' => $realPostId,
                'title' => $post->title,
                'price' => number_format((float) ($post->price ?? 0), 0, ',', '.') . ' ' . $unitText,
                'raw_price' => $post->price,
                'display_image' => $post->display_image,
                'address' => implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province])),
                'status' => $post->status,
                'is_rented' => $isRented,
            ]
        ]);
    }
}
