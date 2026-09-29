<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RentHomeAiService
{
    protected ?string $kiraApiKey;
    protected string $kiraBaseUrl;
    protected string $kiraModel;

    public function __construct()
    {
        $this->kiraApiKey = config('services.kiraai.api_key') ?: env('KIRAAI_API_KEY');
        $this->kiraBaseUrl = config('services.kiraai.base_url', 'https://kiraai.vn/api/v1') ?: env('KIRAAI_BASE_URL', 'https://kiraai.vn/api/v1');
        $this->kiraModel = config('services.kiraai.model', 'gemini-3.8-flash') ?: env('KIRAAI_MODEL', 'gemini-3.8-flash');
    }

    /**
     * Xử lý tin nhắn người dùng và trả về câu trả lời + danh sách phòng gợi ý
     */
    public function ask(string $message, array $history = []): array
    {
        $cards = $this->searchRelevantPosts($message);
        
        // 1. Thử gọi API Kira AI nếu đã cấu hình Key
        if (!empty($this->kiraApiKey)) {
            try {
                $kiraReply = $this->callKiraAi($message, $history, $cards);
                if (!empty($kiraReply)) {
                    return [
                        'reply' => $kiraReply,
                        'cards' => $cards,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('KiraAI API Request Exception: ' . $e->getMessage());
            }
        }

        // 2. Tự động phản hồi thông minh dự phòng nếu Kira AI hết quota hoặc lỗi kết nối
        $fallbackReply = $this->generateSmartFallbackReply($message, $cards);

        return [
            'reply' => $fallbackReply,
            'cards' => $cards,
        ];
    }

    /**
     * Tra cứu phòng trọ thực tế từ MongoDB phù hợp với nhu cầu
     */
    protected function searchRelevantPosts(string $query): array
    {
        $queryLower = mb_strtolower($query, 'UTF-8');
        $cards = [];

        $keywords = ['phòng', 'nhà', 'trọ', 'căn hộ', 'chung cư', 'tìm', 'thuê', 'giá', 'triệu', 'quận', 'huyện', 'gần'];
        $isSearchingRoom = false;
        foreach ($keywords as $kw) {
            if (str_contains($queryLower, $kw)) {
                $isSearchingRoom = true;
                break;
            }
        }

        if (!$isSearchingRoom) {
            return [];
        }

        try {
            $postsQuery = Post::query();

            // Nhận diện quận/huyện trong câu hỏi
            $districts = [
                'cầu giấy', 'đống đa', 'ba đình', 'hai bà trưng', 'thanh xuân', 'hoàng mai', 'hà đông', 'nam từ liêm', 'bắc từ liêm', 'tây hồ', 'long biên',
                'quận 1', 'quận 3', 'quận 5', 'quận 7', 'quận 10', 'bình thạnh', 'phú nhuận', 'gò vấp', 'tân bình', 'thủ đức'
            ];

            foreach ($districts as $district) {
                if (str_contains($queryLower, $district)) {
                    $postsQuery->where(function($q) use ($district) {
                        $q->where('district', 'LIKE', "%{$district}%")
                          ->orWhere('address', 'LIKE', "%{$district}%")
                          ->orWhere('title', 'LIKE', "%{$district}%");
                    });
                    break;
                }
            }

            // Nhận diện mức giá lọc (ví dụ: "dưới 4 triệu", "3tr")
            if (preg_match('/(?:dưới|<|khoảng|tầm)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $queryLower, $matches)) {
                $maxPrice = (float) str_replace(',', '.', $matches[1]) * 1000000;
                $postsQuery->where('price', '<=', $maxPrice);
            }

            $posts = $postsQuery->orderBy('created_at', 'desc')->take(3)->get();

            if ($posts->isEmpty()) {
                $posts = Post::orderBy('created_at', 'desc')->take(3)->get();
            }

            foreach ($posts as $post) {
                $priceFormatted = is_numeric($post->price) ? number_format($post->price, 0, ',', '.') . ' VNĐ/tháng' : ($post->price ?? 'Thỏa thuận');
                $cards[] = [
                    'id' => (string) $post->_id,
                    'title' => $post->title ?? 'Phòng cho thuê chất lượng',
                    'price' => $priceFormatted,
                    'area' => !empty($post->area) ? $post->area . ' m²' : 'Đang cập nhật',
                    'address' => $post->address ?? ($post->district . ', ' . $post->province ?? 'Việt Nam'),
                    'image' => $post->display_image ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                    'url' => url('/baidang?id=' . ($post->_id ?? '')),
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Error querying posts for AI chat: ' . $e->getMessage());
        }

        return $cards;
    }

    /**
     * Gọi API Kira AI theo chuẩn OpenAI Chat Completions
     */
    protected function callKiraAi(string $message, array $history, array $cards): ?string
    {
        $endpoint = rtrim($this->kiraBaseUrl, '/') . '/chat/completions';

        $systemPrompt = "Bạn là Trợ lý AI Chuyên viên Tư vấn của nền tảng thuê nhà RentHome tại Việt Nam. "
            . "Phong cách trả lời: Nhã nhặn, ân cần, tự xưng là 'Em', gọi khách là 'Anh/Chị'. "
            . "RentHome hỗ trợ tìm phòng trọ, căn hộ, chung cư, nhà nguyên căn, đăng tin miễn phí, và quản lý tòa nhà cho chủ trọ. ";

        if (!empty($cards)) {
            $systemPrompt .= " Dưới đây là các căn phòng trong hệ thống RentHome phù hợp với yêu cầu của khách: " . json_encode($cards, JSON_UNESCAPED_UNICODE) . ". Hãy giới thiệu tóm tắt ngắn gọn và mời Anh/Chị xem thẻ chi tiết bên dưới.";
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($history as $h) {
            $role = ($h['role'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $h['text'] ?? ''];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $response = Http::withToken($this->kiraApiKey)
            ->timeout(15)
            ->post($endpoint, [
                'model' => $this->kiraModel,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        }

        Log::warning('KiraAI API response error: ' . $response->status() . ' - ' . $response->body());
        return null;
    }

    /**
     * Phản hồi dự phòng tự động khi chưa nạp tiền hoặc API gián đoạn
     */
    protected function generateSmartFallbackReply(string $query, array $cards): string
    {
        $q = mb_strtolower($query, 'UTF-8');

        if (str_contains($q, 'đăng tin') || str_contains($q, 'cách đăng') || str_contains($q, 'cho thuê')) {
            return "Dạ để đăng tin cho thuê trên **RentHome**, Anh/Chị thực hiện như sau ạ:\n\n"
                 . "1. Đăng nhập tài khoản RentHome (hoặc nhấp Đăng ký nếu chưa có).\n"
                 . "2. Nhấp vào nút **\"Đăng tin\"** trên thanh menu đầu trang.\n"
                 . "3. Chọn loại hình BĐS, nhập địa chỉ, diện tích, giá thuê và các tiện nghi phòng.\n"
                 . "4. Tải lên hình ảnh phòng thực tế, rõ nét để được duyệt nhanh.\n"
                 . "5. Nhấn **\"Hoàn tất\"**, tin sẽ được duyệt trong vòng 15-30 phút.";
        }

        if (str_contains($q, 'tòa nhà') || str_contains($q, 'quản lý') || str_contains($q, 'doanh nghiệp')) {
            return "Dạ tính năng **Quản lý Tòa nhà** dành riêng cho tài khoản Doanh nghiệp / Chủ trọ nhiều phòng:\n\n"
                 . "• Giúp Anh/Chị quản lý danh sách phòng, trạng thái trống / đã thuê.\n"
                 . "• Tạo hợp đồng thuê, theo dõi hóa đơn điện nước và thanh lý công nợ tự động.\n"
                 . "• Để truy cập, Anh/Chị vào mục **\"Doanh nghiệp / Quản lý vận hành\"** trên thanh điều hướng.";
        }

        if (str_contains($q, 'khiếu nại') || str_contains($q, 'lừa đảo') || str_contains($q, 'báo cáo') || str_contains($q, 'sai giá')) {
            return "Dạ RentHome luôn đặt uy tín và an toàn của Quý khách lên hàng đầu:\n\n"
                 . "• Nếu Anh/Chị phát hiện tin đăng có dấu hiệu lừa đảo, giả mạo hoặc sai giá, vui lòng nhấn nút **\"Báo cáo / Khiếu nại\"** ngay tại trang bài đăng đó.\n"
                 . "• Hoặc liên hệ trực tiếp Ban Quản trị qua mục **Liên hệ** để chuyên viên can thiệp xử lý ngay lập tức ạ!";
        }

        if (count($cards) > 0) {
            return "Dạ em đã tìm thấy các căn phòng phù hợp với yêu cầu của Anh/Chị trên hệ thống RentHome ở bên dưới ạ! Anh/Chị nhấp vào để xem chi tiết ảnh và liên hệ chủ nhà nhé:";
        }

        return "Dạ em là trợ lý tư vấn của RentHome! Em có thể giúp Anh/Chị: tìm phòng trọ/căn hộ theo quận và giá, hướng dẫn đăng tin cho thuê, quản lý tòa nhà hoặc tiếp nhận khiếu nại. Anh/Chị cần em hỗ trợ gì ạ?";
    }
}
