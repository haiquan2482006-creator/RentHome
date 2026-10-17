<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Xuất dữ liệu ảnh trực tiếp từ collection images trong MongoDB
     * Route: GET /images/{id} và GET /image/{id}
     */
    public function show($id)
    {
        // 1. Tìm Document trong MongoDB theo ID (hỗ trợ cả _id ObjectId và string)
        $img = null;
        try {
            $img = Image::where('_id', $id)->first();
            if (!$img) {
                $img = Image::find($id);
            }
        } catch (\Throwable $e) {
            $img = null;
        }

        if (!$img) {
            abort(404, 'Không tìm thấy hình ảnh.');
        }

        // 2. Lấy dữ liệu Base64 từ trường data (hoặc base64_data cũ nếu có)
        $base64 = $img->data ?: ($img->base64_data ?? null);
        if (empty($base64)) {
            abort(404, 'Nội dung hình ảnh trống.');
        }

        // Nếu chuỗi base64 có chứa header Data URI (data:image/...;base64,...), loại bỏ tiền tố
        if (str_contains($base64, ',')) {
            $parts = explode(',', $base64, 2);
            $base64 = $parts[1];
        }

        $binary = base64_decode($base64);
        if ($binary === false) {
            abort(500, 'Không thể giải mã dữ liệu hình ảnh.');
        }

        $mimeType = $img->mime_type ?: 'image/jpeg';
        $etag = md5($id . ($img->updated_at ?? ''));

        // Kiểm tra If-None-Match để tối ưu hóa hiệu năng, trả về HTTP 304 Not Modified
        if (request()->header('If-None-Match') === $etag) {
            return response('', 304, [
                'Cache-Control' => 'public, max-age=86400',
                'ETag'          => $etag,
            ]);
        }

        return response($binary, 200, [
            'Content-Type'   => $mimeType,
            'Content-Length' => strlen($binary),
            'Cache-Control'  => 'public, max-age=86400',
            'ETag'           => $etag,
        ]);
    }
}
