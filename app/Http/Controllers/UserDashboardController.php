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

        $userId = (string)($user->_id ?? $user->id);

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            $isAll = empty($assignedIds) || in_array('all', $assignedIds, true) || in_array('*', $assignedIds, true);

            if ($isAll) {
                $parentId = (string)($user->parent_id ?: $userId);
                $query = Post::where(function($q) use ($parentId, $userId) {
                    $q->where('parent_id', $parentId)
                      ->orWhere('user_id', $userId)
                      ->orWhere('staff_id', $userId)
                      ->orWhere('author_id', $userId);
                });
                $totalBuildingsCount = \App\Models\Building::count();
            } else {
                $query = Post::where(function($q) use ($assignedIds, $userId) {
                    $q->whereIn('building_id', $assignedIds)
                      ->orWhere('user_id', $userId)
                      ->orWhere('staff_id', $userId)
                      ->orWhere('author_id', $userId);
                });
                $totalBuildingsCount = count($assignedIds);
            }

            $activePostsCount = (clone $query)->where(function ($q) {
                    $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                      ->orWhere('is_rented', true);
                })
                ->whereNotIn('status', ['pending', 'rejected', 'deleted'])
                ->count();

            $pendingPostsCount = (clone $query)->where('status', 'pending')->count();
            $totalViews = (clone $query)->sum('views') ?? 0;
            $totalLikes = (clone $query)->sum('likes') ?? 0;
        } elseif ($user->isEnterprise() || $user->isAdmin()) {
            $activePostsCount = Post::where(function ($q) {
                    $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                      ->orWhere('is_rented', true);
                })
                ->whereNotIn('status', ['pending', 'rejected', 'deleted'])
                ->count();
            $pendingPostsCount = Post::where('status', 'pending')->count();
            $totalViews = Post::sum('views') ?? 0;
            $totalLikes = Post::sum('likes') ?? 0;
            $totalBuildingsCount = \App\Models\Building::count();
        } else {
            $query = Post::where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('staff_id', $userId)
                  ->orWhere('author_id', $userId);
            });

            $activePostsCount = (clone $query)
                ->whereIn('status', ['active', 'approved'])
                ->whereNotIn('status', ['pending', 'rejected', 'deleted'])
                ->count();
            $pendingPostsCount = (clone $query)->where('status', 'pending')->count();
            $totalViews = (clone $query)->sum('views') ?? 0;
            $totalLikes = (clone $query)->sum('likes') ?? 0;
            $totalBuildingsCount = 0;
            $staffCashDebt = 0;
        }

        if ($user->isSubAccount()) {
            $staffCashDebt = \App\Models\Invoice::where(function ($q) use ($userId) {
                    $q->where('collected_by', $userId)
                      ->orWhere('created_by', $userId);
                })
                ->where('payment_method', 'cash')
                ->where(function ($q) {
                    $q->whereNull('is_remitted_to_admin')
                      ->orWhere('is_remitted_to_admin', false)
                      ->orWhere('is_remitted_to_admin', 0);
                })
                ->where(function ($q) {
                    $q->where('status', 'paid')
                      ->orWhere('payment_status', 'paid');
                })
                ->sum('total_amount');
        } else {
            $staffCashDebt = 0;
        }

        return response()->json([
            'success' => true,
            'stats' => [
                'total_views' => (int)$totalViews,
                'total_likes' => (int)$totalLikes,
                'active_posts_count' => $activePostsCount,
                'pending_posts_count' => $pendingPostsCount,
                'total_buildings_count' => $totalBuildingsCount,
                'staff_cash_debt' => (float)$staffCashDebt,
                'staff_cash_debt_formatted' => number_format((float)$staffCashDebt) . ' VNĐ',
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
        $userId = (string)($user->_id ?? $user->id);
        
        $query = Post::with(['building', 'user']);

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            if (!empty($assignedIds) && !in_array('all', $assignedIds, true) && !in_array('*', $assignedIds, true)) {
                $query->where(function($q) use ($assignedIds, $userId) {
                    $q->whereIn('building_id', $assignedIds)
                      ->orWhere('user_id', $userId)
                      ->orWhere('staff_id', $userId)
                      ->orWhere('author_id', $userId);
                });
            } else {
                $parentId = (string)($user->parent_id ?: $userId);
                $query->where(function($q) use ($parentId, $userId) {
                    $q->where('parent_id', $parentId)
                      ->orWhere('user_id', $userId)
                      ->orWhere('staff_id', $userId)
                      ->orWhere('author_id', $userId);
                });
            }
        } elseif (!$user->isEnterprise() && !$user->isAdmin()) {
            $query->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('staff_id', $userId)
                  ->orWhere('author_id', $userId);
            });
        }

        $activeContractPostIds = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
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
            })->whereNotIn('status', ['pending', 'rejected', 'deleted'])->orderBy('created_at', 'desc')->get();
        } elseif ($status === 'pending') {
            $posts = $query->where('status', 'pending')->orderBy('created_at', 'desc')->get();
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

        $userId = (string)($user->_id ?? $user->id);
        $query = Post::with(['building', 'user'])
            ->whereIn('status', ['approved', 'active', 'rejected', 'hidden', 'deleted', 'pending', 'đã cho thuê', 'rented', 'dang_thue']);

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            if (!empty($assignedIds) && !in_array('all', $assignedIds, true) && !in_array('*', $assignedIds, true)) {
                $query->whereIn('building_id', $assignedIds);
            } else {
                $query->where('user_id', $userId)->orWhere('staff_id', $userId);
            }
        } elseif (!$user->isEnterprise() && !$user->isAdmin()) {
            $query->where('user_id', $userId)->orWhere('staff_id', $userId);
        }

        $posts = $query->orderBy('updated_at', 'desc')->get();
            
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    public function getNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        $userId = (string)($user->_id ?? $user->id);
        $notifications = Notification::where('user_id', $userId)
            ->orWhere('user_id', $user->id)
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
