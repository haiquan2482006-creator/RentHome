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
        $userId = Auth::id();
        
        $activePostsCount = Post::where('user_id', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'approved', 'đã cho thuê', 'rented', 'dang_thue'])
                  ->orWhere('is_rented', true);
            })
            ->count();
            
        // Calculate total views for this user's posts
        $totalViews = Post::where('user_id', $userId)->sum('views') ?? 0;
        
        // Calculate total likes if exists, otherwise dummy
        $totalLikes = Post::where('user_id', $userId)->sum('likes') ?? 0; 
        
        // Count buildings (dummy for now unless you have a Building model)
        $totalBuildingsCount = \App\Models\Building::where('user_id', $userId)->count() ?? 0; 

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
        $userId = Auth::id();
        $status = $request->query('status', 'active');
        
        $query = Post::where('user_id', $userId)->with('imageModels');

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
        $userId = Auth::id();
        
        $posts = Post::where('user_id', $userId)
            ->whereIn('status', ['approved', 'active', 'rejected', 'hidden', 'deleted', 'pending', 'đã cho thuê', 'rented', 'dang_thue'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
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
