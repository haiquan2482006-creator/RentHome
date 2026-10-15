<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Building;
use App\Models\User;
use App\Models\Post;
use App\Models\Contract;
use App\Models\Notification;

class BuildingController extends Controller
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
     * Hiển thị giao diện Quản Lý BĐS & Tòa Nhà dành cho Admin
     */
    public function adminView()
    {
        if (!$this->checkPermission()) {
            return redirect('/login')->withErrors(['login_error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp mới có quyền truy cập trang Quản lý BĐS & Tòa Nhà.']);
        }

        try {
            // Lấy danh sách tất cả các tòa nhà từ MongoDB
            $buildings = Building::orderBy('created_at', 'desc')->get();

            // Lấy danh sách nhân viên nội bộ (role = staff hoặc sub_account)
            $staffList = User::where(function($q) {
                    $q->where('role', 'staff')
                      ->orWhere('role', 'sub_account')
                      ->orWhereNotNull('parent_id');
                })
                ->where(function($q) {
                    $q->whereNull('is_deleted')->orWhere('is_deleted', false);
                })
                ->orderBy('account_name', 'asc')
                ->get();

            // Chuẩn hóa dữ liệu cho từng tòa nhà (gán thông tin nhân viên phụ trách & số phòng thực tế)
            $buildings->transform(function ($b) use ($staffList) {
                $bId = (string)($b->_id ?? $b->id);

                // 1. Tìm theo manager_id / manager_ids lưu trên tòa nhà
                $mIds = array_filter(array_merge(
                    !empty($b->manager_id) ? [(string)$b->manager_id] : [],
                    is_array($b->manager_ids) ? array_map('strval', $b->manager_ids) : [],
                    is_array($b->user_ids) ? array_map('strval', $b->user_ids) : []
                ));

                $manager = null;
                if (!empty($mIds)) {
                    $manager = $staffList->first(function($s) use ($mIds) {
                        return in_array((string)($s->_id ?? $s->id), $mIds, true);
                    });
                }

                // 2. Tìm theo assigned_buildings của nhân viên nếu tòa nhà chưa gán manager_id
                $assignedStaffs = $staffList->filter(function($s) use ($bId, $mIds) {
                    $sId = (string)($s->_id ?? $s->id);
                    if (in_array($sId, $mIds, true)) return true;
                    $assigned = (array)($s->assigned_buildings ?? $s->building_ids ?? []);
                    return in_array($bId, array_map('strval', $assigned), true);
                })->values();

                if (!$manager && $assignedStaffs->isNotEmpty()) {
                    $manager = $assignedStaffs->first();
                }

                $b->manager_info = $manager;
                $b->assigned_staffs = $assignedStaffs->isNotEmpty() ? $assignedStaffs : ($manager ? collect([$manager]) : collect([]));

                // Tự động đồng bộ manager_id vào DB nếu tòa nhà chưa lưu
                if (empty($b->manager_id) && $manager) {
                    try {
                        $b->manager_id = (string)($manager->_id ?? $manager->id);
                        $b->save();
                    } catch (\Exception $e) {}
                }

                // Đếm số phòng bài đăng đã tạo thuộc tòa này
                $b->actual_rooms_count = Post::where('building_id', $bId)->count();

                return $b;
            });

            // Thống kê nhanh
            $stats = [
                'total_buildings' => $buildings->count(),
                'total_rooms' => $buildings->sum(fn($b) => (int)($b->total_rooms ?? 0)),
                'assigned_buildings' => $buildings->filter(fn($b) => !empty($b->manager_id))->count(),
            ];

            $staffs = $staffList;
            return view('Admin.qltoa_nha', compact('buildings', 'staffList', 'staffs', 'stats'));

        } catch (\Exception $e) {
            Log::error('Lỗi tải trang quản lý tòa nhà Admin: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * API: Lấy danh sách Tòa nhà
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
            }

            if ($user->isSubAccount()) {
                $assignedIds = (array) ($user->assigned_buildings ?? []);
                if (empty($assignedIds) || in_array('all', $assignedIds, true) || in_array('*', $assignedIds, true)) {
                    $buildings = Building::orderBy('created_at', 'desc')->get();
                } else {
                    $buildings = Building::whereIn('_id', $assignedIds)->orderBy('created_at', 'desc')->get();
                }
            } elseif ($user->isEnterprise() || $user->isAdmin()) {
                $buildings = Building::orderBy('created_at', 'desc')->get();
            } else {
                $buildings = collect();
            }

            return response()->json([
                'success' => true,
                'buildings' => $buildings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Lấy chi tiết 1 tòa nhà để đổ vào Modal Sửa
     */
    public function getDetail($id)
    {
        try {
            $building = Building::where('_id', $id)->first();
            if (!$building) {
                $building = Building::find($id);
            }

            if (!$building) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy tòa nhà!'], 404);
            }

            if (empty($building->manager_id)) {
                $assignedStaff = User::where(function($q) {
                        $q->where('role', 'staff')
                          ->orWhere('role', 'sub_account')
                          ->orWhereNotNull('parent_id');
                    })
                    ->where(function($q) use ($id) {
                        $q->where('assigned_buildings', $id)
                          ->orWhere('building_ids', $id);
                    })
                    ->first();
                if ($assignedStaff) {
                    $building->manager_id = (string)($assignedStaff->_id ?? $assignedStaff->id);
                }
            }

            return response()->json([
                'success' => true,
                'building' => $building
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Tạo mới Tòa nhà / Cơ sở vào MongoDB
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
            }

            if (!$this->checkPermission() && !$user->isSubAccount()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện chức năng này!'
                ], 403);
            }

            // Chuẩn hóa tên địa chỉ text nếu form gửi qua input hidden province_name, district_name, ward_name
            if ($request->filled('province_name')) {
                $request->merge(['province' => $request->input('province_name')]);
            }
            if ($request->filled('district_name')) {
                $request->merge(['district' => $request->input('district_name')]);
            }
            if ($request->has('ward_name')) {
                $request->merge(['ward' => $request->input('ward_name') ?: '']);
            }

            // Đồng bộ type và business_type
            if ($request->filled('type') && !$request->filled('business_type')) {
                $request->merge(['business_type' => $request->input('type')]);
            }
            if ($request->filled('business_type') && !$request->filled('type')) {
                $request->merge(['type' => $request->input('business_type')]);
            }

            // Validate kỹ các trường theo 4 nhóm yêu cầu
            $validated = $request->validate([
                // Nhóm 1: Thông tin định danh
                'name' => 'required|string|max:255',
                'type' => 'nullable|string',
                'business_type' => 'required|string',
                'province' => 'required|string',
                'district' => 'required|string',
                'ward' => 'nullable|string',
                'address_detail' => 'required|string',

                // Nhóm 2: Bảng giá dịch vụ mặc định (chỉ bắt buộc nếu không phải BĐS Đơn Lập / Nhà liền kề: nha_rieng, cum_nha_lien_ke, dat_nen, dat_phan_lo)
                'electricity_price' => 'required_unless:type,cum_nha_lien_ke,nha_rieng,dat_nen,nha_nguyen_can,dat_phan_lo,Đất nền,Nhà ở / Nhà riêng lẻ,Cụm nhà liền kề / Dự án,Mặt bằng kinh doanh,Đất phân lô|nullable|numeric|min:0',
                'water_price' => 'required_unless:type,cum_nha_lien_ke,nha_rieng,dat_nen,nha_nguyen_can,dat_phan_lo,Đất nền,Nhà ở / Nhà riêng lẻ,Cụm nhà liền kề / Dự án,Mặt bằng kinh doanh,Đất phân lô|nullable|numeric|min:0',
                'water_calc_type' => 'nullable|string|in:m3,person',
                'service_fee' => 'nullable|numeric|min:0',
                'internet_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',

                // Nhóm 3: Tiện ích & Quy mô
                'total_floors' => 'nullable|numeric|min:1',
                'total_rooms' => 'nullable|numeric|min:1',
                'amenities' => 'nullable|array',
                'description' => 'nullable|string',
                'images' => 'nullable|array|max:5',
                'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,jfif|max:10240',
                'image' => 'nullable',

                // Nhóm 4: Phân công nội bộ
                'manager_id' => 'nullable|string',
                'manager_ids' => 'nullable|array',
                'manager_ids.*' => 'nullable|string',
                'status' => 'nullable|string|in:active,maintenance',
            ], [
                'name.required' => 'Vui lòng nhập tên tòa nhà / cơ sở.',
                'business_type.required' => 'Vui lòng chọn loại hình kinh doanh.',
                'province.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
                'district.required' => 'Vui lòng chọn Quận / Huyện.',
                'address_detail.required' => 'Vui lòng nhập số nhà / tên đường.',
                'electricity_price.required_unless' => 'Vui lòng nhập đơn giá điện.',
                'electricity_price.numeric' => 'Đơn giá điện phải là số.',
                'water_price.required_unless' => 'Vui lòng nhập đơn giá nước.',
                'water_price.numeric' => 'Đơn giá nước phải là số.',
                'images.max' => 'Bạn chỉ được chọn tối đa 5 ảnh cho mỗi cơ sở / tòa nhà.',
            ]);

            $typeLabels = [
                'can_ho'          => 'Chung cư mini / Căn hộ dịch vụ',
                'nha_tro'         => 'Nhà trọ / Khu trọ',
                'ky_tuc_xa'       => 'Ký túc xá / Sleepbox',
                'dat_phan_lo'     => 'Đất phân lô / Đất nền',
                'nha_rieng'       => 'Nhà ở / Nhà riêng lẻ',
                'cum_nha_lien_ke' => 'Cụm nhà liền kề / Dự án',
                'dat_nen'         => 'Mặt bằng kinh doanh',
                // Tương thích ngược với các bản ghi cũ
                'can_ho_dich_vu'  => 'Chung cư mini / Căn hộ dịch vụ',
                'chung_cu_mini'   => 'Chung cư mini / Căn hộ dịch vụ',
                'nha_nguyen_can'  => 'Nhà ở / Nhà riêng lẻ',
            ];
            $rawType = $validated['type'] ?? $validated['business_type'];
            $humanType = $typeLabels[$rawType] ?? $rawType;

            $reqGroup = $request->input('property_group');
            $isSingleProperty = $reqGroup === 'single' || (!$reqGroup && in_array(strtolower($rawType), [
                'nha_rieng', 'dat_phan_lo', 'cum_nha_lien_ke', 'nha_nguyen_can', 'nhà ở / nhà riêng lẻ', 'đất phân lô / đất nền', 'đất phân lô', 'cụm nhà liền kề / dự án'
            ]));

            // Dữ liệu chuẩn bị lưu vào MongoDB
            $data = [
                'name' => trim($validated['name']),
                'type' => $rawType,
                'business_type' => $humanType,
                'property_group' => $isSingleProperty ? 'single' : 'multi',
                'province' => trim($validated['province']),
                'district' => trim($validated['district']),
                'ward' => !empty($validated['ward']) ? trim($validated['ward']) : '',
                'address_detail' => trim($validated['address_detail']),

                'electricity_price' => (!$isSingleProperty && isset($validated['electricity_price']) && $validated['electricity_price'] !== '') ? (float)$validated['electricity_price'] : 0,
                'water_price' => (!$isSingleProperty && isset($validated['water_price']) && $validated['water_price'] !== '') ? (float)$validated['water_price'] : 0,
                'water_calc_type' => $validated['water_calc_type'] ?? 'm3',
                'service_fee' => (!$isSingleProperty && isset($validated['service_fee'])) ? (float)$validated['service_fee'] : 0,
                'internet_fee' => (!$isSingleProperty && isset($validated['internet_fee'])) ? (float)$validated['internet_fee'] : 0,
                'parking_fee' => (!$isSingleProperty && isset($validated['parking_fee'])) ? (float)$validated['parking_fee'] : 0,

                'total_floors' => !empty($validated['total_floors']) ? (int)$validated['total_floors'] : 1,
                'total_rooms' => !empty($validated['total_rooms']) ? (int)$validated['total_rooms'] : 1,
                'amenities' => !empty($validated['amenities']) ? array_values(array_unique($validated['amenities'])) : [],
                'description' => $validated['description'] ?? '',

                'manager_id' => !empty($validated['manager_id']) ? (string)$validated['manager_id'] : null,
                'status' => $validated['status'] ?? 'active',

                'user_id' => (string)($user->_id ?? $user->id),
                'parent_id' => $user->parent_id ?: (string)($user->_id ?? $user->id),
            ];

            // Xử lý lưu nhiều File ảnh lên MongoDB (tối đa 5 ảnh vào collection images)
            $savedImageIds = [];
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                if (!is_array($files)) {
                    $files = [$files];
                }
                // Giới hạn tối đa 5 ảnh
                $files = array_slice($files, 0, 5);
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('buildings', 'public');
                        $savedImageIds[] = '/storage/' . $path;
                    }
                }
            } elseif ($request->hasFile('image')) {
                // Fallback nếu gửi 1 ảnh qua trường 'image'
                $file = $request->file('image');
                if ($file && $file->isValid()) {
                    $path = $file->store('buildings', 'public');
                    $savedImageIds[] = '/storage/' . $path;
                }
            } elseif ($request->filled('image_url')) {
                $savedImageIds[] = $request->input('image_url');
            }

            $data['images'] = $savedImageIds;
            $data['image'] = !empty($savedImageIds) ? $savedImageIds[0] : null;

            // Lưu vào collection buildings trong MongoDB
            $managerIds = [];
            if ($request->has('manager_ids') && is_array($request->manager_ids)) {
                $managerIds = array_values(array_filter($request->manager_ids));
            } elseif (!empty($data['manager_id'])) {
                $managerIds = [$data['manager_id']];
            }
            $data['manager_ids'] = $managerIds;
            $data['user_ids'] = $managerIds;
            if (empty($data['manager_id']) && count($managerIds) > 0) {
                $data['manager_id'] = $managerIds[0];
            }

            $building = Building::create($data);
            $bId = (string)($building->_id ?? $building->id);

            // Gán quan hệ Nhiều - Nhiều (Many-to-Many) giữa Building và User
            if (!empty($managerIds)) {
                try {
                    $building->users()->sync($managerIds);
                } catch (\Exception $e) {
                    Log::warning('Lỗi sync users belongsToMany: ' . $e->getMessage());
                }

                // Cập nhật assigned_buildings & building_ids cho các nhân viên
                User::whereIn('_id', $managerIds)->get()->each(function ($staff) use ($bId) {
                    $assigned = (array)($staff->assigned_buildings ?? []);
                    if (!in_array('all', $assigned, true) && !in_array($bId, $assigned, true)) {
                        $assigned[] = $bId;
                        $staff->assigned_buildings = array_values(array_unique($assigned));
                    }
                    $bIds = (array)($staff->building_ids ?? []);
                    if (!in_array($bId, $bIds, true)) {
                        $bIds[] = $bId;
                        $staff->building_ids = array_values(array_unique($bIds));
                    }
                    $staff->save();
                });
            }

            // Tự động tạo 1 bài đăng Post nếu property_group là 'single_room' hoặc 'single'
            $pGroup = $request->input('property_group') ?: ($building->property_group ?? '');
            if (in_array($pGroup, ['single_room', 'single'])) {
                if (class_exists(\App\Models\Post::class)) {
                    Post::create([
                        'building_id' => $bId,
                        'title'       => 'Toàn bộ BĐS - ' . $building->name,
                        'room_number' => 'Toàn bộ',
                        'status'      => 'available',
                        'price'       => 0,
                        'area'        => 0,
                        'user_id'     => (string)($user->_id ?? $user->id),
                        'parent_id'   => $user->parent_id ?: (string)($user->_id ?? $user->id),
                    ]);
                }
            }

            // 4. Response: Hỗ trợ cả AJAX JSON (Modal) và Form Submit Redirect kèm Flash Message
            if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Tạo bất động sản / cơ sở thành công!',
                    'building' => $building
                ]);
            }

            return redirect()->back()->with('success', 'Tạo bất động sản thành công!');

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => collect($ve->errors())->first()[0] ?? 'Dữ liệu không hợp lệ!',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo tòa nhà: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu vào Database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Cập nhật thông tin Tòa nhà / Cơ sở
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
            }

            if (!$this->checkPermission() && !$user->isSubAccount()) {
                return response()->json(['success' => false, 'message' => 'Không có quyền chỉnh sửa!'], 403);
            }

            $building = Building::where('_id', $id)->first();
            if (!$building) {
                $building = Building::find($id);
            }

            if (!$building) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy tòa nhà cần cập nhật!'], 404);
            }

            // Chuẩn hóa tên địa chỉ text nếu form gửi qua input hidden province_name, district_name, ward_name
            if ($request->filled('province_name')) {
                $request->merge(['province' => $request->input('province_name')]);
            }
            if ($request->filled('district_name')) {
                $request->merge(['district' => $request->input('district_name')]);
            }
            if ($request->has('ward_name')) {
                $request->merge(['ward' => $request->input('ward_name') ?: '']);
            }

            // Bảo vệ các trường lõi bị khóa (disabled) trong form edit: không bị ghi đè null
            if (!$request->filled('type') && !$request->filled('business_type')) {
                $request->merge(['type' => $building->type, 'business_type' => $building->business_type]);
            }
            if (!$request->filled('province')) {
                $request->merge(['province' => $building->province]);
            }
            if (!$request->filled('district')) {
                $request->merge(['district' => $building->district]);
            }
            if (!$request->has('ward') || $request->input('ward') === '') {
                $request->merge(['ward' => $building->ward ?? '']);
            }

            // Đồng bộ type và business_type
            if ($request->filled('type') && !$request->filled('business_type')) {
                $request->merge(['business_type' => $request->input('type')]);
            }
            if ($request->filled('business_type') && !$request->filled('type')) {
                $request->merge(['type' => $request->input('business_type')]);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'nullable|string',
                'business_type' => 'required|string',
                'province' => 'required|string',
                'district' => 'required|string',
                'ward' => 'nullable|string',
                'address_detail' => 'required|string',

                'electricity_price' => 'required_unless:type,cum_nha_lien_ke,nha_rieng,dat_nen,nha_nguyen_can,dat_phan_lo,Đất nền,Nhà ở / Nhà riêng lẻ,Cụm nhà liền kề / Dự án,Mặt bằng kinh doanh,Đất phân lô|nullable|numeric|min:0',
                'water_price' => 'required_unless:type,cum_nha_lien_ke,nha_rieng,dat_nen,nha_nguyen_can,dat_phan_lo,Đất nền,Nhà ở / Nhà riêng lẻ,Cụm nhà liền kề / Dự án,Mặt bằng kinh doanh,Đất phân lô|nullable|numeric|min:0',
                'water_calc_type' => 'nullable|string|in:m3,person',
                'service_fee' => 'nullable|numeric|min:0',
                'internet_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',

                'total_floors' => 'nullable|numeric|min:1',
                'total_rooms' => 'nullable|numeric|min:1',
                'amenities' => 'nullable|array',
                'description' => 'nullable|string',
                'images' => 'nullable|array|max:5',
                'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,jfif|max:10240',
                'existing_images' => 'nullable|array',
                'image' => 'nullable',

                'manager_id' => 'nullable|string',
                'status' => 'nullable|string|in:active,maintenance',
            ], [
                'name.required' => 'Vui lòng nhập tên tòa nhà / cơ sở.',
                'business_type.required' => 'Vui lòng chọn loại hình kinh doanh.',
                'province.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
                'district.required' => 'Vui lòng chọn Quận / Huyện.',
                'address_detail.required' => 'Vui lòng nhập số nhà / tên đường.',
                'electricity_price.required_unless' => 'Vui lòng nhập đơn giá điện.',
                'water_price.required_unless' => 'Vui lòng nhập đơn giá nước.',
                'images.max' => 'Bạn chỉ được lưu tối đa 5 ảnh cho mỗi cơ sở / tòa nhà.',
            ]);

            $typeLabels = [
                'can_ho'          => 'Chung cư mini / Căn hộ dịch vụ',
                'nha_tro'         => 'Nhà trọ / Khu trọ',
                'ky_tuc_xa'       => 'Ký túc xá / Sleepbox',
                'dat_phan_lo'     => 'Đất phân lô / Đất nền',
                'nha_rieng'       => 'Nhà ở / Nhà riêng lẻ',
                'cum_nha_lien_ke' => 'Cụm nhà liền kề / Dự án',
                'dat_nen'         => 'Mặt bằng kinh doanh',
                // Tương thích ngược với các bản ghi cũ
                'can_ho_dich_vu'  => 'Chung cư mini / Căn hộ dịch vụ',
                'chung_cu_mini'   => 'Chung cư mini / Căn hộ dịch vụ',
                'nha_nguyen_can'  => 'Nhà ở / Nhà riêng lẻ',
            ];
            $rawType = $validated['type'] ?? $validated['business_type'];
            $humanType = $typeLabels[$rawType] ?? $rawType;

            $reqGroup = $request->input('property_group');
            $isSingleProperty = $reqGroup === 'single' || (!$reqGroup && in_array(strtolower($rawType), [
                'nha_rieng', 'dat_phan_lo', 'cum_nha_lien_ke', 'nha_nguyen_can', 'nhà ở / nhà riêng lẻ', 'đất phân lô / đất nền', 'đất phân lô', 'cụm nhà liền kề / dự án'
            ]));

            $oldManagerId = $building->manager_id;
            $newManagerId = !empty($validated['manager_id']) ? (string)$validated['manager_id'] : null;

            // 1. Lưu lại toàn bộ dữ liệu ban đầu để so sánh và tạo danh sách các mục đã thay đổi
            $oldData = [
                'name'              => (string)($building->name ?? ''),
                'address_detail'    => (string)($building->address_detail ?? ''),
                'electricity_price' => (float)($building->electricity_price ?? 0),
                'water_price'       => (float)($building->water_price ?? 0),
                'water_calc_type'   => (string)($building->water_calc_type ?? 'm3'),
                'service_fee'       => (float)($building->service_fee ?? 0),
                'internet_fee'      => (float)($building->internet_fee ?? 0),
                'parking_fee'       => (float)($building->parking_fee ?? 0),
                'total_floors'      => (int)($building->total_floors ?? 0),
                'total_rooms'       => (int)($building->total_rooms ?? 0),
                'amenities'         => (array)($building->amenities ?? []),
                'description'       => (string)($building->description ?? ''),
                'status'            => (string)($building->status ?? 'active'),
                'manager_id'        => (string)($building->manager_id ?? ''),
                'images'            => (array)($building->images ?: ($building->image ? [$building->image] : [])),
            ];

            $building->name = trim($validated['name']);
            $building->type = $rawType;
            $building->business_type = $humanType;
            $building->property_group = $isSingleProperty ? 'single' : 'multi';
            $building->province = trim($validated['province']);
            $building->district = trim($validated['district']);
            $building->ward = !empty($validated['ward']) ? trim($validated['ward']) : '';
            $building->address_detail = trim($validated['address_detail']);

            $building->electricity_price = (!$isSingleProperty && isset($validated['electricity_price']) && $validated['electricity_price'] !== '') ? (float)$validated['electricity_price'] : 0;
            $building->water_price = (!$isSingleProperty && isset($validated['water_price']) && $validated['water_price'] !== '') ? (float)$validated['water_price'] : 0;
            $building->water_calc_type = $validated['water_calc_type'] ?? 'm3';
            $building->service_fee = (!$isSingleProperty && isset($validated['service_fee'])) ? (float)$validated['service_fee'] : 0;
            $building->internet_fee = (!$isSingleProperty && isset($validated['internet_fee'])) ? (float)$validated['internet_fee'] : 0;
            $building->parking_fee = (!$isSingleProperty && isset($validated['parking_fee'])) ? (float)$validated['parking_fee'] : 0;

            if (isset($validated['total_floors'])) {
                $building->total_floors = (int)$validated['total_floors'];
            }
            if (isset($validated['total_rooms'])) {
                $building->total_rooms = (int)$validated['total_rooms'];
            }
            if (isset($validated['amenities'])) {
                $building->amenities = array_values(array_unique($validated['amenities']));
            }
            if (isset($validated['description'])) {
                $building->description = $validated['description'];
            }
            if (isset($validated['status'])) {
                $building->status = $validated['status'];
            }

            $building->manager_id = $newManagerId;

            // Xử lý ảnh khi cập nhật: giữ lại existing_images và thêm images mới tải lên (tổng tối đa 5)
            $finalImageIds = [];
            if ($request->has('existing_images')) {
                $existing = $request->input('existing_images');
                if (is_array($existing)) {
                    $finalImageIds = array_values(array_filter($existing));
                }
            } elseif (!$request->hasFile('images') && !$request->hasFile('image')) {
                // Nếu client không gửi danh sách existing_images và không upload ảnh mới, giữ nguyên
                $finalImageIds = (array)($building->images ?: ($building->image ? [$building->image] : []));
            }

            // Upload các file ảnh mới thêm vào
            $newFiles = [];
            if ($request->hasFile('images')) {
                $f = $request->file('images');
                $newFiles = is_array($f) ? $f : [$f];
            } elseif ($request->hasFile('image')) {
                $newFiles = [$request->file('image')];
            }

            $remainingSlots = max(0, 5 - count($finalImageIds));
            $newFiles = array_slice($newFiles, 0, $remainingSlots);

            foreach ($newFiles as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('buildings', 'public');
                    $finalImageIds[] = '/storage/' . $path;
                }
            }

            $building->images = array_values(array_slice($finalImageIds, 0, 5));
            $building->image = !empty($building->images) ? $building->images[0] : null;

            $building->save();
            $bId = (string)($building->_id ?? $building->id);

            // Đồng bộ phân công nhân viên nếu có thay đổi
            if ($newManagerId !== $oldManagerId && $newManagerId) {
                $newStaff = User::find($newManagerId);
                if ($newStaff) {
                    $assigned = (array)($newStaff->assigned_buildings ?? []);
                    if (!in_array('all', $assigned, true) && !in_array($bId, $assigned, true)) {
                        $assigned[] = $bId;
                        $newStaff->assigned_buildings = array_values(array_unique($assigned));
                        $newStaff->save();
                    }
                }
            }

            // 2. Tự động so sánh và tạo danh sách các nội dung đã thay đổi
            $detectedChanges = [];

            if ($oldData['name'] !== (string)$building->name) {
                $detectedChanges[] = 'Tên tòa nhà: "' . ($oldData['name'] ?: 'Chưa đặt') . '" ➔ "' . $building->name . '"';
            }

            if ($oldData['address_detail'] !== (string)$building->address_detail) {
                $detectedChanges[] = 'Địa chỉ chi tiết: "' . ($oldData['address_detail'] ?: 'Chưa có') . '" ➔ "' . $building->address_detail . '"';
            }

            if ($oldData['electricity_price'] != (float)$building->electricity_price) {
                $detectedChanges[] = 'Đơn giá Điện: ' . number_format($oldData['electricity_price']) . ' đ ➔ ' . number_format((float)$building->electricity_price) . ' đ/kWh';
            }

            $oldWaterUnit = ($oldData['water_calc_type'] === 'person') ? 'đ/người' : 'đ/m³';
            $newWaterUnit = ($building->water_calc_type === 'person') ? 'đ/người' : 'đ/m³';
            if ($oldData['water_price'] != (float)$building->water_price || $oldData['water_calc_type'] !== (string)$building->water_calc_type) {
                $detectedChanges[] = 'Đơn giá Nước: ' . number_format($oldData['water_price']) . " {$oldWaterUnit} ➔ " . number_format((float)$building->water_price) . " {$newWaterUnit}";
            }

            if ($oldData['service_fee'] != (float)$building->service_fee) {
                $detectedChanges[] = 'Phí dịch vụ chung: ' . number_format($oldData['service_fee']) . ' đ ➔ ' . number_format((float)$building->service_fee) . ' đ/tháng';
            }

            if ($oldData['internet_fee'] != (float)$building->internet_fee) {
                $detectedChanges[] = 'Phí Internet/Wifi: ' . number_format($oldData['internet_fee']) . ' đ ➔ ' . number_format((float)$building->internet_fee) . ' đ/tháng';
            }

            if ($oldData['parking_fee'] != (float)$building->parking_fee) {
                $detectedChanges[] = 'Phí gửi xe: ' . number_format($oldData['parking_fee']) . ' đ ➔ ' . number_format((float)$building->parking_fee) . ' đ/xe';
            }

            if ($oldData['total_floors'] != (int)$building->total_floors && $building->total_floors) {
                $detectedChanges[] = 'Số tầng: ' . ($oldData['total_floors'] ?: 0) . ' tầng ➔ ' . $building->total_floors . ' tầng';
            }

            if ($oldData['total_rooms'] != (int)$building->total_rooms && $building->total_rooms) {
                $unit = ($building->type === 'dat_phan_lo') ? 'lô' : (($building->type === 'cum_nha_lien_ke') ? 'căn' : (($building->type === 'dat_nen') ? 'mặt bằng' : 'phòng'));
                $detectedChanges[] = 'Quy mô: ' . ($oldData['total_rooms'] ?: 0) . " {$unit} ➔ " . $building->total_rooms . " {$unit}";
            }

            $oldAmenities = array_values(array_filter($oldData['amenities']));
            $newAmenities = array_values(array_filter((array)($building->amenities ?? [])));
            sort($oldAmenities);
            sort($newAmenities);
            if ($oldAmenities !== $newAmenities) {
                $added = array_values(array_diff($newAmenities, $oldAmenities));
                $removed = array_values(array_diff($oldAmenities, $newAmenities));
                $amenityParts = [];
                if (!empty($added)) {
                    $amenityParts[] = 'Thêm tiện ích (' . implode(', ', $added) . ')';
                }
                if (!empty($removed)) {
                    $amenityParts[] = 'Bớt tiện ích (' . implode(', ', $removed) . ')';
                }
                $detectedChanges[] = 'Tiện ích: ' . implode('; ', $amenityParts);
            }

            if ($oldData['images'] !== (array)($building->images ?: [])) {
                $detectedChanges[] = 'Hình ảnh: Đã cập nhật ảnh cơ sở (' . count((array)$building->images) . ' ảnh)';
            }

            if (trim($oldData['description']) !== trim((string)$building->description)) {
                $detectedChanges[] = 'Mô tả cơ sở: Đã cập nhật nội dung giới thiệu / lưu ý';
            }

            if ($oldData['status'] !== (string)$building->status) {
                $statusMap = ['active' => 'Đang hoạt động', 'maintenance' => 'Bảo trì / Sửa chữa'];
                $oldSt = $statusMap[$oldData['status']] ?? $oldData['status'];
                $newSt = $statusMap[$building->status] ?? $building->status;
                $detectedChanges[] = 'Trạng thái: "' . $oldSt . '" ➔ "' . $newSt . '"';
            }

            if ($oldData['manager_id'] !== (string)$building->manager_id) {
                $oldStaff = $oldData['manager_id'] ? User::find($oldData['manager_id']) : null;
                $newStaff = $building->manager_id ? User::find($building->manager_id) : null;
                $oldName = $oldStaff ? ($oldStaff->name ?? $oldStaff->username) : 'Chưa phân công';
                $newName = $newStaff ? ($newStaff->name ?? $newStaff->username) : 'Chưa phân công';
                $detectedChanges[] = 'Nhân viên phụ trách: "' . $oldName . '" ➔ "' . $newName . '"';
            }

            // Gửi thông báo đến tài khoản nhân viên đang phụ trách tòa nhà
            try {
                $targetStaffIds = [];
                if (!empty($newManagerId)) {
                    $targetStaffIds[] = (string)$newManagerId;
                }
                if (!empty($building->manager_id)) {
                    $targetStaffIds[] = (string)$building->manager_id;
                }
                if (is_array($building->manager_ids)) {
                    foreach ($building->manager_ids as $mid) {
                        if (!empty($mid)) $targetStaffIds[] = (string)$mid;
                    }
                }
                if (is_array($building->user_ids)) {
                    foreach ($building->user_ids as $uid) {
                        if (!empty($uid)) $targetStaffIds[] = (string)$uid;
                    }
                }

                // Tìm thêm nhân viên có assigned_buildings chứa tòa nhà này
                $assignedStaffs = User::where(function($q) use ($bId) {
                    $q->where('assigned_buildings', $bId)
                      ->orWhere('assigned_buildings', 'all');
                })->get(['_id', 'id']);

                foreach ($assignedStaffs as $s) {
                    $targetStaffIds[] = (string)($s->_id ?? $s->id);
                }

                $targetStaffIds = array_values(array_unique(array_filter($targetStaffIds)));

                $actionUrl = url('/quan-ly-toa-nha/' . $bId);
                $buildingName = $building->name ?? 'Tòa nhà / BĐS';
                $addressParts = array_filter([$building->address_detail, $building->ward, $building->district, $building->province]);
                $addressDetail = trim(implode(', ', $addressParts));

                if (!empty($detectedChanges)) {
                    $changesList = implode("\n", array_map(fn($item) => "• " . $item, $detectedChanges));
                    $notifMessage = "Admin vừa cập nhật thông tin tòa nhà: {$buildingName}" . ($addressDetail ? " ({$addressDetail})" : "") . ".\n\n📋 Chi tiết các nội dung đã thay đổi:\n" . $changesList . "\n\nVui lòng kiểm tra lại thông tin chi tiết.";
                } else {
                    $notifMessage = "Admin vừa cập nhật lại thông tin tòa nhà: {$buildingName}" . ($addressDetail ? " ({$addressDetail})" : "") . ". Vui lòng kiểm tra lại thông tin chi tiết.";
                }

                foreach ($targetStaffIds as $staffId) {
                    Notification::create([
                        'user_id'         => (string)$staffId,
                        'notifiable_id'   => (string)$staffId,
                        'notifiable_type' => User::class,
                        'building_id'     => (string)$bId,
                        'title'           => 'Tòa nhà bạn quản lý vừa được cập nhật',
                        'message'         => $notifMessage,
                        'changes'         => $detectedChanges,
                        'type'            => 'building',
                        'category'        => 'toanha',
                        'is_read'         => false,
                        'action_url'      => $actionUrl,
                    ]);
                }
            } catch (\Exception $notifEx) {
                Log::warning('Lỗi gửi thông báo cập nhật tòa nhà cho nhân viên: ' . $notifEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tòa nhà thành công!',
                'building' => $building
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => collect($ve->errors())->first()[0] ?? 'Dữ liệu không hợp lệ!',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật tòa nhà: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Xóa Tòa nhà / Cơ sở khỏi MongoDB
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
            }

            if (!$this->checkPermission()) {
                return response()->json(['success' => false, 'message' => 'Chỉ Ban Quản Trị mới có quyền xóa tòa nhà!'], 403);
            }

            $building = Building::where('_id', $id)->first();
            if (!$building) {
                $building = Building::find($id);
            }

            if (!$building) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy tòa nhà cần xóa!'], 404);
            }

            $bName = $building->name;
            $bId = (string)($building->_id ?? $building->id);

            // Xóa tòa nhà
            $building->delete();

            // Gỡ bỏ ID tòa nhà này khỏi assigned_buildings của các nhân viên
            User::where('assigned_buildings', $bId)->get()->each(function($u) use ($bId) {
                $assigned = array_diff((array)$u->assigned_buildings, [$bId]);
                $u->assigned_buildings = array_values($assigned);
                $u->save();
            });

            return response()->json([
                'success' => true,
                'message' => "Đã xóa thành công tòa nhà \"{$bName}\"!"
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi xóa tòa nhà: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hàm hiển thị Chi tiết Tòa nhà & Phòng
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
            }

            $building = Building::where('_id', $id)->first();
            if (!$building) {
                $building = Building::find($id);
            }

            if (!$building) {
                abort(404, 'Không tìm thấy tòa nhà.');
            }

            $bId = (string)($building->_id ?? $building->id);

            if ($user->isSubAccount()) {
                if (!$user->canManageBuilding($bId)) {
                    abort(403, 'Bạn không có quyền quản lý tòa nhà này! Bạn chỉ được quản lý các tòa nhà do RentHome phân công.');
                }
            } elseif ($user->isIndividual()) {
                abort(403, 'Tài khoản cá nhân không có quyền quản lý tòa nhà.');
            }

            $rooms = Post::where('building_id', $bId)
                         ->orWhere('building_id', (string)$id)
                         ->orderBy('created_at', 'desc')
                         ->get();

            $roomIds = $rooms->map(fn($r) => (string)($r->_id ?? $r->id))->filter()->toArray();

            $activeContractPostIds = [];
            $activeContractMap = [];
            if (!empty($roomIds)) {
                $contracts = Contract::where('status', '!=', 'cancelled')
                    ->where(function($q) use ($roomIds) {
                        $q->whereIn('post_id', $roomIds)
                          ->orWhereIn('room_id', $roomIds);
                    })
                    ->get(['post_id', 'room_id', 'tenant_name']);

                foreach ($contracts as $c) {
                    if (!empty($c->post_id)) {
                        $activeContractPostIds[] = (string)$c->post_id;
                        $activeContractMap[(string)$c->post_id] = $c;
                    }
                    if (!empty($c->room_id)) {
                        $activeContractPostIds[] = (string)$c->room_id;
                        $activeContractMap[(string)$c->room_id] = $c;
                    }
                }
                $activeContractPostIds = array_unique($activeContractPostIds);
            }

            $rooms->transform(function ($room) use ($activeContractPostIds, $activeContractMap) {
                $roomId = (string)($room->_id ?? $room->id);
                $contract = $activeContractMap[$roomId] ?? null;
                $isRented = ($contract !== null 
                    || $room->is_rented === true 
                    || $room->is_rented === 1 
                    || $room->is_rented === '1'
                    || in_array($room->status, ['đã cho thuê', 'rented', 'dang_thue', 'Đang thuê', 'da_cho_thue']));

                $room->is_rented = $isRented;
                $room->tenant_name = $contract ? $contract->tenant_name : null;
                return $room;
            });

            return view('qlbai_dang.BuildingDetail', compact('building', 'rooms', 'activeContractPostIds', 'activeContractMap'));

        } catch (\Exception $e) {
            Log::error('Lỗi xem chi tiết tòa nhà: ' . $e->getMessage());
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            abort(500, 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}