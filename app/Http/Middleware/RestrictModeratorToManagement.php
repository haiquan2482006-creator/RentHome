<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictModeratorToManagement
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isModerator = (method_exists($user, 'isModerator') && $user->isModerator())
                || in_array(strtolower((string)($user->role ?? '')), ['kiemduyet', 'moderator', 'kiem_duyet'], true);

            if ($isModerator) {
                // Danh sách tiền tố/đường dẫn được phép dành cho Kiểm duyệt viên:
                // 1. Kênh quản lý kiểm duyệt: /kiem-duyet, /kiem-duyet/*
                // 2. API kiểm duyệt: /api/kiem-duyet/*, /api/moderator/*
                // 3. Đăng xuất: /logout
                // 4. Đổi mật khẩu: /doi-mat-khau
                // 5. Hình ảnh, avatar, upload: /image/*, /images/*, /api/user/avatar
                // 6. Health check: /up
                if (
                    $request->is('kiem-duyet*') ||
                    $request->is('api/kiem-duyet*') ||
                    $request->is('api/moderator*') ||
                    $request->is('logout') ||
                    $request->is('doi-mat-khau') ||
                    $request->is('image*') ||
                    $request->is('images*') ||
                    $request->is('api/user/avatar') ||
                    $request->is('up')
                ) {
                    return $next($request);
                }

                // Nếu là request AJAX/JSON mong đợi response JSON
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản Kiểm duyệt viên chỉ có quyền thao tác trong Kênh Kiểm Duyệt.',
                        'redirect' => url('/kiem-duyet/tong-quan')
                    ], 403);
                }

                // Nếu kiểm duyệt viên truy cập bất kỳ trang ngoài nào (trang chủ, tin tức, thuê nhà, ...),
                // tự động chuyển hướng họ về trang Tổng quan kiểm duyệt
                return redirect()->to('/kiem-duyet/tong-quan')->with('info', 'Tài khoản Kiểm duyệt viên chỉ có quyền thao tác trong Kênh Quản Lý Kiểm Duyệt.');
            }
        }

        return $next($request);
    }
}
