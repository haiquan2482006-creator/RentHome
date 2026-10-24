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
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem lịch sử liên hệ.');
    }

    $currentUser = \Illuminate\Support\Facades\Auth::user();
    $userId = (string)\Illuminate\Support\Facades\Auth::id();

    $myContacts = \App\Models\Contact::with(['post'])
        ->where(function ($q) use ($userId, $currentUser) {
            $q->where('sender_id', $userId);
            if (!empty($currentUser->email)) {
                $q->orWhere('email', $currentUser->email);
            }
            if (!empty($currentUser->phone)) {
                $q->orWhere('phone', $currentUser->phone);
            }
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('lich_su_lien_he', compact('myContacts'));
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
    $terminatedContracts = collect();

    if ($currentUser) {
        $contractsBaseQuery = \App\Models\Contract::where(function ($q) use ($currentUser, $userId) {
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
        });

        $rentedContracts = (clone $contractsBaseQuery)
            ->whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
            ->latest()
            ->get();

        $terminatedContracts = (clone $contractsBaseQuery)
            ->whereIn('status', ['terminated', 'da_thanh_ly'])
            ->latest()
            ->get();

        // Nếu chưa tìm thấy theo tenant_id/account/phone, kiểm tra xem có contact đã ký hợp đồng không
        if ($rentedContracts->isEmpty()) {
            $contactPostIds = \App\Models\Contact::where('sender_id', (string)$userId)
                ->whereIn('status', ['đã ký hợp đồng', 'completed', 'approved'])
                ->pluck('post_id')
                ->filter()
                ->map(fn($id) => (string)$id)
                ->toArray();

            if (!empty($contactPostIds)) {
                $rentedContracts = \App\Models\Contract::whereIn('post_id', $contactPostIds)
                    ->whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                    ->latest()
                    ->get();

                if ($terminatedContracts->isEmpty()) {
                    $terminatedContracts = \App\Models\Contract::whereIn('post_id', $contactPostIds)
                        ->whereIn('status', ['terminated', 'da_thanh_ly'])
                        ->latest()
                        ->get();
                }
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
            return ($st === 'unpaid' || $st === 'pending' || $st === 'pending_approval' || $pst === 'unpaid' || $pst === 'pending' || $pst === 'pending_approval') && ($st !== 'paid' && $pst !== 'paid');
        })->values();

        $paidInvoices = $allInvoices->filter(function($inv) {
            $st = strtolower($inv->status ?? '');
            $pst = strtolower($inv->payment_status ?? '');
            return $st === 'paid' || $pst === 'paid';
        })->values();

        $pendingInvoicesCount = $pendingInvoices->count();
        $pendingInvoicesTotal = $pendingInvoices->sum('total_amount');
    }

    return view('nhadangthue.index', compact('rentedContracts', 'totalRentedCount', 'totalRentAmount', 'pendingInvoicesCount', 'pendingInvoicesTotal', 'pendingInvoices', 'paidInvoices', 'allInvoices', 'terminatedContracts'));
})->name('nha-dang-thue');

Route::post('/api/invoices/{code}/pay', function ($code, \Illuminate\Http\Request $request) {
    $cleanCode = trim($code);
    $invoice = \App\Models\Invoice::where('invoice_code', $cleanCode)->orWhere('_id', $cleanCode)->orWhere('id', $cleanCode)->first();
    if ($invoice) {
        // KHÓA AN TOÀN: Nếu khách đã chuyển khoản và đang ở trạng thái pending_approval -> Không cho phép thu tiền mặt
        if ($invoice->status === 'pending_approval' || $invoice->payment_status === 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn này khách đã chuyển khoản và đang chờ Kế toán duyệt. Không thể thu tiền mặt!'
            ], 400);
        }

        $now = now();
        $method = $request->input('payment_method', 'cash');
        $invoice->status = 'paid';
        $invoice->payment_status = 'paid';
        $invoice->payment_date = $now->format('d/m/Y H:i');
        $invoice->payment_method = $method;
        if ($method === 'cash') {
            $invoice->collected_by = (string)(\Illuminate\Support\Facades\Auth::id() ?? $invoice->created_by ?? $invoice->staff_id);
            $invoice->is_remitted_to_admin = false;
        } else {
            $invoice->is_remitted_to_admin = true;
        }
        $invoice->save();

        // Tự động kích hoạt (Active) hợp đồng nếu đang ở trạng thái pending_deposit/pending_payment
        $contractId = (string)($invoice->contract_id ?? '');
        $contract = null;
        if ($contractId) {
            $contract = \App\Models\Contract::where('_id', $contractId)
                ->orWhere('id', $contractId)
                ->orWhere('contract_code', $contractId)
                ->first();
        }

        if ($contract) {
            $isLiquidationInvoice = (
                $invoice->type === 'final_settlement' || 
                $invoice->invoice_type === 'liquidation' || 
                str_starts_with((string)$invoice->invoice_code, 'INV-LIQ-')
            );

            if ($isLiquidationInvoice || $contract->status === 'pending_liquidation') {
                $liqController = new \App\Http\Controllers\ContractLiquidationController();
                $liqController->executeTermination(
                    $contract,
                    \Illuminate\Support\Facades\Auth::user(),
                    "Khách đã hoàn tất thanh toán hóa đơn chốt công nợ thanh lý {$invoice->invoice_code}."
                );

                return response()->json([
                    'success'         => true,
                    'message'         => 'Xác nhận thu tiền thành công! Hợp đồng đã hoàn tất thanh lý (terminated) và phòng đã chuyển sang trạng thái trống (available).',
                    'contract_status' => 'terminated',
                ]);
            }

            $contract->payment_status = 'paid';
            $contract->last_paid_period = $invoice->billing_month ?: $now->format('m/Y');
            $contract->last_paid_date = $now->toDateTimeString();
            if ($contract->status === 'pending_deposit' || $contract->status === 'pending_payment' || $contract->status === 'pending') {
                $contract->status = 'active'; // Hợp đồng chính thức Active!
            }
            $contract->save();

            // Gửi thông báo cho khách thuê nếu có tài khoản liên kết
            if (!empty($contract->tenant_id)) {
                \App\Models\Notification::create([
                    'user_id'  => (string)$contract->tenant_id,
                    'post_id'  => (string)($contract->post_id ?? ''),
                    'title'    => 'Hợp đồng thuê nhà đã chính thức Kích Hoạt!',
                    'message'  => 'Hóa đơn mã ' . $invoice->invoice_code . ' đã được thanh toán thành công. Hợp đồng mã ' . $contract->contract_code . ' đã chính thức có hiệu lực và phòng sẵn sàng giao chìa khóa!',
                    'type'     => 'success',
                    'is_read'  => false,
                    'category' => 'contract',
                ]);
            }
        }

        return response()->json([
            'success'         => true,
            'message'         => 'Xác nhận thu tiền thành công! Hợp đồng đã chính thức được Kích Hoạt.',
            'contract_status' => $contract ? $contract->status : 'active',
        ]);
    }
    return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
});

// API danh sách hóa đơn tiền mặt nhân viên đang giữ (chưa nộp Kế toán)
Route::get('/api/staff/my-cash-debts', function () {
    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
    }
    $user = Auth::user();
    $userId = (string)($user->_id ?? $user->id);

    $invoices = \App\Models\Invoice::where(function ($q) use ($userId) {
            $q->where('collected_by', $userId)
              ->orWhere('created_by', $userId);
        })
        ->where('payment_method', 'cash')
        ->where(function ($q) {
            $q->whereNull('is_remitted_to_admin')
              ->orWhere('is_remitted_to_admin', false)
              ->orWhere('is_remitted_to_admin', 0);
        })
        ->where(function ($q) {
            $q->where('status', 'paid')
              ->orWhere('payment_status', 'paid');
        })
        ->latest('updated_at')
        ->get();

    $totalAmount = $invoices->sum(fn($i) => (float)($i->total_amount ?? 0));

    $formatted = $invoices->map(function ($inv) {
        $post = null;
        if (!empty($inv->post_id)) {
            $post = \App\Models\Post::find($inv->post_id);
        }
        if (!$post && !empty($inv->contract_id)) {
            $c = \App\Models\Contract::find($inv->contract_id) ?: \App\Models\Contract::where('contract_code', $inv->contract_id)->first();
            if ($c && !empty($c->post_id)) {
                $post = \App\Models\Post::find($c->post_id);
            }
        }
        return [
            'id'                     => (string)($inv->_id ?? $inv->id),
            'invoice_code'           => $inv->invoice_code,
            'room_name'              => $post ? $post->title : ($inv->tenant_name ? 'Phòng ' . $inv->tenant_name : 'Phòng trọ'),
            'room_code'              => $inv->room_id ?: ($inv->contract_id ?: '--'),
            'tenant_name'            => $inv->tenant_name ?: 'Khách thuê',
            'total_amount'           => (float)($inv->total_amount ?? 0),
            'total_amount_formatted' => number_format((float)($inv->total_amount ?? 0)) . ' VNĐ',
            'billing_period'         => $inv->billing_period ?: ($inv->billing_month ?: 'Kỳ hiện tại'),
            'payment_date'           => $inv->payment_date ?: ($inv->updated_at ? $inv->updated_at->format('d/m/Y H:i') : '--'),
            'status_label'           => 'Chờ nộp Kế toán'
        ];
    });

    return response()->json([
        'success'                => true,
        'count'                  => $invoices->count(),
        'total_amount'           => $totalAmount,
        'total_amount_formatted' => number_format($totalAmount) . ' VNĐ',
        'invoices'               => $formatted
    ]);
})->middleware(['auth'])->name('api.staff.my_cash_debts');

Route::get('/quan-ly-van-hanh', [\App\Http\Controllers\OperationManagementController::class, 'index'])->name('quan-ly-van-hanh');

Route::get('/qlvan_hanh/Room_list', [\App\Http\Controllers\OperationManagementController::class, 'index'])->name('qlvan_hanh.room_list');

Route::get('/api/operation/check-billing-due', [\App\Http\Controllers\OperationManagementController::class, 'checkRealtimeDue'])->name('operation.check_due');

Route::post('/qlvan_hanh/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('contracts.store');

// Tuyến đường xem chi tiết Hóa đơn chuyên nghiệp (Đặc biệt cho Hóa đơn #1 thanh toán nhận nhà)
Route::get('/qlvan_hanh/hoa_don/{code}', [\App\Http\Controllers\OperationManagementController::class, 'showInvoiceDetail'])->name('qlvan_hanh.invoice_detail');
Route::get('/qlvan_hanh/hoa-don/{code}', [\App\Http\Controllers\OperationManagementController::class, 'showInvoiceDetail']);
Route::get('/hoa-don/{code}', [\App\Http\Controllers\OperationManagementController::class, 'showInvoiceDetail'])->name('invoice.detail');

// ==================== QUY TRÌNH THANH LÝ HỢP ĐỒNG & CHỐT CÔNG NỢ (MONGODB) ====================
Route::get('/qlvan_hanh/thanh_ly_cong_no', [\App\Http\Controllers\ContractLiquidationController::class, 'index'])->name('qlvan_hanh.thanh_ly_cong_no');
Route::get('/thanh-ly-cong-no', [\App\Http\Controllers\ContractLiquidationController::class, 'index'])->name('thanh-ly-cong-no');

// Bước 1: Khách thuê gửi yêu cầu trả phòng
Route::post('/api/contracts/{id}/request-checkout', [\App\Http\Controllers\ContractLiquidationController::class, 'requestCheckout'])->name('api.contracts.request_checkout');

// Bước 2: Lấy dữ liệu hợp đồng & tính toán Realtime
Route::get('/api/contracts/{id}/liquidation-data', [\App\Http\Controllers\ContractLiquidationController::class, 'getLiquidationData'])->name('api.contracts.liquidation_data');
Route::post('/api/contracts/{id}/calculate-liquidation', [\App\Http\Controllers\ContractLiquidationController::class, 'calculateRealtime'])->name('api.contracts.calculate_liquidation');

// Bước 3: Chốt quyết toán rẽ nhánh Kịch bản A / B
Route::post('/api/contracts/{id}/settle-liquidation', [\App\Http\Controllers\ContractLiquidationController::class, 'settleLiquidation'])->name('api.contracts.settle_liquidation');

// Bước 4: Tự động hóa sau khi hoàn tất thanh toán / hoàn tiền
Route::post('/api/contracts/{id}/confirm-refund', [\App\Http\Controllers\ContractLiquidationController::class, 'confirmRefund'])->name('api.contracts.confirm_refund');
Route::post('/api/contracts/{id}/confirm-final-paid', [\App\Http\Controllers\ContractLiquidationController::class, 'confirmFinalInvoicePaid'])->name('api.contracts.confirm_final_paid');


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

// ==================== MODULE QUẢN LÝ TÀI CHÍNH & KẾ TOÁN (ADMIN) ====================
// Luồng 1: Duyệt chuyển khoản (Bank Transfers)
Route::get('/Admin/tai-chinh/duyet-chuyen-khoan', [\App\Http\Controllers\FinanceController::class, 'bankTransfers'])->name('admin.finance.bank_transfers');
Route::get('/admin/tai-chinh/duyet-chuyen-khoan', [\App\Http\Controllers\FinanceController::class, 'bankTransfers']);
Route::get('/Admin/finance/bank-transfers', [\App\Http\Controllers\FinanceController::class, 'bankTransfers']);
Route::post('/Admin/tai-chinh/hoa-don/{id}/duyet', [\App\Http\Controllers\FinanceController::class, 'approveBankTransfer'])->name('admin.finance.approve_bank_transfer');
Route::post('/Admin/tai-chinh/hoa-don/{id}/tu-choi', [\App\Http\Controllers\FinanceController::class, 'rejectBankTransfer'])->name('admin.finance.reject_bank_transfer');

// Luồng 2: Công nợ nhân viên (Staff Cash Debts)
Route::get('/Admin/tai-chinh/cong-no-nhan-vien', [\App\Http\Controllers\FinanceController::class, 'staffDebts'])->name('admin.finance.staff_debts');
Route::get('/admin/tai-chinh/cong-no-nhan-vien', [\App\Http\Controllers\FinanceController::class, 'staffDebts']);
Route::get('/Admin/finance/staff-debts', [\App\Http\Controllers\FinanceController::class, 'staffDebts']);
Route::get('/Admin/tai-chinh/nhan-vien/{staffId}/hoa-don', [\App\Http\Controllers\FinanceController::class, 'staffInvoices'])->name('admin.finance.staff_invoices');
Route::post('/Admin/tai-chinh/nhan-vien/{staffId}/xac-nhan-nop-tien', [\App\Http\Controllers\FinanceController::class, 'remitStaffDebt'])->name('admin.finance.remit_staff_debt');

// Luồng 3: Duyệt Hoàn Cọc (Kịch bản B: Deposit Refunds)
Route::get('/Admin/tai-chinh/duyet-hoan-coc', [\App\Http\Controllers\FinanceController::class, 'refundRequests'])->name('admin.finance.refund_requests');
Route::get('/admin/tai-chinh/duyet-hoan-coc', [\App\Http\Controllers\FinanceController::class, 'refundRequests']);
Route::get('/Admin/finance/refund-requests', [\App\Http\Controllers\FinanceController::class, 'refundRequests']);
Route::post('/Admin/tai-chinh/xac-nhan-hoan-coc/{id}', [\App\Http\Controllers\FinanceController::class, 'confirmRefund'])->name('admin.finance.confirm_refund');
Route::post('/admin/tai-chinh/xac-nhan-hoan-coc/{id}', [\App\Http\Controllers\FinanceController::class, 'confirmRefund']);

// Tiện ích khách thuê báo chuyển khoản kèm ảnh biên lai
Route::post('/api/invoices/{code}/report-transfer', [\App\Http\Controllers\FinanceController::class, 'reportTransfer'])->name('invoice.report_transfer');

// APIs Quản lý Tài khoản con
Route::get('/api/sub-accounts', [\App\Http\Controllers\SubAccountController::class, 'index'])->name('api.sub_accounts.index');
Route::post('/api/sub-accounts', [\App\Http\Controllers\SubAccountController::class, 'store'])->name('api.sub_accounts.store');
Route::put('/api/sub-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'update'])->name('api.sub_accounts.update');
Route::post('/api/sub-accounts/{id}/toggle-lock', [\App\Http\Controllers\SubAccountController::class, 'toggleLock'])->name('api.sub_accounts.toggle_lock');
Route::delete('/api/sub-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'destroy'])->name('api.sub_accounts.destroy');

// APIs Quản lý Tài khoản Khách hàng Cá nhân
Route::post('/api/customer-accounts/{id}/toggle-lock', [\App\Http\Controllers\SubAccountController::class, 'toggleLockCustomer'])->name('api.customer_accounts.toggle_lock');
Route::delete('/api/customer-accounts/{id}', [\App\Http\Controllers\SubAccountController::class, 'destroyCustomer'])->name('api.customer_accounts.destroy');

// ==================== QUẢN LÝ TÀI KHOẢN CÁ NHÂN (KHÁCH THUÊ) - ADMIN ====================
Route::get('/Admin/tai-khoan-ca-nhan', [\App\Http\Controllers\PersonalAccountController::class, 'index'])->name('admin.personal_accounts');
Route::get('/admin/tai-khoan-ca-nhan', [\App\Http\Controllers\PersonalAccountController::class, 'index']);
Route::get('/qltaikhoan_ca_nhan', [\App\Http\Controllers\PersonalAccountController::class, 'index'])->name('admin.qltaikhoan_ca_nhan');
Route::get('/qltaikhoan-ca-nhan', [\App\Http\Controllers\PersonalAccountController::class, 'index']);

// APIs 4 chức năng cốt lõi Quản lý tài khoản cá nhân
Route::get('/admin/customers/{id}/details', [\App\Http\Controllers\PersonalAccountController::class, 'showDetails'])->name('admin.customers.details');
Route::get('/Admin/customers/{id}/details', [\App\Http\Controllers\PersonalAccountController::class, 'showDetails']);
Route::get('/api/admin/personal-accounts/{id}', [\App\Http\Controllers\PersonalAccountController::class, 'showDetails'])->name('api.admin.personal_accounts.show');
Route::post('/api/admin/personal-accounts/{id}/toggle-lock', [\App\Http\Controllers\PersonalAccountController::class, 'toggleLock'])->name('api.admin.personal_accounts.toggle_lock');
Route::delete('/api/admin/personal-accounts/{id}', [\App\Http\Controllers\PersonalAccountController::class, 'destroy'])->name('api.admin.personal_accounts.destroy');
Route::post('/api/admin/personal-accounts/{id}/takedown-posts', [\App\Http\Controllers\PersonalAccountController::class, 'takedownPosts'])->name('api.admin.personal_accounts.takedown_posts');

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

        // Danh sách các trạng thái bài đăng
        $approvedStatuses = ['approved', 'active', 'đã cho thuê', 'rented', 'dang_thue', 'da cho thue', 'da_cho_thue', 'da_duyet', 'đã duyệt'];
        $rejectedStatuses = ['rejected', 'tuchoi', 'tu_choi', 'từ chối', 'tu choi'];
        $pendingStatuses = ['pending', 'cho_duyet', 'chờ duyệt', 'waiting', 'cho duyet'];

        // Thống kê bài đăng thực tế từ DB
        $pendingCount = \App\Models\Post::whereIn('status', $pendingStatuses)->count();
        $approvedCount = \App\Models\Post::whereIn('status', $approvedStatuses)->count();
        $rejectedCount = \App\Models\Post::whereIn('status', $rejectedStatuses)->count();
        $totalCount = \App\Models\Post::count();
        $complaintCount = \App\Models\Complaint::where('status', 'pending')->count();

        $stats = [
            'pending_posts'      => $pendingCount,
            'approved_posts'     => $approvedCount,
            'rejected_posts'     => $rejectedCount,
            'total_posts'        => $totalCount,
            'pending_complaints' => $complaintCount,
        ];

        $pendingPosts = \App\Models\Post::whereIn('status', $pendingStatuses)->with(['user', 'building'])->orderBy('created_at', 'desc')->take(10)->get();

        return view('kiem_duyet_vien.tong_quan', compact('stats', 'pendingPosts', 'pendingCount', 'complaintCount'));
    })->name('moderator.dashboard');

    Route::get('/kiem-duyet/qlkiem-duyet', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }

        // Danh sách các trạng thái bài đăng
        $approvedStatuses = ['approved', 'active', 'đã cho thuê', 'rented', 'dang_thue', 'da cho thue', 'da_cho_thue', 'da_duyet', 'đã duyệt'];
        $rejectedStatuses = ['rejected', 'tuchoi', 'tu_choi', 'từ chối', 'tu choi'];
        $pendingStatuses = ['pending', 'cho_duyet', 'chờ duyệt', 'waiting', 'cho duyet'];

        // Ưu tiên bài đăng pending lên đầu, sau đó đến các bài khác theo created_at desc
        $pendingPosts = \App\Models\Post::whereIn('status', $pendingStatuses)->with(['user', 'building'])->orderBy('created_at', 'desc')->get();
        $otherPosts = \App\Models\Post::whereNotIn('status', $pendingStatuses)->with(['user', 'building'])->orderBy('created_at', 'desc')->get();
        $posts = $pendingPosts->merge($otherPosts);

        $pendingCount = $pendingPosts->count();
        $approvedCount = \App\Models\Post::whereIn('status', $approvedStatuses)->count();
        $rejectedCount = \App\Models\Post::whereIn('status', $rejectedStatuses)->count();
        $totalPosts = \App\Models\Post::count();
        $complaintCount = \App\Models\Complaint::where('status', 'pending')->count();

        $stats = [
            'pending_posts'      => $pendingCount,
            'approved_posts'     => $approvedCount,
            'rejected_posts'     => $rejectedCount,
            'total_posts'        => $totalPosts,
            'pending_complaints' => $complaintCount,
        ];

        $buildings = \App\Models\Building::orderBy('name')->get();
        return view('kiem_duyet_vien.qlkiem_duyet', compact('posts', 'buildings', 'stats', 'pendingCount', 'complaintCount'));
    })->name('moderator.posts');

    Route::get('/kiem-duyet/qlbai-dang', function () {
        return redirect('/kiem-duyet/qlkiem-duyet');
    });

    Route::get('/kiem-duyet/khieu-nai', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        $pendingCount = \App\Models\Post::where('status', 'pending')->count();
        $complaintCount = \App\Models\Complaint::where('status', 'pending')->count();
        $complaints = \App\Models\Complaint::with('user')->orderBy('created_at', 'desc')->get();
        $stats = [
            'pending_complaints'  => $complaintCount,
            'resolved_complaints' => \App\Models\Complaint::where('status', 'resolved')->count(),
            'total_complaints'    => \App\Models\Complaint::count(),
            'pending_posts'       => $pendingCount
        ];
        return view('kiem_duyet_vien.khieu_nai', compact('pendingCount', 'complaintCount', 'complaints', 'stats'));
    })->name('moderator.complaints');

    Route::get('/kiem-duyet/qlkhieu-nai-cap1', function () {
        return redirect('/kiem-duyet/khieu-nai');
    });

    Route::get('/kiem-duyet/qltin-tuc', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        $pendingPostsBadge = \App\Models\Post::where('status', 'pending')->count();
        $pendingComplaints = \App\Models\Complaint::where('status', 'pending')->count();
        return view('kiem_duyet_vien.qltin_tuc', compact('pendingPostsBadge', 'pendingComplaints', 'user'));
    })->name('moderator.news');

    Route::get('/kiem-duyet/lich-su', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }
        $pendingCount = \App\Models\Post::where('status', 'pending')->count();
        $complaintCount = \App\Models\Complaint::where('status', 'pending')->count();
        $activities = \App\Models\AdminActivity::where('category', 'post')
            ->orWhereIn('action_type', ['approve', 'reject'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('kiem_duyet_vien.lich_su', compact('activities', 'pendingCount', 'complaintCount', 'user'));
    })->name('moderator.history');

    Route::get('/kiem-duyet/thong-bao', function () {
        $user = Auth::user();
        if (!$user->isModerator() && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Chỉ tài khoản Kiểm duyệt viên mới có quyền truy cập.');
        }

        $userId = (string)($user->_id ?? $user->id);

        // 1. Thống kê bài đăng chờ duyệt thực tế
        $pendingPostsBadge = \App\Models\Post::where('status', 'pending')->count();
        $pendingPosts = \App\Models\Post::where('status', 'pending')
            ->with(['user', 'building'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Thống kê đơn khiếu nại chờ xử lý thực tế
        $pendingComplaints = \App\Models\Complaint::where('status', 'pending')->count();
        $pendingComplaintsList = \App\Models\Complaint::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Thông báo từ MongoDB notifications
        $dbNotifications = \App\Models\Notification::where(function($q) use ($userId) {
            $q->where('role', 'kiemduyet')
              ->orWhere('user_id', $userId)
              ->orWhere('category', 'moderator')
              ->orWhere('category', 'moderation');
        })
        ->where('type', '!=', 'dismissed')
        ->orderBy('created_at', 'desc')
        ->get();

        // Danh sách ID đã đọc và đã xóa
        $readMarkers = \App\Models\Notification::where('user_id', $userId)
            ->whereIn('category', ['post_moderation_read', 'complaint_read', 'post_dismissed', 'complaint_dismissed'])
            ->get();

        $dismissedPostIds = $readMarkers->where('category', 'post_dismissed')->pluck('post_id')->filter()->map('strval')->toArray();
        $dismissedComplaintIds = $readMarkers->where('category', 'complaint_dismissed')->pluck('action_url')->filter()->map('strval')->toArray();
        $readPostIds = $readMarkers->where('category', 'post_moderation_read')->pluck('post_id')->filter()->map('strval')->toArray();
        $readComplaintIds = $readMarkers->where('category', 'complaint_read')->pluck('action_url')->filter()->map('strval')->toArray();

        return view('kiem_duyet_vien.thong_bao', compact(
            'user',
            'pendingPostsBadge',
            'pendingComplaints',
            'pendingPosts',
            'pendingComplaintsList',
            'dbNotifications',
            'dismissedPostIds',
            'dismissedComplaintIds',
            'readPostIds',
            'readComplaintIds'
        ));
    })->name('moderator.notifications');

    // API: Đánh dấu 1 thông báo là đã đọc
    Route::post('/api/moderator/notifications/{id}/read', function ($id) {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        $userId = (string)($user->_id ?? $user->id);

        if (str_starts_with($id, 'db_')) {
            $realId = substr($id, 3);
            \App\Models\Notification::where('_id', $realId)->orWhere('id', $realId)->update(['is_read' => true]);
        } elseif (str_starts_with($id, 'post_')) {
            $postId = substr($id, 5);
            \App\Models\Notification::updateOrCreate(
                ['user_id' => $userId, 'post_id' => $postId, 'category' => 'post_moderation_read'],
                ['title' => 'Đã đọc bài đăng', 'is_read' => true, 'role' => 'kiemduyet']
            );
        } elseif (str_starts_with($id, 'complaint_')) {
            $cId = substr($id, 10);
            \App\Models\Notification::updateOrCreate(
                ['user_id' => $userId, 'action_url' => $cId, 'category' => 'complaint_read'],
                ['title' => 'Đã đọc khiếu nại', 'is_read' => true, 'role' => 'kiemduyet']
            );
        } else {
            \App\Models\Notification::where('_id', $id)->orWhere('id', $id)->update(['is_read' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Đã đánh dấu đã đọc']);
    })->middleware('auth');

    // API: Đánh dấu tất cả thông báo là đã đọc
    Route::post('/api/moderator/notifications/mark-all-read', function () {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        $userId = (string)($user->_id ?? $user->id);

        \App\Models\Notification::where(function($q) use ($userId) {
            $q->where('role', 'kiemduyet')
              ->orWhere('user_id', $userId)
              ->orWhere('category', 'moderator')
              ->orWhere('category', 'moderation');
        })->update(['is_read' => true]);

        $pendingPostIds = \App\Models\Post::where('status', 'pending')->pluck('_id')->map('strval');
        foreach ($pendingPostIds as $pId) {
            \App\Models\Notification::updateOrCreate(
                ['user_id' => $userId, 'post_id' => (string)$pId, 'category' => 'post_moderation_read'],
                ['title' => 'Đã đọc bài đăng', 'is_read' => true, 'role' => 'kiemduyet']
            );
        }

        $pendingComplaintIds = \App\Models\Complaint::where('status', 'pending')->pluck('_id')->map('strval');
        foreach ($pendingComplaintIds as $cId) {
            \App\Models\Notification::updateOrCreate(
                ['user_id' => $userId, 'action_url' => (string)$cId, 'category' => 'complaint_read'],
                ['title' => 'Đã đọc khiếu nại', 'is_read' => true, 'role' => 'kiemduyet']
            );
        }

        return response()->json(['success' => true, 'message' => 'Đã đánh dấu tất cả thông báo là đã đọc']);
    })->middleware('auth');

    // API: Xóa thông báo
    Route::delete('/api/moderator/notifications/{id}', function ($id) {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        $userId = (string)($user->_id ?? $user->id);

        if (str_starts_with($id, 'db_')) {
            $realId = substr($id, 3);
            \App\Models\Notification::where('_id', $realId)->orWhere('id', $realId)->delete();
        } elseif (str_starts_with($id, 'post_')) {
            $postId = substr($id, 5);
            \App\Models\Notification::updateOrCreate(
                ['user_id' => $userId, 'post_id' => $postId, 'category' => 'post_dismissed'],
                ['title' => 'Đã ẩn bài đăng', 'is_read' => true, 'type' => 'dismissed', 'role' => 'kiemduyet']
            );
        } elseif (str_starts_with($id, 'complaint_')) {
            $cId = substr($id, 10);
            \App\Models\Notification::updateOrCreate(
                ['user_id' => $userId, 'action_url' => $cId, 'category' => 'complaint_dismissed'],
                ['title' => 'Đã ẩn khiếu nại', 'is_read' => true, 'type' => 'dismissed', 'role' => 'kiemduyet']
            );
        } else {
            \App\Models\Notification::where('_id', $id)->orWhere('id', $id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa thông báo']);
    })->middleware('auth');

    // API Kiểm Duyệt: Phê duyệt bài đăng (Step 3)
    Route::post('/api/kiem-duyet/posts/{id}/approve', function ($id) {
        $user = Auth::user();
        if (!$user || (!$user->isModerator() && $user->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Chỉ Kiểm duyệt viên mới có quyền thực hiện.'], 403);
        }

        $post = \App\Models\Post::where('_id', $id)->first() ?: \App\Models\Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng.'], 404);
        }

        $post->status = 'approved';
        $post->approved_at = now();
        $post->approved_by = (string)($user->_id ?? $user->id);
        $post->save();

        // 1. Lưu lịch sử hành động duyệt bài vào AdminActivity
        \App\Models\AdminActivity::log(
            'approve',
            'post',
            'Phê duyệt bài đăng',
            'Đã phê duyệt bài đăng "' . $post->title . '"' . ($post->building ? ' thuộc tòa nhà ' . $post->building->name : '') . ' hiển thị công khai lên Trang chủ.',
            $post,
            ['post_id' => (string)($post->_id ?? $post->id), 'approved_by' => (string)($user->_id ?? $user->id)]
        );

        // 2. Tạo thông báo tự động gửi về cho Nhân viên đăng bài
        $authorId = (string)($post->user_id ?? $post->staff_id ?? $post->author_id);
        $buildingName = $post->building ? ($post->building->name ?? '') : '';
        if ($authorId) {
            \App\Models\Notification::create([
                'user_id'    => $authorId,
                'role'       => 'nhanvien',
                'category'   => 'post_approved',
                'type'       => 'success',
                'title'      => 'Bài đăng đã được phê duyệt',
                'message'    => 'Bài đăng [' . $post->title . ']' . ($buildingName ? ' thuộc tòa nhà [' . $buildingName . ']' : '') . ' của bạn đã được Kiểm duyệt viên phê duyệt và đang hiển thị trên Trang chủ.',
                'post_id'    => (string)($post->_id ?? $post->id),
                'action_url' => url('/chitietbaidang/' . ($post->_id ?? $post->id)),
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gửi thêm thông báo cho doanh nghiệp chủ quản (nếu là tài khoản nhân viên con)
            if (!empty($post->parent_id) && (string)$post->parent_id !== $authorId) {
                \App\Models\Notification::create([
                    'user_id'    => (string)$post->parent_id,
                    'role'       => 'doanhnghiep',
                    'category'   => 'post_approved',
                    'type'       => 'success',
                    'title'      => 'Bài đăng đã được phê duyệt',
                    'message'    => 'Bài đăng [' . $post->title . ']' . ($buildingName ? ' thuộc tòa nhà [' . $buildingName . ']' : '') . ' của nhân viên đã được Kiểm duyệt viên phê duyệt và đang hiển thị trên Trang chủ.',
                    'post_id'    => (string)($post->_id ?? $post->id),
                    'action_url' => url('/chitietbaidang/' . ($post->_id ?? $post->id)),
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã phê duyệt bài đăng "' . $post->title . '" thành công!',
            'post'    => $post
        ]);
    })->middleware('auth');

    // API Kiểm Duyệt: Từ chối bài đăng (Step 4)
    Route::post('/api/kiem-duyet/posts/{id}/reject', function (\Illuminate\Http\Request $request, $id) {
        $user = Auth::user();
        if (!$user || (!$user->isModerator() && $user->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Chỉ Kiểm duyệt viên mới có quyền thực hiện.'], 403);
        }

        $post = \App\Models\Post::where('_id', $id)->first() ?: \App\Models\Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng.'], 404);
        }

        $reason = $request->input('reason', 'Nội dung chưa đáp ứng quy chuẩn hệ thống');

        $post->status = 'rejected';
        $post->reject_reason = $reason;
        $post->rejected_at = now();
        $post->rejected_by = (string)($user->_id ?? $user->id);
        $post->save();

        // 1. Lưu lịch sử hành động từ chối bài vào AdminActivity
        \App\Models\AdminActivity::log(
            'reject',
            'post',
            'Từ chối bài đăng',
            'Từ chối bài đăng "' . $post->title . '". Lý do: ' . $reason,
            $post,
            ['post_id' => (string)($post->_id ?? $post->id), 'reason' => $reason]
        );

        // 2. Tạo thông báo tự động gửi về cho Nhân viên đăng bài
        $authorId = (string)($post->user_id ?? $post->staff_id ?? $post->author_id);
        if ($authorId) {
            \App\Models\Notification::create([
                'user_id'    => $authorId,
                'role'       => 'nhanvien',
                'category'   => 'post_rejected',
                'type'       => 'error',
                'title'      => 'Bài đăng bị từ chối phê duyệt',
                'message'    => 'Bài đăng [' . $post->title . '] bị từ chối phê duyệt. Lý do: [' . $reason . ']. Vui lòng chỉnh sửa và gửi lại.',
                'post_id'    => (string)($post->_id ?? $post->id),
                'action_url' => url('/Overview?tab=pending'),
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($post->parent_id) && (string)$post->parent_id !== $authorId) {
                \App\Models\Notification::create([
                    'user_id'    => (string)$post->parent_id,
                    'role'       => 'doanhnghiep',
                    'category'   => 'post_rejected',
                    'type'       => 'error',
                    'title'      => 'Bài đăng nhân viên bị từ chối',
                    'message'    => 'Bài đăng [' . $post->title . '] bị từ chối phê duyệt. Lý do: [' . $reason . '].',
                    'post_id'    => (string)($post->_id ?? $post->id),
                    'action_url' => url('/Overview?tab=pending'),
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối bài đăng "' . $post->title . '"!',
            'post'    => $post
        ]);
    })->middleware('auth');

    // API Kiểm Duyệt: Xử lý khiếu nại Cấp 1 (Nhắc nhở, Gỡ bài, Bác đơn)
    Route::post('/api/kiem-duyet/complaints/{id}/resolve', [\App\Http\Controllers\ComplaintController::class, 'resolve'])->middleware('auth')->name('moderator.complaints.resolve');

    // API Quản lý & Kiểm duyệt Tin tức BĐS (RSS & Moderation)
    Route::get('/api/kiem-duyet/news', [\App\Http\Controllers\NewsModerationController::class, 'index'])->name('moderator.news.index');
    Route::post('/api/kiem-duyet/news/fetch-rss', [\App\Http\Controllers\NewsModerationController::class, 'fetchRss'])->name('moderator.news.fetch_rss');
    Route::post('/api/kiem-duyet/news', [\App\Http\Controllers\NewsModerationController::class, 'store'])->name('moderator.news.store');
    Route::put('/api/kiem-duyet/news/{id}', [\App\Http\Controllers\NewsModerationController::class, 'update'])->name('moderator.news.update');
    Route::post('/api/kiem-duyet/news/{id}/approve', [\App\Http\Controllers\NewsModerationController::class, 'approve'])->name('moderator.news.approve');
    Route::delete('/api/kiem-duyet/news/{id}', [\App\Http\Controllers\NewsModerationController::class, 'destroy'])->name('moderator.news.destroy');
});

// API Public: Tin tức BĐS đã phê duyệt cho Trang chủ
Route::get('/api/news/approved', [\App\Http\Controllers\NewsModerationController::class, 'getApprovedNews'])->name('news.approved');

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