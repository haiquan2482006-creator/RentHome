<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $userId = Auth::id();
        $contactInfo = '';

        if (!Auth::check()) {
            $senderName = $request->input('sender_name', '');
            $senderPhone = $request->input('sender_phone', '');
            if (empty($senderPhone) && empty($senderName)) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Vui lòng đăng nhập hoặc cung cấp số điện thoại liên hệ để gửi khiếu nại!'], 401);
                }
                return redirect()->back()->with('error', 'Vui lòng đăng nhập hoặc cung cấp thông tin liên hệ!');
            }
            $contactInfo = " [Người gửi: {$senderName} - SĐT: {$senderPhone}]";
        }

        $request->validate([
            'post_id' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $imageId = null;

        // Lưu ảnh vào Image model (MongoDB) nếu có
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();

            $newImage = \App\Models\Image::create([
                'base64_data' => $base64Data,
                'mime_type' => $mimeType
            ]);

            $imageId = $newImage->_id;
        }

        $reasonType = $request->input('reason_type', 'Khiếu nại kiểm duyệt');
        $fullContent = "[{$reasonType}] " . $request->input('content') . $contactInfo;

        $complaint = Complaint::create([
            'user_id' => $userId,
            'post_id' => $request->input('post_id'),
            'content' => $fullContent,
            'image' => $imageId,
            'status' => 'pending',
            'type' => $request->input('type', 'appeal')
        ]);

        $msg = 'Khiếu nại của bạn đã được gửi đến ban kiểm duyệt thành công. Chúng tôi sẽ xác minh và xử lý trong thời gian sớm nhất!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'complaint_id' => $complaint->_id ?? $complaint->id
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}