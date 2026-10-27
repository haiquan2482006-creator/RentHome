<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Building;
use App\Models\Post;
use App\Models\Contract;
use App\Models\AdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubAccountController extends Controller
{
    /**
     * Kiểm tra quyền quản trị Doanh nghiệp (Admin hoặc Doanh nghiệp RentHome)
     */
    private function checkPermission()
    {
        $user = Auth::user();
        return $user && ($user->isAdmin() || ($user->isEnterprise() && !$user->isSubAccount()));
    }

    /**
     * Hiển thị giao diện Quản Lý Tài Khoản Con (NV) dành cho Admin
     */
    public function viewPage()
    {
        if (!$this->checkPermission()) {
            return redirect('/login')->withErrors(['login_error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp mới có quyền truy cập.']);
        }

        $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail', 'manager_id', 'manager_ids', 'user_ids']);
        $subAccounts = $this->getSubAccountsWithKPI($allBuildings);
        $customerAccounts = $this->getCustomerAccounts();

        return view('Admin.qltai_khoan_con', compact('allBuildings', 'subAccounts', 'customerAccounts'));
    }

    /**
     * Lấy danh sách toàn bộ tài khoản khách hàng cá nhân tự đăng ký trong hệ thống
     */
    private function getCustomerAccounts()
    {
        $excludedRoles = [
            'admin', 'Admin',
            'doanhnghiep', 'enterprise',
            'staff', 'sub_account',
            'kiemduyet', 'moderator', 'kiem_duyet',
            'nhansu', 'nhan su', 'nhan_su',
            'cskh', 'chamsockh', 'cham_soc_kh', 'chăm sóc kh', 'chăm sóc khách hàng', 'cham soc kh'
        ];

        return User::where(function($q) use ($excludedRoles) {
                $q->where('role', 'canhan')
                  ->orWhere('role', 'user')
                  ->orWhere('role', 'khach')
                  ->orWhereNull('role')
                  ->orWhere(function($subQ) use ($excludedRoles) {
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
            ->where(function($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy danh sách tài khoản con kèm chỉ số KPI (Bài đăng & Hợp đồng)
     */
    private function getSubAccountsWithKPI($allBuildings = null)
    {
        if ($allBuildings === null) {
            $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail', 'manager_id', 'manager_ids', 'user_ids']);
        }

        $currentUserId = (string)(Auth::id() ?? '');
        $subAccounts = User::where(function($q) {
                $q->where('role', 'sub_account')
                  ->orWhere('role', 'staff')
                  ->orWhere('role', 'nhansu')
                  ->orWhere('role', 'nhan su')
                  ->orWhere('role', 'nhan_su')
                  ->orWhere('role', 'kiemduyet')
                  ->orWhere('role', 'moderator')
                  ->orWhere('role', 'doanhnghiep')
                  ->orWhere('role', 'enterprise')
                  ->orWhere('role', 'cskh')
                  ->orWhere('role', 'chamsockh')
                  ->orWhere('role', 'cham_soc_kh')
                  ->orWhere('role', 'chăm sóc kh')
                  ->orWhere('staff_role', 'like', '%chăm sóc%')
                  ->orWhere('staff_role', 'like', '%cskh%')
                  ->orWhereNotNull('parent_id');
            })
            ->where(function($q) use ($currentUserId) {
                if ($currentUserId) {
                    $q->where('_id', '!=', $currentUserId);
                }
            })
            ->where(function($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $subAccounts->transform(function ($account) use ($allBuildings) {
            $staffId = (string)($account->_id ?? $account->id);

            // KPI: Số bài đã đăng do tài khoản con này tạo
            $account->posts_count = Post::where(function($q) use ($staffId) {
                $q->where('user_id', $staffId)
                  ->orWhere('staff_id', $staffId);
            })->count();

            // KPI: Số hợp đồng đang quản lý do tài khoản con này phụ trách/tạo
            $account->contracts_count = Contract::where(function($q) use ($staffId) {
                $q->where('created_by', $staffId)
                  ->orWhere('staff_id', $staffId);
            })->count();

            // Kiểm tra xem tài khoản có phải là Kiểm duyệt viên không
            $isModerator = (method_exists($account, 'isModerator') && $account->isModerator())
                || in_array(strtolower($account->role ?? ''), ['kiemduyet', 'moderator', 'kiem_duyet'], true)
                || str_contains(mb_strtolower($account->staff_role ?? ''), 'kiểm duyệt')
                || str_contains(mb_strtolower($account->staff_role ?? ''), 'kiem duyet');

            // Kiểm tra xem tài khoản có phải là Chăm sóc khách hàng không
            $isCskh = (method_exists($account, 'isCskh') && $account->isCskh())
                || in_array(strtolower($account->role ?? ''), ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'], true)
                || str_contains(mb_strtolower($account->staff_role ?? ''), 'chăm sóc')
                || str_contains(mb_strtolower($account->staff_role ?? ''), 'cskh');

            if ($isModerator) {
                // Tự động làm sạch dữ liệu nếu Kiểm duyệt viên bị gán nhầm tòa nhà trong DB
                if (!empty($account->assigned_buildings) || !empty($account->building_ids)) {
                    try {
                        $account->assigned_buildings = [];
                        $account->building_ids = [];
                        $account->save();
                    } catch (\Throwable $e) {}
                }

                $account->is_moderator = true;
                $account->is_cskh = false;
                $account->is_all_buildings = false;
                $account->assigned_buildings = [];
                $account->assigned_buildings_details = collect([]);
            } elseif ($isCskh) {
                // Tự động làm sạch dữ liệu nếu CSKH bị gán nhầm tòa nhà trong DB
                if (!empty($account->assigned_buildings) || !empty($account->building_ids)) {
                    try {
                        $account->assigned_buildings = [];
                        $account->building_ids = [];
                        $account->save();
                    } catch (\Throwable $e) {}
                }

                $account->is_moderator = false;
                $account->is_cskh = true;
                $account->is_all_buildings = false;
                $account->assigned_buildings = [];
                $account->assigned_buildings_details = collect([]);

                // KPI bổ sung nếu là CSKH: Số khiếu nại / liên hệ đã xử lý
                $account->tickets_count = \App\Models\Complaint::where(function($q) use ($staffId) {
                    $q->where('handled_by', $staffId)
                      ->orWhere('resolver_id', $staffId)
                      ->orWhere('status', 'resolved');
                })->count();
            } else {
                $account->is_moderator = false;
                $account->is_cskh = false;
                $account->is_all_buildings = false;

                // Strict Matching: Chỉ lấy tòa nhà nếu ID khớp chính xác với manager_id hoặc nằm trong manager_ids / user_ids, hoặc trong assigned_buildings
                $userAssigned = (array) ($account->assigned_buildings ?? []);
                $userBldIds = (array) ($account->building_ids ?? []);
                $explicitAssignedIds = [];

                foreach (array_merge($userAssigned, $userBldIds) as $item) {
                    $s = trim((string)$item);
                    if (!empty($s) && $s !== 'all' && $s !== '*') {
                        $explicitAssignedIds[] = $s;
                    }
                }

                foreach ($allBuildings as $b) {
                    $bId = (string)($b->_id ?? $b->id);
                    $bManagerId = (string)($b->manager_id ?? '');
                    $bManagerIds = is_array($b->manager_ids) ? array_map('strval', $b->manager_ids) : [];
                    $bUserIds = is_array($b->user_ids) ? array_map('strval', $b->user_ids) : [];

                    $isManager = ($bManagerId === $staffId)
                        || in_array($staffId, $bManagerIds, true)
                        || in_array($staffId, $bUserIds, true);

                    if ($isManager) {
                        $explicitAssignedIds[] = $bId;
                    }
                }

                $explicitAssignedIds = array_values(array_unique(array_filter($explicitAssignedIds)));

                // Lọc danh sách tòa nhà theo ID chặt chẽ - TUYỆT ĐỐI KHÔNG FALLBACK ALL BUILDINGS
                $matchedBuildings = $allBuildings->filter(function ($b) use ($explicitAssignedIds, $staffId) {
                    $bId = (string)($b->_id ?? $b->id);
                    $bManagerId = (string)($b->manager_id ?? '');
                    $bManagerIds = is_array($b->manager_ids) ? array_map('strval', $b->manager_ids) : [];
                    $bUserIds = is_array($b->user_ids) ? array_map('strval', $b->user_ids) : [];

                    return in_array($bId, $explicitAssignedIds, true)
                        || $bManagerId === $staffId
                        || in_array($staffId, $bManagerIds, true)
                        || in_array($staffId, $bUserIds, true);
                })->values();

                $account->assigned_buildings_details = $matchedBuildings;
                $account->assigned_buildings = $matchedBuildings->pluck('_id')->map('strval')->values()->toArray();
            }

            // Chuẩn hóa quyền hạn (mặc định nếu trống)
            if (empty($account->permissions)) {
                $account->permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];
            }

            // Chuẩn hóa chức vụ hiển thị
            if (empty($account->staff_role) || in_array($account->staff_role, ['Nhân viên', 'Doanh nghiệp'], true)) {
                if ($isCskh) {
                    $account->staff_role = 'Chăm sóc KH';
                } elseif ($isModerator) {
                    $account->staff_role = 'Kiểm duyệt viên';
                } else {
                    $account->staff_role = 'Nhân sự';
                }
            }

            // Không gán mật khẩu mẫu nếu chưa có
            if (empty($account->plain_password)) {
                $account->plain_password = '';
            }

            return $account;
        });

        return $subAccounts;
    }

    /**
     * API: Lấy danh sách tài khoản con của Doanh nghiệp RentHome
     */
    public function index()
    {
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền quản lý tài khoản con.'], 403);
        }

        try {
            $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail', 'manager_id', 'manager_ids', 'user_ids']);
            $subAccounts = $this->getSubAccountsWithKPI($allBuildings);
            $customerAccounts = $this->getCustomerAccounts();

            return response()->json([
                'success' => true,
                'sub_accounts' => $subAccounts,
                'customer_accounts' => $customerAccounts,
                'all_buildings' => $allBuildings
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách tài khoản con: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin tạo tài khoản con mới (nv1, nv2...) với phân quyền chi tiết
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền tạo tài khoản con.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string',
            'staff_role' => 'nullable|string',
            'assigned_buildings' => 'nullable|array',
            'assigned_buildings.*' => 'string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'email.required' => 'Vui lòng nhập địa chỉ Gmail / Email đăng nhập.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'phone.required' => 'Vui lòng nhập số điện thoại / Zalo công việc.',
            'password.required' => 'Vui lòng nhập mật khẩu khởi tạo.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
        ]);

        try {
            $email = trim($validated['email']);

            // Tự động tạo username duy nhất từ email (VD: nhanvien@gmail.com -> nhanvien)
            $baseUsername = strtolower(explode('@', $email)[0]);
            $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUsername) ?: 'nv';

            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $rawRole = $request->input('role');
            $rawStaffRole = $request->input('staff_role');

            $isModerator = in_array(strtolower((string)$rawRole), ['kiemduyet', 'moderator', 'kiem_duyet'], true)
                || str_contains(mb_strtolower((string)$rawStaffRole), 'kiểm duyệt')
                || str_contains(mb_strtolower((string)$rawStaffRole), 'kiem duyet');

            $isCskh = in_array(strtolower((string)$rawRole), ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'], true)
                || str_contains(mb_strtolower((string)$rawStaffRole), 'chăm sóc')
                || str_contains(mb_strtolower((string)$rawStaffRole), 'cskh');

            if ($isModerator) {
                $roleInput = 'kiemduyet';
                $staffRole = !empty($rawStaffRole) ? $rawStaffRole : 'Kiểm duyệt viên';
            } elseif ($isCskh) {
                $roleInput = 'chăm sóc kh';
                $staffRole = !empty($rawStaffRole) ? $rawStaffRole : 'Chăm sóc KH';
            } else {
                // Tài khoản con do admin tạo mặc định là nhân sự
                $roleInput = 'nhansu';
                $staffRole = (!empty($rawStaffRole) && !in_array($rawStaffRole, ['Doanh nghiệp', 'doanhnghiep', 'Nhân viên'], true)) ? $rawStaffRole : 'Nhân sự';
            }

            // Tòa nhà phân công: Kiểm duyệt viên và CSKH tuyệt đối là [], nhân viên lọc sạch 'all' / '*'
            if ($isModerator || $isCskh) {
                $assignedBuildings = [];
            } else {
                $rawAssigned = (array)($request->input('assigned_buildings', []));
                $cleanAssigned = [];
                foreach ($rawAssigned as $item) {
                    $s = trim((string)$item);
                    if (!empty($s) && $s !== 'all' && $s !== '*') {
                        $cleanAssigned[] = $s;
                    }
                }
                $assignedBuildings = array_values(array_unique($cleanAssigned));
            }

            // Quyền hạn nghiệp vụ - Cố định toàn quyền 4 nghiệp vụ
            $permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];

            $subAccount = User::create([
                'username'           => $username,
                'account_name'       => $validated['name'],
                'email'              => $email,
                'phone'              => $validated['phone'],
                'password'           => Hash::make($validated['password']),
                'plain_password'     => $validated['password'],
                'role'               => $roleInput,
                'parent_id'          => (string)($user->_id ?? $user->id),
                'staff_role'         => $staffRole,
                'assigned_buildings' => $assignedBuildings,
                'building_ids'       => $assignedBuildings,
                'permissions'        => $permissions,
                'status'             => 'active',
                'is_locked'          => false,
                'is_deleted'         => false,
            ]);

            // Đồng bộ sang Building: Gán manager_id nếu tài khoản KHÔNG phải kiểm duyệt viên / CSKH và có chọn tòa nhà
            $staffId = (string)($subAccount->_id ?? $subAccount->id);
            if (!$isModerator && !$isCskh && !empty($assignedBuildings)) {
                Building::whereIn('_id', $assignedBuildings)->get()->each(function($bld) use ($staffId) {
                    $mIds = is_array($bld->manager_ids) ? $bld->manager_ids : [];
                    if (!in_array($staffId, $mIds, true)) {
                        $mIds[] = $staffId;
                        $bld->manager_ids = array_values(array_unique($mIds));
                    }
                    if (empty($bld->manager_id)) {
                        $bld->manager_id = $staffId;
                    }
                    $uIds = is_array($bld->user_ids) ? $bld->user_ids : [];
                    if (!in_array($staffId, $uIds, true)) {
                        $uIds[] = $staffId;
                        $bld->user_ids = array_values(array_unique($uIds));
                    }
                    $bld->save();
                });
            }

            // Ghi log hoạt động tạo tài khoản con
            AdminActivity::log(
                'create',
                'account',
                'Tạo tài khoản con doanh nghiệp mới',
                "Đã tạo thành công tài khoản con: {$subAccount->account_name} ({$email}) và cấp quyền quản lý tòa nhà.",
                $subAccount
            );

            return response()->json([
                'success' => true,
                'message' => 'Tạo tài khoản con (' . $email . ') thành công! Nhân viên đã có thể đăng nhập bằng Gmail.',
                'sub_account' => $subAccount
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo tài khoản con: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cập nhật thông tin và phân quyền cho tài khoản con
     */
    public function update(Request $request, $id)
    {
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền cập nhật tài khoản con.'], 403);
        }

        $subAccount = User::find($id);
        if (!$subAccount || !$subAccount->isSubAccount()) {
            return response()->json(['error' => 'Không tìm thấy tài khoản con!'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string',
            'staff_role' => 'nullable|string',
            'assigned_buildings' => 'nullable|array',
            'assigned_buildings.*' => 'string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên nếu muốn đổi.',
        ]);

        try {
            $subAccount->account_name = $validated['name'];
            $subAccount->phone = $validated['phone'];

            $isModerator = (method_exists($subAccount, 'isModerator') && $subAccount->isModerator())
                || in_array(strtolower($subAccount->role ?? ''), ['kiemduyet', 'moderator', 'kiem_duyet'], true)
                || in_array(strtolower($request->input('role') ?? ''), ['kiemduyet', 'moderator', 'kiem_duyet'], true)
                || str_contains(mb_strtolower($subAccount->staff_role ?? ''), 'kiểm duyệt')
                || str_contains(mb_strtolower($request->input('staff_role') ?? ''), 'kiểm duyệt');

            $isCskh = (method_exists($subAccount, 'isCskh') && $subAccount->isCskh())
                || in_array(strtolower($subAccount->role ?? ''), ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'], true)
                || in_array(strtolower($request->input('role') ?? ''), ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'], true)
                || str_contains(mb_strtolower($subAccount->staff_role ?? ''), 'chăm sóc')
                || str_contains(mb_strtolower($request->input('staff_role') ?? ''), 'chăm sóc')
                || str_contains(mb_strtolower($subAccount->staff_role ?? ''), 'cskh')
                || str_contains(mb_strtolower($request->input('staff_role') ?? ''), 'cskh');

            if ($isModerator) {
                $subAccount->role = 'kiemduyet';
                $subAccount->staff_role = !empty($validated['staff_role']) ? $validated['staff_role'] : ($subAccount->staff_role ?: 'Kiểm duyệt viên');
            } elseif ($isCskh) {
                $subAccount->role = 'chăm sóc kh';
                $subAccount->staff_role = !empty($validated['staff_role']) ? $validated['staff_role'] : ($subAccount->staff_role ?: 'Chăm sóc KH');
            } else {
                // Tài khoản do admin tạo / cập nhật: chuyển từ doanh nghiệp sang nhansu / Nhân sự
                $inputRole = $request->input('role');
                if (!empty($inputRole) && !in_array($inputRole, ['doanhnghiep', 'enterprise'], true)) {
                    $subAccount->role = $inputRole;
                } else {
                    $subAccount->role = 'nhansu';
                }

                $inputStaffRole = $validated['staff_role'] ?? null;
                if (!empty($inputStaffRole) && !in_array($inputStaffRole, ['Doanh nghiệp', 'doanhnghiep', 'Nhân viên'], true)) {
                    $subAccount->staff_role = $inputStaffRole;
                } else {
                    $subAccount->staff_role = 'Nhân sự';
                }
            }

            $cleanAssigned = [];
            if ($isModerator || $isCskh) {
                // Kiểm duyệt viên & CSKH: Tuyệt đối không phân công tòa nhà
                $subAccount->assigned_buildings = [];
                $subAccount->building_ids = [];
            } else {
                if ($request->has('assigned_buildings')) {
                    $rawAssigned = (array)($request->input('assigned_buildings', []));
                    foreach ($rawAssigned as $item) {
                        $s = trim((string)$item);
                        if (!empty($s) && $s !== 'all' && $s !== '*') {
                            $cleanAssigned[] = $s;
                        }
                    }
                    $cleanAssigned = array_values(array_unique($cleanAssigned));
                    $subAccount->assigned_buildings = $cleanAssigned;
                    $subAccount->building_ids = $cleanAssigned;
                } else {
                    $cleanAssigned = (array)($subAccount->assigned_buildings ?? []);
                }
            }

            // Quyền hạn nghiệp vụ - Cố định toàn quyền 4 nghiệp vụ
            $subAccount->permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];

            if (!empty($validated['password'])) {
                $subAccount->password = Hash::make($validated['password']);
                $subAccount->plain_password = $validated['password'];
            }

            $subAccount->save();

            // Đồng bộ sang Building: Gán / hủy manager_id chính xác cho đúng nhân viên
            $staffId = (string)($subAccount->_id ?? $subAccount->id);
            if ($isModerator || $isCskh) {
                // Đảm bảo không có tòa nhà nào giữ ID của kiểm duyệt viên / CSKH
                Building::where('manager_id', $staffId)->update(['manager_id' => null]);
                Building::all()->each(function($bld) use ($staffId) {
                    $modified = false;
                    if (is_array($bld->manager_ids) && in_array($staffId, array_map('strval', $bld->manager_ids), true)) {
                        $bld->manager_ids = array_values(array_diff(array_map('strval', $bld->manager_ids), [$staffId]));
                        $modified = true;
                    }
                    if (is_array($bld->user_ids) && in_array($staffId, array_map('strval', $bld->user_ids), true)) {
                        $bld->user_ids = array_values(array_diff(array_map('strval', $bld->user_ids), [$staffId]));
                        $modified = true;
                    }
                    if ($modified) {
                        $bld->save();
                    }
                });
            } else {
                if ($request->has('assigned_buildings')) {
                    // Tòa nhà mới được phân công
                    if (!empty($cleanAssigned)) {
                        Building::whereIn('_id', $cleanAssigned)->get()->each(function($bld) use ($staffId) {
                            $mIds = is_array($bld->manager_ids) ? $bld->manager_ids : [];
                            if (!in_array($staffId, $mIds, true)) {
                                $mIds[] = $staffId;
                                $bld->manager_ids = array_values(array_unique($mIds));
                            }
                            if (empty($bld->manager_id)) {
                                $bld->manager_id = $staffId;
                            }
                            $uIds = is_array($bld->user_ids) ? $bld->user_ids : [];
                            if (!in_array($staffId, $uIds, true)) {
                                $uIds[] = $staffId;
                                $bld->user_ids = array_values(array_unique($uIds));
                            }
                            $bld->save();
                        });
                    }

                    // Tòa nhà bị bỏ phân công
                    Building::where('manager_id', $staffId)
                        ->when(!empty($cleanAssigned), function($q) use ($cleanAssigned) {
                            $q->whereNotIn('_id', $cleanAssigned);
                        })
                        ->get()
                        ->each(function($bld) use ($staffId) {
                            $bld->manager_id = null;
                            if (is_array($bld->manager_ids)) {
                                $bld->manager_ids = array_values(array_diff(array_map('strval', $bld->manager_ids), [$staffId]));
                            }
                            if (is_array($bld->user_ids)) {
                                $bld->user_ids = array_values(array_diff(array_map('strval', $bld->user_ids), [$staffId]));
                            }
                            $bld->save();
                        });
                }
            }

            // Ghi log cập nhật tài khoản con
            AdminActivity::log(
                'update',
                'account',
                'Cập nhật tài khoản con & phân quyền',
                "Đã cập nhật thông tin và phân quyền quản lý cho tài khoản {$subAccount->account_name} ({$subAccount->email}).",
                $subAccount
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tài khoản con thành công!',
                'sub_account' => $subAccount
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật tài khoản con: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Khóa hoặc mở khóa tài khoản con
     */
    public function toggleLock(Request $request, $id)
    {
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền thao tác.'], 403);
        }

        $subAccount = User::where('_id', $id)->orWhere('id', $id)->first();
        if (!$subAccount) {
            return response()->json(['error' => 'Không tìm thấy tài khoản!'], 404);
        }

        if ($subAccount->isAdmin() || $subAccount->role === 'admin') {
            return response()->json(['error' => 'Không thể khóa tài khoản Ban Quản Trị!'], 403);
        }

        $isCurrentlyLocked = ($subAccount->is_locked === true || in_array($subAccount->status, ['locked', 'khoa', 'vi_pham', 'banned']));
        if ($isCurrentlyLocked && $subAccount->locked_until && now()->gte($subAccount->locked_until)) {
            $isCurrentlyLocked = false;
        }

        $newLockState = !$isCurrentlyLocked;
        $subAccount->is_locked = $newLockState;
        $subAccount->status = $newLockState ? 'locked' : 'active';
        $subAccount->locked_at = $newLockState ? Carbon::now() : null;

        if ($newLockState) {
            $lockReason = $request->input('lock_reason', $request->input('reason', 'Tài khoản vi phạm quy định làm việc.'));
            $lockDuration = $request->input('lock_duration');
            $isIndefinite = ($lockDuration === 'indefinite' || (int)$lockDuration >= 9999);
            if ($isIndefinite) {
                $subAccount->locked_until = Carbon::now()->addYears(100);
            } elseif ($lockDuration) {
                $subAccount->locked_until = Carbon::now()->addDays((int)$lockDuration);
            } else {
                $subAccount->locked_until = Carbon::now()->addYears(100);
            }
            $subAccount->lock_reason = $lockReason;
        } else {
            $subAccount->locked_until = null;
            $subAccount->lock_reason = null;
        }
        $subAccount->save();

        $actionText = $newLockState ? 'Khóa' : 'Mở khóa';
        $accName = $subAccount->account_name ?: ($subAccount->username ?: $subAccount->email);

        // Ghi log khóa / mở khóa tài khoản con
        AdminActivity::log(
            $newLockState ? 'lock' : 'unlock',
            'account',
            "{$actionText} tài khoản nhân viên",
            "Admin đã {$actionText} quyền truy cập của tài khoản nhân viên: {$accName} ({$subAccount->email}).",
            $subAccount,
            [
                'reason' => $subAccount->lock_reason,
                'locked_until' => $subAccount->locked_until,
                'user_id' => (string)($subAccount->_id ?? $subAccount->id)
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Đã {$actionText} tài khoản thành công!",
            'is_locked' => $subAccount->is_locked,
            'status' => $subAccount->status,
            'locked_until' => $subAccount->locked_until ? Carbon::parse($subAccount->locked_until)->format('d/m/Y H:i') : null,
            'lock_reason' => $subAccount->lock_reason,
        ]);
    }

    /**
     * Khóa hoặc mở khóa tài khoản khách hàng cá nhân
     */
    public function toggleLockCustomer(Request $request, $id)
    {
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền thao tác.'], 403);
        }

        $customer = User::where('_id', $id)->orWhere('id', $id)->first();
        if (!$customer) {
            return response()->json(['error' => 'Không tìm thấy tài khoản khách hàng!'], 404);
        }

        if ($customer->isAdmin()) {
            return response()->json(['error' => 'Không thể thao tác trên tài khoản Quản trị viên!'], 403);
        }

        $isCurrentlyLocked = ($customer->is_locked === true || $customer->status === 'locked' || $customer->status === 'khoa');
        if ($isCurrentlyLocked && $customer->locked_until && now()->gte($customer->locked_until)) {
            $isCurrentlyLocked = false;
        }

        $newLockState = !$isCurrentlyLocked;
        $customer->is_locked = $newLockState;
        $customer->status = $newLockState ? 'locked' : 'active';
        $customer->locked_at = $newLockState ? Carbon::now() : null;

        if ($newLockState) {
            $lockReason = $request->input('lock_reason', $request->input('reason', 'Tài khoản vi phạm tiêu chuẩn cộng đồng.'));
            $lockDuration = $request->input('lock_duration');
            if ($lockDuration === 'indefinite' || (int)$lockDuration >= 9999) {
                $customer->locked_until = Carbon::now()->addYears(100);
            } elseif ($lockDuration) {
                $customer->locked_until = Carbon::now()->addDays((int)$lockDuration);
            }
            $customer->lock_reason = $lockReason;
        } else {
            $customer->locked_until = null;
            $customer->lock_reason = null;
        }
        $customer->save();

        $actionText = $newLockState ? 'Khóa' : 'Mở khóa';
        $custName = $customer->account_name ?? $customer->username ?? $customer->email;

        // Ghi log khóa / mở khóa khách hàng
        AdminActivity::log(
            $newLockState ? 'lock' : 'unlock',
            'account',
            "{$actionText} tài khoản khách hàng",
            "Admin đã {$actionText} tài khoản người dùng: {$custName} ({$customer->email}).",
            $customer
        );

        return response()->json([
            'success' => true,
            'message' => "Đã {$actionText} tài khoản khách hàng thành công!",
            'is_locked' => $customer->is_locked,
            'status' => $customer->status,
            'locked_until' => $customer->locked_until ? Carbon::parse($customer->locked_until)->format('d/m/Y H:i') : null,
            'lock_reason' => $customer->lock_reason,
        ]);
    }

    /**
     * Xóa tài khoản khách hàng cá nhân
     */
    public function destroyCustomer($id)
    {
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền xóa tài khoản.'], 403);
        }

        $customer = User::where('_id', $id)->orWhere('id', $id)->first();
        if (!$customer) {
            return response()->json(['error' => 'Không tìm thấy tài khoản khách hàng!'], 404);
        }

        if ($customer->isAdmin()) {
            return response()->json(['error' => 'Không thể xóa tài khoản Quản trị viên!'], 403);
        }

        $custName = $customer->account_name ?? $customer->username ?? $customer->email;
        $custEmail = $customer->email;

        // Ghi log xóa tài khoản khách hàng
        AdminActivity::log(
            'delete',
            'account',
            'Xóa tài khoản khách hàng',
            "Admin đã xóa tài khoản khách hàng: {$custName} ({$custEmail}) khỏi danh sách hoạt động.",
            $customer
        );

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tài khoản khách hàng khỏi hệ thống thành công!'
        ]);
    }
}

