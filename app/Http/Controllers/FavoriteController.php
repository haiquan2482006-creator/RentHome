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

        $posts = Post::with(['user', 'imageModels'])
            ->whereIn('_id', $postIds)
            ->orWhereIn('id', $postIds)
            ->get();

        $mappedPosts = $posts->map(function ($post) {
            $unitText = 'đ/tháng';
            if ($post->price_unit === 'nam') {
                $unitText = 'đ/năm';
            } elseif ($post->price_unit === 'm2') {
                $unitText = 'đ/m²';
            } elseif ($post->price_unit === 'tong') {
                $unitText = 'VNĐ';
            }

            $address = implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province]));

            return [
                'id' => (string) ($post->_id ?? $post->id),
                'title' => $post->title,
                'price' => number_format((float) ($post->price ?? 0), 0, ',', '.') . ' ' . $unitText,
                'raw_price' => $post->price,
                'area' => $post->area,
                'address' => $address,
                'display_image' => $post->display_image,
                'status' => $post->status,
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

            return response()->json([
                'success' => true,
                'is_favorite' => false,
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

        // Cập nhật số lượt thích trên bài viết nếu có trường likes
        try {
            $post->increment('likes');
        } catch (\Exception $e) {
            // bỏ qua nếu không có trường
        }

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

        return response()->json([
            'success' => true,
            'is_favorite' => true,
            'message' => 'Đã thêm bài viết vào danh sách yêu thích!',
            'post' => [
                'id' => $realPostId,
                'title' => $post->title,
                'price' => number_format((float) ($post->price ?? 0), 0, ',', '.') . ' ' . $unitText,
                'display_image' => $post->display_image,
                'address' => implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province])),
            ]
        ]);
    }
}
