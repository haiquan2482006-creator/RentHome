<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
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
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->is_locked || $user->status === 'locked') {
                Auth::logout();
                return back()->withErrors(['login_error' => 'Tài khoản của bạn đã bị khóa! Vui lòng liên hệ Ban Quản Trị.']);
            }

            if ($user->role === 'admin') {
                return redirect()->intended('/Admin')->with('success', 'Xin chào Admin! Đăng nhập thành công vào trang quản trị.');
            }

            if ($user->isModerator() || $user->role === 'kiemduyet' || $user->role === 'moderator') {
                return redirect()->intended('/kiem-duyet/tong-quan')->with('success', 'Xin chào ' . ($user->account_name ?: 'Kiểm duyệt viên') . '! Đăng nhập thành công vào Kênh Kiểm Duyệt.');
            }

            // 3. Tài khoản con (doanh nghiệp / nhân viên phụ trách) -> Chuyển về Trang chủ (Home)
            if ($user->isSubAccount() || !empty($user->parent_id) || in_array($user->role, ['sub_account', 'staff'])) {
                $request->session()->forget('url.intended');
                return redirect('/')->with('success', 'Xin chào ' . ($user->account_name ?: $user->username ?: 'Tài khoản Doanh nghiệp') . '! Đăng nhập thành công.');
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
            'role' => 'user',
            'status' => 'active',
            'is_locked' => false,
            'is_deleted' => false,
        ];

        User::create($userData);

        return redirect('/login')->with('success', 'Đăng ký tài khoản người thuê nhà thành công! Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
