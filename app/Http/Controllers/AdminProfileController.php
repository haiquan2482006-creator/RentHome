<?php

namespace App\Http\Controllers;

use App\Models\AdminActivity;
use App\Models\Building;
use App\Models\Contract;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ cá nhân của Quản trị viên (Admin)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập trang hồ sơ.');
        }

        // Kiểm tra quyền Quản trị viên
        if (!$user->isAdmin() && $user->role !== 'admin') {
            return redirect('/login')->with('error', 'Chỉ tài khoản Quản trị viên (Admin) mới có quyền truy cập trang này.');
        }

        // Thống kê nhanh đặc quyền hệ thống
        $totalBuildings = Building::count();
        $totalRooms = Post::count();
        $totalSubAccounts = User::whereIn('role', ['staff', 'nhansu', 'sub_account', 'cskh', 'kiemduyet'])->count();
        $totalContracts = Contract::count();

        // 6 hoạt động gần nhất của Admin
        $userId = (string)($user->_id ?? $user->id);
        $recentActivities = AdminActivity::where('user_id', $userId)
            ->orWhere('author_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        if ($recentActivities->isEmpty()) {
            $recentActivities = AdminActivity::orderBy('created_at', 'desc')->take(6)->get();
        }

        $bankCode = OperationManagementController::getVietQRBankCode($user->bank_name ?? 'MB Bank');

        return view('Admin.ho_so', compact(
            'user',
            'totalBuildings',
            'totalRooms',
            'totalSubAccounts',
            'totalContracts',
            'recentActivities',
            'bankCode'
        ));
    }

    /**
     * Cập nhật thông tin hồ sơ Quản trị viên
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        if (!$user->isAdmin() && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền quản trị.'], 403);
        }

        $uId = (string)($user->_id ?? $user->id);

        $validated = $request->validate([
            'account_name' => 'nullable|string|max:100',
            'username'     => 'nullable|string|max:50',
            'email'        => 'required|email|max:100',
            'phone'        => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:150',
            'address'      => 'nullable|string|max:255',
            'tax_code'     => 'nullable|string|max:30',
            'bank_name'    => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_owner'   => 'nullable|string|max:100',
            'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email'    => 'Địa chỉ email không đúng định dạng.',
            'avatar.image'   => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'avatar.max'     => 'Dung lượng ảnh tối đa là 3MB.',
        ]);

        // 1. Cập nhật Avatar nếu có tải ảnh mới
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            if ($file && $file->isValid()) {
                Image::where('target_type', 'avatar')->where('target_id', $uId)->delete();

                $imgDoc = Image::create([
                    'target_type' => 'avatar',
                    'target_id'   => $uId,
                    'filename'    => $file->getClientOriginalName(),
                    'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                    'size'        => $file->getSize(),
                    'data'        => base64_encode(file_get_contents($file->getRealPath())),
                    'is_cover'    => true,
                    'uploaded_by' => $uId,
                ]);

                $user->avatar = '/images/' . $imgDoc->_id;
            }
        }

        // 2. Cập nhật thông tin định danh
        $name = trim($request->input('account_name', $user->account_name ?: $user->name));
        $user->account_name = $name;
        $user->name         = $name;
        $user->ho_ten       = $name;
        $user->display_name = $name;
        $user->ten_hien_thi = $name;

        if ($request->has('username') && $request->filled('username')) {
            $user->username = trim($request->input('username'));
        }
        $user->email = trim($request->input('email'));
        $user->phone    = trim($request->input('phone', ''));

        // 3. Cập nhật thông tin Doanh nghiệp / Trụ sở điều hành
        $user->company_name = trim($request->input('company_name', ''));
        $user->address      = trim($request->input('address', ''));
        $user->tax_code     = trim($request->input('tax_code', ''));

        // 4. Cập nhật tài khoản Ngân hàng thụ hưởng doanh nghiệp
        // - Số tài khoản: không được điền chữ, chỉ giữ lại chữ số và khoảng cách
        $cleanAccount = preg_replace('/[^0-9\s]/', '', $request->input('bank_account', ''));

        // - Chủ tài khoản: không được điền dấu và ký tự đặc biệt, viết hoa
        $asciiOwner = \Illuminate\Support\Str::ascii($request->input('bank_owner', ''));
        $cleanOwner = strtoupper(preg_replace('/[^A-Za-z0-9\s]/', '', $asciiOwner));

        $user->bank_name    = trim($request->input('bank_name', ''));
        $user->bank_account = trim($cleanAccount);
        $user->bank_owner   = trim($cleanOwner);

        $user->save();

        // 5. Ghi nhật ký Audit Trail Admin
        AdminActivity::log(
            'update',
            'admin_profile',
            'Cập nhật hồ sơ Quản trị viên',
            "Quản trị viên [{$user->account_name}] đã cập nhật thông tin hồ sơ và tài khoản ngân hàng thụ hưởng.",
            $user,
            [
                'admin_id'     => $uId,
                'account_name' => $user->account_name,
                'email'        => $user->email,
                'phone'        => $user->phone,
                'bank_name'    => $user->bank_name,
                'bank_account' => $user->bank_account,
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Cập nhật hồ sơ Quản trị viên thành công!',
                'avatar'       => $user->display_avatar,
                'account_name' => $user->account_name,
            ]);
        }

        return back()->with('success', 'Cập nhật hồ sơ Quản trị viên thành công!');
    }

    /**
     * Đổi mật khẩu tài khoản Quản trị viên
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        if (!$user->isAdmin() && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền quản trị.'], 403);
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
            'new_password.confirmed'             => 'Xác nhận mật khẩu mới không trùng khớp.',
            'new_password_confirmation.required' => 'Vui lòng nhập lại xác nhận mật khẩu mới.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác. Vui lòng kiểm tra lại.',
                    'errors'  => ['current_password' => ['Mật khẩu hiện tại không chính xác.']]
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        AdminActivity::log(
            'security',
            'admin_password',
            'Đổi mật khẩu Quản trị viên',
            "Quản trị viên [{$user->account_name}] đã thay đổi mật khẩu đăng nhập tài khoản Admin.",
            $user
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu Quản trị viên thành công!',
            ]);
        }

        return back()->with('success', 'Đổi mật khẩu Quản trị viên thành công!');
    }
}

