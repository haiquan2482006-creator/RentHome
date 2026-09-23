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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('complaints', 'public');
            $imagePath = '/storage/' . $path;
        }

        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
            'image' => $imagePath,
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
