<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PasswordResetController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $code = rand(100000, 999999);

        // Lưu vào session để tí nữa so sánh
        Session::put('reset_password_email', $email);
        Session::put('reset_password_code', $code);

        try {
            Mail::send('emails.reset_password', ['code' => $code], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Mã xác nhận quên mật khẩu - RentHome');
            });
            
            return response()->json(['success' => true, 'message' => 'Mã xác nhận đã được gửi thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gửi email thất bại: ' . $e->getMessage()]);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $savedEmail = Session::get('reset_password_email');
        $savedCode = Session::get('reset_password_code');

        if ($request->email == $savedEmail && $request->code == $savedCode) {
            return redirect('/dat-lai-mat-khau');
        }

        // Nếu sai mã
        return back()->withErrors(['error' => 'Mã xác nhận không đúng!'])->withInput();
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        $email = Session::get('reset_password_email');
        if (!$email) {
            return redirect('/quen-mat-khau')->withErrors(['error' => 'Hết hạn, vui lòng làm lại từ đầu.']);
        }

        // Giả sử bạn đang lưu người dùng ở bảng users bằng model User mặc định
        // Uncomment dòng dưới đây để thực sự cập nhật vào Database
        // \App\Models\User::where('email', $email)->update(['password' => bcrypt($request->new_password)]);

        Session::forget(['reset_password_email', 'reset_password_code']);

        return redirect('/login')->with('success', 'Mật khẩu đã được đặt lại thành công!');
    }
}
