<?php

namespace App\Http\Controllers;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ cá nhân của nhân sự nội bộ (CSKH / Kiểm duyệt viên)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập trang hồ sơ.');
        }

        // Xác định vai trò hiển thị
        $roleTitle = 'Nhân Sự Nội Bộ';
        $portalPrefix = 'cskh';
        if (method_exists($user, 'isCskh') && $user->isCskh()) {
            $roleTitle = 'Chăm Sóc Khách Hàng (CSKH)';
            $portalPrefix = 'cskh';
        } elseif (method_exists($user, 'isModerator') && $user->isModerator()) {
            $roleTitle = 'Kiểm Duyệt Viên';
            $portalPrefix = 'kiem-duyet';
        } elseif (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $roleTitle = 'Quản Trị Viên (Admin)';
            $portalPrefix = 'admin';
        }

        if ($portalPrefix === 'kiem-duyet' || $request->is('kiem-duyet/*')) {
            return view('kiem_duyet_vien.ho_so', compact('user', 'roleTitle', 'portalPrefix'));
        }

        return view('cham_soc_kh.ho_so', compact('user', 'roleTitle', 'portalPrefix'));
    }

    /**
     * Cập nhật thông tin cá nhân
     * Tuân thủ Audit Trail: KHÔNG cho phép sửa name (tên thật), email, role
     * Chỉ cho phép sửa display_name (bí danh), phone, avatar
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập.'], 401);
        }

        $validated = $request->validate([
            'display_name' => 'nullable|string|max:50',
            'phone'        => ['nullable', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ], [
            'display_name.max' => 'Bí danh không được vượt quá 50 ký tự.',
            'phone.regex'      => 'Số điện thoại không đúng định dạng.',
            'avatar.image'     => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'avatar.mimes'     => 'Ảnh đại diện hỗ trợ định dạng: jpeg, png, jpg, gif, webp.',
            'avatar.max'       => 'Kích thước ảnh tối đa là 3MB.',
        ]);

        // Cập nhật avatar nếu có upload file mới
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . ($user->_id ?? $user->id) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = '/storage/' . $path;
        }

        // Cập nhật bí danh (display_name & ten_hien_thi)
        if ($request->has('display_name')) {
            $cleanDisplayName = trim($request->input('display_name'));
            $user->display_name = $cleanDisplayName;
            $user->ten_hien_thi = $cleanDisplayName;
        }

        // Cập nhật số điện thoại
        if ($request->has('phone')) {
            $user->phone = trim($request->input('phone'));
        }

        $user->save();

        if (class_exists(AdminActivity::class)) {
            AdminActivity::log(
                'update',
                'user_profile',
                'Cập nhật hồ sơ cá nhân nội bộ',
                "Nhân sự [{$user->name}] đã cập nhật hồ sơ cá nhân (Bí danh: {$user->display_name}).",
                $user,
                [
                    'user_id'      => (string)($user->_id ?? $user->id),
                    'display_name' => $user->display_name,
                    'phone'        => $user->phone,
                ]
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Lưu thay đổi hồ sơ cá nhân thành công!',
                'display_name' => $user->display_name,
                'phone'        => $user->phone,
                'avatar'       => $user->display_avatar,
            ]);
        }

        return back()->with('success', 'Lưu thay đổi hồ sơ cá nhân thành công!');
    }

    /**
     * Đổi mật khẩu tài khoản nội bộ
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập.'], 401);
        }

        $validated = $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:6|different:current_password|confirmed',
            'new_password_confirmation' => 'required|string',
        ], [
            'current_password.required'          => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'              => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'                   => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'new_password.different'             => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
            'new_password.confirmed'             => 'Xác nhận mật khẩu mới không khớp.',
            'new_password_confirmation.required' => 'Vui lòng nhập lại mật khẩu mới.',
        ]);

        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác. Vui lòng kiểm tra lại.',
                    'errors'  => ['current_password' => ['Mật khẩu hiện tại không chính xác.']]
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        if (isset($user->plain_password)) {
            $user->plain_password = $request->new_password;
        }
        $user->save();

        if (class_exists(AdminActivity::class)) {
            AdminActivity::log(
                'update',
                'user_security',
                'Đổi mật khẩu tài khoản nội bộ',
                "Nhân sự [{$user->name}] đã thực hiện đổi mật khẩu tài khoản.",
                $user,
                ['user_id' => (string)($user->_id ?? $user->id)]
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới của bạn.',
            ]);
        }

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}

