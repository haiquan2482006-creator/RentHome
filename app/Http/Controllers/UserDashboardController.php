<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function getStats()
    {
        $userId = Auth::id();
        
        $activePostsCount = Post::where('user_id', $userId)
            ->whereIn('status', ['active', 'approved'])
            ->count();
            
        // Calculate total views for this user's posts
        $totalViews = Post::where('user_id', $userId)->sum('views') ?? 0;
        
        // Calculate total likes if exists, otherwise dummy
        $totalLikes = Post::where('user_id', $userId)->sum('likes') ?? 0; 
        
        // Count buildings (dummy for now unless you have a Building model)
        $totalBuildingsCount = 0; 

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
        
        $query = Post::where('user_id', $userId);
        
        if ($status === 'active') {
            $posts = $query->whereIn('status', ['active', 'approved'])->orderBy('created_at', 'desc')->get();
        } else {
            $posts = $query->where('status', $status)->orderBy('created_at', 'desc')->get();
        }
        
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    public function getHistory()
    {
        $userId = Auth::id();
        
        $posts = Post::where('user_id', $userId)
            ->whereIn('status', ['approved', 'active', 'rejected', 'hidden', 'deleted', 'pending'])
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
