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
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập hoặc email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginType => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->account_type === 'admin' || $user->role === 'admin') {
                return redirect()->intended('/Admin')->with('success', 'Xin chào Admin! Đăng nhập thành công.');
            }

            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }


        return back()->withErrors([
            'login_error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
        ])->withInput($request->only('username'));
    }

    public function showRegisterForm()
    {
        return view('dangnhapdk.Register');
    }

    public function register(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'account_type' => 'required|in:canhan,doanhnghiep',
        ];

        $messages = [
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.unique' => 'Tên đăng nhập này đã tồn tại.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'account_name.required' => 'Vui lòng nhập tên tài khoản.',
            'company_name.required' => 'Vui lòng nhập tên doanh nghiệp.',
            'company_address.required' => 'Vui lòng nhập địa chỉ doanh nghiệp.',
            'tax_code.required' => 'Vui lòng nhập mã số thuế.',
            'company_email.required' => 'Vui lòng nhập email doanh nghiệp.',
        ];

        if ($request->account_type === 'canhan') {
            $rules['account_name'] = 'required|string|max:255';
        } else {
            $rules['company_name'] = 'required|string|max:255';
            $rules['company_address'] = 'required|string|max:255';
            $rules['tax_code'] = 'required|string|max:50';
            $rules['company_email'] = 'required|email';
            $rules['business_license'] = 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120';
        }

        $validated = $request->validate($rules, $messages);

        $licensePath = null;
        if ($request->hasFile('business_license')) {
            $licensePath = $request->file('business_license')->store('business_licenses', 'public');
        }

        $userData = [
            'email' => $validated['email'],
            'username' => $validated['username'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'account_type' => $validated['account_type'],
        ];

        if ($validated['account_type'] === 'canhan') {
            $userData['account_name'] = $validated['account_name'];
        } else {
            $userData['company_name'] = $validated['company_name'];
            $userData['company_address'] = $validated['company_address'];
            $userData['tax_code'] = $validated['tax_code'];
            $userData['company_email'] = $validated['company_email'];
            $userData['business_license'] = $licensePath;
        }

        User::create($userData);

        return redirect('/login')->with('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Bạn đã đăng xuất.');
    }
}
