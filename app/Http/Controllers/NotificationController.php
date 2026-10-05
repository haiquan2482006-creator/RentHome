<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getUnread()
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();
        $notifications = $user->unreadNotifications()->take(5)->get();

        return response()->json([
            'success' => true,
            'unreadCount' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
    }

    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('thongbao.index', compact('notifications'));
    }
}
