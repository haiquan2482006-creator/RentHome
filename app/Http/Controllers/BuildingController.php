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
            $buildings = Building::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc')
                                ->get(); 

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
            // Nhận các trường dữ liệu text
            $data = $request->only([
                'name', 'project', 'type', 'province', 'district', 'ward', 'address_detail', 'total_rooms', 'description'
            ]);
            
            // Tự động gắn ID của người đang đăng nhập
            $data['user_id'] = Auth::id(); 
            $data['status'] = 'active'; 

            // Xử lý lưu File Ảnh đại diện (Nếu có chọn ảnh)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->store('buildings', 'public');
                $data['image'] = $path; // Lưu đường dẫn ảnh vào DB
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
            $building = Building::where('_id', $id)->first();
            
            if (!$building) {
                // Thử tìm bằng 'id' phòng trường hợp MySQL thay vì MongoDB
                $building = Building::find($id);
            }

            if (!$building) {
                abort(404, 'Không tìm thấy tòa nhà.');
            }

            // Đảm bảo chỉ chủ tòa nhà mới được xem
            if ($building->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền truy cập tòa nhà này.');
            }

            // Tìm tất cả các Phòng (Post) thuộc tòa nhà đó
            $rooms = \App\Models\Post::where('building_id', $id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

            return view('qlbai_dang.BuildingDetail', compact('building', 'rooms'));

        } catch (\Exception $e) {
            Log::error('Lỗi xem chi tiết tòa nhà: ' . $e->getMessage());
            abort(500, 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}