<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SubAccountController extends Controller
{
    /**
     * Lấy danh sách tài khoản con của Doanh nghiệp RentHome
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user || (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin')) {
            return response()->json(['error' => 'Chỉ tài khoản Doanh nghiệp RentHome mới có quyền quản lý tài khoản con.'], 403);
        }

        try {
            $subAccounts = User::where('account_type', 'sub_account')
                ->where(function($q) {
                    $q->whereNull('is_deleted')->orWhere('is_deleted', false);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Lấy danh sách tất cả tòa nhà của RentHome để map tên và trả về cho client
            $allBuildings = Building::all(['_id', 'id', 'name', 'project', 'total_rooms', 'province', 'district', 'ward', 'address_detail']);

            $subAccounts->transform(function ($account) use ($allBuildings) {
                $assignedIds = (array) ($account->assigned_buildings ?? []);
                $assignedBuildings = $allBuildings->filter(function ($b) use ($assignedIds) {
                    $bId = (string)($b->_id ?? $b->id);
                    return in_array($bId, array_map('strval', $assignedIds));
                })->values();

                $account->assigned_buildings_details = $assignedBuildings;
                return $account;
            });

            return response()->json([
                'success' => true,
                'sub_accounts' => $subAccounts,
                'all_buildings' => $allBuildings
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách tài khoản con: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Doanh nghiệp RentHome tạo tài khoản con mới và phân quyền tòa nhà
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin')) {
            return response()->json(['error' => 'Chỉ tài khoản Doanh nghiệp RentHome mới có quyền tạo tài khoản con.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'assigned_buildings' => 'required|array|min:1',
            'assigned_buildings.*' => 'string'
        ], [
            'name.required' => 'Vui lòng nhập tên người quản lý.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu khởi tạo.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'assigned_buildings.required' => 'Vui lòng chọn ít nhất 1 tòa nhà để phân quyền quản lý.',
            'assigned_buildings.min' => 'Vui lòng chọn ít nhất 1 tòa nhà để phân quyền quản lý.',
        ]);

        try {
            $subAccount = User::create([
                'username' => explode('@', $validated['email'])[0],
                'account_name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'account_type' => 'sub_account',
                'role' => 'sub_account',
                'parent_id' => (string)($user->_id ?? $user->id),
                'assigned_buildings' => array_values(array_unique($validated['assigned_buildings'])),
                'status' => 'active',
                'is_locked' => false,
                'is_deleted' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo tài khoản con thành công! Nhân viên đã được phân quyền quản lý tòa nhà chỉ định.',
                'sub_account' => $subAccount
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo tài khoản con: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cập nhật thông tin và phân quyền tòa nhà cho tài khoản con
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin')) {
            return response()->json(['error' => 'Chỉ tài khoản Doanh nghiệp RentHome mới có quyền cập nhật tài khoản con.'], 403);
        }

        $subAccount = User::find($id);
        if (!$subAccount || $subAccount->account_type !== 'sub_account') {
            return response()->json(['error' => 'Không tìm thấy tài khoản con!'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'assigned_buildings' => 'required|array|min:1',
            'assigned_buildings.*' => 'string'
        ], [
            'name.required' => 'Vui lòng nhập tên người quản lý.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên nếu muốn đổi.',
            'assigned_buildings.required' => 'Vui lòng chọn ít nhất 1 tòa nhà để phân quyền quản lý.',
            'assigned_buildings.min' => 'Vui lòng chọn ít nhất 1 tòa nhà để phân quyền quản lý.',
        ]);

        try {
            $subAccount->account_name = $validated['name'];
            $subAccount->phone = $validated['phone'];
            $subAccount->assigned_buildings = array_values(array_unique($validated['assigned_buildings']));

            if (!empty($validated['password'])) {
                $subAccount->password = Hash::make($validated['password']);
            }

            $subAccount->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phân quyền tòa nhà cho tài khoản con thành công!',
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
        $user = Auth::user();
        if (!$user || (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin')) {
            return response()->json(['error' => 'Chỉ tài khoản Doanh nghiệp RentHome mới có quyền thao tác.'], 403);
        }

        $subAccount = User::find($id);
        if (!$subAccount || $subAccount->account_type !== 'sub_account') {
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
     * Xóa tài khoản con
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin')) {
            return response()->json(['error' => 'Chỉ tài khoản Doanh nghiệp RentHome mới có quyền xóa tài khoản con.'], 403);
        }

        $subAccount = User::find($id);
        if (!$subAccount || $subAccount->account_type !== 'sub_account') {
            return response()->json(['error' => 'Không tìm thấy tài khoản con!'], 404);
        }

        $subAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tài khoản con khỏi hệ thống thành công!'
        ]);
    }
}
