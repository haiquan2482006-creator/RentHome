<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('Home');
});

Route::get('/Admin', function (){
    $recentUsers = \App\Models\User::where('role', '!=', 'admin')
        ->where('account_type', '!=', 'admin')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
    $totalUsers = \App\Models\User::count();
    return view('Admin.Overview', compact('recentUsers', 'totalUsers'));
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/Register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/quen-mat-khau', function () {
    return view('dangnhapdk.quenmatkhau_b1'); 
});

Route::get('/dat-lai-mat-khau', function () {
    return view('dangnhapdk.quenmatkhau_b2'); 
});

Route::get('/nha-dang-thue', function () {
    return view('nhadangthue.index');
})->name('nha-dang-thue');

Route::get('/quan-ly-van-hanh', function () {
    return view('qlvan_hanh.Room_list');
})->name('quan-ly-van-hanh');

Route::get('/qlvan_hanh/Room_list', function () {
    return view('qlvan_hanh.Room_list');
});

Route::get('/qlvan_hanh/thanh_ly_cong_no', function () {
    return view('qlvan_hanh.thanh_ly_cong_no');
});

Route::get('/thanh-ly-cong-no', function () {
    return view('qlvan_hanh.thanh_ly_cong_no');
})->name('thanh-ly-cong-no');

// Các route POST xử lý logic gửi email và xác nhận
Route::post('/send-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'verifyCode']);
Route::post('/dat-lai-mat-khau', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword']);

Route::get('/qlnguoi_dung', function (\Illuminate\Http\Request $request) {
    $search = trim($request->query('search', ''));
    $accountType = $request->query('account_type', '');

    $query = \App\Models\User::where('role', '!=', 'admin')
        ->where('account_type', '!=', 'admin')
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', '!=', true);
        })
        ->where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'deleted');
        });

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('account_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%");
        });
    }

    if (!empty($accountType) && in_array($accountType, ['canhan', 'doanhnghiep'])) {
        $query->where('account_type', $accountType);
    }

    $users = $query->orderBy('created_at', 'desc')->paginate(15);
    $totalUsers = \App\Models\User::where('role', '!=', 'admin')
        ->where('account_type', '!=', 'admin')
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', '!=', true);
        })
        ->where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'deleted');
        })
        ->count();

    return view('Admin.qlnguoi_dung', compact('users', 'search', 'accountType', 'totalUsers'));
});

Route::delete('/qlnguoi_dung/{id}', function ($id) {
    $user = \App\Models\User::find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'Không tìm thấy người dùng cần xóa!');
    }

    if ($user->role === 'admin' || $user->account_type === 'admin') {
        return redirect()->back()->with('error', 'Không thể xóa tài khoản Quản trị viên!');
    }

    $user->is_deleted = true;
    $user->status = 'deleted';
    $user->deleted_at = now()->format('d/m/Y H:i');
    $user->deleted_by = 'Admin';
    $user->save();

    return redirect()->back()->with('success', 'Đã chuyển tài khoản người dùng vào phần Lịch sử!');
});

Route::post('/qlnguoi_dung/lock/{id}', function ($id) {
    $user = \App\Models\User::find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'Không tìm thấy người dùng!');
    }

    $user->is_locked = true;
    $user->status = 'locked';
    $user->locked_at = now()->format('d/m/Y H:i');
    $user->save();

    return redirect()->back()->with('success', 'Đã khóa tài khoản thành công!');
});

Route::get('/sua_giao_dien', function () {
    return view('Admin.sua_giao_dien');
});

Route::post('/sua_giao_dien', function (\Illuminate\Http\Request $request) {
    $settings = $request->only([
        'banner_title',
        'banner_subtitle',
        'banner_cta',
        'banner_url',
        'brand_name',
        'brand_slogan',
        'logo_url',
        'theme_color',
        'footer_phone',
        'footer_email',
        'footer_address',
        'footer_copyright',
        'amenities'
    ]);

    $settingsPath = storage_path('app/theme_settings.json');
    $dir = dirname($settingsPath);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return response()->json([
        'status' => 'success',
        'message' => 'Đã lưu cấu hình giao diện thành công! Giao diện ngoài đã được cập nhật.'
    ]);
});

Route::get('/qltin_tuc', function () {
    return view('Admin.qltin_tuc');
});

Route::get('/yeu_cau_ki_luat', function () {
    return view('Admin.yeu_cau_ki_luat');
});

Route::get('/lich_su', function () {
    $lockedUsers = \App\Models\User::where('is_locked', true)
        ->orWhere('status', 'locked')
        ->orderBy('updated_at', 'desc')
        ->get();

    $deletedUsers = \App\Models\User::where('is_deleted', true)
        ->orWhere('status', 'deleted')
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('Admin.lich_su', compact('lockedUsers', 'deletedUsers'));
});

Route::post('/qlnguoi_dung/unlock/{id}', function ($id) {
    $user = \App\Models\User::find($id);
    if ($user) {
        $user->is_locked = false;
        $user->status = 'active';
        $user->locked_at = null;
        $user->save();
        return redirect()->back()->with('success', "Đã mở khóa thành công cho tài khoản ID: #{$id}!");
    }
    return redirect()->back()->with('error', "Không tìm thấy tài khoản!");
});

Route::post('/qlnguoi_dung/restore/{id}', function ($id) {
    $user = \App\Models\User::find($id);
    if ($user) {
        $user->is_deleted = false;
        $user->status = 'active';
        $user->deleted_at = null;
        $user->deleted_by = null;
        $user->save();
        return redirect()->back()->with('success', "Đã khôi phục thành công tài khoản ID: #{$id} về danh sách Quản lý Người dùng!");
    }
    return redirect()->back()->with('error', "Không tìm thấy tài khoản!");
});

Route::delete('/qlnguoi_dung/force-delete/{id}', function ($id) {
    $user = \App\Models\User::find($id);
    if ($user) {
        $user->delete();
        return redirect()->back()->with('success', "Đã xóa vĩnh viễn tài khoản ID: #{$id} khỏi hệ thống!");
    }
    return redirect()->back()->with('error', "Không tìm thấy tài khoản!");
});

Route::get('/Overview', function(){
    if (!Auth::check()) {
        return redirect()->guest(route('login'));
    }
    return view('qlbai_dang.Overview');
});

Route::get('/dangbai', function(){
    if (!Auth::check()) {
        return redirect()->guest(route('login'));
    }
    return view('qlbai_dang.dangbai');
})->name('dangbai');