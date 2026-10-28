<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin' || $user->isAdmin()) {
                return redirect('/Admin');
            }
            if ($user->isModerator() || $user->role === 'kiemduyet' || $user->role === 'moderator') {
                return redirect('/kiem-duyet/tong-quan');
            }
            if ($user->role === 'doanhnghiep' || $user->isEnterprise()) {
                return redirect('/Overview');
            }
            return redirect('/');
        }

        return view('dangnhapdk.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Vui lòng nhập Email hoặc Tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $loginInput = trim($request->input('email'));
        $password = $request->input('password');

        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $authFields = $isEmail 
            ? ['email' => $loginInput, 'password' => $password] 
            : ['username' => $loginInput, 'password' => $password];

        if (Auth::attempt($authFields)) {
            $user = Auth::user();

            // Tự động cho phép đăng nhập lại & mở khóa nếu đã hết thời gian khóa
            if ($user->locked_until && now()->greaterThanOrEqualTo($user->locked_until)) {
                $user->is_locked = false;
                $user->status = 'active';
                $user->locked_until = null;
                $user->lock_reason = null;
                $user->save();
            }

            // BƯỚC 3: Sửa logic cảnh báo đăng nhập (LoginCheck)
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                // Đăng xuất ngay lập tức
                Auth::logout();
                $request->session()->invalidate();
                
                $lyDo = $user->lock_reason ?: 'Vi phạm tiêu chuẩn cộng đồng.';
                
                // Nếu thời gian khóa còn lớn hơn 50 năm -> Hiểu là khóa vô thời hạn (đến khi mở lại)
                $isIndefinite = abs(Carbon::parse($user->locked_until)->diffInYears(now())) > 50 
                    || Carbon::parse($user->locked_until)->diffInYears(now()) > 50
                    || Carbon::parse($user->locked_until)->greaterThan(now()->addYears(50));

                if ($isIndefinite) {
                    $errorMessage = "Tài khoản của bạn đã bị khóa đến khi được mở lại bởi Quản trị viên. Lý do: {$lyDo}. Vui lòng liên hệ Ban Quản Trị để được hỗ trợ.";
                } else {
                    $thoiGianMoKhoa = Carbon::parse($user->locked_until)->format('d/m/Y H:i');
                    $errorMessage = "Tài khoản của bạn đã bị khóa. Thời gian mở khóa: {$thoiGianMoKhoa}. Lý do: {$lyDo}. Khi hết thời gian trên, bạn có thể đăng nhập bình thường.";
                }
                
                // Bắn lỗi quay lại trang đăng nhập
                throw ValidationException::withMessages([
                    'email' => $errorMessage,
                ]);
            }

            // Kiểm tra trường hợp tài khoản bị khóa không có locked_until
            if ($user->is_locked || in_array($user->status, ['locked', 'khoa', 'vi_pham', 'banned'])) {
                Auth::logout();
                $request->session()->invalidate();
                $lyDo = $user->lock_reason ?: 'Vi phạm tiêu chuẩn cộng đồng.';
                throw ValidationException::withMessages([
                    'email' => "Tài khoản của bạn đã bị khóa đến khi được mở lại bởi Quản trị viên. Lý do: {$lyDo}. Vui lòng liên hệ Ban Quản Trị để được hỗ trợ.",
                ]);
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->intended('/Admin')->with('success', 'Xin chào Admin! Đăng nhập thành công vào trang quản trị.');
            }

            if ($user->isModerator() || $user->role === 'kiemduyet' || $user->role === 'moderator') {
                $request->session()->forget('url.intended');
                return redirect('/kiem-duyet/tong-quan')->with('success', 'Xin chào ' . ($user->account_name ?: 'Kiểm duyệt viên') . '! Đăng nhập thành công vào Kênh Kiểm Duyệt.');
            }

            // 2.2 Nhân viên Chăm sóc khách hàng (CSKH) -> Chuyển thẳng về Trung tâm CSKH & Khiếu nại
            if ((method_exists($user, 'isCskh') && $user->isCskh()) || in_array($user->role, ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'])) {
                $request->session()->forget('url.intended');
                return redirect('/cskh/khieu-nai')->with('success', 'Xin chào ' . ($user->account_name ?: 'Chăm sóc khách hàng') . '! Đăng nhập thành công vào Trung tâm CSKH.');
            }

            // 3. Tài khoản con (nhân sự / nhân viên phụ trách) -> Chuyển về Trang chủ (Home)
            if ($user->isSubAccount() || !empty($user->parent_id) || in_array($user->role, ['sub_account', 'staff', 'nhansu', 'nhan su', 'nhan_su'])) {
                $request->session()->forget('url.intended');
                return redirect('/')->with('success', 'Xin chào ' . ($user->account_name ?: $user->username ?: 'Nhân sự RentHome') . '! Đăng nhập thành công.');
            }

            // 4. Doanh nghiệp mẹ / Quản trị Doanh nghiệp chính -> Chuyển về Overview
            if ($user->role === 'doanhnghiep' || $user->isEnterprise()) {
                return redirect()->intended('/Overview')->with('success', 'Xin chào Quản trị Doanh nghiệp RentHome! Đăng nhập thành công.');
            }

            return redirect()->intended('/')->with('success', 'Đăng nhập thành công! Chào mừng bạn đến với RentHome.');
        }

        return back()->withErrors([
            'login_error' => 'Email hoặc mật khẩu không chính xác.',
        ])->withInput($request->only('email'));
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('dangnhapdk.Register');
    }

    public function register(Request $request)
    {
        // Tài khoản Doanh nghiệp được cấp trực tiếp dưới tên RentHome, không cho đăng ký ngoài
        $rules = [
            'account_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ];

        $messages = [
            'account_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Địa chỉ Email không hợp lệ.',
            'email.unique' => 'Email này đã được đăng ký trong hệ thống.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];

        $validated = $request->validate($rules, $messages);

        $userData = [
            'account_name' => $validated['account_name'],
            'email' => $validated['email'],
            'username' => explode('@', $validated['email'])[0],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'canhan',
            'status' => 'active',
            'is_locked' => false,
            'is_deleted' => false,
        ];

        User::create($userData);

        return redirect('/login')->with('success', 'Đăng ký tài khoản cá nhân thành công! Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
