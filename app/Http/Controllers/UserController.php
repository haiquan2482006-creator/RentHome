<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Cập nhật thông tin tài khoản và ảnh đại diện (Avatar)
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện.');
        }

        $user = Auth::user();
        $uId = (string)($user->_id ?? $user->id);

        // 1. Cập nhật thông tin Doanh nghiệp hoặc Cá nhân
        if ($user->isEnterprise() || $user->role === 'doanhnghiep') {
            if ($request->has('representative')) {
                $user->representative = $request->input('representative');
                $user->account_name = $request->input('representative');
            }
            if ($request->has('company_name')) {
                $user->company_name = $request->input('company_name');
            }
            if ($request->has('business_license_date')) {
                $user->business_license_date = $request->input('business_license_date');
            }
            if ($request->has('representative_id_card')) {
                $user->representative_id_card = $request->input('representative_id_card');
                $user->cccd_number = $request->input('representative_id_card');
            }
            if ($request->has('representative_id_date')) {
                $user->representative_id_date = $request->input('representative_id_date');
            }
            if ($request->has('representative_id_place')) {
                $user->representative_id_place = $request->input('representative_id_place');
            }
            if ($request->has('website')) {
                $user->website = $request->input('website');
            }
            if ($request->has('hotline')) {
                $user->hotline = $request->input('hotline');
            }
            if ($request->has('company_description')) {
                $user->company_description = $request->input('company_description');
            }
        } else {
            if ($request->has('account_name')) {
                $user->account_name = $request->input('account_name');
            }
        }

        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('bank_name')) {
            $user->bank_name = $request->input('bank_name');
        }
        if ($request->has('bank_account')) {
            $user->bank_account = $request->input('bank_account');
        }
        if ($request->has('bank_owner')) {
            $user->bank_owner = $request->input('bank_owner');
        }
        if ($request->has('tax_code')) {
            $user->tax_code = $request->input('tax_code');
        }

        // 2. Xử lý tải lên Ảnh đại diện (Avatar) -> Lưu trực tiếp vào MongoDB collection images
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            if ($file && $file->isValid()) {
                // Xóa ảnh avatar cũ của user đó trong collection images để tránh rác DB
                Image::where('target_type', 'avatar')->where('target_id', $uId)->delete();

                // Lưu ảnh avatar mới vào collection images
                $imgDoc = Image::create([
                    'target_type' => 'avatar',
                    'target_id'   => $uId,
                    'filename'    => $file->getClientOriginalName(),
                    'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                    'size'        => $file->getSize(),
                    'data'        => base64_encode(file_get_contents($file->getRealPath())),
                    'is_cover'    => true,
                    'uploaded_by' => $uId,
                ]);

                // Cập nhật đường dẫn avatar trong collection users
                $user->avatar = '/images/' . $imgDoc->_id;
            }
        }

        // 3. Xử lý ảnh giấy tờ định danh (CCCD / GPKD) nếu có
        if ($request->hasFile('id_front')) {
            $file = $request->file('id_front');
            if ($file && $file->isValid()) {
                Image::where('target_type', 'id_front')->where('target_id', $uId)->delete();
                $imgDoc = Image::create([
                    'target_type' => 'id_front',
                    'target_id'   => $uId,
                    'filename'    => $file->getClientOriginalName(),
                    'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                    'size'        => $file->getSize(),
                    'data'        => base64_encode(file_get_contents($file->getRealPath())),
                    'is_cover'    => false,
                    'uploaded_by' => $uId,
                ]);
                $user->id_front = '/images/' . $imgDoc->_id;
            }
        }

        if ($request->hasFile('id_back')) {
            $file = $request->file('id_back');
            if ($file && $file->isValid()) {
                Image::where('target_type', 'id_back')->where('target_id', $uId)->delete();
                $imgDoc = Image::create([
                    'target_type' => 'id_back',
                    'target_id'   => $uId,
                    'filename'    => $file->getClientOriginalName(),
                    'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                    'size'        => $file->getSize(),
                    'data'        => base64_encode(file_get_contents($file->getRealPath())),
                    'is_cover'    => false,
                    'uploaded_by' => $uId,
                ]);
                $user->id_back = '/images/' . $imgDoc->_id;
            }
        }

        if ($request->hasFile('business_license')) {
            $file = $request->file('business_license');
            if ($file && $file->isValid()) {
                Image::where('target_type', 'business_license')->where('target_id', $uId)->delete();
                $imgDoc = Image::create([
                    'target_type' => 'business_license',
                    'target_id'   => $uId,
                    'filename'    => $file->getClientOriginalName(),
                    'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                    'size'        => $file->getSize(),
                    'data'        => base64_encode(file_get_contents($file->getRealPath())),
                    'is_cover'    => false,
                    'uploaded_by' => $uId,
                ]);
                $user->business_license = '/images/' . $imgDoc->_id;
            }
        }

        $user->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công!',
                'avatar'  => $user->display_avatar,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * API tải lên ảnh đại diện riêng lẻ (dành cho Admin hoặc Ajax)
     */
    public function uploadAvatar(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $user = Auth::user();
        $uId = (string)($user->_id ?? $user->id);

        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            Image::where('target_type', 'avatar')->where('target_id', $uId)->delete();

            $imgDoc = Image::create([
                'target_type' => 'avatar',
                'target_id'   => $uId,
                'filename'    => $file->getClientOriginalName(),
                'mime_type'   => $file->getClientMimeType() ?: 'image/jpeg',
                'size'        => $file->getSize(),
                'data'        => base64_encode(file_get_contents($file->getRealPath())),
                'is_cover'    => true,
                'uploaded_by' => $uId,
            ]);

            $user->avatar = '/images/' . $imgDoc->_id;
            $user->save();

            return response()->json([
                'success'    => true,
                'message'    => 'Cập nhật ảnh đại diện thành công!',
                'avatar_url' => url('/images/' . $imgDoc->_id),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy file ảnh hợp lệ.'], 400);
    }
}
