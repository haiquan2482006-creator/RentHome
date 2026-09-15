<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('Home');
});

Route::get('/Admin', function (){
    $recentUsers = \App\Models\User::where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'moderator');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'moderator');
        })
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    $totalUsers = \App\Models\User::where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'moderator');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'moderator');
        })
        ->count();

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

Route::get('/qlvan_hanh/tao_hoa_don', function () {
    return view('qlvan_hanh.tao_hoa_don');
});

Route::get('/tao-hoa-don', function () {
    return view('qlvan_hanh.tao_hoa_don');
})->name('tao-hoa-don');

Route::get('/qlvan_hanh/tao_hop_dong', function () {
    return view('qlvan_hanh.tao_hop_dong');
});

Route::get('/tao-hop-dong', function () {
    return view('qlvan_hanh.tao_hop_dong');
})->name('tao-hop-dong');


// Các route POST xử lý logic gửi email và xác nhận
Route::post('/send-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'verifyCode']);
Route::post('/dat-lai-mat-khau', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword']);

Route::get('/qlnguoi_dung', function (\Illuminate\Http\Request $request) {
    $search = trim($request->query('search', ''));
    $accountType = $request->query('account_type', '');

    $query = \App\Models\User::where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'moderator');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'moderator');
        })
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
    $totalUsers = \App\Models\User::where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'admin');
        })
        ->where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'moderator');
        })
        ->where(function ($q) {
            $q->whereNull('account_type')->orWhere('account_type', '!=', 'moderator');
        })
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

// Routes dành cho Kiểm duyệt viên (Moderator)
Route::get('/Moderator', function () {
    return view('Moderator.Overview');
})->name('moderator.overview');

Route::get('/moderator', function () {
    return redirect('/Moderator');
});

Route::get('/moderator/qlpheduyet', function () {
    return view('Moderator.qlpheduyet');
});

Route::get('/moderator/qlnguoidung', function () {
    $search = trim(request('search', ''));
    $query = \App\Models\User::where(function($q) {
        $q->whereNull('role')->orWhere('role', '!=', 'admin');
    })->where(function($q) {
        $q->whereNull('role')->orWhere('role', '!=', 'moderator');
    });

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('account_name', 'like', "%{$search}%");
        });
    }

    $users = $query->orderBy('created_at', 'desc')->paginate(15);
    return view('Moderator.qlnguoidung', compact('users', 'search'));
});

Route::get('/moderator/qlkhieunai', function () {
    return view('Moderator.qlkhieunai');
});

Route::get('/moderator/lichsu', function () {
    return view('Moderator.lichsu');
});

Route::get('/moderator/chitiet-nguoidung/{id}', function ($id) {
    $targetUser = \App\Models\User::find($id);
    if (!$targetUser) {
        $targetUser = (object)[
            'id' => $id,
            'username' => 'takimanh531',
            'account_name' => 'Công Ty TNHH BĐS TakiManh',
            'email' => "takimanh531@gmail.com",
            'phone' => '06524543456',
            'account_type' => 'Doanhnghiep',
            'created_at' => '10/09/2026',
        ];
    }

    // Gắn thông tin mở rộng cho tài khoản (xác thực, giấy tờ doanh nghiệp, ngân hàng)
    $targetUser->phone_verified = true;
    $targetUser->cccd_verified = true;
    $targetUser->cccd_number = '079201089921';
    $targetUser->cccd_front = 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=600&q=80';
    $targetUser->cccd_back = 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80';

    // Thông tin Doanh nghiệp & Tài khoản Ngân hàng
    $isBusiness = strtolower($targetUser->account_type ?? '') === 'doanhnghiep' || strtolower($targetUser->account_type ?? '') === 'doanh nghiệp';
    $targetUser->is_business = true; // Luôn hiển thị dữ liệu demo kinh doanh cho môi trường kiểm thử
    $targetUser->company_name = 'Công ty TNHH Bất Động Sản TakiManh Việt Nam';
    $targetUser->tax_code = '0316988231';
    $targetUser->license_image = 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80';
    $targetUser->bank_name = 'Ngân hàng TMCP Quân Đội (MB Bank)';
    $targetUser->bank_account_no = '999988886666';
    $targetUser->bank_account_holder = 'CONG TY TNHH BDS TAKIMANH VIET NAM';

    // Thống kê lịch sử & độ uy tín
    $targetUser->complaints_count = 0;
    $targetUser->warnings_count = 0;
    $targetUser->reputation_score = 98; // 98/100 uy tín
    
    $posts = [
        (object)[
            'id' => 201,
            'title' => 'Cho thuê căn hộ studio đầy đủ nội thất cao cấp trung tâm Quận 1',
            'address' => 'Đường Nguyễn Trãi, Quận 1, TP.HCM',
            'price' => '8,500,000 VNĐ / tháng',
            'thumb' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=400&q=80',
            'created_at' => '10:30 - 12/09/2026',
            'status' => 'active',
            'status_label' => 'Đang hiển thị'
        ],
        (object)[
            'id' => 202,
            'title' => 'Phòng trọ rộng 25m2 có ban công thoáng mát gần đại học Y Dược',
            'address' => 'Đường An Dương Vương, Quận 5, TP.HCM',
            'price' => '4,200,000 VNĐ / tháng',
            'thumb' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=400&q=80',
            'created_at' => '15:45 - 08/09/2026',
            'status' => 'active',
            'status_label' => 'Đang hiển thị'
        ],
        (object)[
            'id' => 203,
            'title' => 'Nhà nguyên căn 2 tầng thích hợp ở gia đình hoặc làm văn phòng nhỏ',
            'address' => 'Đường Cách Mạng Tháng 8, Quận 3, TP.HCM',
            'price' => '16,000,000 VNĐ / tháng',
            'thumb' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=400&q=80',
            'created_at' => '09:15 - 01/09/2026',
            'status' => 'pending',
            'status_label' => 'Chờ duyệt'
        ]
    ];

    return view('Moderator.chitiet_nguoidung', compact('targetUser', 'posts'));
})->name('moderator.user_detail');

Route::post('/moderator/approve-post/{id}', function ($id) {
    return redirect()->back()->with('success', "Đã phê duyệt thành công bài đăng ID: #{$id}!");
});

Route::post('/moderator/reject-post/{id}', function (\Illuminate\Http\Request $request, $id) {
    $reason = $request->input('reason', 'Bài đăng không đạt yêu cầu tiêu chuẩn.');
    return redirect()->back()->with('success', "Đã từ chối bài đăng ID: #{$id}. Lý do: {$reason}");
});

Route::post('/moderator/remove-post/{id}', function (\Illuminate\Http\Request $request, $id) {
    $reason = $request->input('reason', 'Vi phạm quy định tin đăng.');
    return redirect()->back()->with('success', "Đã gỡ bài đăng ID: #{$id} thành công! (Lý do: {$reason})");
});

Route::post('/moderator/respond-complaint/{id}', function (\Illuminate\Http\Request $request, $id) {
    $response = $request->input('response_content', 'Đã ghi nhận và xử lý khiếu nại.');
    return redirect()->back()->with('success', "Đã gửi phản hồi cho khiếu nại ID: #{$id} thành công!");
});

Route::post('/moderator/user-warn/{id}', function (\Illuminate\Http\Request $request, $id) {
    $warningMsg = $request->input('warning_message', 'Nhắc nhở tuân thủ quy định đăng tin trên RentHome.');
    return redirect()->back()->with('success', "Đã gửi thông báo cảnh báo vi phạm tới tài khoản ID: #{$id}! (Nội dung: {$warningMsg})");
});

Route::post('/moderator/user-request-verify/{id}', function (\Illuminate\Http\Request $request, $id) {
    $note = $request->input('verify_note', 'Yêu cầu cập nhật hình ảnh CCCD/Giấy phép kinh doanh chính chủ.');
    return redirect()->back()->with('success', "Đã gửi yêu cầu bắt buộc cập nhật thông tin xác thực tới tài khoản ID: #{$id}!");
});

Route::post('/moderator/user-ban/{id}', function (\Illuminate\Http\Request $request, $id) {
    $banReason = $request->input('ban_reason', 'Khóa tài khoản do vi phạm tiêu chuẩn cộng đồng.');
    return redirect()->back()->with('success', "ĐÃ KHÓA TÀI KHOẢN ID: #{$id} THÀNH CÔNG! Lý do lưu hệ thống: {$banReason}");
});