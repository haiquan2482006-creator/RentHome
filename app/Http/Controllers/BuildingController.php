<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Building; // Kích hoạt gọi Model Building

class BuildingController extends Controller
{
    // 1. Hàm lấy danh sách Tòa nhà để hiển thị ra màn hình
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
            }

            if ($user->isSubAccount()) {
                // Tài khoản con CHỈ lấy các tòa nhà được phân công
                $assignedIds = (array) ($user->assigned_buildings ?? []);
                $buildings = Building::whereIn('_id', $assignedIds)->orderBy('created_at', 'desc')->get();
            } elseif ($user->isEnterprise() || $user->role === 'admin' || $user->account_type === 'admin') {
                // Doanh nghiệp RentHome quản lý toàn bộ tòa nhà của hệ thống/doanh nghiệp
                $buildings = Building::orderBy('created_at', 'desc')->get();
            } else {
                // Tài khoản cá nhân không quản lý tòa nhà
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

    // 2. Hàm nhận dữ liệu từ Form và Lưu vào MongoDB
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
            }

            // Chỉ tài khoản Doanh nghiệp (RentHome) hoặc Admin mới được phép tạo tòa nhà
            if (!$user->isEnterprise() && $user->role !== 'admin' && $user->account_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ tài khoản Doanh nghiệp RentHome mới có quyền tạo thêm tòa nhà mới! Tài khoản con chỉ quản lý các tòa nhà được phân công.'
                ], 403);
            }

            // Nhận các trường dữ liệu text
            $data = $request->only([
                'name', 'project', 'type', 'province', 'district', 'ward', 'address_detail', 'total_rooms', 'description'
            ]);
            
            // Tự động gắn ID của người đang đăng nhập
            $data['user_id'] = $user->_id ?? $user->id; 
            $data['status'] = 'active'; 

            // Xử lý lưu File Ảnh đại diện (Nếu có chọn ảnh)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $base64Data = base64_encode(file_get_contents($file->getRealPath()));
                $mimeType = $file->getMimeType();
                
                $newImage = \App\Models\Image::create([
                    'base64_data' => $base64Data,
                    'mime_type' => $mimeType
                ]);
                $data['image'] = $newImage->_id; // Lưu ID ảnh vào DB
            }
            // LƯU VÀO DATABASE
            $building = Building::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tạo tòa nhà thành công!',
                'building' => $building
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi tạo tòa nhà: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu vào Database: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. Hàm hiển thị Chi tiết Tòa nhà
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

            // Kiểm tra phân quyền:
            // 1. Tài khoản con CHỈ được xem tòa nhà trong assigned_buildings
            if ($user->isSubAccount()) {
                if (!$user->canManageBuilding($bId)) {
                    abort(403, 'Bạn không có quyền quản lý tòa nhà này! Bạn chỉ được quản lý các tòa nhà do RentHome phân công.');
                }
            } elseif ($user->isIndividual()) {
                // Tài khoản cá nhân không có quyền quản lý tòa nhà
                abort(403, 'Tài khoản cá nhân không có quyền quản lý tòa nhà.');
            }

            // Tìm tất cả các Phòng (Post) thuộc tòa nhà đó
            $rooms = \App\Models\Post::where('building_id', $bId)
                                    ->orWhere('building_id', (string)$id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

            return view('qlbai_dang.BuildingDetail', compact('building', 'rooms'));

        } catch (\Exception $e) {
            Log::error('Lỗi xem chi tiết tòa nhà: ' . $e->getMessage());
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            abort(500, 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}