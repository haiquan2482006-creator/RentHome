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
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Địa chỉ Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->account_type === 'admin' || $user->role === 'admin') {
                return redirect()->intended('/Admin')->with('success', 'Xin chào Admin! Đăng nhập thành công.');
            }

            if ($user->account_type === 'doanhnghiep' || $user->role === 'doanhnghiep') {
                return redirect()->intended('/Overview')->with('success', 'Xin chào Doanh nghiệp RentHome! Đăng nhập thành công.');
            }

            if ($user->account_type === 'sub_account' || $user->role === 'sub_account') {
                return redirect()->intended('/Overview')->with('success', 'Xin chào Quản lý tòa nhà! Đăng nhập thành công.');
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
            'account_type' => 'canhan',
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
