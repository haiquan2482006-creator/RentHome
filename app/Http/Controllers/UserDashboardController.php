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
            $activePostsCount = Post::whereIn('building_id', $assignedIds)
                ->where(function ($q) {
                    $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                      ->orWhere('is_rented', true);
                })
                ->count();
            $totalViews = Post::whereIn('building_id', $assignedIds)->sum('views') ?? 0;
            $totalLikes = Post::whereIn('building_id', $assignedIds)->sum('likes') ?? 0;
            $totalBuildingsCount = count($assignedIds);
        } elseif ($user->isEnterprise() || $user->role === 'admin' || $user->account_type === 'admin') {
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
        
        $query = Post::with('imageModels');

        if ($user->isSubAccount()) {
            $assignedIds = (array) ($user->assigned_buildings ?? []);
            $query->whereIn('building_id', $assignedIds);
        } elseif (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin') {
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
        } elseif (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin') {
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
        $userId = Auth::id();
        Notification::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}
