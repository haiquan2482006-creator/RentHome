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

// Các route POST xử lý logic gửi email và xác nhận
Route::post('/send-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'verifyCode']);
Route::post('/dat-lai-mat-khau', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword']);

Route::get('/qlnguoi_dung', function (\Illuminate\Http\Request $request) {
    $search = trim($request->query('search', ''));
    $accountType = $request->query('account_type', '');

    $query = \App\Models\User::where('role', '!=', 'admin')
        ->where('account_type', '!=', 'admin');

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
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
    $totalUsers = \App\Models\User::where('role', '!=', 'admin')->where('account_type', '!=', 'admin')->count();

    return view('Admin.qlnguoi_dung', compact('users', 'search', 'accountType', 'totalUsers'));
});

Route::get('/sua_giao_dien', function () {
    return view('Admin.sua_giao_dien');
});

Route::get('/qltin_tuc', function () {
    return view('Admin.qltin_tuc');
});

Route::get('/yeu_cau_ki_luat', function () {
    return view('Admin.yeu_cau_ki_luat');
});

Route::get('/lich_su', function () {
    return view('Admin.lich_su');
});

