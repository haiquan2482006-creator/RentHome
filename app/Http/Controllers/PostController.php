<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Contract;
use App\Models\Image;
use App\Models\Building;
use App\Models\Room;
use App\Models\AdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập!'], 401);
        }

        $user = Auth::user();

        // 1. Tài khoản cá nhân KHÔNG THỂ đăng bài cho thuê
        if ($user->isIndividual()) {
            return response()->json([
                'error' => 'Tài khoản cá nhân chỉ có thể thuê nhà, không thể đăng bài cho thuê! Chức năng đăng bài dành riêng cho Doanh nghiệp RentHome và Quản lý tòa nhà.'
            ], 403);
        }

        // 2. Tài khoản con CHỈ được đăng bài cho các tòa nhà được phân quyền
        if ($user->isSubAccount()) {
            $buildingId = $request->input('building_id');
            if (empty($buildingId)) {
                return response()->json([
                    'error' => 'Vui lòng chọn Tòa nhà được phân công quản lý khi đăng tin!'
                ], 422);
            }
            if (!$user->canManageBuilding($buildingId)) {
                return response()->json([
                    'error' => 'Bạn không có quyền đăng bài cho tòa nhà này! Bạn chỉ được quản lý các tòa nhà được Doanh nghiệp phân công.'
                ], 403);
            }
        }

        $validatedData = $request->validate([
            'post_id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'price_unit' => 'nullable|string',
            'area' => 'required|numeric',
            'floor' => 'nullable|numeric',
            'floor_number' => 'nullable|numeric',
            'bedroom' => 'nullable|numeric',
            'bathroom' => 'nullable|numeric',
            'property_type' => 'nullable|string',
            'description' => 'nullable|string',
            'account_role' => 'nullable|string',
            'building_id' => 'nullable|string', 
            'amenities' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $postId = $request->input('post_id');
        $post = null;

        if ($postId) {
            $post = Post::where('_id', $postId)->orWhere('id', $postId)->first();
            if ($post) {
                if ($user->isSubAccount() && !empty($post->building_id) && !$user->canManageBuilding($post->building_id)) {
                    return response()->json(['error' => 'Bạn không có quyền chỉnh sửa bài đăng của tòa nhà này!'], 403);
                }
                if (!$user->isEnterprise() && !$user->isAdmin() && $post->user_id != $user->id && !$user->canManageBuilding($post->building_id)) {
                    return response()->json(['error' => 'Không có quyền sửa bài đăng này!'], 403);
                }
            }
        }

        $imagePaths = $post ? ($post->images ?? []) : [];

        $buildingId = !empty($validatedData['building_id']) ? (string)$validatedData['building_id'] : null;
        $building = null;
        if ($buildingId) {
            $building = \App\Models\Building::find($buildingId);
        }

        // Tự động kế thừa tiện ích Tòa nhà VÀ gộp với tiện ích riêng của phòng
        $buildingAmenities = ($building && is_array($building->amenities)) ? $building->amenities : [];
        $submittedAmenities = !empty($validatedData['amenities']) ? (array)$validatedData['amenities'] : [];
        $mergedAmenities = array_values(array_unique(array_merge($buildingAmenities, $submittedAmenities)));

        $floorVal = $request->filled('floor') ? (int)$request->input('floor') : ($request->filled('floor_number') ? (int)$request->input('floor_number') : null);

        $dataToSave = [
            'user_id' => (string)Auth::id(),
            'author_id' => (string)Auth::id(),
            'parent_id' => (string)($user->parent_id ?: Auth::id()),
            'staff_id' => (string)Auth::id(),
            'title' => $validatedData['title'],
            'price' => $validatedData['price'],
            'price_unit' => $validatedData['price_unit'] ?? 'thang',
            'area' => $validatedData['area'],
            'floor' => $floorVal,
            'floor_number' => $floorVal,
            'bedroom' => $request->filled('bedroom') ? (int)$request->input('bedroom') : null,
            'bathroom' => $request->filled('bathroom') ? (int)$request->input('bathroom') : null,
            'province' => $building ? $building->province : null,
            'district' => $building ? $building->district : null,
            'ward' => $building ? $building->ward : null,
            'address' => $building ? ($building->address_detail ?: $building->address) : null,
            'property_type' => $validatedData['property_type'] ?? 'nha_o',
            'description' => $validatedData['description'] ?? '',
            'account_role' => $validatedData['account_role'] ?? 'doanhnghiep',
            'building_id' => $buildingId,
            'amenities' => $mergedAmenities,
            'images' => $imagePaths,
            'status' => 'pending' // Bắt buộc là pending (Chờ phê duyệt), tuyệt đối không để approved hay active ngay khi tạo
        ];

        if ($post) {
            $post->update($dataToSave);
            $msg = 'Đã gửi bài đăng thành công! Bài viết đang chờ Kiểm duyệt viên phê duyệt.';
        } else {
            $post = Post::create($dataToSave);
            $msg = 'Đã gửi bài đăng thành công! Bài viết đang chờ Kiểm duyệt viên phê duyệt.';
        }

        $targetPostId = (string)($post->_id ?? $post->id);

        // Lưu toàn bộ file ảnh được upload trực tiếp vào collection images trong MongoDB
        if ($request->hasFile('images')) {
            $newPaths = [];
            $files = $request->file('images');
            if (!is_array($files)) {
                $files = [$files];
            }
            $isFirst = true;
            foreach ($files as $image) {
                if ($image && $image->isValid()) {
                    $imgDoc = Image::create([
                        'target_type' => 'post',
                        'target_id'   => $targetPostId,
                        'filename'    => $image->getClientOriginalName(),
                        'mime_type'   => $image->getClientMimeType() ?: 'image/jpeg',
                        'size'        => $image->getSize(),
                        'data'        => base64_encode(file_get_contents($image->getRealPath())),
                        'is_cover'    => $isFirst,
                        'uploaded_by' => Auth::id() ? (string)Auth::id() : null,
                    ]);
                    $newPaths[] = '/images/' . $imgDoc->_id;
                    $isFirst = false;
                }
            }

            if (!empty($newPaths)) {
                // Xóa ảnh cũ của bài viết này trong collection images nếu người dùng đã tải ảnh mới
                $currentHex = array_map(fn($p) => str_replace(['/images/', 'images/'], '', $p), $newPaths);
                Image::where('target_type', 'post')
                    ->where('target_id', $targetPostId)
                    ->whereNotIn('_id', $currentHex)
                    ->delete();

                $post->images = $newPaths;
                $post->display_image = $newPaths[0];
                $post->save();
            }
        }

        // Tạo 1 bản ghi thông báo mới cho phía Kiểm duyệt viên
        try {
            \App\Models\Notification::create([
                'title'       => 'Tin đăng mới cần kiểm duyệt',
                'message'     => 'Nhân viên ' . ($user->account_name ?? ($user->username ?? 'Nhân viên')) . ' vừa gửi bài viết "' . $post->title . '"' . ($building ? ' thuộc tòa nhà ' . $building->name : '') . ' đang chờ phê duyệt.',
                'category'    => 'post_moderation',
                'role'        => 'kiemduyet',
                'type'        => 'post',
                'post_id'     => $targetPostId,
                'action_url'  => url('/kiem-duyet/qlkiem-duyet?post_id=' . $targetPostId),
                'is_read'     => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Lỗi tạo notification kiểm duyệt: ' . $e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'message'      => $msg,
            'redirect_url' => '/Overview?tab=pending',
            'post_id'      => $targetPostId
        ]);
    }

    public function getApproved(Request $request)
    {
        $activeContractPostIds = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
            ->pluck('post_id')
            ->filter()
            ->toArray();

        $query = Post::with(['user', 'building'])
            ->whereIn('status', ['approved', 'active'])
            ->where(function ($q) {
                $q->whereNull('is_rented')
                  ->orWhere('is_rented', false);
            })
            ->whereNotIn('status', ['đã cho thuê', 'rented', 'dang_thue', 'pending', 'rejected']);

        if (!empty($activeContractPostIds)) {
            $query->whereNotIn('_id', $activeContractPostIds)
                  ->whereNotIn('id', $activeContractPostIds);
        }

        if ($request->filled('keyword')) {
            $kw = $request->query('keyword');
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('description', 'like', "%{$kw}%")
                  ->orWhere('address', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('property_type', $request->query('type'));
        }

        if ($request->filled('location')) {
            $loc = $request->query('location');
            $query->where(function ($q) use ($loc) {
                $q->where('province', 'like', "%{$loc}%")
                  ->orWhere('district', 'like', "%{$loc}%")
                  ->orWhere('ward', 'like', "%{$loc}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->take(24)->get();
        return response()->json(['posts' => $posts]);
    }

    public function getFeatured()
    {
        $activeContractPostIds = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
            ->pluck('post_id')
            ->filter()
            ->toArray();

        $query = Post::with(['user', 'building'])
            ->whereIn('status', ['approved', 'active'])
            ->where(function ($q) {
                $q->whereNull('is_rented')
                  ->orWhere('is_rented', false);
            })
            ->whereNotIn('status', ['đã cho thuê', 'rented', 'dang_thue', 'pending', 'rejected']);

        if (!empty($activeContractPostIds)) {
            $query->whereNotIn('_id', $activeContractPostIds)
                  ->whereNotIn('id', $activeContractPostIds);
        }

        $posts = $query->orderBy('created_at', 'desc')->take(6)->get();
        $mapped = $posts->map(function($post) {
            $buildingName = $post->building ? ($post->building->name ?? '') : '';
            $locationStr = implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province]));
            return [
                'id' => (string)($post->_id ?? $post->id),
                'title' => $post->title . ($buildingName ? ' - ' . $buildingName : ''),
                'price' => number_format($post->price, 0, ',', '.') . ' VNĐ',
                'location' => $locationStr ?: ($buildingName ?: 'Việt Nam'),
                'image' => $post->display_image ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'tag' => 'NỔI BẬT',
                'user_id' => (string) ($post->user_id ?? ''),
                'beds' => '1-2 PN',
                'baths' => '1-2 WC',
                'area' => $post->area . ' m²',
                'note' => $buildingName ? 'Tòa nhà ' . $buildingName : 'Bất động sản chính chủ RentHome',
            ];
        });
        return response()->json($mapped);
    }

    /**
     * Chuyển phòng sang tòa nhà khác theo chuẩn bảo vệ toàn vẹn dữ liệu Enterprise
     */
    public function transferBuilding(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $targetBuildingId = $request->input('target_building_id') ?: $request->input('building_id');
        if (empty($targetBuildingId)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn Tòa nhà / Cơ sở đích tiếp nhận!'], 422);
        }

        $post = Post::where('_id', $id)->first() ?: Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin phòng / bài đăng!'], 404);
        }

        // 1. Kiểm tra quyền sở hữu / quản lý
        if (!$user->isAdmin()) {
            $userId = (string)($user->_id ?? $user->id);
            $parentUserId = (string)($user->parent_id ?? '');
            if ((string)$post->user_id !== $userId && (string)$post->parent_id !== $userId && (string)$post->user_id !== $parentUserId) {
                if ($user->isSubAccount() && method_exists($user, 'canManageBuilding') && !$user->canManageBuilding($post->building_id)) {
                    return response()->json(['success' => false, 'message' => 'Bạn không có quyền chuyển bài đăng này!'], 403);
                }
            }
        }

        // 2. RÀNG BUỘC TOÀN VẸN: Kiểm tra tình trạng thuê (Đang thuê -> CHẶN TUYỆT ĐỐI)
        $postId = (string)($post->_id ?? $post->id);
        $rawStatus = strtolower(trim((string)($post->status ?? '')));
        $isRented = (
            $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
            in_array($rawStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue'])
        );

        if (!$isRented) {
            $hasActiveContract = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                ->where(function($q) use ($postId) {
                    $q->where('post_id', $postId)
                      ->orWhere('room_id', $postId);
                })
                ->exists();
            if ($hasActiveContract) {
                $isRented = true;
            }
        }

        if ($isRented) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chuyển tòa nhà khi phòng đang có khách thuê!'
            ], 400);
        }

        // 3. Kiểm tra tòa nhà đích hợp lệ và không trùng tòa nhà hiện tại
        $targetBuilding = Building::where('_id', $targetBuildingId)->first() ?: Building::find($targetBuildingId);
        if (!$targetBuilding) {
            return response()->json(['success' => false, 'message' => 'Tòa nhà đích không tồn tại hoặc đã bị xóa!'], 404);
        }

        $currentBuildingId = (string)($post->building_id ?? '');
        $newBuildingId = (string)($targetBuilding->_id ?? $targetBuilding->id);

        if ($currentBuildingId === $newBuildingId) {
            return response()->json(['success' => false, 'message' => 'Phòng này hiện đã thuộc cơ sở được chọn!'], 422);
        }

        // Kiểm tra quyền đối với tòa nhà đích nếu là nhân viên con
        if ($user->isSubAccount() && method_exists($user, 'canManageBuilding') && !$user->canManageBuilding($newBuildingId)) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền phân công sang tòa nhà đích này!'], 403);
        }

        // Lấy tên tòa nhà cũ phục vụ log
        $oldBuilding = $currentBuildingId ? (Building::where('_id', $currentBuildingId)->first() ?: Building::find($currentBuildingId)) : null;
        $oldBuildingName = $oldBuilding ? $oldBuilding->name : 'Chưa phân cơ sở';

        // 4. CẬP NHẬT DỮ LIỆU ĐẢM BẢO KHÔNG BỊ MỒ CÔI
        $post->building_id = $newBuildingId;
        if (!empty($targetBuilding->province)) $post->province = $targetBuilding->province;
        if (!empty($targetBuilding->district)) $post->district = $targetBuilding->district;
        if (!empty($targetBuilding->ward)) $post->ward = $targetBuilding->ward;
        if (!empty($targetBuilding->address_detail) || !empty($targetBuilding->address)) {
            $post->address = $targetBuilding->address_detail ?: $targetBuilding->address;
        }
        $post->save();

        // 5. Đồng bộ bản ghi trong collection rooms (nếu có)
        try {
            Room::where('post_id', $postId)->orWhere('_id', $postId)->update([
                'building_id' => $newBuildingId
            ]);
        } catch (\Exception $e) {
            Log::warning('Không thể cập nhật Room collection khi transfer: ' . $e->getMessage());
        }

        // 6. Ghi log hoạt động AdminActivity
        if (class_exists(AdminActivity::class)) {
            AdminActivity::log(
                'update',
                'building',
                'Chuyển phòng sang tòa nhà khác',
                'Đã chuyển phòng "' . $post->title . '" từ cơ sở "' . $oldBuildingName . '" sang cơ sở "' . $targetBuilding->name . '".',
                $targetBuilding,
                [
                    'post_id'         => $postId,
                    'old_building_id' => $currentBuildingId,
                    'new_building_id' => $newBuildingId,
                    'user_id'         => (string)($user->_id ?? $user->id)
                ]
            );
        }

        return response()->json([
            'success'              => true,
            'message'              => 'Đã chuyển phòng "' . $post->title . '" sang cơ sở "' . $targetBuilding->name . '" thành công!',
            'target_building_name' => $targetBuilding->name
        ]);
    }

    /**
     * Gỡ bài đăng khỏi tòa nhà (bỏ liên kết building_id)
     */
    public function unlink($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $post = Post::where('_id', $id)->first() ?: Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng!'], 404);
        }

        if (!$user->isAdmin()) {
            $userId = (string)($user->_id ?? $user->id);
            $parentUserId = (string)($user->parent_id ?? '');
            if ((string)$post->user_id !== $userId && (string)$post->parent_id !== $userId && (string)$post->user_id !== $parentUserId) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền gỡ bài đăng này!'], 403);
            }
        }

        // Kiểm tra phòng đang cho thuê
        $postId = (string)($post->_id ?? $post->id);
        $rawStatus = strtolower(trim((string)($post->status ?? '')));
        $isRented = (
            $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
            in_array($rawStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue'])
        );
        if (!$isRented) {
            $hasActiveContract = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                ->where(function($q) use ($postId) {
                    $q->where('post_id', $postId)
                      ->orWhere('room_id', $postId);
                })
                ->exists();
            if ($hasActiveContract) {
                $isRented = true;
            }
        }
        if ($isRented) {
            return response()->json(['success' => false, 'message' => 'Không thể thao tác khi phòng đang có khách thuê!'], 400);
        }

        $post->building_id = null;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã gỡ bài đăng khỏi tòa nhà thành công!'
        ]);
    }

    /**
     * Xóa vĩnh viễn bài đăng
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $post = Post::where('_id', $id)->first() ?: Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng!'], 404);
        }

        if (!$user->isAdmin()) {
            $userId = (string)($user->_id ?? $user->id);
            $parentUserId = (string)($user->parent_id ?? '');
            if ((string)$post->user_id !== $userId && (string)$post->parent_id !== $userId && (string)$post->user_id !== $parentUserId) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa bài đăng này!'], 403);
            }
        }

        $postId = (string)($post->_id ?? $post->id);

        // Kiểm tra phòng đang cho thuê -> Chặn xóa để bảo vệ toàn vẹn dữ liệu
        $rawStatus = strtolower(trim((string)($post->status ?? '')));
        $isRented = (
            $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
            in_array($rawStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue'])
        );
        if (!$isRented) {
            $hasActiveContract = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                ->where(function($q) use ($postId) {
                    $q->where('post_id', $postId)
                      ->orWhere('room_id', $postId);
                })
                ->exists();
            if ($hasActiveContract) {
                $isRented = true;
            }
        }
        if ($isRented) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa phòng / bài đăng khi đang có khách thuê!'], 400);
        }

        // Tự động xóa toàn bộ ảnh của bài đăng khỏi collection images
        Image::where('target_type', 'post')->where('target_id', $postId)->delete();

        // Tự động dọn dẹp bản ghi phòng trong collection rooms (nếu có)
        try {
            Room::where('post_id', $postId)->orWhere('_id', $postId)->delete();
        } catch (\Exception $ex) {}

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bài đăng thành công!'
        ]);
    }

    /**
     * Hủy đăng bài
     */
    public function cancel($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $post = Post::where('_id', $id)->first() ?: Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng!'], 404);
        }

        if (!$user->isAdmin()) {
            $userId = (string)($user->_id ?? $user->id);
            $parentUserId = (string)($user->parent_id ?? '');
            if ((string)$post->user_id !== $userId && (string)$post->parent_id !== $userId && (string)$post->user_id !== $parentUserId) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền thao tác bài đăng này!'], 403);
            }
        }

        $post->status = 'cancelled';
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy đăng bài thành công!'
        ]);
    }

    /**
     * Hiển thị chi tiết bài đăng và đếm lượt xem ngầm (Silent View Tracking)
     * Sử dụng Session để chống spam F5, TUYỆT ĐỐI KHÔNG gửi thông báo.
     */
    public function show($id)
    {
        $post = Post::with(['user', 'building'])->where('_id', $id)->first() ?: Post::with(['user', 'building'])->find($id);
        if (!$post) {
            abort(404, 'Bài đăng không tồn tại hoặc đã bị xóa.');
        }

        // 1. Silent View Tracking: Kiểm tra session chống spam
        $viewedPosts = session()->get('viewed_posts', []);
        $postIdStr = (string)($post->_id ?? $post->id ?? $id);

        if (!in_array($postIdStr, $viewedPosts, true)) {
            // Tăng views ngầm trong MongoDB, TUYỆT ĐỐI KHÔNG gửi Notification
            $post->increment('views');

            // Đánh dấu đã xem vào session để tránh cộng dồn khi F5
            $viewedPosts[] = $postIdStr;
            session()->put('viewed_posts', $viewedPosts);
        }

        $hasContacted = false;
        $contactId = '';
        $contactStatus = '';

        if (Auth::check()) {
            $contact = \App\Models\Contact::where('post_id', $postIdStr)
                ->where('sender_id', (string)Auth::id())
                ->first();

            if ($contact) {
                $hasContacted = true;
                $contactId = (string)($contact->_id ?? $contact->id);
                $contactStatus = $contact->status ?? '';
            }
        }

        // 2. Kiểm tra tình trạng đã thuê (Dự phòng cho khách truy cập trực tiếp từ bookmark/link cũ)
        $rawStatus = strtolower(trim((string)($post->status ?? '')));
        $isRented = (bool)(
            $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
            in_array($rawStatus, ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue', 'inactive'])
        );

        if (!$isRented) {
            try {
                $hasActiveContract = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])
                    ->where(function($q) use ($postIdStr) {
                        $q->where('post_id', $postIdStr)
                          ->orWhere('room_id', $postIdStr);
                    })
                    ->exists();
                if ($hasActiveContract) {
                    $isRented = true;
                }
            } catch (\Exception $e) {}
        }
        $post->is_rented = $isRented;

        return view('chitietbaidang', compact('post', 'hasContacted', 'contactId', 'contactStatus', 'isRented'));
    }
}

