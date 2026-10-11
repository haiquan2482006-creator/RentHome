<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('Home');
});

Route::get('/baidang', function (\Illuminate\Http\Request $request) {
    $postId = $request->query('id');
    if ($postId) {
        $post = \App\Models\Post::with(['user', 'building', 'imageModels'])->where('_id', $postId)->orWhere('id', $postId)->first();
        if ($post) {
            return view('chitiet_phong', compact('post'));
        }
    }
    return view('baidang');
})->name('baidang');

Route::get('/baidang/{id}', function ($id) {
    $post = \App\Models\Post::with(['user', 'building', 'imageModels'])->where('_id', $id)->orWhere('id', $id)->first();
    if ($post) {
        return view('chitiet_phong', compact('post'));
    }
    return redirect('/baidang');
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

    return redirect('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80');
})->name('image.serve');

Route::get('/chi-tiet-phong/{id}', function ($id) {
    return redirect()->to('/baidang?id=' . $id);
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
Route::get('/lich-su-lien-he', [\App\Http\Controllers\ContactController::class, 'myRequests'])->middleware('auth')->name('contact.my_requests');
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
    
    if ($user->account_type === 'doanhnghiep') {
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
        $file = $request->file('avatar');
        $img = \App\Models\Image::create(['base64_data' => base64_encode(file_get_contents($file->getRealPath())), 'mime_type' => $file->getMimeType()]);
        $user->avatar = $img->_id;
    }
    if ($request->hasFile('id_front')) {
        $file = $request->file('id_front');
        $img = \App\Models\Image::create(['base64_data' => base64_encode(file_get_contents($file->getRealPath())), 'mime_type' => $file->getMimeType()]);
        $user->id_front = $img->_id;
    }
    if ($request->hasFile('id_back')) {
        $file = $request->file('id_back');
        $img = \App\Models\Image::create(['base64_data' => base64_encode(file_get_contents($file->getRealPath())), 'mime_type' => $file->getMimeType()]);
        $user->id_back = $img->_id;
    }
    if ($request->hasFile('business_license')) {
        $file = $request->file('business_license');
        $img = \App\Models\Image::create(['base64_data' => base64_encode(file_get_contents($file->getRealPath())), 'mime_type' => $file->getMimeType()]);
        $user->business_license = $img->_id;
    }

    if ($request->has('tax_code')) {
        $user->tax_code = $request->input('tax_code');
    }
    
    if ($request->has('address')) {
        $user->address = $request->input('address');
        if ($user->account_type === 'doanhnghiep') {
            $user->company_address = $request->input('address');
        }
    }
    
    $user->save();
    
    return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
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

    $totalPosts = \App\Models\Post::count();
    $totalViolations = \App\Models\User::where('is_locked', true)->orWhere('status', 'locked')->count();
    $totalViews = \App\Models\Post::sum('views') ?? 0;

    return view('Admin.Overview', compact('recentUsers', 'totalUsers', 'totalPosts', 'totalViolations', 'totalViews'));
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/Register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/quen-mat-khau', function () {
    return view('dangnhapdk.quyen_mat_khau_otp'); 
});

Route::get('/dat-lai-mat-khau', function () {
    return view('dangnhapdk.quen_mat_khau_xac_nhan'); 
});

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

Route::get('/qlvan_hanh/tao_hop_dong', function (\Illuminate\Http\Request $request) {
    $userId = \Illuminate\Support\Facades\Auth::id();
    $activeStatuses = ['active', 'approved'];

    $postsQuery = \App\Models\Post::whereIn('status', $activeStatuses)
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        })
        ->whereNull('deleted_at');

    if ($userId) {
        $userPosts = (clone $postsQuery)->where('user_id', $userId)->get();
        $posts = $userPosts->isNotEmpty() ? $userPosts : $postsQuery->get();
    } else {
        $posts = $postsQuery->get();
    }

    $contacts = $userId ? \App\Models\Contact::where('owner_id', $userId)->latest()->get() : collect();
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
});

Route::get('/tao-hop-dong', function (\Illuminate\Http\Request $request) {
    $userId = \Illuminate\Support\Facades\Auth::id();
    $activeStatuses = ['active', 'approved'];

    $postsQuery = \App\Models\Post::whereIn('status', $activeStatuses)
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        })
        ->whereNull('deleted_at');

    if ($userId) {
        $userPosts = (clone $postsQuery)->where('user_id', $userId)->get();
        $posts = $userPosts->isNotEmpty() ? $userPosts : $postsQuery->get();
    } else {
        $posts = $postsQuery->get();
    }

    $contacts = $userId ? \App\Models\Contact::where('owner_id', $userId)->latest()->get() : collect();
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
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('representative', 'like', "%{$search}%");
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
    $buildings = \App\Models\Building::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
    return view('qlbai_dang.dangbai', compact('buildings'));
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
    $complaints = \App\Models\Complaint::with(['user', 'post'])->orderBy('created_at', 'desc')->get();
    return view('Moderator.qlkhieunai', compact('complaints'));
});

Route::get('/moderator/lichsu', function () {
    return view('Moderator.lichsu');
});

Route::get('/moderator/chitiet-nguoidung/{id}', function ($id) {
    // 1. Tìm thông tin User THỰC TẾ trong Database
    $targetUser = \App\Models\User::find($id);
    
    if (!$targetUser) {
        return redirect()->back()->with('error', 'Không tìm thấy tài khoản người dùng!');
    }

    // 2. GIỮ LẠI THÔNG TIN ẢO CHỜ PHÁT TRIỂN (Dùng toán tử ?? để nếu DB chưa có thì lấy dữ liệu ảo)
    $targetUser->phone_verified = $targetUser->phone_verified ?? true;
    $targetUser->cccd_verified = $targetUser->cccd_verified ?? true;
    $targetUser->cccd_number = $targetUser->cccd_number ?? '079201089921';
    $targetUser->cccd_front = $targetUser->cccd_front ?? 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=600&q=80';
    $targetUser->cccd_back = $targetUser->cccd_back ?? 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80';

    $targetUser->is_business = true; 
    $targetUser->company_name = $targetUser->company_name ?? 'Công ty TNHH Bất Động Sản TakiManh Việt Nam';
    $targetUser->tax_code = $targetUser->tax_code ?? '0316988231';
    $targetUser->license_image = $targetUser->license_image ?? 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80';
    $targetUser->bank_name = $targetUser->bank_name ?? 'Ngân hàng TMCP Quân Đội (MB Bank)';
    $targetUser->bank_account_no = $targetUser->bank_account_no ?? '999988886666';
    $targetUser->bank_account_holder = $targetUser->bank_account_holder ?? 'CONG TY TNHH BDS TAKIMANH VIET NAM';

    $targetUser->complaints_count = $targetUser->complaints_count ?? 0;
    $targetUser->warnings_count = $targetUser->warnings_count ?? 0;
    $targetUser->reputation_score = $targetUser->reputation_score ?? 98;
    
    // 3. LẤY DỮ LIỆU TÒA NHÀ THẬT TỪ DATABASE
    // Móc những tòa nhà do chính user ($id) này tạo ra
    $buildings = \App\Models\Building::where('user_id', $id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

    // 4. LẤY DỮ LIỆU BÀI ĐĂNG THẬT TỪ DATABASE
    // Móc những bài đăng do chính user ($id) này tạo ra
    $posts = \App\Models\Post::where('user_id', $id)
                             ->with('imageModels')
                             ->orderBy('created_at', 'desc')
                             ->get();

    // Truyền tất cả ra giao diện
    return view('Moderator.chitiet_nguoidung', compact('targetUser', 'posts', 'buildings'));
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
    $action = $request->input('action', 'reply');
    
    $complaint = \App\Models\Complaint::find($id);
    if ($complaint) {
        $complaint->status = 'resolved';
        $complaint->admin_response = $response;
        $complaint->save();

        if ($action === 'accept' && $complaint->post_id) {
            $post = \App\Models\Post::find($complaint->post_id);
            if ($post) {
                $post->status = 'approved';
                $post->save();
            }
        }

        if ($complaint->user_id) {
            \App\Models\Notification::create([
                'user_id' => $complaint->user_id,
                'post_id' => $complaint->post_id,
                'title' => 'Phản hồi khiếu nại',
                'message' => 'Quản trị viên đã phản hồi khiếu nại của bạn về bài đăng #' . substr($complaint->post_id, -4) . '. Nội dung: ' . $response,
                'type' => 'info',
                'category' => 'system',
                'is_read' => false
            ]);
        }
    }
    
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['success' => true, 'message' => "Đã xử lý khiếu nại thành công!"]);
    }
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

Route::get('/moderator/toa-nha/{id}', function ($id) {
    $building = \App\Models\Building::find($id);
    
    if (!$building) {
        abort(404, 'Không tìm thấy tòa nhà.');
    }

    // Truy vấn danh sách các bài đăng (phòng) thuộc Tòa nhà đó
    $posts = \App\Models\Post::where('building_id', $id)->paginate(10);

    return view('qlbai_dang.BuildingDetail', compact('building', 'posts'));
})->name('moderator.building_detail');

// APIs for Posts
Route::post('/api/posts', [\App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
Route::get('/api/posts/approved', [\App\Http\Controllers\PostController::class, 'getApproved'])->name('posts.approved');
Route::get('/api/posts/featured', [\App\Http\Controllers\PostController::class, 'getFeatured'])->name('posts.featured');

// APIs for Favorites
Route::get('/api/favorites', [\App\Http\Controllers\FavoriteController::class, 'getFavorites'])->name('favorites.list');
Route::post('/api/favorites/toggle', [\App\Http\Controllers\FavoriteController::class, 'toggleFavorite'])->name('favorites.toggle');

// APIs for Complaints
Route::post('/api/complaints', [\App\Http\Controllers\ComplaintController::class, 'store'])->name('complaints.store');

// APIs for Moderator
Route::get('/api/moderator/posts/pending', [\App\Http\Controllers\ModeratorController::class, 'getPending'])->name('moderator.posts.pending');
Route::post('/api/moderator/posts/{id}/approve', [\App\Http\Controllers\ModeratorController::class, 'approve'])->name('moderator.posts.approve');
Route::post('/api/moderator/posts/{id}/reject', [\App\Http\Controllers\ModeratorController::class, 'reject'])->name('moderator.posts.reject');
Route::get('/api/moderator/posts/history', [\App\Http\Controllers\ModeratorController::class, 'getHistory'])->name('moderator.posts.history');
Route::post('/api/moderator/posts/{id}/restore', [\App\Http\Controllers\ModeratorController::class, 'restore'])->name('moderator.posts.restore');

// User Dashboard APIs
Route::get('/api/user/posts/stats', [\App\Http\Controllers\UserDashboardController::class, 'getStats']);

// Notification APIs & Routes
Route::get('/api/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->middleware('auth');
Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->middleware('auth');
Route::get('/thong-bao', [\App\Http\Controllers\NotificationController::class, 'index'])->middleware('auth')->name('notifications.index');

Route::get('/api/notifications', [\App\Http\Controllers\UserDashboardController::class, 'getNotifications']);
Route::post('/api/notifications/read', [\App\Http\Controllers\UserDashboardController::class, 'markNotificationsRead']);
Route::get('/api/user/posts', [\App\Http\Controllers\UserDashboardController::class, 'getPosts']);
Route::get('/api/user/posts/history', [\App\Http\Controllers\UserDashboardController::class, 'getHistory']);
// ========================================================
// APIs for Buildings (Quản lý Tòa nhà - Dành cho Doanh Nghiệp)
// ========================================================
Route::get('/api/buildings', [\App\Http\Controllers\BuildingController::class, 'index']);
Route::post('/api/buildings', [\App\Http\Controllers\BuildingController::class, 'store']);
Route::get('/quan-ly-toa-nha/{id}', [\App\Http\Controllers\BuildingController::class, 'show']);

// ========================================================
// RentHome AI Assistant Routes
// ========================================================
Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'sendMessage'])->name('ai.chat');
Route::post('/ai/reset', [\App\Http\Controllers\AiChatController::class, 'resetChat'])->name('ai.reset');