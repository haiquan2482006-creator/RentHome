<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Lấy toàn bộ thông báo của user đang đăng nhập (Tài khoản Cá nhân / Khách thuê)
     * Hỗ trợ lọc theo tab: tất cả, chưa đọc, đã đọc & phân trang.
     */
    public function personalIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $userId = (string)($user->_id ?? $user->id);

        $baseQuery = Notification::where(function ($q) use ($userId, $user) {
            $q->where('user_id', $userId)
              ->orWhere('user_id', $user->id);
        });

        // Thống kê số lượng
        $totalCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)->where('is_read', false)->count();
        $readCount = (clone $baseQuery)->where('is_read', true)->count();

        // Lọc theo Tab
        $currentTab = $request->get('tab', 'all');
        $query = clone $baseQuery;
        if ($currentTab === 'unread') {
            $query->where('is_read', false);
        } elseif ($currentTab === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('thongbao_ca_nhan', compact(
            'notifications',
            'totalCount',
            'unreadCount',
            'readCount',
            'currentTab'
        ));
    }

    /**
     * Lấy 5 thông báo chưa đọc phục vụ topbar / dropdown
     */
    public function getUnread()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $userId = (string)($user->_id ?? $user->id);

        $unreadCount = Notification::where(function ($q) use ($userId, $user) {
            $q->where('user_id', $userId)->orWhere('user_id', $user->id);
        })->where('is_read', false)->count();

        $notifications = Notification::where(function ($q) use ($userId, $user) {
            $q->where('user_id', $userId)->orWhere('user_id', $user->id);
        })->where('is_read', false)->orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'success' => true,
            'unreadCount' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Đánh dấu 1 thông báo đã đọc
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $userId = (string)($user->_id ?? $user->id);

        $notification = Notification::where(function ($q) use ($id) {
            $q->where('_id', $id)->orWhere('id', $id);
        })->first();

        if ($notification && ((string)$notification->user_id === $userId || (string)$notification->user_id === (string)$user->id || $user->isAdmin())) {
            $notification->is_read = true;
            $notification->save();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
        }
        return redirect()->back()->with('error', 'Không tìm thấy thông báo.');
    }

    /**
     * Đánh dấu tất cả thông báo của user là đã đọc
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $userId = (string)($user->_id ?? $user->id);

        Notification::where(function ($q) use ($userId, $user) {
            $q->where('user_id', $userId)->orWhere('user_id', $user->id);
        })->where('is_read', false)->update(['is_read' => true]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.']);
        }
        return redirect()->back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    /**
     * Xóa toàn bộ thông báo đã đọc của user
     */
    public function deleteRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $userId = (string)($user->_id ?? $user->id);

        Notification::where(function ($q) use ($userId, $user) {
            $q->where('user_id', $userId)->orWhere('user_id', $user->id);
        })->where('is_read', true)->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa toàn bộ thông báo đã đọc.']);
        }
        return redirect()->back()->with('success', 'Đã xóa toàn bộ thông báo đã đọc.');
    }

    /**
     * Xóa 1 thông báo
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $userId = (string)($user->_id ?? $user->id);

        $notification = Notification::where(function ($q) use ($id) {
            $q->where('_id', $id)->orWhere('id', $id);
        })->first();

        if (!$notification) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo!'], 404);
            }
            return redirect()->back()->with('error', 'Không tìm thấy thông báo!');
        }

        if ((string)$notification->user_id !== $userId && (string)$notification->user_id !== (string)$user->id && !$user->isAdmin()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa thông báo này!'], 403);
            }
            return redirect()->back()->with('error', 'Bạn không có quyền xóa thông báo này!');
        }

        $notification->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa thông báo thành công!']);
        }
        return redirect()->back()->with('success', 'Đã xóa thông báo thành công!');
    }

    /**
     * Trang xem thông báo tổng quát
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'isCskh') && $user->isCskh()) {
            return redirect()->route('cskh.notifications');
        }
        $userId = (string)($user->_id ?? $user->id);
        $notifications = Notification::where('user_id', $userId)
            ->orWhere('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('thongbao.index', compact('notifications'));
    }
}
