<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập!'], 401);
        }

        $validatedData = $request->validate([
            'post_id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'price_unit' => 'nullable|string',
            'area' => 'required|numeric',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|string',
            'address' => 'nullable|string',
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
            if ($post && $post->user_id != Auth::id()) {
                return response()->json(['error' => 'Không có quyền sửa bài đăng này!'], 403);
            }
        }

        $imagePaths = $post ? $post->images : [];
        if ($request->hasFile('images')) {
            $newPaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');
                $newPaths[] = '/storage/' . $path;
            }
            // Replace images completely if new ones are uploaded for simplicity
            $imagePaths = $newPaths; 
        }

        $dataToSave = [
            'user_id' => Auth::id(),
            'title' => $validatedData['title'],
            'price' => $validatedData['price'],
            'price_unit' => $validatedData['price_unit'] ?? 'thang',
            'area' => $validatedData['area'],
            'province' => $validatedData['province'],
            'district' => $validatedData['district'],
            'ward' => $validatedData['ward'],
            'address' => $validatedData['address'],
            'property_type' => $validatedData['property_type'] ?? 'nha_o',
            'description' => $validatedData['description'],
            'account_role' => $validatedData['account_role'],
            'building_id' => $validatedData['building_id'] ?? null,
            'amenities' => $validatedData['amenities'] ?? [],
            'images' => $imagePaths,
            'status' => 'pending'
        ];

        if ($post) {
            $post->update($dataToSave);
            $msg = 'Cập nhật bài viết thành công, đang chờ kiểm duyệt!';
        } else {
            $post = clone Post::create($dataToSave);
            $msg = 'Đăng bài thành công, bài viết đang chờ kiểm duyệt!';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'post_id' => $post->_id ?? $post->id
        ]);
    }

    public function getApproved()
    {
        $posts = Post::with('user')->where('status', 'approved')->orderBy('created_at', 'desc')->get();
        return response()->json(['posts' => $posts]);
    }
}
