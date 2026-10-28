<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Hiển thị danh sách toàn bộ tin tức đã được duyệt
     */
    public function index(Request $request)
    {
        // Tự động kiểm tra và quét RSS ngầm nếu tin cũ
        NewsModerationController::autoFetchIfOutdated(2);

        $query = NewsArticle::where('status', 'approved');

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $kw = trim((string)$request->query('keyword'));
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('summary', 'like', "%{$kw}%")
                  ->orWhere('content', 'like', "%{$kw}%");
            });
        }

        // Lọc theo chuyên mục
        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        // Lọc theo nguồn báo
        if ($request->filled('source') && $request->query('source') !== 'all') {
            $query->where('source_name', $request->query('source'));
        }

        // Sắp xếp
        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('published_at', 'asc')->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('moderated_at', 'desc')
                  ->orderBy('published_at', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        $articles = $query->get();

        // Lấy toàn bộ danh sách đã duyệt để hiển thị bộ lọc động & bài nổi bật
        $allApproved = NewsArticle::where('status', 'approved')
            ->orderBy('moderated_at', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();

        $categories = $allApproved->pluck('category')->filter()->unique()->values()->all();
        $sources = $allApproved->pluck('source_name')->filter()->unique()->values()->all();

        // Bài viết nổi bật (Hero)
        $featuredArticle = $allApproved->where('is_featured', true)->first() ?? $allApproved->first();

        // Cài đặt giao diện thương hiệu
        $themeSettings = file_exists(storage_path('app/theme_settings.json'))
            ? json_decode(file_get_contents(storage_path('app/theme_settings.json')), true)
            : [];

        return view('tintuc', compact('articles', 'allApproved', 'categories', 'sources', 'featuredArticle', 'themeSettings'));
    }

    /**
     * Chuyển hướng trực tiếp sang link bài báo gốc đã kéo về
     */
    public function show($id)
    {
        $article = NewsArticle::where('status', 'approved')
            ->where(function ($q) use ($id) {
                $q->where('_id', $id)->orWhere('id', $id);
            })
            ->first();

        if (!$article) {
            return redirect()->route('news.index')->with('error', 'Bài viết không tồn tại hoặc chưa được duyệt.');
        }

        if (!empty($article->source_url)) {
            return redirect()->away($article->source_url);
        }

        return redirect()->route('news.index');
    }
}
