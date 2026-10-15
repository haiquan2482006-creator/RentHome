<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if ($request->hasFile('images')) {
            $newPaths = [];
            foreach ($request->file('images') as $image) {
                if ($image && $image->isValid()) {
                    $path = $image->store('posts', 'public');
                    $newPaths[] = '/storage/' . $path;
                }
            }
            $imagePaths = $newPaths; 
        }

        $buildingId = !empty($validatedData['building_id']) ? (string)$validatedData['building_id'] : null;
        $building = null;
        if ($buildingId) {
            $building = \App\Models\Building::find($buildingId);
        }

        // Tự động kế thừa tiện ích Tòa nhà VÀ gộp với tiện ích riêng của phòng
        $buildingAmenities = ($building && is_array($building->amenities)) ? $building->amenities : [];
        $submittedAmenities = !empty($validatedData['amenities']) ? (array)$validatedData['amenities'] : [];
        $mergedAmenities = array_values(array_unique(array_merge($buildingAmenities, $submittedAmenities)));

        $dataToSave = [
            'user_id' => (string)Auth::id(),
            'parent_id' => (string)($user->parent_id ?: Auth::id()),
            'staff_id' => (string)Auth::id(),
            'title' => $validatedData['title'],
            'price' => $validatedData['price'],
            'price_unit' => $validatedData['price_unit'] ?? 'thang',
            'area' => $validatedData['area'],
            'province' => $building ? $building->province : null,
            'district' => $building ? $building->district : null,
            'ward' => $building ? $building->ward : null,
            'address' => $building ? $building->address_detail : null,
            'property_type' => $validatedData['property_type'] ?? 'nha_o',
            'description' => $validatedData['description'],
            'account_role' => $validatedData['account_role'] ?? 'doanhnghiep',
            'building_id' => $buildingId,
            'amenities' => $mergedAmenities,
            'images' => $imagePaths,
            'status' => 'approved' // Bài đăng của Doanh nghiệp & Quản lý tòa nhà được duyệt tự động hiển thị
        ];

        if ($post) {
            $post->update($dataToSave);
            $msg = 'Cập nhật bài viết thành công!';
        } else {
            $post = clone Post::create($dataToSave);
            $msg = 'Đăng bài thành công! Bài viết đã hiển thị trên hệ thống RentHome.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'post_id' => $post->_id ?? $post->id
        ]);
    }

    public function getApproved(Request $request)
    {
        $activeContractPostIds = Contract::where('status', '!=', 'cancelled')
            ->pluck('post_id')
            ->filter()
            ->toArray();

        $query = Post::with(['user'])
            ->whereIn('status', ['approved', 'active'])
            ->where(function ($q) {
                $q->whereNull('is_rented')
                  ->orWhere('is_rented', false);
            })
            ->whereNotIn('status', ['đã cho thuê', 'rented', 'dang_thue']);

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
        $activeContractPostIds = Contract::where('status', '!=', 'cancelled')
            ->pluck('post_id')
            ->filter()
            ->toArray();

        $query = Post::with(['user'])
            ->whereIn('status', ['approved', 'active'])
            ->where(function ($q) {
                $q->whereNull('is_rented')
                  ->orWhere('is_rented', false);
            })
            ->whereNotIn('status', ['đã cho thuê', 'rented', 'dang_thue']);

        if (!empty($activeContractPostIds)) {
            $query->whereNotIn('_id', $activeContractPostIds)
                  ->whereNotIn('id', $activeContractPostIds);
        }

        $posts = $query->orderBy('created_at', 'desc')->take(6)->get();
        // The frontend expects the array of featured posts directly, or we can map it to match the expected format
        $mapped = $posts->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'price' => number_format($post->price, 0, ',', '.') . ' VNĐ',
                'location' => implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province])),
                'image' => $post->display_image ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'tag' => 'NỔI BẬT',
                'user_id' => (string) ($post->user_id ?? ''),
                'beds' => '1-2 PN',
                'baths' => '1-2 WC',
                'area' => $post->area . ' m²',
                'note' => 'Bất động sản chính chủ RentHome',
            ];
        });
        return response()->json($mapped);
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
}
