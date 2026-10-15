<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::get('/image/{id}', function ($id) {
    if (strlen($id) !== 24 || !ctype_xdigit($id)) {
        abort(404);
    }

    $cacheDir = storage_path('app/public/image_cache');
    $cacheFile = $cacheDir . '/' . $id;

    if (file_exists($cacheFile)) {
        $mimeFile = $cacheFile . '.mime';
        $mime = file_exists($mimeFile) ? trim(file_get_contents($mimeFile)) : 'image/jpeg';
        return response()->file($cacheFile, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    if (class_exists(\App\Models\Image::class)) {
        $img = \App\Models\Image::find($id);
        if ($img && !empty($img->base64_data)) {
            $binary = base64_decode($img->base64_data);
            $mime = $img->mime_type ?: 'image/jpeg';

            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }
            @file_put_contents($cacheFile, $binary);
            @file_put_contents($cacheFile . '.mime', $mime);

            return response($binary, 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }
    }

    return redirect('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80');
})->name('image.serve');

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

Route::post('/trangcanhanuser', function (\Illuminate\Http\Request $request) {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện.');
    }
    
    $user = \Illuminate\Support\Facades\Auth::user();
    
    if ($user->isEnterprise() || $user->role === 'doanhnghiep') {
        if ($request->has('representative')) {
            $rep = $request->input('representative');
            $user->representative = $rep;
            $user->account_name = $rep; // Người đại diện là tên của tài khoản doanh nghiệp
        }
        if ($request->has('company_name')) {
            $user->company_name = $request->input('company_name');
        }
        if ($request->has('business_license_date')) {
            $user->business_license_date = $request->input('business_license_date');
        }
        if ($request->has('representative_id_card')) {
            $user->representative_id_card = $request->input('representative_id_card');
            $user->cccd_number = $request->input('representative_id_card');
        }
        if ($request->has('representative_id_date')) {
            $user->representative_id_date = $request->input('representative_id_date');
        }
        if ($request->has('representative_id_place')) {
            $user->representative_id_place = $request->input('representative_id_place');
        }
        if ($request->has('website')) {
            $user->website = $request->input('website');
        }
        if ($request->has('hotline')) {
            $user->hotline = $request->input('hotline');
        }
        if ($request->has('company_description')) {
            $user->company_description = $request->input('company_description');
        }
    } else {
        if ($request->has('account_name')) {
            $user->account_name = $request->input('account_name');
        }
    }
    if ($request->has('phone')) {
        $user->phone = $request->input('phone');
    }
    if ($request->has('bank_name')) {
        $user->bank_name = $request->input('bank_name');
    }
    if ($request->has('bank_account')) {
        $user->bank_account = $request->input('bank_account');
    }
    if ($request->has('bank_owner')) {
        $user->bank_owner = $request->input('bank_owner');
    }

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = '/storage/' . $path;
    }
    if ($request->hasFile('id_front')) {
        $path = $request->file('id_front')->store('documents', 'public');
        $user->id_front = '/storage/' . $path;
    }
    if ($request->hasFile('id_back')) {
        $path = $request->file('id_back')->store('documents', 'public');
        $user->id_back = '/storage/' . $path;
    }
    if ($request->hasFile('business_license')) {
        $path = $request->file('business_license')->store('documents', 'public');
        $user->business_license = '/storage/' . $path;
    }

    if ($request->has('tax_code')) {
        $user->tax_code = $request->input('tax_code');
    }
    
    if ($request->has('address')) {
        $user->address = $request->input('address');
        if ($user->isEnterprise() || $user->role === 'doanhnghiep') {
            $user->company_address = $request->input('address');
        }
    }
    
    $user->save();
    
    return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
});

Route::get('/Admin', function () {
    if (!Auth::check()) {
        return redirect()->guest(route('login'));
    }
    $user = Auth::user();
    if ($user->role !== 'admin') {
        if ($user->role === 'doanhnghiep' || $user->isEnterprise() || $user->isSubAccount()) {
            return redirect('/Overview');
        }
        return redirect('/')->with('error', 'Bạn không có quyền truy cập trang Quản trị Admin!');
    }

    $recentUsers = \App\Models\User::where('role', '!=', 'admin')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    $totalUsers = \App\Models\User::where('role', '!=', 'admin')->count();
    $totalPosts = \App\Models\Post::count();
    $totalViolations = \App\Models\User::where('is_locked', true)->orWhere('status', 'locked')->count();
    $totalViews = \App\Models\Post::sum('views') ?? 0;

    return view('Admin.Overview', compact('recentUsers', 'totalUsers', 'totalPosts', 'totalViolations', 'totalViews'));
})->name('admin.overview');

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

    return response()->json([
        'status' => 'success',
        'message' => 'Đã lưu cấu hình giao diện thành công! Giao diện ngoài đã được cập nhật.'
    ]);
});

// Quản lý Lịch sử tài khoản (Admin)
Route::get('/lich_su', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login')->with('error', 'Chỉ tài khoản Quản trị viên mới có quyền truy cập trang Lịch sử.');
    }
    $lockedUsers = \App\Models\User::where(function ($q) {
            $q->where('is_locked', true)
              ->orWhere('status', 'locked')
              ->orWhere('status', 'khoa');
        })
        ->orderBy('updated_at', 'desc')
        ->get();

    $deletedUsers = \App\Models\User::where(function ($q) {
            $q->where('is_deleted', true)
              ->orWhere('status', 'deleted')
              ->orWhere('status', 'da_xoa');
        })
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('Admin.lich_su', compact('lockedUsers', 'deletedUsers'));
})->name('admin.history');

Route::get('/Admin/lich-su', function () {
    return redirect('/lich_su');
});

// Quản lý Thông báo hệ thống (Admin)
Route::get('/thong_bao', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login')->with('error', 'Chỉ tài khoản Quản trị viên mới có quyền truy cập trang Thông báo.');
    }
    return view('Admin.thong_bao');
})->name('admin.notifications');

Route::get('/Admin/thong-bao', function () {
    return redirect('/thong_bao');
});

Route::post('/Admin/thong-bao/send', function (\Illuminate\Http\Request $request) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect('/login');
    }
    return redirect()->back()->with('success', 'Đã phát thông báo thành công đến các tài khoản!');
});

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
    return view('qlbai_dang.Overview');
})->name('overview');

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
Route::get('/quan-ly-toa-nha/{id}', [\App\Http\Controllers\BuildingController::class, 'show']);

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