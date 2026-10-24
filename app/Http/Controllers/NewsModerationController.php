<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\NewsArticle;
use App\Models\AdminActivity;

class NewsModerationController extends Controller
{
    /**
     * Dữ liệu nguồn RSS mặc định
     */
    protected array $rssSources = [
        [
            'name'     => 'VnExpress',
            'url'      => 'https://vnexpress.net/rss/bat-dong-san.rss',
            'category' => 'Tin thị trường',
        ],
        [
            'name'     => 'CafeF',
            'url'      => 'https://cafef.vn/bat-dong-san.rss',
            'category' => 'Cẩm nang pháp lý',
        ],
        [
            'name'     => 'Dân Trí',
            'url'      => 'https://dantri.com.vn/rss/bat-dong-san.rss',
            'category' => 'Phân tích giá',
        ],
    ];

    /**
     * Lấy danh sách tin tức cho trang Quản lý kiểm duyệt
     * GET /api/kiem-duyet/news
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $keyword = trim((string)$request->query('keyword', ''));

        // Đảm bảo có dữ liệu ban đầu nếu database trống
        $this->ensureSeedSampleData();

        // Tự động kiểm tra và kéo tin RSS ngầm nếu lần kéo trước cách đây hơn 2 tiếng
        self::autoFetchIfOutdated(2);

        $query = NewsArticle::query();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('summary', 'like', "%{$keyword}%")
                  ->orWhere('source_name', 'like', "%{$keyword}%")
                  ->orWhere('category', 'like', "%{$keyword}%");
            });
        }

        $articles = $query->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        $stats = [
            'total'    => NewsArticle::count(),
            'pending'  => NewsArticle::where('status', 'pending')->count(),
            'approved' => NewsArticle::where('status', 'approved')->count(),
            'rejected' => NewsArticle::where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success'  => true,
            'articles' => $articles,
            'stats'    => $stats,
        ]);
    }

    /**
     * Tự động quét RSS nếu bài viết mới nhất hoặc lần kéo trước cách đây quá số giờ chỉ định
     * và giới hạn tối đa $dailyLimit tin mới mỗi ngày (mặc định 10 tin/ngày).
     */
    public static function autoFetchIfOutdated(int $hoursThreshold = 2, int $dailyLimit = 10): int
    {
        try {
            // Kiểm tra số lượng bài báo đã kéo trong ngày hôm nay
            $todayStart = Carbon::today()->startOfDay();
            $todayCount = NewsArticle::where('created_at', '>=', $todayStart)->count();

            if ($todayCount >= $dailyLimit) {
                // Đã đạt giới hạn tối đa 10 tin trong ngày, không kéo thêm
                return 0;
            }

            $latestArticle = NewsArticle::orderBy('created_at', 'desc')->first();
            $shouldFetch = false;

            if (!$latestArticle) {
                $shouldFetch = true;
            } else {
                $createdAt = $latestArticle->created_at ? Carbon::parse($latestArticle->created_at) : null;
                if (!$createdAt || $createdAt->diffInHours(now()) >= $hoursThreshold) {
                    $shouldFetch = true;
                }
            }

            if ($shouldFetch) {
                $result = self::performRssFetch($dailyLimit, false);
                return $result['new_count'] ?? 0;
            }
        } catch (\Throwable $e) {
            Log::warning("Lỗi autoFetchIfOutdated: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Kéo tin tức từ các nguồn RSS feed Bất động sản
     * POST /api/kiem-duyet/news/fetch-rss
     */
    public function fetchRss(Request $request)
    {
        $force = $request->boolean('force', false);
        $result = self::performRssFetch(10, $force);

        return response()->json([
            'success'       => true,
            'message'       => $result['message'] ?? "Đã quét RSS! Kéo về {$result['new_count']} tin mới.",
            'new_count'     => $result['new_count'],
            'quota_reached' => $result['quota_reached'] ?? false,
            'today_count'   => $result['today_count'] ?? 0,
            'errors'        => $result['errors'] ?? [],
        ]);
    }

    /**
     * Hàm lõi xử lý cào RSS với giới hạn tối đa số tin mỗi ngày (Mặc định 10 tin/ngày)
     */
    public static function performRssFetch(int $dailyLimit = 10, bool $force = false): array
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayFetchedCount = NewsArticle::where('created_at', '>=', $todayStart)->count();

        // Nếu đã đủ 10 tin trong ngày và không ép buộc (force) thì dừng lại
        if (!$force && $todayFetchedCount >= $dailyLimit) {
            return [
                'new_count'     => 0,
                'total_fetched' => 0,
                'quota_reached' => true,
                'today_count'   => $todayFetchedCount,
                'message'       => "Hôm nay hệ thống đã đạt giới hạn tối đa {$dailyLimit} tin mới ({$todayFetchedCount} tin đã lưu hôm nay).",
                'errors'        => [],
            ];
        }

        // Số lượng tin còn lại có thể kéo trong ngày hôm nay
        $remainingQuota = $force ? 10 : max(0, $dailyLimit - $todayFetchedCount);
        if ($remainingQuota <= 0) {
            return [
                'new_count'     => 0,
                'total_fetched' => 0,
                'quota_reached' => true,
                'today_count'   => $todayFetchedCount,
                'message'       => "Đã đạt giới hạn tối đa {$dailyLimit} bài báo mỗi ngày.",
                'errors'        => [],
            ];
        }

        $sources = [
            [
                'name'     => 'VnExpress',
                'url'      => 'https://vnexpress.net/rss/bat-dong-san.rss',
                'category' => 'Tin thị trường',
            ],
            [
                'name'     => 'CafeF',
                'url'      => 'https://cafef.vn/bat-dong-san.rss',
                'category' => 'Cẩm nang pháp lý',
            ],
            [
                'name'     => 'Dân Trí',
                'url'      => 'https://dantri.com.vn/rss/bat-dong-san.rss',
                'category' => 'Phân tích giá',
            ],
        ];

        $newCount = 0;
        $totalFetched = 0;
        $errors = [];

        // Chia đều hạn ngạch giữa các nguồn để nội dung đa dạng (VD: mỗi nguồn 3-4 bài)
        $perSourceLimit = max(2, (int) ceil($remainingQuota / count($sources)));

        foreach ($sources as $source) {
            if ($newCount >= $remainingQuota) {
                break;
            }

            try {
                $response = Http::timeout(6)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) RentHome/1.0',
                ])->get($source['url']);

                if (!$response->successful()) {
                    $errors[] = "Không thể kết nối RSS {$source['name']}";
                    continue;
                }

                $body = $response->body();
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$xml || !isset($xml->channel->item)) {
                    continue;
                }

                $sourceFetched = 0;
                foreach ($xml->channel->item as $item) {
                    if ($newCount >= $remainingQuota || $sourceFetched >= $perSourceLimit) {
                        break;
                    }

                    $totalFetched++;
                    $link = trim((string)$item->link);
                    $title = trim((string)$item->title);
                    $rawDesc = (string)$item->description;
                    $pubDateStr = (string)$item->pubDate;

                    if (empty($link) || empty($title)) {
                        continue;
                    }

                    // Kiểm tra chống trùng lặp theo source_url
                    $exists = NewsArticle::where('source_url', $link)->exists();
                    if ($exists) {
                        continue;
                    }

                    // Tách ảnh đại diện từ description hoặc enclosure
                    $imageUrl = null;
                    if (isset($item->enclosure) && !empty($item->enclosure['url'])) {
                        $imageUrl = (string)$item->enclosure['url'];
                    }

                    if (!$imageUrl && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $rawDesc, $match)) {
                        $imageUrl = $match[1];
                    }

                    if (!$imageUrl) {
                        $fallbackImages = [
                            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                        ];
                        $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                    }

                    $summary = trim(strip_tags($rawDesc));
                    if (mb_strlen($summary) > 350) {
                        $summary = mb_substr($summary, 0, 350) . '...';
                    }

                    $publishedAt = null;
                    try {
                        $publishedAt = $pubDateStr ? Carbon::parse($pubDateStr) : now();
                    } catch (\Exception $e) {
                        $publishedAt = now();
                    }

                    // Lưu bài báo mới với status = 'pending'
                    NewsArticle::create([
                        'title'        => $title,
                        'summary'      => $summary ?: $title,
                        'content'      => $summary,
                        'image_url'    => $imageUrl,
                        'source_name'  => $source['name'],
                        'source_url'   => $link,
                        'category'     => $source['category'],
                        'status'       => 'pending',
                        'is_featured'  => false,
                        'published_at' => $publishedAt,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    $newCount++;
                    $sourceFetched++;
                }
            } catch (\Throwable $e) {
                Log::warning("RSS fetch error [{$source['name']}]: " . $e->getMessage());
                $errors[] = "Lỗi kéo tin từ {$source['name']}: " . $e->getMessage();
            }
        }

        $totalToday = $todayFetchedCount + $newCount;

        return [
            'new_count'     => $newCount,
            'total_fetched' => $totalFetched,
            'quota_reached' => $totalToday >= $dailyLimit,
            'today_count'   => $totalToday,
            'message'       => "Đã kéo {$newCount} bài báo mới hôm nay (Hạn mức tối đa {$dailyLimit} tin/ngày, hiện có {$totalToday} tin hôm nay).",
            'errors'        => $errors,
        ];
    }

    /**
     * Thêm tin tức thủ công từ Kiểm duyệt viên
     * POST /api/kiem-duyet/news
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:300',
            'summary'     => 'required|string|max:1000',
            'source_name' => 'nullable|string|max:100',
            'source_url'  => 'nullable|string|max:500',
            'category'    => 'nullable|string|max:100',
            'image_url'   => 'nullable|string|max:500',
            'is_featured' => 'nullable|boolean',
        ]);

        $isFeatured = $request->boolean('is_featured', false);
        if ($isFeatured) {
            NewsArticle::where('is_featured', true)->update(['is_featured' => false]);
        }

        $article = NewsArticle::create([
            'title'        => $request->input('title'),
            'summary'      => $request->input('summary'),
            'content'      => $request->input('summary'),
            'source_name'  => $request->input('source_name', 'RentHome News'),
            'source_url'   => $request->input('source_url', url('/')),
            'category'     => $request->input('category', 'Tin thị trường'),
            'image_url'    => $request->input('image_url') ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
            'status'       => 'pending',
            'is_featured'  => $isFeatured,
            'published_at' => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm bài viết mới vào danh sách chờ duyệt!',
            'article' => $article,
        ]);
    }

    /**
     * Chỉnh sửa bài báo
     * PUT /api/kiem-duyet/news/{id}
     */
    public function update(Request $request, $id)
    {
        $article = NewsArticle::where('_id', $id)->first() ?: NewsArticle::find($id);
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài báo!'], 404);
        }

        $request->validate([
            'title'       => 'required|string|max:300',
            'summary'     => 'required|string|max:1000',
            'source_name' => 'nullable|string|max:100',
            'source_url'  => 'nullable|string|max:500',
            'category'    => 'nullable|string|max:100',
            'image_url'   => 'nullable|string|max:500',
            'is_featured' => 'nullable|boolean',
        ]);

        $isFeatured = $request->boolean('is_featured', false);
        if ($isFeatured) {
            NewsArticle::where('_id', '!=', $id)->where('is_featured', true)->update(['is_featured' => false]);
        }

        $article->title = $request->input('title');
        $article->summary = $request->input('summary');
        $article->content = $request->input('summary');
        if ($request->filled('source_name')) $article->source_name = $request->input('source_name');
        if ($request->filled('source_url'))  $article->source_url = $request->input('source_url');
        if ($request->filled('category'))    $article->category = $request->input('category');
        if ($request->filled('image_url'))   $article->image_url = $request->input('image_url');
        $article->is_featured = $isFeatured;
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật bài báo thành công!',
            'article' => $article,
        ]);
    }

    /**
     * Phê duyệt bài báo hiển thị lên Trang chủ
     * POST /api/kiem-duyet/news/{id}/approve
     */
    public function approve(Request $request, $id)
    {
        $article = NewsArticle::where('_id', $id)->first() ?: NewsArticle::find($id);
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài báo!'], 404);
        }

        $user = Auth::user();
        $adminId = $user ? (string)($user->_id ?? $user->id) : 'moderator';

        $article->status = 'approved';
        $article->moderated_by = $adminId;
        $article->moderated_at = now();
        $article->save();

        // Ghi log hoạt động
        AdminActivity::log(
            'approve',
            'news',
            'Phê duyệt bài báo lên Trang chủ',
            "Kiểm duyệt viên đã duyệt bài báo [{$article->title}] nguồn {$article->source_name} hiển thị lên Trang chủ.",
            $article,
            ['news_id' => (string)$id, 'title' => $article->title]
        );

        return response()->json([
            'success' => true,
            'message' => "Đã duyệt bài viết [{$article->title}] hiển thị lên Trang chủ thành công!",
            'article' => $article,
        ]);
    }

    /**
     * Xóa bài báo khỏi hệ thống
     * DELETE /api/kiem-duyet/news/{id}
     */
    public function destroy($id)
    {
        $article = NewsArticle::where('_id', $id)->first() ?: NewsArticle::find($id);
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài báo!'], 404);
        }

        $title = $article->title;
        $article->delete();

        AdminActivity::log(
            'delete',
            'news',
            'Xóa bài báo tin tức',
            "Kiểm duyệt viên đã xóa bài báo [{$title}].",
            null,
            ['news_id' => (string)$id, 'title' => $title]
        );

        return response()->json([
            'success' => true,
            'message' => "Đã xóa bài báo [{$title}] thành công!",
        ]);
    }

    /**
     * API Public cho Trang chủ
     * GET /api/news/approved
     * Trả về tối đa 5 bài (1 bài nổi bật + 4 bài lưới)
     */
    public function getApprovedNews()
    {
        $this->ensureSeedSampleData();

        $articles = NewsArticle::where('status', 'approved')
            ->orderBy('is_featured', 'desc')
            ->orderBy('moderated_at', 'desc')
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success'  => true,
            'articles' => $articles,
        ]);
    }

    /**
     * Tự động tạo dữ liệu mẫu nếu database đang trống
     */
    protected function ensureSeedSampleData(): void
    {
        if (NewsArticle::count() > 0) {
            return;
        }

        $samples = [
            [
                'title'        => 'Nhu cầu thuê chung cư và nhà trọ tại các đô thị lớn tiếp tục tăng mạnh',
                'summary'      => 'Thị trường bất động sản cho thuê ghi nhận lượng quan tâm tăng cao vào quý mới, đặc biệt ở phân khúc studio và căn hộ 2 phòng ngủ tại TP.HCM và Hà Nội.',
                'content'      => 'Thị trường bất động sản cho thuê ghi nhận lượng quan tâm tăng cao vào quý mới, đặc biệt ở phân khúc studio và căn hộ 2 phòng ngủ tại TP.HCM và Hà Nội.',
                'image_url'    => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80',
                'source_name'  => 'VnExpress',
                'source_url'   => 'https://vnexpress.net/bat-dong-san',
                'category'     => 'Tin thị trường',
                'is_featured'  => true,
                'status'       => 'approved',
                'published_at' => now()->subHours(2),
                'moderated_at' => now()->subHours(1),
            ],
            [
                'title'        => 'Điểm mặt loạt dự án căn hộ sắp bàn giao mở ra nguồn cung nhà thuê mới',
                'summary'      => 'Hàng loạt dự án tại khu Đông và khu Tây chuẩn bị đón cư dân, bổ sung hàng nghìn căn hộ cho thuê chất lượng cao với mức giá cạnh tranh.',
                'content'      => 'Hàng loạt dự án tại khu Đông và khu Tây chuẩn bị đón cư dân, bổ sung hàng nghìn căn hộ cho thuê chất lượng cao với mức giá cạnh tranh.',
                'image_url'    => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'Batdongsan',
                'source_url'   => 'https://batdongsan.com.vn/tin-thi-truong',
                'category'     => 'Tin thị trường',
                'is_featured'  => false,
                'status'       => 'approved',
                'published_at' => now()->subHours(5),
                'moderated_at' => now()->subHours(3),
            ],
            [
                'title'        => 'Những lưu ý pháp lý quan trọng khi ký hợp đồng thuê nhà theo quy định mới',
                'summary'      => 'Các quy định pháp lý mới về quyền và nghĩa vụ giữa bên thuê và bên cho thuê, thủ tục đăng ký tạm trú và phương thức bảo toàn tiền cọc an toàn.',
                'content'      => 'Các quy định pháp lý mới về quyền và nghĩa vụ giữa bên thuê và bên cho thuê, thủ tục đăng ký tạm trú và phương thức bảo toàn tiền cọc an toàn.',
                'image_url'    => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'CafeF',
                'source_url'   => 'https://cafef.vn/bat-dong-san.chn',
                'category'     => 'Cẩm nang pháp lý',
                'is_featured'  => false,
                'status'       => 'approved',
                'published_at' => now()->subDays(1),
                'moderated_at' => now()->subHours(6),
            ],
            [
                'title'        => 'Xu hướng thuê trọ \'All-in-one\' tích hợp tiện ích thông minh hút giới trẻ',
                'summary'      => 'Mô hình phòng trọ thông minh tích hợp khóa từ, camera an ninh và quản lý hóa đơn qua ứng dụng đang trở thành lựa chọn hàng đầu của sinh viên và nhân viên văn phòng.',
                'content'      => 'Mô hình phòng trọ thông minh tích hợp khóa từ, camera an ninh và quản lý hóa đơn qua ứng dụng đang trở thành lựa chọn hàng đầu của sinh viên và nhân viên văn phòng.',
                'image_url'    => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'VnExpress',
                'source_url'   => 'https://vnexpress.net/kinh-doanh/bat-dong-san',
                'category'     => 'Xu hướng sống',
                'is_featured'  => false,
                'status'       => 'approved',
                'published_at' => now()->subDays(2),
                'moderated_at' => now()->subHours(8),
            ],
            [
                'title'        => 'Giá thuê nhà mặt phố và căn hộ dịch vụ tại trung tâm thiết lập mặt bằng mới',
                'summary'      => 'Làn sóng kinh doanh bán lẻ và dịch vụ ẩm thực quay trở lại thúc đẩy tỷ lệ lấp đầy mặt bằng cho thuê khu vực trung tâm đạt trên 90%.',
                'content'      => 'Làn sóng kinh doanh bán lẻ và dịch vụ ẩm thực quay trở lại thúc đẩy tỷ lệ lấp đầy mặt bằng cho thuê khu vực trung tâm đạt trên 90%.',
                'image_url'    => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'Dân Trí',
                'source_url'   => 'https://dantri.com.vn/bat-dong-san.htm',
                'category'     => 'Phân tích giá',
                'is_featured'  => false,
                'status'       => 'approved',
                'published_at' => now()->subDays(3),
                'moderated_at' => now()->subHours(12),
            ],
            // 2 Tin Pending chờ duyệt mẫu
            [
                'title'        => 'Hà Nội và TP.HCM ghi nhận nguồn cung phòng trọ cao cấp tăng trưởng 25%',
                'summary'      => 'Sự dịch chuyển từ phòng trọ truyền thống sang căn hộ mini khép kín full nội thất ngày càng rõ nét với nhu cầu lưu trú chất lượng cao của thế hệ Gen Z.',
                'content'      => 'Sự dịch chuyển từ phòng trọ truyền thống sang căn hộ mini khép kín full nội thất ngày càng rõ nét với nhu cầu lưu trú chất lượng cao của thế hệ Gen Z.',
                'image_url'    => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'VnExpress',
                'source_url'   => 'https://vnexpress.net/bat-dong-san-tang-truong-phong-tro',
                'category'     => 'Tin thị trường',
                'is_featured'  => false,
                'status'       => 'pending',
                'published_at' => now()->subHours(4),
            ],
            [
                'title'        => 'Cẩm nang phòng tránh rủi ro lừa đảo tiền cọc khi thuê nhà qua mạng xã hội',
                'summary'      => 'Những thủ đoạn tinh vi giả mạo chủ nhà thu tiền cọc trước khi xem phòng và cách đối soát thông tin an toàn qua các nền tảng kiểm duyệt như RentHome.',
                'content'      => 'Những thủ đoạn tinh vi giả mạo chủ nhà thu tiền cọc trước khi xem phòng và cách đối soát thông tin an toàn qua các nền tảng kiểm duyệt như RentHome.',
                'image_url'    => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'Dân Trí',
                'source_url'   => 'https://dantri.com.vn/bat-dong-san-tranh-lua-dao-coc',
                'category'     => 'Cẩm nang pháp lý',
                'is_featured'  => false,
                'status'       => 'pending',
                'published_at' => now()->subHours(6),
            ]
        ];

        foreach ($samples as $s) {
            NewsArticle::create($s);
        }
    }

    /**
     * Thêm tin mẫu pending khi fetch không có tin mới
     */
    protected function seedPendingSamples(): void
    {
        $samples = [
            [
                'title'        => 'Thị trường cho thuê căn hộ ven sông đón làn sóng khách thuê quốc tế',
                'summary'      => 'Chuyên gia nước ngoài và gia đình trẻ ưu tiên tìm kiếm không gian sống trong lành với tiện ích hồ bơi, công viên khép kín.',
                'content'      => 'Chuyên gia nước ngoài và gia đình trẻ ưu tiên tìm kiếm không gian sống trong lành với tiện ích hồ bơi, công viên khép kín.',
                'image_url'    => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'VnExpress',
                'source_url'   => 'https://vnexpress.net/tin-thue-nha-' . time(),
                'category'     => 'Xu hướng sống',
                'is_featured'  => false,
                'status'       => 'pending',
                'published_at' => now(),
            ],
            [
                'title'        => 'Chiến lược tối ưu chi phí thuê nhà và sinh hoạt cho sinh viên năm nhất',
                'summary'      => 'Bí quyết tìm bạn ở ghép tương hợp, chọn vị trí gần tuyến buýt/metro và quản lý chi phí điện nước minh bạch.',
                'content'      => 'Bí quyết tìm bạn ở ghép tương hợp, chọn vị trí gần tuyến buýt/metro và quản lý chi phí điện nước minh bạch.',
                'image_url'    => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80',
                'source_name'  => 'Batdongsan',
                'source_url'   => 'https://batdongsan.com.vn/tin-sinh-vien-' . time(),
                'category'     => 'Cẩm nang pháp lý',
                'is_featured'  => false,
                'status'       => 'pending',
                'published_at' => now(),
            ],
        ];

        foreach ($samples as $s) {
            NewsArticle::create($s);
        }
    }
}
