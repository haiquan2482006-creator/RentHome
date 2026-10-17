<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('Home');
});

Route::get('/baidang', function (\Illuminate\Http\Request $request) {
    $postId = $request->query('id');
    if ($postId) {
        return redirect()->to('/chitietbaidang/' . $postId);
    }
    return view('baidang');
})->name('baidang');

Route::get('/baidang/{id}', function ($id) {
    return redirect()->to('/chitietbaidang/' . $id);
})->name('baidang.detail');

Route::get('/images/{id}', [\App\Http\Controllers\ImageController::class, 'show'])->name('images.show');
Route::get('/image/{id}', [\App\Http\Controllers\ImageController::class, 'show'])->name('image.serve');

Route::get('/chi-tiet-phong/{id}', function ($id) {
    return redirect()->to('/chitietbaidang/' . $id);
});

Route::get('/chitietbaidang/{id}', function ($id) {
    $post = \App\Models\Post::with('user', 'building')->find($id);
    if (!$post) {
        abort(404, 'Bài đăng không tồn tại hoặc đã bị xóa.');
    }

    $hasContacted = false;
    $contactId = '';
    $contactStatus = '';
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        $contact = \App\Models\Contact::where('post_id', $id)
            ->where('sender_id', \Illuminate\Support\Facades\Auth::id())
            ->first();
            
        if ($contact) {
            $hasContacted = true;
            $contactId = $contact->_id ?? $contact->id;
            $contactStatus = $contact->status ?? '';
        }
    }

    return view('chitietbaidang', compact('post', 'hasContacted', 'contactId', 'contactStatus'));
})->name('post.detail');

Route::post('/bai-dang/{id}/lien-he', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/thong-bao-lien-he', [\App\Http\Controllers\ContactController::class, 'index'])->middleware('auth')->name('contact.index');
Route::get('/lich-su-lien-he', function () {
    return redirect()->route('contact.index');
})->middleware('auth')->name('contact.my_requests');
Route::delete('/contact/{id}/revoke', [\App\Http\Controllers\ContactController::class, 'revoke'])->middleware('auth')->name('contact.revoke');
Route::patch('/contact/{id}/status', [\App\Http\Controllers\ContactController::class, 'updateStatus'])->middleware('auth')->name('contact.updateStatus');

Route::get('/trangcanhanuser', function () {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập trang cá nhân.');
    }
    return view('trangcanhanuser');
})->name('user.dashboard');

Route::get('/doi-mat-khau', function () {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đổi mật khẩu.');
    }
    return view('doi_mat_khau');
})->name('user.change_password');

Route::post('/doi-mat-khau', function (\Illuminate\Http\Request $request) {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện.');
    }

    $request->validate([
        'current_password' => 'required',
        'new_password' => [
            'required',
            'string',
            'min:6',
            'different:current_password',
            'confirmed',
        ],
        'new_password_confirmation' => 'required',
    ], [
        'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
        'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
        'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        'new_password.different' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
        'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        'new_password_confirmation.required' => 'Vui lòng nhập lại mật khẩu mới.',
    ]);

    $user = \Illuminate\Support\Facades\Auth::user();

    if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])->withInput();
    }

    $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Đổi mật khẩu thành công!');
})->name('user.update_password');

Route::post('/trangcanhanuser', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('user.profile.update');
Route::post('/api/user/avatar', [\App\Http\Controllers\UserController::class, 'uploadAvatar'])->name('user.avatar.upload');

Route::get('/Admin', [DashboardController::class, 'index'])->name('admin.overview');

Route::get('/admin', function () {
    return redirect('/Admin');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/Register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/nha_dang_thue', function () {
    return redirect('/nha-dang-thue');
});
Route::get('/nha dang thue', function () {
    return redirect('/nha-dang-thue');
});

Route::get('/nha-dang-thue', function () {
    $currentUser = \Illuminate\Support\Facades\Auth::user();
    $userId = \Illuminate\Support\Facades\Auth::id();

    $rentedContracts = collect();

    if ($currentUser) {
        $rentedContracts = \App\Models\Contract::where(function ($q) use ($currentUser, $userId) {
            $q->where('tenant_id', (string)$userId);
            if (!empty($currentUser->username)) {
                $q->orWhere('tenant_account', $currentUser->username);
            }
            if (!empty($currentUser->phone)) {
                $q->orWhere('tenant_phone', $currentUser->phone);
            }
            if (!empty($currentUser->email)) {
                $q->orWhere('tenant_email', $currentUser->email);
            }
        })
        ->where('status', '!=', 'cancelled')
        ->latest()
        ->get();

        // Nếu chưa tìm thấy theo tenant_id/account/phone, kiểm tra xem có contact đã ký hợp đồng không
        if ($rentedContracts->isEmpty()) {
            $contactPostIds = \App\Models\Contact::where('sender_id', (string)$userId)
                ->whereIn('status', ['đã ký hợp đồng', 'completed'])
                ->pluck('post_id')
                ->filter()
                ->map(fn($id) => (string)$id)
                ->toArray();

            if (!empty($contactPostIds)) {
                $rentedContracts = \App\Models\Contract::whereIn('post_id', $contactPostIds)
                    ->where('status', '!=', 'cancelled')
                    ->latest()
                    ->get();
            }
        }
    }

    // Đảm bảo mỗi hợp đồng đang thuê đều có hóa đơn cọc & tháng đầu tiên
    foreach ($rentedContracts as $contract) {
        $cId = (string)($contract->_id ?? $contract->id);
        $invoice = \App\Models\Invoice::where('contract_id', $cId)->first();
        if ($invoice) {
            $needsSave = false;
            if (empty($invoice->deposit) && !empty($contract->deposit)) {
                $invoice->deposit = (float)$contract->deposit;
                $needsSave = true;
            }
            if (empty($invoice->rent_price) && !empty($contract->rent_price)) {
                $invoice->rent_price = (float)$contract->rent_price;
                $needsSave = true;
            }
            // Đối với hóa đơn kỳ đầu tiên khi mới nhận nhà: điện và nước bằng 0 VNĐ (chỉ ghi nhận chỉ số đầu)
            if (str_contains($invoice->billing_period ?? '', 'Kỳ đầu tiên') || str_contains($invoice->note ?? '', 'đầu tiên')) {
                if ((float)$invoice->electricity_amount != 0 || (float)$invoice->water_amount != 0) {
                    $invoice->electricity_amount = 0;
                    $invoice->water_amount = 0;
                    $servAmount = (float)($contract->internet_price ?? 0) + (float)($contract->garbage_price ?? 0);
                    $invoice->service_amount = $servAmount;
                    $invoice->total_amount = (float)$invoice->deposit + (float)$invoice->rent_price + $servAmount;
                    $needsSave = true;
                }
            }
            if ($needsSave) {
                $invoice->save();
            }
        } else {
            $post = $contract->post ?: \App\Models\Post::find($contract->post_id);
            $depositAmount = (float)($contract->deposit ?? 0);
            $rentPriceAmount = (float)($contract->rent_price ?? ($post->price ?? 0));
            $elecAmount = 0; // Tháng đầu tiên: chỉ số đầu, thành tiền = 0 VNĐ
            $waterAmount = 0; // Tháng đầu tiên: chỉ số đầu, thành tiền = 0 VNĐ
            $servAmount = (float)($contract->internet_price ?? 0) + (float)($contract->garbage_price ?? 0);
            $totAmount = $depositAmount + $rentPriceAmount + $elecAmount + $waterAmount + $servAmount;

            \App\Models\Invoice::create([
                'contract_id'        => $cId,
                'room_id'            => (string)($contract->room_id ?? $contract->post_id),
                'post_id'            => (string)$contract->post_id,
                'owner_id'           => (string)$contract->owner_id,
                'tenant_id'          => (string)($contract->tenant_id ?? $userId),
                'tenant_name'        => $contract->tenant_name ?? ($currentUser->account_name ?? 'Khách thuê'),
                'invoice_code'       => 'INV-' . date('Ym') . '-' . strtoupper(substr(md5($cId), -4)),
                'billing_month'      => date('m/Y'),
                'billing_period'     => 'Kỳ đầu tiên (Cọc & Tháng đầu)',
                'month'              => (int)date('m'),
                'year'               => (int)date('Y'),
                'rent_price'         => $rentPriceAmount,
                'deposit'            => $depositAmount,
                'electricity_amount' => $elecAmount,
                'water_amount'       => $waterAmount,
                'service_amount'     => $servAmount,
                'total_amount'       => $totAmount,
                'status'             => 'unpaid',
                'payment_status'     => 'unpaid',
                'note'               => 'Hóa đơn tiền cọc và tháng đầu tiên khi nhận nhà (Điện nước ghi nhận chỉ số đầu, thành tiền 0 VNĐ)',
            ]);
        }
    }

    $totalRentedCount = $rentedContracts->count();
    $totalRentAmount = $rentedContracts->sum('rent_price');

    // Lấy tất cả hóa đơn của các hợp đồng đang thuê
    $contractIds = $rentedContracts->map(fn($c) => (string)($c->_id ?? $c->id))->toArray();
    $pendingInvoicesCount = 0;
    $pendingInvoicesTotal = 0;
    $pendingInvoices = collect();
    $paidInvoices = collect();
    $allInvoices = collect();

    if (!empty($contractIds)) {
        $allInvoices = \App\Models\Invoice::whereIn('contract_id', $contractIds)
            ->latest()
            ->get();

        $pendingInvoices = $allInvoices->filter(function($inv) {
            $st = strtolower($inv->status ?? '');
            $pst = strtolower($inv->payment_status ?? '');
            return ($st === 'unpaid' || $st === 'pending' || $pst === 'unpaid' || $pst === 'pending') && ($st !== 'paid' && $pst !== 'paid');
        })->values();

        $paidInvoices = $allInvoices->filter(function($inv) {
            $st = strtolower($inv->status ?? '');
            $pst = strtolower($inv->payment_status ?? '');
            return $st === 'paid' || $pst === 'paid';
        })->values();

        $pendingInvoicesCount = $pendingInvoices->count();
        $pendingInvoicesTotal = $pendingInvoices->sum('total_amount');
    }

    return view('nhadangthue.index', compact('rentedContracts', 'totalRentedCount', 'totalRentAmount', 'pendingInvoicesCount', 'pendingInvoicesTotal', 'pendingInvoices', 'paidInvoices', 'allInvoices'));
})->name('nha-dang-thue');

Route::post('/api/invoices/{code}/pay', function ($code) {
    $invoice = \App\Models\Invoice::where('invoice_code', $code)->orWhere('_id', $code)->first();
    if ($invoice) {
        $invoice->status = 'paid';
        $invoice->payment_status = 'paid';
        $invoice->payment_date = now()->format('d/m/Y H:i');
        $invoice->save();
        return response()->json(['success' => true, 'message' => 'Thanh toán hóa đơn thành công!']);
    }
    return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
});

Route::get('/quan-ly-van-hanh', [\App\Http\Controllers\OperationManagementController::class, 'index'])->name('quan-ly-van-hanh');

Route::get('/qlvan_hanh/Room_list', [\App\Http\Controllers\OperationManagementController::class, 'index'])->name('qlvan_hanh.room_list');

Route::get('/api/operation/check-billing-due', [\App\Http\Controllers\OperationManagementController::class, 'checkRealtimeDue'])->name('operation.check_due');

Route::post('/qlvan_hanh/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('contracts.store');

Route::get('/qlvan_hanh/thanh_ly_cong_no', function () {
    return view('qlvan_hanh.thanh_ly_cong_no');
});

Route::get('/thanh-ly-cong-no', function () {
    return view('qlvan_hanh.thanh_ly_cong_no');
})->name('thanh-ly-cong-no');

Route::get('/qlvan_hanh/tao_hoa_don', [\App\Http\Controllers\OperationManagementController::class, 'createInvoice'])->name('qlvan_hanh.create_invoice');
Route::post('/qlvan_hanh/tao_hoa_don', [\App\Http\Controllers\OperationManagementController::class, 'storeInvoice'])->name('qlvan_hanh.store_invoice');
Route::post('/qlvan_hanh/update_payment_status', [\App\Http\Controllers\OperationManagementController::class, 'updatePaymentStatus'])->name('qlvan_hanh.update_payment_status');

Route::get('/tao-hoa-don', [\App\Http\Controllers\OperationManagementController::class, 'createInvoice'])->name('tao-hoa-don');
Route::post('/tao-hoa-don', [\App\Http\Controllers\OperationManagementController::class, 'storeInvoice']);

$handleTaoHopDong = function (\Illuminate\Http\Request $request) {
    $user = \Illuminate\Support\Facades\Auth::user();
    $activeStatuses = ['active', 'approved'];

    $postsQuery = \App\Models\Post::whereIn('status', $activeStatuses)
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        })
        ->where(function ($q) {
            $q->whereNull('is_rented')->orWhere('is_rented', false);
        })
        ->where('status', '!=', 'đã cho thuê')
        ->whereNull('deleted_at');

    if ($user && $user->isSubAccount() && !empty($user->assigned_buildings) && !in_array('all', $user->assigned_buildings, true)) {
        $assignedIds = array_map('strval', (array)$user->assigned_buildings);
        $postsQuery->whereIn('building_id', $assignedIds);
    }

    $posts = $postsQuery->get();

    $contactsQuery = \App\Models\Contact::where('status', '!=', 'đã ký hợp đồng');
    if ($posts->isNotEmpty()) {
        $postIds = $posts->pluck('_id')->map(fn($id) => (string)$id)->toArray();
        $contactsQuery->whereIn('post_id', $postIds);
    }
    $contacts = $contactsQuery->latest()->get();
    if ($contacts->isEmpty()) {
        $contacts = \App\Models\Contact::latest()->get();
    }

    $contactId = $request->query('contact_id');
    $roomId = $request->query('room_id') ?? $request->query('post_id') ?? $request->query('room');

    $selectedContact = null;
    if ($contactId) {
        $selectedContact = \App\Models\Contact::find($contactId);
        if ($selectedContact && !$contacts->contains(fn($c) => (string)($c->_id ?? $c->id) === (string)$contactId)) {
            $contacts->prepend($selectedContact);
        }
        if (!$roomId && $selectedContact && !empty($selectedContact->post_id)) {
            $roomId = $selectedContact->post_id;
        }
    }

    $selectedRoom = null;
    if ($roomId) {
        $selectedRoom = \App\Models\Post::find($roomId);
        if ($selectedRoom && !$posts->contains(fn($p) => (string)($p->_id ?? $p->id) === (string)$roomId)) {
            $posts->prepend($selectedRoom);
        }
    }

    return view('qlvan_hanh.tao_hop_dong', [
        'availableRooms' => $posts,
        'posts' => $posts,
        'contacts' => $contacts,
        'selectedContactId' => $contactId,
        'contact_id' => $contactId,
        'selectedContact' => $selectedContact,
        'selectedRoomId' => $roomId,
        'selectedRoom' => $selectedRoom,
        'room' => $selectedRoom,
    ]);
};

Route::get('/qlvan_hanh/tao_hop_dong', $handleTaoHopDong)->name('qlvan_hanh.tao_hop_dong');
Route::get('/tao-hop-dong', $handleTaoHopDong)->name('tao-hop-dong');




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

    // Ghi nhận hoạt động cấu hình giao diện
    \App\Models\AdminActivity::log(
        'config',
        'system',
        'Cập nhật cấu hình giao diện website',
        'Admin đã cập nhật lại banner trang chủ, logo, màu sắc thương hiệu và thông tin chân trang của RentHome.',
        'theme_settings'
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Đã lưu cấu hình giao diện thành công! Giao diện ngoài đã được cập nhật.'
    ]);
});

// Quản lý Lịch sử hoạt động của Admin
Route::get('/lich_su', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login')->with('error', 'Chỉ tài khoản Quản trị viên mới có quyền truy cập trang Lịch sử.');
    }

    // 1. Toàn bộ hoạt động của Admin (Tài khoản, Tòa nhà, Bài đăng, Hợp đồng, Cấu hình...)
    $activities = \App\Models\AdminActivity::getCompiledActivities();

    // 2. Danh sách tài khoản bị khóa
    $lockedUsers = \App\Models\User::where(function ($q) {
            $q->where('is_locked', true)
              ->orWhere('status', 'locked')
              ->orWhere('status', 'khoa');
        })
        ->orderBy('updated_at', 'desc')
        ->get();

    // 3. Danh sách tài khoản đã xóa
    $deletedUsers = \App\Models\User::where(function ($q) {
            $q->where('is_deleted', true)
              ->orWhere('status', 'deleted')
              ->orWhere('status', 'da_xoa');
        })
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('Admin.lich_su', compact('activities', 'lockedUsers', 'deletedUsers'));
})->name('admin.history');

Route::get('/Admin/lich-su', function () {
    return redirect('/lich_su');
});

// Quản lý Thông báo hệ thống (Admin)
Route::get('/thong_bao', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login')->with('error', 'Chỉ tài khoản Quản trị viên mới có quyền truy cập trang Thông báo.');
    }
    $notifications = \App\Models\Notification::orderBy('created_at', 'desc')->get();
    return view('Admin.thong_bao', compact('notifications'));
})->name('admin.notifications');

Route::get('/Admin/thong-bao', function () {
    return redirect('/thong_bao');
});

Route::post('/Admin/thong-bao/send', function (\Illuminate\Http\Request $request) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login');
    }
    $title = $request->input('title', 'Thông báo chung từ Ban Quản Trị');
    $content = $request->input('content', 'Nội dung thông báo hệ thống');

    // Lưu thông báo thực tế vào hệ thống
    \App\Models\Notification::create([
        'title'      => $title,
        'message'    => $content,
        'type'       => 'info',
        'category'   => 'system',
        'is_read'    => false,
        'created_at' => now(),
    ]);

    // Ghi log hoạt động phát thông báo
    \App\Models\AdminActivity::log(
        'notify',
        'system',
        'Phát thông báo hệ thống toàn sàn',
        "Đã phát thông báo: \"{$title}\" tới toàn bộ người dùng và doanh nghiệp.",
        null,
        ['title' => $title, 'content' => $content]
    );

    return redirect()->back()->with('success', 'Đã phát thông báo thành công đến các tài khoản!');
});

Route::delete('/api/admin/notifications/{id}', function ($id) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    $notif = \App\Models\Notification::where('_id', $id)->orWhere('id', $id)->first();
    if ($notif) {
        $notif->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
})->middleware('auth');

Route::post('/api/admin/notifications/{id}/read', function ($id) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    $notif = \App\Models\Notification::where('_id', $id)->orWhere('id', $id)->first();
    if ($notif) {
        $notif->is_read = true;
        $notif->save();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
})->middleware('auth');

Route::post('/api/admin/notifications/mark-all-read', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    \App\Models\Notification::where('is_read', false)->update(['is_read' => true]);
    return response()->json(['success' => true]);
})->middleware('auth');

Route::post('/qlnguoi_dung/unlock/{id}', function ($id) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login');
    }
    $user = \App\Models\User::where('_id', $id)->orWhere('id', $id)->first();
    if ($user) {
        $user->is_locked = false;
        $user->status = 'active';
        $user->locked_at = null;
        $user->lock_reason = null;
        $user->save();

        // Ghi log hoạt động mở khóa
        $name = $user->account_name ?? $user->username ?? $user->email;
        \App\Models\AdminActivity::log(
            'unlock',
            'account',
            'Mở khóa tài khoản người dùng',
            "Admin đã mở khóa khôi phục quyền truy cập cho tài khoản: {$name} ({$user->email})",
            $user
        );

        return redirect()->back()->with('success', "Đã mở khóa thành công cho tài khoản!");
    }
    return redirect()->back()->with('error', "Không tìm thấy tài khoản!");
});

Route::post('/qlnguoi_dung/restore/{id}', function ($id) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login');
    }
    $user = \App\Models\User::where('_id', $id)->orWhere('id', $id)->first();
    if ($user) {
        $user->is_deleted = false;
        $user->status = 'active';
        $user->deleted_at = null;
        $user->deleted_by = null;
        $user->save();

        // Ghi log hoạt động khôi phục tài khoản
        $name = $user->account_name ?? $user->username ?? $user->email;
        \App\Models\AdminActivity::log(
            'restore',
            'account',
            'Khôi phục tài khoản đã xóa',
            "Admin đã khôi phục lại tài khoản: {$name} ({$user->email}) trở lại trạng thái hoạt động",
            $user
        );

        return redirect()->back()->with('success', "Đã khôi phục thành công tài khoản!");
    }
    return redirect()->back()->with('error', "Không tìm thấy tài khoản!");
});

Route::delete('/qlnguoi_dung/force-delete/{id}', function ($id) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login');
    }
    $user = \App\Models\User::where('_id', $id)->orWhere('id', $id)->first();
    if ($user) {
        $name = $user->account_name ?? $user->username ?? $user->email;
        $email = $user->email;

        // Ghi log hoạt động xóa vĩnh viễn trước khi xóa
        \App\Models\AdminActivity::log(
            'force_delete',
            'account',
            'Xóa vĩnh viễn tài khoản khỏi hệ thống',
            "CẢNH BÁO: Admin đã xóa vĩnh viễn tài khoản: {$name} ({$email}) khỏi cơ sở dữ liệu",
            $user
        );

        $user->delete();
        return redirect()->back()->with('success', "Đã xóa vĩnh viễn tài khoản khỏi hệ thống!");
    }
    return redirect()->back()->with('error', "Không tìm thấy tài khoản!");
});

// Quản lý Tài khoản con (NV) của Doanh nghiệp
Route::get('/qltai_khoan_con', [\App\Http\Controllers\SubAccountController::class, 'viewPage'])->name('admin.sub_accounts');
Route::get('/Admin/tai-khoan-con', [\App\Http\Controllers\SubAccountController::class, 'viewPage']);

// Quản lý Bất Động Sản & Tòa Nhà của Doanh Nghiệp (Admin)
Route::get('/qltoa_nha', [\App\Http\Controllers\BuildingController::class, 'adminView'])->name('admin.buildings');
Route::get('/Admin/toa-nha', [\App\Http\Controllers\BuildingController::class, 'adminView']);

// APIs Quản lý Tài khoản con
Route::get('/api/sub-accounts', [\App\Http\Controllers\SubAccountController::class, 'index'])->name('api.sub_accounts.index');
Route::post('/api/sub-accounts', [\App\Http\Controllers\SubAccountController::class, 'store'])->name('api.sub_accounts.store');
Route::put('/api/sub-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'update'])->name('api.sub_accounts.update');
Route::post('/api/sub-accounts/{id}/toggle-lock', [\App\Http\Controllers\SubAccountController::class, 'toggleLock'])->name('api.sub_accounts.toggle_lock');
Route::delete('/api/sub-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'destroy'])->name('api.sub_accounts.destroy');

// APIs Quản lý Tài khoản Khách hàng Cá nhân
Route::post('/api/customer-accounts/{id}/toggle-lock', [\App\Http\Controllers\SubAccountController::class, 'toggleLockCustomer'])->name('api.customer_accounts.toggle_lock');
Route::delete('/api/customer-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'destroyCustomer'])->name('api.customer_accounts.destroy');

Route::get('/Overview', function (\Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return redirect()->guest(route('login'));
    }
    // Nếu là tài khoản Admin và không yêu cầu tab cụ thể, chuyển đến trang Tổng quan Admin (/Admin)
    if (Auth::user()->role === 'admin' && !$request->has('tab')) {
        return redirect('/Admin');
    }
    // Nếu là tài khoản Kiểm duyệt viên và không yêu cầu tab cụ thể, chuyển đến trang Tổng quan Kiểm duyệt (/kiem-duyet/tong-quan)
    if ((Auth::user()->isModerator() || Auth::user()->role === 'kiemduyet' || Auth::user()->role === 'moderator') && !$request->has('tab')) {
        return redirect('/kiem-duyet/tong-quan');
    }
    return view('qlbai_dang.Overview');
})->name('overview');

// ========================================================
// Routes Kênh Kiểm Duyệt Viên (Moderator)
// ========================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/kiem-duyet', function () {
        return redirect('/kiem-duyet/tong-quan');
    })->name('moderator.home');

    Route::get('/kiem-duyet/tong-quan', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập trang này.');
        }

        // Thống kê bài đăng thực tế từ DB
        $pendingCount = \App\Models\Post::where('status', 'pending')->count();
        $approvedCount = \App\Models\Post::where(function($q) {
            $q->where('status', 'active')->orWhere('status', 'approved');
        })->count();
        $rejectedCount = \App\Models\Post::where(function($q) {
            $q->where('status', 'rejected')->orWhere('status', 'tuchoi');
        })->count();
        $complaintCount = 0;

        $stats = [
            'pending_posts'      => $pendingCount,
            'approved_posts'     => $approvedCount,
            'rejected_posts'     => $rejectedCount,
            'pending_complaints' => $complaintCount,
        ];

        $pendingPosts = \App\Models\Post::where('status', 'pending')->with('user', 'building')->orderBy('created_at', 'desc')->take(10)->get();

        return view('kiem_duyet_vien.tong_quan', compact('stats', 'pendingPosts', 'pendingCount'));
    })->name('moderator.dashboard');

    Route::get('/kiem-duyet/qlbai-dang', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        return view('kiem_duyet_vien.qlbai_dang');
    })->name('moderator.posts');

    Route::get('/kiem-duyet/qlkhieu-nai-cap1', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        return view('kiem_duyet_vien.qlkhieu_nai_cap1');
    })->name('moderator.complaints');

    Route::get('/kiem-duyet/qltin-tuc', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        return view('kiem_duyet_vien.qltin_tuc');
    })->name('moderator.news');

    Route::get('/kiem-duyet/lich-su', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        return view('kiem_duyet_vien.lich_su');
    })->name('moderator.history');

    Route::get('/kiem-duyet/thong-bao', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        return view('kiem_duyet_vien.thong_bao');
    })->name('moderator.notifications');
});

Route::get('/dangbai', function(){
    if (!Auth::check()) {
        return redirect()->guest(route('login'));
    }
    $buildings = \App\Models\Building::orderBy('created_at', 'desc')->get();
    return view('qlbai_dang.dangbai', compact('buildings'));
})->name('dangbai');

// APIs for Posts
Route::post('/api/posts', [\App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
Route::get('/api/posts/approved', [\App\Http\Controllers\PostController::class, 'getApproved'])->name('posts.approved');
Route::get('/api/posts/featured', [\App\Http\Controllers\PostController::class, 'getFeatured'])->name('posts.featured');
Route::post('/api/posts/{id}/unlink', [\App\Http\Controllers\PostController::class, 'unlink'])->middleware('auth')->name('posts.unlink');
Route::delete('/api/posts/{id}/delete', [\App\Http\Controllers\PostController::class, 'destroy'])->middleware('auth')->name('posts.delete');
Route::delete('/api/posts/{id}', [\App\Http\Controllers\PostController::class, 'destroy'])->middleware('auth');
Route::post('/api/posts/{id}/cancel', [\App\Http\Controllers\PostController::class, 'cancel'])->middleware('auth')->name('posts.cancel');

// APIs for Favorites
Route::get('/api/favorites', [\App\Http\Controllers\FavoriteController::class, 'getFavorites'])->name('favorites.list');
Route::post('/api/favorites/toggle', [\App\Http\Controllers\FavoriteController::class, 'toggleFavorite'])->name('favorites.toggle');

// User Dashboard APIs
Route::get('/api/user/posts/stats', [\App\Http\Controllers\UserDashboardController::class, 'getStats']);

// Notification APIs & Routes
Route::get('/api/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->middleware('auth');
Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->middleware('auth');
Route::get('/thong-bao', [\App\Http\Controllers\NotificationController::class, 'index'])->middleware('auth')->name('notifications.index');

Route::get('/api/notifications', [\App\Http\Controllers\UserDashboardController::class, 'getNotifications']);
Route::post('/api/notifications/read', [\App\Http\Controllers\UserDashboardController::class, 'markNotificationsRead']);
Route::delete('/api/notifications/clear-read', [\App\Http\Controllers\UserDashboardController::class, 'clearReadNotifications'])->middleware('auth');
Route::delete('/api/notifications/{id}', [\App\Http\Controllers\UserDashboardController::class, 'deleteNotification'])->middleware('auth');
Route::post('/api/notifications/{id}/mark-read', [\App\Http\Controllers\UserDashboardController::class, 'markNotificationAsRead'])->middleware('auth');
Route::get('/api/user/posts', [\App\Http\Controllers\UserDashboardController::class, 'getPosts']);
Route::get('/api/user/posts/history', [\App\Http\Controllers\UserDashboardController::class, 'getHistory']);

// ========================================================
// APIs for Buildings (Quản lý Tòa nhà - Dành cho Doanh Nghiệp)
// ========================================================
Route::get('/api/buildings', [\App\Http\Controllers\BuildingController::class, 'index'])->name('api.buildings.index');
Route::post('/api/buildings', [\App\Http\Controllers\BuildingController::class, 'store'])->name('api.buildings.store');
Route::get('/api/buildings/{id}', [\App\Http\Controllers\BuildingController::class, 'getDetail'])->name('api.buildings.detail');
Route::put('/api/buildings/{id}', [\App\Http\Controllers\BuildingController::class, 'update'])->name('api.buildings.update');
Route::post('/api/buildings/{id}', [\App\Http\Controllers\BuildingController::class, 'update']);
Route::delete('/api/buildings/{id}', [\App\Http\Controllers\BuildingController::class, 'destroy'])->name('api.buildings.destroy');
Route::get('/api/buildings/{id}/rooms-detail', [\App\Http\Controllers\BuildingController::class, 'getRoomsDetail'])->name('api.buildings.rooms_detail');

// Chi tiết Vận hành Tòa nhà dành cho ADMIN (File: Admin/chi_tiet_toa_nha_admin.blade.php)
Route::get('/Admin/chi-tiet-toa-nha/{id}', [\App\Http\Controllers\BuildingController::class, 'adminDetail'])->name('admin.buildings.operational_detail');
Route::get('/admin/chi-tiet-toa-nha/{id}', [\App\Http\Controllers\BuildingController::class, 'adminDetail']);

// Quản lý Danh sách Bài đăng & Phòng dành cho ĐỐI TÁC / NHÂN VIÊN (File: qlbai_dang/BuildingDetail.blade.php)
Route::get('/quan-ly-toa-nha/{id}', [\App\Http\Controllers\BuildingController::class, 'show'])->name('partner.buildings.room_management');

// APIs for Sub-Accounts (Quản lý tài khoản con của Doanh nghiệp RentHome)
Route::get('/api/sub-accounts', [\App\Http\Controllers\SubAccountController::class, 'index'])->middleware('auth')->name('sub_accounts.index');
Route::post('/api/sub-accounts', [\App\Http\Controllers\SubAccountController::class, 'store'])->middleware('auth')->name('sub_accounts.store');
Route::put('/api/sub-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'update'])->middleware('auth')->name('sub_accounts.update');
Route::delete('/api/sub-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'destroy'])->middleware('auth')->name('sub_accounts.destroy');
Route::post('/api/sub-accounts/{id}/toggle-lock', [\App\Http\Controllers\SubAccountController::class, 'toggleLock'])->middleware('auth')->name('sub_accounts.toggle_lock');

// ========================================================
// RentHome AI Assistant Routes
// ========================================================
Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'sendMessage'])->name('ai.chat');
Route::post('/ai/reset', [\App\Http\Controllers\AiChatController::class, 'resetChat'])->name('ai.reset');