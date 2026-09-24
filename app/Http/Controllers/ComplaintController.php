<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập!'], 401);
        }

        $request->validate([
            'post_id' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $imageId = null;

        // ĐOẠN NÀY ĐÃ ĐƯỢC SỬA ĐỂ LƯU VÀO DATABASE
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();

            // Lưu trực tiếp ảnh thành chuỗi Base64 vào Collection Images trong MongoDB
            $newImage = \App\Models\Image::create([
                'base64_data' => $base64Data,
                'mime_type' => $mimeType
            ]);

            // Lấy ID của ảnh vừa tạo
            $imageId = $newImage->_id;
        }

        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
            'image' => $imageId, // LƯU ID CỦA MONGODB
            'status' => 'pending',
            'type' => 'appeal'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi phản hồi thành công. Chúng tôi sẽ xem xét sớm nhất có thể.',
            'complaint_id' => $complaint->_id ?? $complaint->id
        ]);
    }
}