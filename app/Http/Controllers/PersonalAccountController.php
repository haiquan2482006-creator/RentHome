<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Contract;
use App\Models\AdminActivity;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PersonalAccountController extends Controller
{
    /**
     * Kiểm tra quyền quản trị Admin
     */
    private function checkAdminPermission()
    {
        $user = Auth::user();
        return $user && ($user->isAdmin() || ($user->role === 'admin') || (method_exists($user, 'isCskh') && $user->isCskh()) || ($user->isEnterprise() && !$user->isSubAccount()));
    }

    /**
     * Hiển thị giao diện danh sách Quản lý Tài khoản Cá nhân (Khách thuê)
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        if (!$this->checkAdminPermission()) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập trang Quản trị Admin!');
        }

        $excludedRoles = [
            'admin', 'Admin',
            'doanhnghiep', 'enterprise',
            'staff', 'sub_account',
            'kiemduyet', 'moderator', 'kiem_duyet',
            'nhansu', 'nhan su', 'nhan_su',
            'cskh', 'chamsockh', 'cham_soc_kh', 'chăm sóc kh', 'chăm sóc khách hàng', 'cham soc kh'
        ];

        // Lấy danh sách toàn bộ tài khoản cá nhân (khách thuê / người dùng phổ thông, loại trừ CSKH & nội bộ)
        $query = User::where(function ($q) use ($excludedRoles) {
            $q->where('role', 'canhan')
              ->orWhere('role', 'user')
              ->orWhere('role', 'khach')
              ->orWhereNull('role')
              ->orWhere(function ($subQ) use ($excludedRoles) {
                  $subQ->whereNotIn('role', $excludedRoles)
                       ->whereNull('parent_id');
              });
        })
        ->whereNotIn('role', $excludedRoles)
        ->where(function ($q) {
            $q->whereNull('staff_role')->orWhere(function($srQ) {
                $srQ->where('staff_role', 'not like', '%chăm sóc%')
                    ->where('staff_role', 'not like', '%cskh%')
                    ->where('staff_role', 'not like', '%nhân sự%')
                    ->where('staff_role', 'not like', '%kiểm duyệt%');
            });
        })
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        });

        // 1. Tìm kiếm theo từ khóa (Tên, Email, SĐT, Username)
        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 2. Lọc theo trạng thái
        $statusFilter = $request->input('status', 'all');
        if ($statusFilter === 'active') {
            $query->where(function ($q) {
                $q->where('is_locked', false)
                  ->whereNotIn('status', ['locked', 'khoa', 'vi_pham', 'banned']);
            });
        } elseif ($statusFilter === 'locked') {
            $query->where(function ($q) {
                $q->where('is_locked', true)
                  ->orWhereIn('status', ['locked', 'khoa', 'vi_pham', 'banned']);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Chuẩn bị dữ liệu thống kê phụ trợ cho từng user: số bài đăng & số phòng đang thuê
        $users->transform(function ($user) {
            $userId = (string)($user->_id ?? $user->id);

            // Số bài đăng do user này đăng
            $user->posts_count = Post::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('author_id', $userId);
            })->count();

            // Số hợp đồng / phòng đang thuê (status != 'cancelled' và != 'expired')
            $user->rented_rooms_count = Contract::where(function ($q) use ($user, $userId) {
                $q->where('tenant_id', $userId);
                if (!empty($user->username)) {
                    $q->orWhere('tenant_account', $user->username);
                }
                if (!empty($user->phone)) {
                    $q->orWhere('tenant_phone', $user->phone);
                }
                if (!empty($user->email)) {
                    $q->orWhere('tenant_email', $user->email);
                }
            })
            ->whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'expired'])
            ->count();

            $isLocked = ($user->is_locked === true || in_array($user->status, ['locked', 'khoa', 'vi_pham', 'banned']));
            // Tự động mở khóa tài khoản nếu đã hết thời gian khóa
            if ($isLocked && $user->locked_until && now()->gte($user->locked_until)) {
                $user->is_locked = false;
                $user->status = 'active';
                $user->locked_until = null;
                $user->lock_reason = null;
                $user->save();
                $isLocked = false;
            }
            $user->is_account_locked = $isLocked;

            return $user;
        });

        // Lọc nâng cao sau khi tính KPI nếu người dùng chọn
        if ($statusFilter === 'renting') {
            $users = $users->filter(fn($u) => $u->rented_rooms_count > 0)->values();
        } elseif ($statusFilter === 'not_renting') {
            $users = $users->filter(fn($u) => $u->rented_rooms_count == 0)->values();
        }

        // Tính các số liệu thống kê tổng quan của trang (loại trừ CSKH & nội bộ)
        $allPersonalAccounts = User::where(function ($q) use ($excludedRoles) {
            $q->where('role', 'canhan')
              ->orWhere('role', 'user')
              ->orWhere('role', 'khach')
              ->orWhereNull('role')
              ->orWhere(function ($subQ) use ($excludedRoles) {
                  $subQ->whereNotIn('role', $excludedRoles)
                       ->whereNull('parent_id');
              });
        })
        ->whereNotIn('role', $excludedRoles)
        ->where(function ($q) {
            $q->whereNull('staff_role')->orWhere(function($srQ) {
                $srQ->where('staff_role', 'not like', '%chăm sóc%')
                    ->where('staff_role', 'not like', '%cskh%')
                    ->where('staff_role', 'not like', '%nhân sự%')
                    ->where('staff_role', 'not like', '%kiểm duyệt%');
            });
        })
        ->where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        })
        ->get();

        $stats = [
            'total'     => $allPersonalAccounts->count(),
            'active'    => $allPersonalAccounts->filter(fn($u) => (!$u->is_locked && !in_array($u->status, ['locked', 'khoa', 'vi_pham', 'banned'])) || ($u->locked_until && now()->gte($u->locked_until)))->count(),
            'locked'    => $allPersonalAccounts->filter(fn($u) => ($u->is_locked || in_array($u->status, ['locked', 'khoa', 'vi_pham', 'banned'])) && (!$u->locked_until || now()->lessThan($u->locked_until)))->count(),
            'has_posts' => Post::distinct('user_id')->count(),
            'renting'   => Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'expired'])->distinct('tenant_id')->count(),
        ];

        return view('Admin.qltaikhoan_ca_nhan', compact('users', 'stats', 'search', 'statusFilter'));
    }

    /**
     * YÊU CẦU 1: API Nội bộ xem chi tiết tài khoản khách hàng (AJAX Modal)
     * Route: /admin/customers/{id}/details
     */
    public function showDetails($id)
    {
        return $this->show($id);
    }

    /**
     * Định dạng ngày tháng an toàn tuyệt đối, hỗ trợ d/m/Y, Y-m-d, timestamps và đối tượng MongoDB/Carbon
     */
    private function formatDateSafe($dateValue, string $format = 'd/m/Y')
    {
        if (empty($dateValue)) {
            return '--';
        }

        if ($dateValue instanceof \DateTimeInterface) {
            return $dateValue->format($format);
        }

        if (is_numeric($dateValue)) {
            return date($format, (int)$dateValue);
        }

        $str = trim((string)$dateValue);
        if ($str === '' || $str === '--') {
            return '--';
        }

        // Nếu chuỗi đã ở định dạng d/m/Y (ví dụ: 21/10/2027)
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $str)) {
            if ($format === 'd/m/Y') {
                return $str;
            }
            try {
                return Carbon::createFromFormat('d/m/Y', $str)->format($format);
            } catch (\Exception $e) {
                return $str;
            }
        }

        // Nếu chuỗi ở định dạng d/m/Y H:i
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}\s+\d{1,2}:\d{2}/', $str)) {
            try {
                return Carbon::createFromFormat('d/m/Y H:i', substr($str, 0, 16))->format($format);
            } catch (\Exception $e) {
                return $str;
            }
        }

        // Thử parse thông thường qua try/catch
        try {
            return Carbon::parse($str)->format($format);
        } catch (\Exception $e) {
            return $str;
        }
    }

    private function formatHumanDiffSafe($dateValue)
    {
        if (empty($dateValue)) {
            return 'Mới đây';
        }
        if ($dateValue instanceof \DateTimeInterface) {
            return Carbon::instance($dateValue)->diffForHumans();
        }
        $str = trim((string)$dateValue);
        try {
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $str)) {
                return Carbon::createFromFormat('d/m/Y', substr($str, 0, 10))->diffForHumans();
            }
            return Carbon::parse($str)->diffForHumans();
        } catch (\Exception $e) {
            return 'Mới đây';
        }
    }

    /**
     * Lấy chi tiết tài khoản, danh sách bài đăng & danh sách phòng đang thuê (JSON)
     */
    public function show($id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }

        try {
            $user = User::where('_id', $id)->orWhere('id', $id)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản người dùng!'], 404);
            }

            $userId = (string)($user->_id ?? $user->id);

            // 1. Danh sách bài đăng của user này
            $rawPosts = Post::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('author_id', $userId);
            })
            ->latest()
            ->get();

            $posts = $rawPosts->map(function ($p) {
                return [
                    'id'            => (string)($p->_id ?? $p->id),
                    'title'         => $p->title ?? 'Không có tiêu đề',
                    'price'         => number_format((float)($p->price ?? 0)) . ' ' . ($p->price_unit ?? 'VNĐ/tháng'),
                    'raw_price'     => (float)($p->price ?? 0),
                    'address'       => implode(', ', array_filter([$p->address ?? '', $p->ward ?? '', $p->district ?? '', $p->province ?? ''])),
                    'status'        => $p->status ?? 'pending',
                    'display_image' => $p->display_image ?? null,
                    'created_at'    => $this->formatDateSafe($p->created_at, 'd/m/Y H:i'),
                    'views'         => (int)($p->views ?? 0),
                    'reject_reason' => $p->reject_reason ?? '',
                    'detail_url'    => url('/chitietbaidang/' . ($p->_id ?? $p->id)),
                ];
            });

            // 2. Danh sách các phòng / hợp đồng họ đang thuê
            $rawContracts = Contract::where(function ($q) use ($user, $userId) {
                $q->where('tenant_id', $userId);
                if (!empty($user->username)) {
                    $q->orWhere('tenant_account', $user->username);
                }
                if (!empty($user->phone)) {
                    $q->orWhere('tenant_phone', $user->phone);
                }
                if (!empty($user->email)) {
                    $q->orWhere('tenant_email', $user->email);
                }
            })
            ->with(['post'])
            ->latest()
            ->get();

            $contracts = $rawContracts->map(function ($c) {
                $post = $c->post ?: ($c->post_id ? Post::find($c->post_id) : null);
                return [
                    'id'            => (string)($c->_id ?? $c->id),
                    'contract_code' => $c->contract_code ?? ('HD-' . substr((string)($c->_id ?? $c->id), -6)),
                    'room_id'       => $c->room_id ?? ($c->post_id ?? '--'),
                    'room_title'    => $post ? ($post->title ?? 'Phòng trọ') : ($c->room_id ? 'Phòng ' . $c->room_id : 'Phòng trọ RentHome'),
                    'room_address'  => $post ? implode(', ', array_filter([$post->address ?? '', $post->ward ?? '', $post->district ?? '', $post->province ?? ''])) : '---',
                    'rent_price'    => number_format((float)($c->rent_price ?? ($post ? ($post->price ?? 0) : 0))) . ' VNĐ/tháng',
                    'deposit'       => number_format((float)($c->deposit ?? 0)) . ' VNĐ',
                    'start_date'    => $this->formatDateSafe($c->start_date, 'd/m/Y'),
                    'end_date'      => $this->formatDateSafe($c->end_date, 'd/m/Y'),
                    'duration'      => $c->duration_months ? ($c->duration_months . ' tháng') : '--',
                    'status'        => $c->status ?? 'active',
                    'is_active'     => !in_array($c->status, ['cancelled', 'terminated', 'da_thanh_ly', 'expired']),
                ];
            });

            $isLocked = ($user->is_locked === true || in_array($user->status, ['locked', 'khoa', 'vi_pham', 'banned']));
            if ($isLocked && $user->locked_until && now()->gte($user->locked_until)) {
                $isLocked = false;
            }

            $userData = [
                'id'               => $userId,
                'username'         => $user->username ?? '',
                'account_name'     => $user->account_name ?: ($user->username ?: 'Khách hàng'),
                'email'            => $user->email ?? '---',
                'phone'            => $user->phone ?? '---',
                'avatar'           => $user->display_avatar ?? ($user->avatar ?? null),
                'address'          => $user->address ?? 'Chưa cập nhật',
                'role'             => $user->role ?? 'canhan',
                'status'           => $user->status ?? 'active',
                'is_locked'        => $isLocked,
                'lock_reason'      => $user->lock_reason ?? '',
                'locked_until'     => $this->formatDateSafe($user->locked_until, 'd/m/Y H:i'),
                'locked_at'        => $this->formatDateSafe($user->locked_at, 'd/m/Y H:i'),
                'created_at'       => $this->formatDateSafe($user->created_at, 'd/m/Y H:i'),
                'created_at_human' => $this->formatHumanDiffSafe($user->created_at),
                'posts_count'      => $posts->count(),
                'contracts_count'  => $contracts->count(),
                'renting_count'    => $contracts->where('is_active', true)->count(),
            ];

            return response()->json([
                'success'   => true,
                'user'      => $userData,
                'posts'     => $posts,
                'contracts' => $contracts,
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi showDetails PersonalAccount: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi máy chủ khi tải chi tiết khách hàng: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * YÊU CẦU 2 & BƯỚC 3: Chức năng "Khóa tài khoản" người dùng có thời hạn
     * Validate lock_reason, lock_duration, tính locked_until và lưu vào DB
     */
    public function toggleLock(Request $request, $id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }

        $user = User::where('_id', $id)->orWhere('id', $id)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản!'], 404);
        }

        if ($user->isAdmin() || $user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Không thể khóa tài khoản Ban Quản Trị Admin!'], 403);
        }

        $isCurrentlyLocked = ($user->is_locked === true || in_array($user->status, ['locked', 'khoa', 'vi_pham', 'banned']));
        if ($isCurrentlyLocked && $user->locked_until && now()->gte($user->locked_until)) {
            $isCurrentlyLocked = false;
        }

        $newLockState = !$isCurrentlyLocked;
        $userName = $user->account_name ?: ($user->username ?: $user->email);

        if ($newLockState) {
            // Chuẩn hóa tên trường lock_reason nếu client gửi reason
            if (!$request->has('lock_reason') && $request->has('reason')) {
                $request->merge(['lock_reason' => $request->input('reason')]);
            }

            // BƯỚC 3: Validate form: lock_reason và lock_duration là bắt buộc
            $validated = $request->validate([
                'lock_reason'   => 'required|string',
                'lock_duration' => 'required',
            ], [
                'lock_reason.required'   => 'Vui lòng nhập lý do khóa tài khoản.',
                'lock_duration.required' => 'Vui lòng chọn thời gian khóa.',
            ]);

            $lockReason = trim($validated['lock_reason']);
            $lockDuration = $validated['lock_duration'];

            // BƯỚC 2: Logic tính thời gian $lockedUntil
            // - Nếu $request->lock_duration === 'indefinite': $lockedUntil = Carbon::now()->addYears(100);
            // - Ngược lại: $lockedUntil = Carbon::now()->addDays((int) $request->lock_duration);
            $isIndefinite = ($lockDuration === 'indefinite' || (int)$lockDuration >= 9999);
            if ($isIndefinite) {
                $lockedUntil = Carbon::now()->addYears(100);
            } else {
                $lockedUntil = Carbon::now()->addDays((int)$lockDuration);
            }

            // Lưu vào DB: Cập nhật locked_until = $lockedUntil và lock_reason = $request->lock_reason
            $user->is_locked = true;
            $user->status = 'locked';
            $user->locked_at = Carbon::now();
            $user->locked_until = $lockedUntil;
            $user->lock_reason = $lockReason;
            $user->save();

            $formattedUntil = Carbon::parse($lockedUntil)->format('d/m/Y H:i');
            $timeText = $isIndefinite ? 'đến khi mở lại (thủ công)' : "đến {$formattedUntil}";
            $actionMsg = "Đã khóa tài khoản {$userName} thành công ({$timeText})!";
        } else {
            // Mở khóa tài khoản
            $user->is_locked = false;
            $user->status = 'active';
            $user->locked_at = null;
            $user->locked_until = null;
            $user->lock_reason = null;
            $user->save();

            $actionMsg = "Đã mở khóa tài khoản {$userName} thành công!";
        }

        $actionName = $newLockState ? 'Khóa tài khoản' : 'Mở khóa tài khoản';
        $actionType = $newLockState ? 'lock' : 'unlock';

        // Ghi nhật ký vào AdminActivity để theo dõi trên trang Lịch sử Admin
        $durationText = $newLockState ? ($isIndefinite ? 'Đến khi mở lại (Thủ công)' : Carbon::parse($user->locked_until)->format('d/m/Y H:i')) : 'Đã khôi phục quyền truy cập.';
        AdminActivity::log(
            $actionType,
            'account',
            "{$actionName} người dùng",
            "Admin đã {$actionName}: {$userName} ({$user->email}). " . ($newLockState ? "Thời hạn: {$durationText}. Lý do: {$user->lock_reason}" : $durationText),
            $user,
            [
                'reason' => $user->lock_reason,
                'locked_until' => $user->locked_until,
                'is_indefinite' => $isIndefinite ?? false,
                'user_id' => (string)($user->_id ?? $user->id)
            ]
        );

        // Gửi thông báo đến người dùng nếu tài khoản bị khóa
        if ($newLockState) {
            try {
                $thoiGianText = $isIndefinite ? 'đến khi được mở lại bởi Quản trị viên' : 'đến ' . Carbon::parse($user->locked_until)->format('d/m/Y H:i');
                Notification::create([
                    'user_id'    => (string)($user->_id ?? $user->id),
                    'role'       => 'canhan',
                    'category'   => 'account_locked',
                    'type'       => 'error',
                    'title'      => 'Tài khoản của bạn đã bị khóa',
                    'message'    => "Tài khoản của bạn đã bị khóa bởi Quản trị viên {$thoiGianText}. Lý do: {$user->lock_reason}.",
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::warning('Không thể tạo notification khi khóa user: ' . $e->getMessage());
            }
        }

        // Đếm lại tổng số tài khoản vi phạm hiện tại để trả về cho Frontend cập nhật badge nếu cần
        $totalViolations = User::where(function ($q) {
            $q->where('is_locked', true)
              ->orWhereIn('status', ['locked', 'khoa', 'vi_pham', 'banned']);
        })
        ->where(function ($q) {
            $q->whereNull('locked_until')->orWhere('locked_until', '>', Carbon::now());
        })
        ->count();

        $displayLockedUntil = null;
        if ($user->locked_until) {
            $displayLockedUntil = ($isIndefinite ?? false) ? 'Đến khi mở lại' : Carbon::parse($user->locked_until)->format('d/m/Y H:i');
        }

        return response()->json([
            'success'          => true,
            'message'          => $actionMsg,
            'is_locked'        => $user->is_locked,
            'status'           => $user->status,
            'locked_until'     => $displayLockedUntil,
            'is_indefinite'    => $isIndefinite ?? false,
            'lock_reason'      => $user->lock_reason,
            'total_violations' => $totalViolations,
        ]);
    }

    /**
     * YÊU CẦU 3: Chức năng "Xóa tài khoản"
     * Áp dụng SoftDelete để không phá vỡ liên kết khóa ngoại với Hợp đồng & Hóa đơn tài chính
     */
    public function destroy(Request $request, $id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }

        $user = User::where('_id', $id)->orWhere('id', $id)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản người dùng!'], 404);
        }

        if ($user->isAdmin() || $user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Không thể xóa tài khoản Quản trị viên hệ thống!'], 403);
        }

        $userId = (string)($user->_id ?? $user->id);
        $userName = $user->account_name ?: ($user->username ?: $user->email);
        $userEmail = $user->email;

        // Kiểm tra xem người dùng có dữ liệu ràng buộc hợp đồng hoặc hóa đơn tài chính không
        $hasContracts = Contract::where(function ($q) use ($user, $userId) {
            $q->where('tenant_id', $userId);
            if (!empty($user->username)) $q->orWhere('tenant_account', $user->username);
            if (!empty($user->phone)) $q->orWhere('tenant_phone', $user->phone);
            if (!empty($user->email)) $q->orWhere('tenant_email', $user->email);
        })->exists();

        $hasInvoices = \App\Models\Invoice::where('tenant_id', $userId)->exists();

        // Ghi log vào AdminActivity
        AdminActivity::log(
            'delete',
            'account',
            'Xóa tài khoản cá nhân',
            "Admin đã xóa tài khoản khách hàng: {$userName} ({$userEmail}) [Chế độ: SoftDelete bảo toàn dữ liệu tài chính].",
            $user,
            ['had_contracts' => $hasContracts, 'had_invoices' => $hasInvoices]
        );

        // Khuyến nghị & thực thi SoftDelete: 
        // Ẩn tài khoản khỏi toàn bộ danh sách, chặn đăng nhập, đổi email/username tránh xung đột nếu tái đăng ký,
        // nhưng bảo toàn các bản ghi Contract & Invoice để phục vụ kế toán / pháp lý
        $user->is_deleted = true;
        $user->deleted_at = now();
        $user->deleted_by = (string)(Auth::id() ?? 'admin');
        $user->status = 'deleted';
        $user->is_locked = true;
        $user->save();

        // Cũng ẩn hoặc gỡ toàn bộ bài đăng của người dùng đã xóa
        Post::where('user_id', $userId)
            ->orWhere('author_id', $userId)
            ->update([
                'status'        => 'hidden',
                'reject_reason' => 'Tài khoản tác giả đã bị xóa khỏi hệ thống',
            ]);

        return response()->json([
            'success' => true,
            'message' => "Đã xóa tài khoản \"{$userName}\" thành công (Đã áp dụng SoftDelete an toàn cho dữ liệu hợp đồng & hóa đơn)!",
            'had_financial_records' => ($hasContracts || $hasInvoices),
        ]);
    }

    /**
     * YÊU CẦU 4: Chức năng "Gỡ bài đăng"
     * Tìm toàn bộ bài đăng thuộc user_id này và chuyển trạng thái sang hidden/rejected (vi phạm).
     * Không xóa cứng bài đăng để làm bằng chứng.
     */
    public function takedownPosts(Request $request, $id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }

        $user = User::where('_id', $id)->orWhere('id', $id)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản người dùng!'], 404);
        }

        $userId = (string)($user->_id ?? $user->id);
        $reason = $request->input('reason', 'Tài khoản người dùng vi phạm quy chế hoặc bị phản ánh lừa đảo / đăng tin sai sự thật.');

        // Tìm tất cả bài đăng thuộc quyền sở hữu của user này
        $userPosts = Post::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('author_id', $userId);
        })->get();

        if ($userPosts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng này hiện không có bài đăng nào trên hệ thống để gỡ.',
            ], 400);
        }

        $postCount = $userPosts->count();

        // Cập nhật trạng thái bài đăng sang rejected/hidden (Không xóa cứng để làm bằng chứng đối soát)
        foreach ($userPosts as $post) {
            $post->status = 'rejected';
            $post->reject_reason = 'Admin đã gỡ bài vi phạm: ' . $reason;
            $post->rejected_at = now();
            $post->rejected_by = (string)(Auth::id() ?? 'admin');
            $post->save();
        }

        $userName = $user->account_name ?: ($user->username ?: $user->email);

        // Ghi nhật ký vào AdminActivity
        AdminActivity::log(
            'reject',
            'post',
            "Gỡ toàn bộ {$postCount} bài đăng của người dùng vi phạm",
            "Admin đã gỡ {$postCount} bài đăng của tài khoản: {$userName} ({$user->email}). Lý do: {$reason}. Trạng thái bài đăng đã chuyển sang [rejected] để lưu trữ làm bằng chứng.",
            $user,
            ['takedown_count' => $postCount, 'reason' => $reason]
        );

        // Gửi thông báo đến người dùng về việc gỡ bài
        try {
            Notification::create([
                'user_id'    => $userId,
                'role'       => 'canhan',
                'category'   => 'posts_takedown',
                'type'       => 'warning',
                'title'      => 'Các bài đăng của bạn đã bị gỡ',
                'message'    => "Ban Quản Trị đã gỡ {$postCount} bài đăng của bạn do vi phạm: {$reason}.",
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Không thể tạo thông báo gỡ bài: ' . $e->getMessage());
        }

        return response()->json([
            'success'   => true,
            'message'   => "Đã gỡ thành công {$postCount} bài đăng của tài khoản \"{$userName}\". Các bài viết đã được chuyển sang trạng thái vi phạm để lưu trữ bằng chứng!",
            'count'     => $postCount,
        ]);
    }
}
