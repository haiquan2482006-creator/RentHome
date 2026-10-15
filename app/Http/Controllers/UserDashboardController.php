<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function getStats()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            $isAll = empty($assignedIds) || in_array('all', $assignedIds, true) || in_array('*', $assignedIds, true);

            if ($isAll) {
                $activePostsCount = Post::where(function ($q) {
                        $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                          ->orWhere('is_rented', true);
                    })
                    ->count();
                $totalViews = Post::sum('views') ?? 0;
                $totalLikes = Post::sum('likes') ?? 0;
                $totalBuildingsCount = \App\Models\Building::count();
            } else {
                $activePostsCount = Post::whereIn('building_id', $assignedIds)
                    ->where(function ($q) {
                        $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                          ->orWhere('is_rented', true);
                    })
                    ->count();
                $totalViews = Post::whereIn('building_id', $assignedIds)->sum('views') ?? 0;
                $totalLikes = Post::whereIn('building_id', $assignedIds)->sum('likes') ?? 0;
                $totalBuildingsCount = count($assignedIds);
            }
        } elseif ($user->isEnterprise() || $user->isAdmin()) {
            $activePostsCount = Post::where(function ($q) {
                    $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                      ->orWhere('is_rented', true);
                })
                ->count();
            $totalViews = Post::sum('views') ?? 0;
            $totalLikes = Post::sum('likes') ?? 0;
            $totalBuildingsCount = \App\Models\Building::count();
        } else {
            $userId = $user->_id ?? $user->id;
            $activePostsCount = Post::where('user_id', $userId)->count();
            $totalViews = Post::where('user_id', $userId)->sum('views') ?? 0;
            $totalLikes = Post::where('user_id', $userId)->sum('likes') ?? 0;
            $totalBuildingsCount = 0;
        }

        return response()->json([
            'success' => true,
            'stats' => [
                'total_views' => $totalViews,
                'total_likes' => $totalLikes,
                'active_posts_count' => $activePostsCount,
                'total_buildings_count' => $totalBuildingsCount
            ]
        ]);
    }

    public function getPosts(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        $status = $request->query('status', 'active');
        
        $query = Post::query();

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            if (!empty($assignedIds) && !in_array('all', $assignedIds, true) && !in_array('*', $assignedIds, true)) {
                $query->whereIn('building_id', $assignedIds);
            }
        } elseif (!$user->isEnterprise() && !$user->isAdmin()) {
            $query->where('user_id', $user->_id ?? $user->id);
        }

        $activeContractPostIds = Contract::where('status', '!=', 'cancelled')
            ->pluck('post_id')
            ->filter()
            ->toArray();
        
        if ($status === 'active') {
            $posts = $query->where(function ($q) use ($activeContractPostIds) {
                $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                  ->orWhere('is_rented', true);
                if (!empty($activeContractPostIds)) {
                    $q->orWhereIn('_id', $activeContractPostIds)
                      ->orWhereIn('id', $activeContractPostIds);
                }
            })->orderBy('created_at', 'desc')->get();
        } else {
            $posts = $query->where('status', $status)->orderBy('created_at', 'desc')->get();
        }

        $posts->transform(function ($post) use ($activeContractPostIds) {
            $postId = (string)($post->_id ?? $post->id);
            $isRented = ($post->is_rented === true 
                || in_array($post->status, ['đã cho thuê', 'rented', 'dang_thue'])
                || in_array($postId, $activeContractPostIds));
                
            $post->is_rented = $isRented;
            return $post;
        });
        
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    public function getHistory()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $query = Post::whereIn('status', ['approved', 'active', 'rejected', 'hidden', 'deleted', 'pending', 'đã cho thuê', 'rented', 'dang_thue']);

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            $query->whereIn('building_id', $assignedIds);
        } elseif (!$user->isEnterprise() && !$user->isAdmin()) {
            $query->where('user_id', $user->_id ?? $user->id);
        }

        $posts = $query->orderBy('updated_at', 'desc')->get();
            
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    public function getNotifications()
    {
        $userId = Auth::id();
        \Log::info('getNotifications called. Auth ID: ' . ($userId ?? 'NULL'));
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function markNotificationsRead()
    {
        $userId = (string)Auth::id();
        Notification::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Xóa 1 thông báo cụ thể khỏi MongoDB Database
     */
    public function deleteNotification($id)
    {
        $userId = (string)Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $notification = Notification::where(function($q) use ($id) {
            $q->where('_id', $id)->orWhere('id', $id);
        })->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo!'], 404);
        }

        // Kiểm tra quyền sở hữu
        $user = Auth::user();
        if ((string)$notification->user_id !== $userId && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa thông báo này!'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo thành công khỏi database!'
        ]);
    }

    /**
     * Xóa toàn bộ thông báo đã đọc của user khỏi MongoDB Database
     */
    public function clearReadNotifications()
    {
        $userId = (string)Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        Notification::where('user_id', $userId)
            ->where('is_read', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ thông báo đã đọc khỏi database!'
        ]);
    }

    /**
     * Đánh dấu 1 thông báo đã đọc trong MongoDB Database
     */
    public function markNotificationAsRead($id)
    {
        $userId = (string)Auth::id();
        if (!$userId) {
            return response()->json(['success' => false], 401);
        }

        $notification = Notification::where(function($q) use ($id) {
            $q->where('_id', $id)->orWhere('id', $id);
        })->first();

        if ($notification && ((string)$notification->user_id === $userId || Auth::user()->isAdmin())) {
            $notification->is_read = true;
            $notification->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
