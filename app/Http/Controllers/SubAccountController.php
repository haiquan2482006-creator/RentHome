<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Building;
use App\Models\Post;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail']);
        $subAccounts = $this->getSubAccountsWithKPI($allBuildings);
        $customerAccounts = $this->getCustomerAccounts();

        return view('Admin.qltai_khoan_con', compact('allBuildings', 'subAccounts', 'customerAccounts'));
    }

    /**
     * Lấy danh sách toàn bộ tài khoản khách hàng cá nhân tự đăng ký trong hệ thống
     */
    private function getCustomerAccounts()
    {
        return User::where(function($q) {
                $q->where('role', 'user')
                  ->orWhere('role', 'canhan')
                  ->orWhere('role', 'khach')
                  ->orWhere(function($subQ) {
                      $subQ->whereNotIn('role', ['admin', 'doanhnghiep', 'enterprise', 'staff', 'sub_account'])
                           ->whereNull('parent_id');
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
            $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail']);
        }

        $currentUserId = (string)(Auth::id() ?? '');
        $subAccounts = User::where(function($q) {
                $q->where('role', 'sub_account')
                  ->orWhere('role', 'staff')
                  ->orWhere('role', 'kiemduyet')
                  ->orWhere('role', 'moderator')
                  ->orWhere('role', 'doanhnghiep')
                  ->orWhere('role', 'enterprise')
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

            // Xử lý danh sách tòa nhà được phân công
            $assignedIds = (array) ($account->assigned_buildings ?? []);
            if (empty($assignedIds) || in_array('all', $assignedIds, true) || in_array('*', $assignedIds, true)) {
                $account->is_all_buildings = true;
                $account->assigned_buildings_details = $allBuildings;
            } else {
                $account->is_all_buildings = false;
                $assignedBuildings = $allBuildings->filter(function ($b) use ($assignedIds) {
                    $bId = (string)($b->_id ?? $b->id);
                    return in_array($bId, array_map('strval', $assignedIds));
                })->values();

                $account->assigned_buildings_details = $assignedBuildings;
            }

            // Chuẩn hóa quyền hạn (mặc định nếu trống)
            if (empty($account->permissions)) {
                $account->permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];
            }

            // Chuẩn hóa chức vụ hiển thị
            if (empty($account->staff_role)) {
                $account->staff_role = 'Nhân viên';
            }

            // Đảm bảo plain_password có giá trị cho tài khoản con
            if (empty($account->plain_password)) {
                $account->plain_password = '123456';
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
            $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail']);
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

            // Tòa nhà phân công
            $assignedBuildings = !empty($validated['assigned_buildings']) 
                ? array_values(array_unique($validated['assigned_buildings'])) 
                : ['all'];

            // Quyền hạn nghiệp vụ - Cố định toàn quyền 4 nghiệp vụ
            $permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];

            $roleInput = $request->input('role', 'staff');
            $staffRole = !empty($validated['staff_role']) ? $validated['staff_role'] : 'Nhân viên';

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
                'permissions'        => $permissions,
                'status'             => 'active',
                'is_locked'          => false,
                'is_deleted'         => false,
            ]);

            // Đồng bộ sang Building: Gán manager_id nếu tòa nhà chưa có người phụ trách
            $staffId = (string)($subAccount->_id ?? $subAccount->id);
            if (!empty($assignedBuildings) && !in_array('all', $assignedBuildings, true)) {
                Building::whereIn('_id', $assignedBuildings)->get()->each(function($bld) use ($staffId) {
                    $mIds = is_array($bld->manager_ids) ? $bld->manager_ids : [];
                    if (!in_array($staffId, $mIds, true)) {
                        $mIds[] = $staffId;
                        $bld->manager_ids = array_values(array_unique($mIds));
                    }
                    if (empty($bld->manager_id)) {
                        $bld->manager_id = $staffId;
                    }
                    $bld->save();
                });
            }

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

            if ($request->has('role') && !empty($request->input('role'))) {
                $subAccount->role = $request->input('role');
            }

            if (!empty($validated['staff_role'])) {
                $subAccount->staff_role = $validated['staff_role'];
            }

            if (isset($validated['assigned_buildings'])) {
                $subAccount->assigned_buildings = !empty($validated['assigned_buildings']) 
                    ? array_values(array_unique($validated['assigned_buildings'])) 
                    : ['all'];
            }

            // Quyền hạn nghiệp vụ - Cố định toàn quyền 4 nghiệp vụ
            $subAccount->permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];

            if (!empty($validated['password'])) {
                $subAccount->password = Hash::make($validated['password']);
                $subAccount->plain_password = $validated['password'];
            }

            $subAccount->save();

            // Đồng bộ sang Building: Gán manager_id cho các tòa nhà được phân công
            $staffId = (string)($subAccount->_id ?? $subAccount->id);
            if (isset($validated['assigned_buildings'])) {
                $assignedList = (array)($subAccount->assigned_buildings ?? []);
                if (!empty($assignedList) && !in_array('all', $assignedList, true)) {
                    Building::whereIn('_id', $assignedList)->get()->each(function($bld) use ($staffId) {
                        $mIds = is_array($bld->manager_ids) ? $bld->manager_ids : [];
                        if (!in_array($staffId, $mIds, true)) {
                            $mIds[] = $staffId;
                            $bld->manager_ids = array_values(array_unique($mIds));
                        }
                        if (empty($bld->manager_id)) {
                            $bld->manager_id = $staffId;
                        }
                        $bld->save();
                    });
                }
            }

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
    public function toggleLock($id)
    {
        if (!$this->checkPermission()) {
            return response()->json(['error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp RentHome mới có quyền thao tác.'], 403);
        }

        $subAccount = User::find($id);
        if (!$subAccount || !$subAccount->isSubAccount()) {
            return response()->json(['error' => 'Không tìm thấy tài khoản con!'], 404);
        }

        $isCurrentlyLocked = ($subAccount->is_locked === true || $subAccount->status === 'locked');
        $subAccount->is_locked = !$isCurrentlyLocked;
        $subAccount->status = $isCurrentlyLocked ? 'active' : 'locked';
        $subAccount->locked_at = $isCurrentlyLocked ? null : now()->format('d/m/Y H:i');
        $subAccount->save();

        $actionText = $isCurrentlyLocked ? 'Mở khóa' : 'Khóa';
        return response()->json([
            'success' => true,
            'message' => "Đã {$actionText} tài khoản con thành công!",
            'is_locked' => $subAccount->is_locked,
            'status' => $subAccount->status
        ]);
    }

    /**
     * Khóa hoặc mở khóa tài khoản khách hàng cá nhân
     */
    public function toggleLockCustomer($id)
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
        $customer->is_locked = !$isCurrentlyLocked;
        $customer->status = $isCurrentlyLocked ? 'active' : 'locked';
        $customer->locked_at = $isCurrentlyLocked ? null : now()->format('d/m/Y H:i');
        $customer->save();

        $actionText = $isCurrentlyLocked ? 'Mở khóa' : 'Khóa';
        return response()->json([
            'success' => true,
            'message' => "Đã {$actionText} tài khoản khách hàng thành công!",
            'is_locked' => $customer->is_locked,
            'status' => $customer->status
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

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tài khoản khách hàng khỏi hệ thống thành công!'
        ]);
    }
}

