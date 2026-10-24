<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Building;
use App\Models\User;
use App\Models\Post;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\AdminActivity;
use App\Models\Image;

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
        $user = Auth::user();
        if ($user && ($user->isSubAccount() || in_array($user->role, ['sub_account', 'staff', 'nhansu', 'nhan su', 'nhan_su']) || !empty($user->parent_id))) {
            return redirect('/Overview?tab=buildings');
        }

        if (!$this->checkPermission()) {
            return redirect('/login')->withErrors(['login_error' => 'Chỉ tài khoản Ban Quản Trị / Doanh nghiệp mới có quyền truy cập trang Quản lý BĐS & Tòa Nhà.']);
        }

        try {
            // Lấy danh sách tất cả các tòa nhà từ MongoDB
            $buildings = Building::orderBy('created_at', 'desc')->get();

            // Lấy danh sách nhân viên nội bộ phụ trách kinh doanh/vận hành (Loại bỏ admin và kiểm duyệt viên)
            $staffList = User::where(function($q) {
                    $q->where('role', 'staff')
                      ->orWhere('role', 'sub_account')
                      ->orWhere('role', 'nhansu')
                      ->orWhere('role', 'nhan su')
                      ->orWhere('role', 'nhan_su')
                      ->orWhere('role', 'doanhnghiep')
                      ->orWhere('role', 'enterprise')
                      ->orWhereNotNull('parent_id');
                })
                ->whereNotIn('role', ['admin', 'kiemduyet', 'moderator', 'kiem_duyet'])
                ->where(function($q) {
                    $q->whereNull('is_deleted')->orWhere('is_deleted', false);
                })
                ->orderBy('account_name', 'asc')
                ->get()
                ->filter(function($u) {
                    return !method_exists($u, 'isModerator') || !$u->isModerator();
                })
                ->values();

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

                // Đếm số bài đăng (nhà/phòng) và hợp đồng đang thuê thuộc tòa nhà này
                $bPostsQuery = Post::where('building_id', $bId);
                if (strlen($bId) === 24 && ctype_xdigit($bId)) {
                    try {
                        $bPostsQuery = $bPostsQuery->orWhere('building_id', new \MongoDB\BSON\ObjectId($bId));
                    } catch (\Exception $e) {}
                }
                $postsList = $bPostsQuery->where(function($q) {
                    $q->whereNull('is_deleted')->orWhere('is_deleted', false);
                })->get();

                $postIds = $postsList->map(fn($p) => (string)($p->_id ?? $p->id))->filter()->toArray();
                $postsTotalInColl = count($postIds);

                $bRoomsCount = Room::where('building_id', $bId);
                if (strlen($bId) === 24 && ctype_xdigit($bId)) {
                    try {
                        $bRoomsCount = $bRoomsCount->orWhere('building_id', new \MongoDB\BSON\ObjectId($bId));
                    } catch (\Exception $e) {}
                }
                $roomsTotalInColl = $bRoomsCount->count();

                $actualUnits = max($roomsTotalInColl, $postsTotalInColl);

                // Đếm số phòng/nhà đang cho thuê
                $activeContractsCount = 0;
                if (!empty($postIds)) {
                    $activeContractsCount = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'da_huy'])
                        ->where(function($q) use ($postIds, $bId) {
                            $q->whereIn('post_id', $postIds)
                              ->orWhereIn('room_id', $postIds)
                              ->orWhere('building_id', $bId);
                        })->count();
                }
                $rentedPostsCount = $postsList->filter(function($p) {
                    return $p->is_rented === true || in_array($p->status, ['đã cho thuê', 'rented', 'dang_thue']);
                })->count();

                $activeRentCount = max($activeContractsCount, $rentedPostsCount);

                $b->actual_rooms_count = $actualUnits;
                $b->actual_posts_count = $actualUnits;
                $b->active_rent_count = $activeRentCount;
                $b->can_delete = ($actualUnits === 0 && $activeRentCount === 0);

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
                          ->orWhere('role', 'nhansu')
                          ->orWhere('role', 'nhan su')
                          ->orWhere('role', 'nhan_su')
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

                // Nhóm 2: Bảng giá dịch vụ mặc định (Không bắt buộc, có thể để trống hoặc theo giá nhà nước)
                'electricity_price' => 'nullable|numeric|min:0',
                'water_price' => 'nullable|numeric|min:0',
                'water_calc_type' => 'nullable|string|in:m3,person',
                'service_fee' => 'nullable|numeric|min:0',
                'internet_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'has_service_pricing' => 'nullable',

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
                'electricity_price.numeric' => 'Đơn giá điện phải là số.',
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

            $hasServicePricing = $request->has('has_service_pricing')
                ? $request->boolean('has_service_pricing')
                : (!$isSingleProperty && ($request->filled('electricity_price') || $request->filled('water_price')));

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

                'has_service_pricing' => $hasServicePricing,
                'electricity_price' => ($hasServicePricing && $request->filled('electricity_price')) ? (float)$validated['electricity_price'] : null,
                'water_price' => ($hasServicePricing && $request->filled('water_price')) ? (float)$validated['water_price'] : null,
                'water_calc_type' => $validated['water_calc_type'] ?? 'm3',
                'service_fee' => ($hasServicePricing && $request->filled('service_fee')) ? (float)$validated['service_fee'] : null,
                'internet_fee' => ($hasServicePricing && $request->filled('internet_fee')) ? (float)$validated['internet_fee'] : null,
                'parking_fee' => ($hasServicePricing && $request->filled('parking_fee')) ? (float)$validated['parking_fee'] : null,

                'total_floors' => !empty($validated['total_floors']) ? (int)$validated['total_floors'] : 1,
                'total_rooms' => !empty($validated['total_rooms']) ? (int)$validated['total_rooms'] : 1,
                'amenities' => !empty($validated['amenities']) ? array_values(array_unique($validated['amenities'])) : [],
                'description' => $validated['description'] ?? '',

                'manager_id' => !empty($validated['manager_id']) ? (string)$validated['manager_id'] : null,
                'status' => $validated['status'] ?? 'active',

                'user_id' => (string)($user->_id ?? $user->id),
                'parent_id' => $user->parent_id ?: (string)($user->_id ?? $user->id),
            ];

            // Xử lý tạm thời danh sách file ảnh để lưu vào collection images sau khi tạo Tòa nhà
            $uploadedFiles = [];
            $savedImageIds = [];
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                if (!is_array($files)) {
                    $files = [$files];
                }
                $uploadedFiles = array_slice($files, 0, 5);
            } elseif ($request->hasFile('image')) {
                $uploadedFiles = [$request->file('image')];
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

            // Nghiêm cấm gán kiểm duyệt viên làm quản lý tòa nhà
            if (!empty($managerIds)) {
                $validManagerIds = [];
                foreach ($managerIds as $mId) {
                    $u = User::find($mId);
                    if ($u && !((method_exists($u, 'isModerator') && $u->isModerator()) || in_array($u->role, ['kiemduyet', 'moderator', 'kiem_duyet']))) {
                        $validManagerIds[] = (string)$mId;
                    }
                }
                $managerIds = array_values(array_unique($validManagerIds));
            }

            $data['manager_ids'] = $managerIds;
            $data['user_ids'] = $managerIds;
            $data['manager_id'] = count($managerIds) > 0 ? $managerIds[0] : null;

            $building = Building::create($data);
            $bId = (string)($building->_id ?? $building->id);

            // Lưu các file ảnh trực tiếp vào collection images trong MongoDB
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $idx => $file) {
                    if ($file && $file->isValid()) {
                        $imgDoc = Image::create([
                            'target_type' => 'building',
                            'target_id'   => $bId,
                            'filename'    => $file->getClientOriginalName(),
                            'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                            'size'        => $file->getSize(),
                            'data'        => base64_encode(file_get_contents($file->getRealPath())),
                            'is_cover'    => (count($savedImageIds) === 0),
                            'uploaded_by' => Auth::id() ? (string)Auth::id() : null,
                        ]);
                        $savedImageIds[] = (string)$imgDoc->_id;
                    }
                }
                $building->images = $savedImageIds;
                $coverId = !empty($savedImageIds) ? $savedImageIds[0] : null;
                $building->image = $coverId ? (str_starts_with($coverId, 'http') ? $coverId : '/images/' . $coverId) : null;
                $building->display_image = $building->image;
                $building->save();
            }

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
                    $assigned = array_values(array_diff($assigned, ['all', '*']));
                    if (!in_array($bId, $assigned, true)) {
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

                'electricity_price' => 'nullable|numeric|min:0',
                'water_price' => 'nullable|numeric|min:0',
                'water_calc_type' => 'nullable|string|in:m3,person',
                'service_fee' => 'nullable|numeric|min:0',
                'internet_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'has_service_pricing' => 'nullable',

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
            if ($newManagerId) {
                $u = User::find($newManagerId);
                if ($u && ((method_exists($u, 'isModerator') && $u->isModerator()) || in_array($u->role, ['kiemduyet', 'moderator', 'kiem_duyet']))) {
                    $newManagerId = null;
                }
            }

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

            $hasServicePricing = $request->has('has_service_pricing')
                ? $request->boolean('has_service_pricing')
                : (!$isSingleProperty && ($request->filled('electricity_price') || $request->filled('water_price')));

            $building->has_service_pricing = $hasServicePricing;
            $building->electricity_price = ($hasServicePricing && $request->filled('electricity_price')) ? (float)$validated['electricity_price'] : null;
            $building->water_price = ($hasServicePricing && $request->filled('water_price')) ? (float)$validated['water_price'] : null;
            $building->water_calc_type = $validated['water_calc_type'] ?? 'm3';
            $building->service_fee = ($hasServicePricing && $request->filled('service_fee')) ? (float)$validated['service_fee'] : null;
            $building->internet_fee = ($hasServicePricing && $request->filled('internet_fee')) ? (float)$validated['internet_fee'] : null;
            $building->parking_fee = ($hasServicePricing && $request->filled('parking_fee')) ? (float)$validated['parking_fee'] : null;

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
            $building->manager_ids = $newManagerId ? [$newManagerId] : [];
            $building->user_ids = $newManagerId ? [$newManagerId] : [];

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

            $bId = (string)($building->_id ?? $building->id);

            // Upload các file ảnh mới thêm vào lưu trực tiếp vào collection images trong MongoDB
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
                    $imgDoc = Image::create([
                        'target_type' => 'building',
                        'target_id'   => $bId,
                        'filename'    => $file->getClientOriginalName(),
                        'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                        'size'        => $file->getSize(),
                        'data'        => base64_encode(file_get_contents($file->getRealPath())),
                        'is_cover'    => (count($finalImageIds) === 0),
                        'uploaded_by' => Auth::id() ? (string)Auth::id() : null,
                    ]);
                    $finalImageIds[] = (string)$imgDoc->_id;
                }
            }

            // Dọn dẹp ảnh cũ trong images collection nếu bị gỡ khỏi gallery
            $currentHexIds = [];
            foreach ($finalImageIds as $item) {
                $clean = str_replace(['/images/', '/image/', 'images/', 'image/'], '', (string)$item);
                if (strlen($clean) === 24 && ctype_xdigit($clean)) {
                    $currentHexIds[] = $clean;
                }
            }
            if (!empty($currentHexIds)) {
                Image::where('target_type', 'building')
                    ->where('target_id', $bId)
                    ->whereNotIn('_id', $currentHexIds)
                    ->delete();
            }

            $building->images = array_values(array_slice($finalImageIds, 0, 5));
            $coverId = !empty($building->images) ? $building->images[0] : null;
            if ($coverId) {
                $building->image = str_starts_with($coverId, 'http') ? $coverId : (str_starts_with($coverId, '/images/') ? $coverId : (str_starts_with($coverId, '/storage/') ? $coverId : '/images/' . $coverId));
            } else {
                $building->image = null;
            }
            $building->display_image = $building->image;

            $building->save();

            // Đồng bộ phân công nhân viên nếu có thay đổi
            if ($newManagerId !== $oldManagerId) {
                if ($newManagerId) {
                    $newStaff = User::find($newManagerId);
                    if ($newStaff && !((method_exists($newStaff, 'isModerator') && $newStaff->isModerator()) || in_array($newStaff->role, ['kiemduyet', 'moderator', 'kiem_duyet']))) {
                        $assigned = (array)($newStaff->assigned_buildings ?? []);
                        $assigned = array_values(array_diff($assigned, ['all', '*']));
                        if (!in_array($bId, $assigned, true)) {
                            $assigned[] = $bId;
                        }
                        $newStaff->assigned_buildings = array_values(array_unique($assigned));

                        $bIds = (array)($newStaff->building_ids ?? []);
                        if (!in_array($bId, $bIds, true)) {
                            $bIds[] = $bId;
                        }
                        $newStaff->building_ids = array_values(array_unique($bIds));
                        $newStaff->save();
                    }
                }
                if ($oldManagerId) {
                    $oldStaff = User::find($oldManagerId);
                    if ($oldStaff) {
                        $assigned = array_diff((array)($oldStaff->assigned_buildings ?? []), [$bId]);
                        $oldStaff->assigned_buildings = array_values($assigned);

                        $bIds = array_diff((array)($oldStaff->building_ids ?? []), [$bId]);
                        $oldStaff->building_ids = array_values($bIds);
                        $oldStaff->save();
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
     * Áp dụng quy tắc bảo vệ dữ liệu tuyệt đối (Hard Block):
     * - Quy tắc 1: CHẶN TUYỆT ĐỐI nếu tòa nhà đang chứa bất kỳ phòng nào (bất kể đang thuê hay đang trống).
     *   Query vào collection rooms (và posts) để đếm số phòng thuộc building_id.
     *   Nếu count > 0, lập tức trả về HTTP 400 kèm message chứa số lượng phòng.
     * - Quy tắc 2: Chỉ cho phép XÓA khi tòa nhà trống không hoàn toàn (count == 0). Trả về HTTP 200 Success.
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

            $building = Building::where('_id', $id)->first() ?: Building::find($id);

            if (!$building) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy tòa nhà cần xóa!'], 404);
            }

            $bName = $building->name;
            $bId = (string)($building->_id ?? $building->id);

            // 1. Query vào collection 'posts' để tìm toàn bộ các Nhà / Căn hộ / Phòng (Bài đăng) thuộc tòa nhà này
            $postsQuery = Post::where(function($q) use ($bId) {
                $q->where('building_id', $bId);
                if (strlen($bId) === 24 && ctype_xdigit($bId)) {
                    try {
                        $q->orWhere('building_id', new \MongoDB\BSON\ObjectId($bId));
                    } catch (\Exception $e) {}
                }
            })->where(function($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            });
            $postsList = $postsQuery->get();
            $postIds = $postsList->map(fn($p) => (string)($p->_id ?? $p->id))->filter()->toArray();
            $postsCount = count($postIds);

            // 2. Đồng thời kiểm tra collection 'rooms'
            $roomsQuery = Room::where(function($q) use ($bId) {
                $q->where('building_id', $bId);
                if (strlen($bId) === 24 && ctype_xdigit($bId)) {
                    try {
                        $q->orWhere('building_id', new \MongoDB\BSON\ObjectId($bId));
                    } catch (\Exception $e) {}
                }
            });
            $roomsCount = $roomsQuery->count();

            // Tổng số nhà (bài đăng) thực tế liên kết với tòa nhà
            $totalHouses = max($postsCount, $roomsCount);

            // 3. Kiểm tra số lượng hợp đồng thuê đang hoạt động (active contracts) và bài đăng đang thuê
            $activeContractsCount = 0;
            if (!empty($postIds)) {
                $activeContractsCount = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'da_huy'])
                    ->where(function($q) use ($postIds, $bId) {
                        $q->whereIn('post_id', $postIds)
                          ->orWhereIn('room_id', $postIds)
                          ->orWhere('building_id', $bId);
                    })->count();
            }
            $rentedPostsCount = $postsList->filter(function($p) {
                return $p->is_rented === true || in_array($p->status, ['đã cho thuê', 'rented', 'dang_thue']);
            })->count();
            $totalRented = max($activeContractsCount, $rentedPostsCount);

            // =========================================================================
            // QUY TẮC BẢO VỆ DỮ LIỆU TUYỆT ĐỐI (HARD BLOCK):
            // CHẶN TUYỆT ĐỐI NẾU TÒA NHÀ CÒN BÀI ĐĂNG HOẶC ĐANG CÓ PHÒNG THUÊ
            // =========================================================================
            if ($totalHouses > 0 || $totalRented > 0) {
                $reasons = [];
                if ($totalHouses > 0) {
                    $reasons[] = "{$totalHouses} bài đăng / căn nhà";
                }
                if ($totalRented > 0) {
                    $reasons[] = "{$totalRented} căn đang có hợp đồng thuê";
                }
                $reasonDesc = implode(' và ', $reasons);

                return response()->json([
                    'success'       => false,
                    'error_code'    => 'BUILDING_NOT_EMPTY',
                    'houses_count'  => $totalHouses,
                    'posts_count'   => $totalHouses,
                    'rooms_count'   => $totalHouses,
                    'rented_count'  => $totalRented,
                    'message'       => "🚨 Không thể xóa! Tòa nhà này đang chứa {$reasonDesc} ở bên trong. Bạn phải thanh lý hợp đồng và xóa toàn bộ bài đăng/nhà trước khi có thể xóa tòa nhà này."
                ], 400);
            }

            // =========================================================================
            // QUY TẮC 2: CHỈ CHO PHÉP XÓA KHI TÒA NHÀ TRỐNG KHÔNG HOÀN TOÀN (count == 0)
            // =========================================================================

            // Gỡ bỏ ID tòa nhà này khỏi assigned_buildings của các nhân viên
            User::where('assigned_buildings', $bId)->get()->each(function($u) use ($bId) {
                $assigned = array_diff((array)$u->assigned_buildings, [$bId]);
                $u->assigned_buildings = array_values($assigned);
                $u->save();
            });

            // Xóa ảnh của tòa nhà trong collection images
            Image::where('target_type', 'building')->where('target_id', $bId)->delete();

            // Dọn dẹp dữ liệu hợp đồng / hóa đơn mồ côi nếu có gắn trực tiếp building_id
            try {
                Contract::where('building_id', $bId)->delete();
                Invoice::where('building_id', $bId)->delete();
            } catch (\Exception $ex) {}

            // Xóa Tòa nhà (Lớp Vỏ)
            $building->delete();

            // Ghi lại nhật ký quản trị
            AdminActivity::log(
                'delete',
                'building',
                "Xóa tòa nhà \"{$bName}\"",
                "Đã xóa thành công tòa nhà \"{$bName}\" (tòa nhà trống không chứa bất kỳ phòng nào bên trong).",
                $building,
                ['building_id' => $bId, 'rooms_count' => 0]
            );

            return response()->json([
                'success' => true,
                'message' => "Đã xóa thành công tòa nhà \"{$bName}\"!"
            ], 200);

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
                $contracts = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
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
            Log::error('Lỗi xem chi tiết bài đăng tòa nhà: ' . $e->getMessage());
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            abort(500, 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Xem chi tiết vận hành & quản lý phòng trực thuộc tòa nhà (Dành riêng cho Admin)
     * URL: /Admin/chi-tiet-toa-nha/{id}
     */
    public function adminDetail($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
            }

            $detailData = $this->getBuildingDetailData($id);
            if (!$detailData) {
                abort(404, 'Không tìm thấy thông tin tòa nhà.');
            }

            return view('Admin.chi_tiet_toa_nha_admin', $detailData);

        } catch (\Exception $e) {
            Log::error('Lỗi xem chi tiết vận hành tòa nhà Admin: ' . $e->getMessage());
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            abort(500, 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Lấy dữ liệu chi tiết vận hành & danh sách phòng con của tòa nhà
     */
    public function getBuildingDetailData($id)
    {
        $building = Building::where('_id', $id)->first() ?: Building::find($id);
        if (!$building) {
            return null;
        }

        $bId = (string)($building->_id ?? $building->id);
        $totalScale = (int)($building->total_rooms ?: 16);

        $posts = Post::where('building_id', $bId)
                     ->orWhere('building_id', (string)$id)
                     ->orderBy('created_at', 'desc')
                     ->get();

        // Lấy thông tin hợp đồng
        $postIds = $posts->map(fn($p) => (string)($p->_id ?? $p->id))->filter()->toArray();
        $contracts = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
            ->where(function($q) use ($postIds) {
                $q->whereIn('post_id', $postIds)
                  ->orWhereIn('room_id', $postIds);
            })->get();

        $contractMap = [];
        foreach ($contracts as $c) {
            if (!empty($c->post_id)) $contractMap[(string)$c->post_id] = $c;
            if (!empty($c->room_id)) $contractMap[(string)$c->room_id] = $c;
        }

        // Xây dựng danh sách phòng
        $rooms = [];
        foreach ($posts as $idx => $p) {
            $pId = (string)($p->_id ?? $p->id);
            $contract = $contractMap[$pId] ?? null;

            $isRented = ($contract !== null || $p->is_rented === true || in_array($p->status, ['rented', 'dang_thue', 'đã cho thuê']));
            $roomStatus = $isRented ? 'rented' : 'empty';

            // Trạng thái thu tiền thực tế dựa trên hóa đơn
            $paymentStatus = 'none';
            if ($isRented) {
                $hasUnpaid = Invoice::where(function($q) use ($pId) {
                    $q->where('room_id', $pId)->orWhere('post_id', $pId);
                })->where(function($q) {
                    $q->where('status', 'unpaid')->orWhere('payment_status', 'unpaid');
                })->exists();

                $paymentStatus = $hasUnpaid ? 'unpaid' : 'paid';
            }

            $postStatus = $p->status ?? 'active';
            if (!in_array($postStatus, ['active', 'pending', 'rejected'])) {
                $postStatus = ($p->status === 'approved' || $p->status === 'hien_thi') ? 'active' : 'pending';
            }

            $priceVal = (float)($p->price ?? 4500000);
            $areaVal = (int)($p->area ?? $p->acreage ?? 28);
            $floorVal = !empty($p->floor) ? $p->floor : ('Tầng ' . (intdiv($idx, 4) + 1));
            $roomCode = !empty($p->room_number) ? $p->room_number : ('P.' . (101 + $idx));

            $rooms[] = (object)[
                'id'             => $pId,
                'room_number'    => $roomCode,
                'title'          => $p->title ?? ($roomCode . ' - Căn hộ Studio'),
                'thumbnail'      => $p->thumbnail ?? $p->display_image ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400',
                'created_at'     => $p->created_at ? $p->created_at->format('d/m/Y') : now()->format('d/m/Y'),
                'area'           => $areaVal,
                'floor'          => $floorVal,
                'price'          => $priceVal,
                'price_formatted'=> number_format($priceVal, 0, ',', '.') . ' đ/tháng',
                'room_status'    => $roomStatus,
                'payment_status' => $paymentStatus,
                'post_status'    => $postStatus,
                'detail_url'     => url('/chitietbaidang/' . $pId),
            ];
        }

        // Tính toán số liệu thống kê Tỷ Lệ Lấp Đầy & Dòng Tiền dựa trên dữ liệu thực tế
        $totalRoomsCount = count($rooms);
        $emptyRoomsCount = collect($rooms)->where('room_status', 'empty')->count();
        $rentedRoomsCount = collect($rooms)->where('room_status', 'rented')->count();

        // Tiến độ nhập liệu
        $createdRoomsCount = $totalRoomsCount;
        $missingRoomsCount = max(0, $totalScale - $createdRoomsCount);
        $progressPercent = $totalScale > 0 ? round(($createdRoomsCount / $totalScale) * 100, 1) : 0;

        // Kiểm duyệt
        $postsActiveCount = collect($rooms)->where('post_status', 'active')->count();
        $postsPendingCount = collect($rooms)->where('post_status', 'pending')->count();
        $postsRejectedCount = collect($rooms)->where('post_status', 'rejected')->count();

        // Dòng tiền thực tế
        $monthlyRevenue = collect($rooms)
            ->where('room_status', 'rented')
            ->sum('price');
        $paidRoomsCount = collect($rooms)->where('payment_status', 'paid')->count();
        $unpaidRoomsCount = collect($rooms)->where('payment_status', 'unpaid')->count();

        // Xác định tên loại hình
        $typeMap = [
            'can_ho'           => 'Chung cư mini / Căn hộ dịch vụ',
            'can_ho_dich_vu'   => 'Chung cư mini / Căn hộ dịch vụ',
            'chung_cu_mini'    => 'Chung cư mini / Căn hộ dịch vụ',
            'nha_tro'          => 'Nhà trọ / Khu trọ',
            'day_tro'          => 'Nhà trọ / Khu trọ',
            'ky_tuc_xa'        => 'Ký túc xá / Sleepbox',
            'nha_rieng'        => 'Nhà ở / Nhà riêng lẻ',
            'nha_nguyen_can'   => 'Nhà ở / Nhà riêng lẻ',
            'cum_nha_lien_ke'  => 'Cụm nhà liền kề / Dự án',
            'dat_phan_lo'      => 'Đất phân lô / Đất nền',
            'dat_nen'          => 'Mặt bằng kinh doanh / Đất nền',
            'toa_nha_van_phong'=> 'Tòa nhà văn phòng',
        ];
        $rawType = $building->type ?? $building->business_type ?? '';
        $typeText = $typeMap[$rawType] ?? ($rawType ?: 'Chung cư mini / Căn hộ dịch vụ');

        return [
            'building'    => $building,
            'buildingId'  => $bId,
            'typeText'    => $typeText,
            'stats'       => (object)[
                'total_scale'          => $totalScale,
                'empty_rooms'          => $emptyRoomsCount,
                'rented_rooms'         => $rentedRoomsCount,
                'created_rooms'        => $createdRoomsCount,
                'missing_rooms'        => $missingRoomsCount,
                'progress_percent'     => $progressPercent,
                'posts_active'         => $postsActiveCount,
                'posts_pending'        => $postsPendingCount,
                'posts_rejected'       => $postsRejectedCount,
                'monthly_revenue'      => $monthlyRevenue,
                'monthly_revenue_text' => number_format($monthlyRevenue, 0, ',', '.') . ' VNĐ / tháng',
                'paid_rooms'           => $paidRoomsCount,
                'unpaid_rooms'         => $unpaidRoomsCount,
            ],
            'rooms'       => $rooms,
        ];
    }

    /**
     * API: Lấy chi tiết vận hành & danh sách phòng con của tòa nhà dành cho AJAX
     */
    public function getRoomsDetail($id)
    {
        try {
            $data = $this->getBuildingDetailData($id);
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy tòa nhà!'], 404);
            }

            return response()->json([
                'success' => true,
                'building' => [
                    'id'          => $data['buildingId'],
                    'name'        => $data['building']->name,
                    'type_text'   => $data['typeText'],
                    'total_rooms' => $data['stats']->total_scale,
                    'total_floors'=> $data['building']->total_floors ?? 1,
                    'address'     => $data['building']->full_address ?? '',
                ],
                'stats' => (array)$data['stats'],
                'rooms' => $data['rooms'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi lấy chi tiết phòng tòa nhà: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}