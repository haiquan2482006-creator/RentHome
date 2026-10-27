<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckLockedAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bỏ qua kiểm tra nếu request đang truy cập trang đăng nhập hoặc đăng xuất
        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();

            // 1. Tự động mở khóa nếu tài khoản đã hết thời hạn khóa
            if ($user->locked_until && now()->greaterThanOrEqualTo($user->locked_until)) {
                $user->is_locked = false;
                $user->status = 'active';
                $user->locked_until = null;
                $user->lock_reason = null;
                $user->save();
            }

            // 2. Kiểm tra nếu tài khoản đang bị khóa có thời hạn hoặc vô thời hạn
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                $lyDo = $user->lock_reason ?: 'Vi phạm tiêu chuẩn cộng đồng.';

                // Tính toán câu cảnh báo (Vô thời hạn hoặc Có thời hạn)
                $isIndefinite = abs(Carbon::parse($user->locked_until)->diffInYears(now())) > 50 
                    || Carbon::parse($user->locked_until)->diffInYears(now()) > 50
                    || Carbon::parse($user->locked_until)->greaterThan(now()->addYears(50));

                if ($isIndefinite) {
                    $errorMessage = "Tài khoản của bạn đã bị khóa. Thời gian: Khóa đến khi Ban Quản Trị mở lại. Lý do: {$lyDo}. Vui lòng liên hệ CSKH để được hỗ trợ.";
                } else {
                    $thoiGianMoKhoa = Carbon::parse($user->locked_until)->format('d/m/Y H:i');
                    $errorMessage = "Tài khoản của bạn đã bị khóa. Thời gian mở khóa: {$thoiGianMoKhoa}. Lý do: {$lyDo}. Khi hết thời gian trên, bạn có thể đăng nhập bình thường.";
                }

                // Đăng xuất và hủy session
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success'  => false,
                        'message'  => $errorMessage,
                        'redirect' => route('login'),
                    ], 403);
                }

                // Đá văng về trang login kèm lỗi
                return redirect()->route('login')->withErrors(['email' => $errorMessage]);
            }

            // 3. Kiểm tra trường hợp tài khoản bị đánh dấu khóa vĩnh viễn (không có locked_until)
            if ($user->is_locked || in_array($user->status, ['locked', 'khoa', 'vi_pham', 'banned'])) {
                $lyDo = $user->lock_reason ?: 'Vi phạm tiêu chuẩn cộng đồng.';
                $errorMessage = "Tài khoản của bạn đã bị khóa. Thời gian: Khóa đến khi Ban Quản Trị mở lại. Lý do: {$lyDo}. Vui lòng liên hệ CSKH để được hỗ trợ.";

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success'  => false,
                        'message'  => $errorMessage,
                        'redirect' => route('login'),
                    ], 403);
                }

                return redirect()->route('login')->withErrors(['email' => $errorMessage]);
            }
        }

        return $next($request);
    }
}
