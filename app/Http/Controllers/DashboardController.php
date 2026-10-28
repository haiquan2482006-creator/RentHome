<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang Tổng quan Quản trị Admin (/Admin)
     * Thống kê số liệu thực tế 100% từ cơ sở dữ liệu MongoDB (Áp dụng Cache 5 phút)
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();
        if ($user->role !== 'admin') {
            if (method_exists($user, 'isCskh') && $user->isCskh()) {
                return redirect('/cskh/khieu-nai');
            }
            if ($user->role === 'doanhnghiep' || $user->isEnterprise() || $user->isSubAccount()) {
                return redirect('/Overview');
            }
            return redirect('/')->with('error', 'Bạn không có quyền truy cập trang Quản trị Admin!');
        }

        // Áp dụng Caching cho các thống kê tổng quan nặng (300 giây / 5 phút)
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'totalUsers'      => User::count(),
                'totalPosts'      => Post::count(),
                'totalViolations' => User::where('is_locked', true)
                    ->orWhereIn('status', ['locked', 'khoa', 'vi_pham', 'banned'])
                    ->count(),
                'totalViews'      => (int)(Post::sum('views') ?? 0),
            ];
        });

        $totalUsers = $stats['totalUsers'];
        $totalPosts = $stats['totalPosts'];
        $totalViolations = $stats['totalViolations'];
        $totalViews = $stats['totalViews'];

        // Danh sách 5-10 tài khoản mới đăng ký gần nhất (loại trừ tài khoản admin)
        $recentUsers = User::whereNotIn('role', ['admin', 'Admin'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('Admin.Overview', compact(
            'totalUsers',
            'totalPosts',
            'totalViolations',
            'totalViews',
            'recentUsers'
        ));
    }
}
