<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * Ngăn chặn hoàn toàn việc trình duyệt lưu cache trang nhạy cảm/đã đăng nhập,
     * ngăn chặn lỗi bảo mật khi đăng xuất mà bấm Back hoặc dán URL vào tab mới.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Không áp dụng no-store cho file ảnh, tài nguyên tĩnh hoặc health check
        if (!$request->is('image/*') && !$request->is('images/*') && !$request->is('up')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
