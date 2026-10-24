<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang Tổng quan Quản trị Admin (/Admin)
     * Thống kê số liệu thực tế 100% từ cơ sở dữ liệu MongoDB
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();
        if ($user->role !== 'admin') {
            if ($user->role === 'doanhnghiep' || $user->isEnterprise() || $user->isSubAccount()) {
                return redirect('/Overview');
            }
            return redirect('/')->with('error', 'Bạn không có quyền truy cập trang Quản trị Admin!');
        }

        // 1. Tổng số tài khoản trong hệ thống thực tế
        $totalUsers = User::count();

        // 2. Tổng số bài đăng / phòng thực tế đang tồn tại trong DB
        $totalPosts = Post::count();

        // 3. Số tài khoản vi phạm / bị khóa
        $totalViolations = User::where('is_locked', true)
            ->orWhereIn('status', ['locked', 'khoa', 'vi_pham', 'banned'])
            ->count();

        // 4. Tổng lượt truy cập của các bài đăng
        $totalViews = (int)(Post::sum('views') ?? 0);

        // 5. Danh sách 5-10 tài khoản mới đăng ký gần nhất (loại trừ tài khoản admin)
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
