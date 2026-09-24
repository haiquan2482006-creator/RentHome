<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    public function getPending()
    {
        // THÊM 'imageModels' VÀO TRONG MẢNG WITH
        $posts = Post::with(['user', 'building', 'imageModels'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['posts' => $posts]);
    }

    public function approve($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Không tìm thấy bài đăng!'], 404);
        }
        $post->status = 'approved';
        $post->save();

        if ($post->user_id) {
            $postUser = \App\Models\User::find($post->user_id);
            $category = $postUser && $postUser->account_role ? $postUser->account_role : 'canhan';
            
            Notification::create([
                'user_id' => $post->user_id,
                'post_id' => $post->_id,
                'title' => 'Bài đăng đã được kiểm duyệt',
                'message' => 'Bài viết "' . $post->title . '" của bạn đã được phê duyệt và đang hiển thị trên hệ thống.',
                'type' => 'success',
                'is_read' => false,
                'category' => $category
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã phê duyệt bài đăng thành công!']);
    }

    public function reject(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Không tìm thấy bài đăng!'], 404);
        }
        
        $reason = $request->input('reason', 'Không hợp lệ');
        $post->status = 'rejected';
        $post->reject_reason = $reason;
        $post->save();

        if ($post->user_id) {
            $postUser = \App\Models\User::find($post->user_id);
            $category = $postUser && $postUser->account_role ? $postUser->account_role : 'canhan';
            
            Notification::create([
                'user_id' => $post->user_id,
                'post_id' => $post->_id,
                'title' => 'Bài đăng bị từ chối',
                'message' => 'Bài viết "' . $post->title . '" của bạn không được phê duyệt. Lý do: ' . $reason,
                'type' => 'error',
                'is_read' => false,
                'category' => $category
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã từ chối bài đăng!']);
    }

    public function getHistory()
    {
        // THÊM 'imageModels' VÀO TRONG MẢNG WITH
        $posts = Post::with(['user', 'imageModels'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json(['posts' => $posts]);
    }

    public function restore($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Không tìm thấy bài đăng!'], 404);
        }

        $post->status = 'pending';
        $post->save();

        return response()->json(['success' => true, 'message' => 'Đã khôi phục bài đăng về trạng thái chờ duyệt!']);
    }
}
