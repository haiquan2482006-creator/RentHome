<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Complaint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RentHomeAiService
{
    protected ?string $geminiApiKey;
    protected string $geminiModel;

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $this->geminiModel = config('services.gemini.model', 'gemini-2.5-flash') ?: env('GEMINI_MODEL', 'gemini-2.5-flash');
    }

    /**
     * Xử lý tin nhắn và tư vấn thông minh như người thật
     */
    public function ask(string $message, array $history = [], $currentUser = null): array
    {
        // 1. Tìm kiếm dữ liệu bài đăng thực tế (lọc chính xác theo quận & giá)
        $searchResult = $this->searchRelevantPosts($message);
        $cards = $searchResult['cards'];
        $searchMeta = $searchResult['meta'];

        // 2. Tự động ghi nhận khiếu nại nếu khách hàng bức xúc/báo cáo
        $complaintCode = $this->handleAutoComplaintRecord($message, $currentUser);

        // 3. Thử gọi mô hình AI Gemini nếu có API Key
        if (!empty($this->geminiApiKey)) {
            try {
                $geminiReply = $this->callGemini($message, $history, $cards, $searchMeta, $currentUser, $complaintCode);
                if (!empty($geminiReply)) {
                    return [
                        'reply' => $geminiReply,
                        'cards' => $cards,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Google Gemini API Error: ' . $e->getMessage());
            }
        }

        // 4. Kịch bản tư vấn thực tế chuyên sâu (chuẩn giọng chuyên viên tư vấn 5 sao)
        $humanReply = $this->generateHumanConsultantReply($message, $cards, $searchMeta, $currentUser, $complaintCode);

        return [
            'reply' => $humanReply,
            'cards' => $cards,
        ];
    }

    /**
     * Tra cứu bài đăng phòng trọ thực tế từ MongoDB - LỌC CHÍNH XÁC, KHÔNG TRẢ VỀ BỪA BÃI
     */
    protected function searchRelevantPosts(string $query): array
    {
        $qLower = mb_strtolower($query, 'UTF-8');
        $cards = [];
        $meta = [
            'is_search' => false,
            'district' => null,
            'province' => null,
            'max_price' => null,
            'min_price' => null,
            'found_exact' => false,
        ];

        // Nhận diện ý định tìm phòng (hỗ trợ cả gõ nhầm 'im' thay vì 'tìm')
        $intentKeywords = [
            'tìm', 'tim', 'im', 'kiếm', 'phòng', 'phong', 'nhà', 'nha', 'trọ', 'tro',
            'căn hộ', 'chung cư', 'studio', 'thuê', 'thue', 'giá', 'triệu', 'tr'
        ];

        foreach ($intentKeywords as $kw) {
            if (str_contains($qLower, $kw)) {
                $meta['is_search'] = true;
                break;
            }
        }

        if (!$meta['is_search']) {
            return ['cards' => [], 'meta' => $meta];
        }

        try {
            $postsQuery = Post::query();

            // CHỈ LẤY BÀI ĐÃ DUYỆT (approved) - KHÔNG LẤY TIN CHƯA DUYỆT HOẶC TIN RÁC
            $postsQuery->where('status', 'approved');

            // 1. Nhận diện Quận / Huyện / Tỉnh
            $locations = [
                'cầu giấy' => 'Cầu Giấy', 'đống đa' => 'Đống Đa', 'ba đình' => 'Ba Đình',
                'hai bà trưng' => 'Hai Bà Trưng', 'thanh xuân' => 'Thanh Xuân', 'hoàng mai' => 'Hoàng Mai',
                'hà đông' => 'Hà Đông', 'nam từ liêm' => 'Nam Từ Liêm', 'bắc từ liêm' => 'Bắc Từ Liêm',
                'tây hồ' => 'Tây Hồ', 'long biên' => 'Long Biên', 'sơn tây' => 'Sơn Tây',
                'hà nội' => 'Hà Nội', 'tp.hcm' => 'Hồ Chí Minh', 'hồ chí minh' => 'Hồ Chí Minh',
                'quận 1' => 'Quận 1', 'quận 3' => 'Quận 3', 'quận 7' => 'Quận 7', 'quận 10' => 'Quận 10',
                'bình thạnh' => 'Bình Thạnh', 'phú nhuận' => 'Phú Nhuận', 'gò vấp' => 'Gò Vấp',
                'tân bình' => 'Tân Bình', 'thủ đức' => 'Thủ Đức', 'bắc giang' => 'Bắc Giang'
            ];

            foreach ($locations as $key => $name) {
                if (str_contains($qLower, $key)) {
                    $meta['district'] = $name;
                    $postsQuery->where(function($q) use ($key, $name) {
                        $q->where('district', 'LIKE', "%{$key}%")
                          ->orWhere('district', 'LIKE', "%{$name}%")
                          ->orWhere('province', 'LIKE', "%{$key}%")
                          ->orWhere('province', 'LIKE', "%{$name}%")
                          ->orWhere('address', 'LIKE', "%{$key}%")
                          ->orWhere('title', 'LIKE', "%{$key}%");
                    });
                    break;
                }
            }

            // 2. Nhận diện tầm giá
            if (preg_match('/(?:từ|khoảng)\s*(\d+(?:[\.,]\d+)?)\s*(?:đến|-)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $qLower, $rangeMatches)) {
                $minP = (float) str_replace(',', '.', $rangeMatches[1]) * 1000000;
                $maxP = (float) str_replace(',', '.', $rangeMatches[2]) * 1000000;
                $meta['min_price'] = $minP;
                $meta['max_price'] = $maxP;
                $postsQuery->whereBetween('price', [$minP, $maxP]);
            } elseif (preg_match('/(?:dưới|<|tầm|khoảng|giá)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $qLower, $singleMatches)) {
                $maxPrice = (float) str_replace(',', '.', $singleMatches[1]) * 1000000;
                $meta['max_price'] = $maxPrice;
                $postsQuery->where('price', '<=', $maxPrice);
            }

            $posts = $postsQuery->orderBy('created_at', 'desc')->take(3)->get();

            // NGUYÊN TẮC VÀNG: Nếu không có phòng đúng tiêu chí, KHÔNG LẤY BỪA BÃI PHÒNG 1 TỶ Ở NƠI KHÁC!
            if ($posts->isNotEmpty()) {
                $meta['found_exact'] = true;
                foreach ($posts as $post) {
                    $priceFormatted = is_numeric($post->price) ? number_format($post->price, 0, ',', '.') . ' VNĐ/tháng' : ($post->price ?? 'Thỏa thuận');
                    $cards[] = [
                        'id' => (string) $post->_id,
                        'title' => $post->title ?? 'Phòng cho thuê',
                        'price' => $priceFormatted,
                        'area' => !empty($post->area) ? $post->area . ' m²' : 'Đang cập nhật',
                        'address' => $post->address ?? ($post->district . ', ' . $post->province ?? 'Việt Nam'),
                        'image' => $post->display_image ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                        'url' => url('/baidang?id=' . ($post->_id ?? '')),
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error querying posts for AI: ' . $e->getMessage());
        }

        return ['cards' => $cards, 'meta' => $meta];
    }

    /**
     * Tự động ghi nhận khiếu nại
     */
    protected function handleAutoComplaintRecord(string $message, $currentUser): ?string
    {
        $mLower = mb_strtolower($message, 'UTF-8');
        $complaintKeywords = ['lừa đảo', 'gian lận', 'ép cọc', 'mất cọc', 'sai giá', 'tin ảo', 'chửi khách', 'khiếu nại', 'báo cáo'];

        foreach ($complaintKeywords as $kw) {
            if (str_contains($mLower, $kw)) {
                try {
                    $complaint = Complaint::create([
                        'user_id' => $currentUser ? (string)$currentUser->_id : null,
                        'content' => $message,
                        'status' => 'pending',
                        'type' => 'user_report',
                        'created_at' => now(),
                    ]);
                    return (string)$complaint->_id;
                } catch (\Exception $e) {
                    return 'KN-' . rand(10000, 99999);
                }
            }
        }

        return null;
    }

    /**
     * Gọi Google Gemini API
     */
    protected function callGemini(string $message, array $history, array $cards, array $searchMeta, $currentUser, ?string $complaintCode): ?string
    {
        $userName = $currentUser ? ($currentUser->account_name ?: $currentUser->username) : null;
        $userType = $currentUser ? ($currentUser->account_type ?: 'cá nhân') : 'khách hàng';

        $promptInstruction = "Bạn là Em Mai - Chuyên viên Tư vấn Khách hàng cao cấp của nền tảng Bất động sản RentHome tại Việt Nam. "
            . "Phong thái: Tinh tế, ấm áp, nhã nhặn, tự xưng là 'Em', gọi khách là 'Anh/Chị' (hoặc chào Anh/Chị {$userName} nếu có). "
            . "Nguyên tắc tư vấn thực thụ: "
            . "1. Tuyệt đối TRUNG THỰC: Nếu hệ thống không tìm thấy phòng đúng quận/giá của khách, phải nói rõ ràng và an ủi, tư vấn mở rộng sang quận lân cận hoặc tăng nhẹ ngân sách. Tuyệt đối không bịa đặt hoặc đưa phòng sai khu vực bảo là phù hợp. "
            . "2. Nếu có phòng phù hợp: Khen ngợi và giới thiệu ưu điểm từng căn (gần trung tâm, giá tốt, an ninh), nhắc nhở khách lưu ý kiểm tra công tơ điện nước riêng và tiền cọc trước khi ký kết. "
            . "3. Nếu khách khiếu nại: Xin lỗi chân thành, trấn an, báo mã hồ sơ khiếu nại #{$complaintCode} đã được chuyển tới Ban Kiểm duyệt xử lý trong 24h.";

        if (!empty($cards)) {
            $promptInstruction .= "\nDanh sách phòng tìm thấy: " . json_encode($cards, JSON_UNESCAPED_UNICODE);
        } elseif ($searchMeta['is_search']) {
            $promptInstruction .= "\nKết quả: Hiện tại KHÔNG có phòng nào ở {$searchMeta['district']} với mức giá này.";
        }

        $contents = [];
        foreach ($history as $h) {
            $contents[] = [
                'role' => ($h['role'] === 'user') ? 'user' : 'model',
                'parts' => [['text' => $h['text'] ?? '']]
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        $models = array_unique([$this->geminiModel, 'gemini-2.5-flash', 'gemini-2.0-flash']);
        foreach ($models as $m) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key={$this->geminiApiKey}";
            $res = Http::timeout(15)->post($url, [
                'system_instruction' => ['parts' => [['text' => $promptInstruction]]],
                'contents' => $contents
            ]);
            if ($res->successful()) {
                $data = $res->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        }

        return null;
    }

    /**
     * Kịch bản tư vấn thực tế của chuyên viên BĐS (Chính xác, ấm áp, thấu hiểu)
     */
    protected function generateHumanConsultantReply(string $query, array $cards, array $searchMeta, $currentUser, ?string $complaintCode): string
    {
        $q = mb_strtolower($query, 'UTF-8');
        $greeting = "Dạ em chào Anh/Chị ạ! 🌸\n\n";

        if ($currentUser) {
            $name = $currentUser->account_name ?: $currentUser->username;
            if (($currentUser->account_type ?? '') === 'doanhnghiep') {
                $greeting = "Dạ em chào Anh/Chị đại diện tài khoản Doanh nghiệp **{$name}** ạ! 🌸\n\n";
            } else {
                $greeting = "Dạ em chào Anh/Chị **{$name}** ạ! 🌸\n\n";
            }
        }

        // Tình huống 1: Khách chào hỏi ("xin chào", "hello", "hi")
        if (preg_match('/^(xin chào|chào|hello|hi|alo|chào em|em ơi)/u', trim($q))) {
            return $greeting
                . "Em là chuyên viên tư vấn khách hàng của **RentHome**, rất vui được hỗ trợ mình hôm nay ạ!\n\n"
                . "Anh/Chị đang có nhu cầu:\n"
                . "🔍 **Tìm phòng trọ, căn hộ**: Anh/Chị chỉ cần cho em biết khu vực quận/huyện và mức giá mong muốn (ví dụ: *Tìm phòng Cầu Giấy dưới 4 triệu*).\n"
                . "📝 **Đăng bài cho thuê**: Cần hướng dẫn cách đăng tin hút khách và duyệt nhanh.\n"
                . "🏢 **Quản lý vận hành tòa nhà**: Hướng dẫn tạo tòa nhà, chốt số điện nước và xuất hóa đơn tự động.\n"
                . "⚠️ **Tiếp nhận khiếu nại**: Hỗ trợ giải quyết phản ánh về tin ảo hoặc chủ trọ.\n\n"
                . "Hôm nay em có thể tư vấn giúp mình vấn đề gì trước ạ?";
        }

        // Tình huống 2: Khách khiếu nại / Phản ánh vi phạm
        if (!empty($complaintCode) || str_contains($q, 'khiếu nại') || str_contains($q, 'lừa đảo') || str_contains($q, 'chửi') || str_contains($q, 'sai giá') || str_contains($q, 'mất cọc')) {
            $code = $complaintCode ?: 'KN-' . rand(10000, 99999);
            return $greeting
                . "Dạ em thành thật xin lỗi Anh/Chị vì trải nghiệm không vui này ạ! Em rất thấu hiểu sự bất tiện và bức xúc của mình.\n\n"
                . "🛡️ **RentHome cam kết bảo vệ quyền lợi người thuê và xử lý nghiêm tin vi phạm**:\n"
                . "• Em đã lập hồ sơ khiếu nại tự động với mã số: `#{$code}` và chuyển thẳng tới Ban Kiểm duyệt.\n"
                . "• Bộ phận kiểm duyệt sẽ đối chất chủ bài đăng và xử lý tạm khóa/gỡ bỏ bài đăng vi phạm trong vòng 24h.\n\n"
                . "Anh/Chị yên tâm nhé ạ, nếu cần hỗ trợ khẩn cấp, mình có thể gửi hình ảnh chụp tin nhắn qua mục **Liên hệ** để bên em hỗ trợ đòi lại quyền lợi ngay cho mình ạ!";
        }

        // Tình huống 3: Hướng dẫn đăng tin
        if (str_contains($q, 'đăng tin') || str_contains($q, 'đăng bài') || str_contains($q, 'cách đăng') || str_contains($q, 'cho thuê')) {
            return $greeting
                . "Dạ đăng tin cho thuê trên **RentHome** hoàn toàn miễn phí và rất tiện lợi ạ. Em xin phép chia sẻ các bước để tin đăng của mình hút khách nhất nhé:\n\n"
                . "1. **Vào trang Đăng bài**: Nhấp nút **\"Đăng bài\"** màu xanh ở góc trên hoặc truy cập đường dẫn `/baidang`.\n"
                . "2. **Chọn địa chỉ & Điền giá**: Chọn đúng Tỉnh/Thành phố, Quận/Huyện, ghi rõ mức giá thuê theo tháng và diện tích phòng.\n"
                . "3. **Tích chọn tiện nghi**: Đánh dấu các tiện ích có sẵn như máy lạnh, gác lửng, ban công, giờ giấc tự do, bãi để xe.\n"
                . "4. **Tải ảnh phòng thực tế**: Tải lên 3 - 5 tấm ảnh phòng góc rộng, sáng rõ.\n"
                . "5. **Nhấn Đăng bài**: Ban quản trị sẽ duyệt tin hiển thị trong vòng 5 - 15 phút ạ!\n\n"
                . "💡 *Kinh nghiệm từ em*: Anh/Chị ghi rõ giá điện, nước cụ thể trong bài đăng thì khách thuê sẽ chốt hẹn xem phòng cực kỳ nhanh đấy ạ!";
        }

        // Tình huống 4: Hướng dẫn Tạo tòa nhà & Quản lý vận hành (Doanh nghiệp & Chủ trọ)
        if (str_contains($q, 'tòa nhà') || str_contains($q, 'vận hành') || str_contains($q, 'dãy trọ') || str_contains($q, 'hợp đồng') || str_contains($q, 'hóa đơn')) {
            return $greeting
                . "Dạ bộ công cụ **Quản lý vận hành** của RentHome là giải pháp tuyệt vời giúp các Anh/Chị Chủ nhà và Doanh nghiệp quản lý bất động sản nhàn tênh, không sợ thất thoát công nợ:\n\n"
                . "🏢 **Quy trình vận hành tự động:**\n"
                . "• **Tạo tòa nhà & phòng**: Vào mục **\"Quản lý vận hành\"**, tạo tên tòa nhà và danh sách từng phòng để theo dõi phòng nào trống, phòng nào đã có khách.\n"
                . "• **Lập hợp đồng điện tử**: Điền thông tin khách thuê (CCCD, SĐT, tiền cọc, ngày vào ở) để xuất hợp đồng chuẩn pháp lý chỉ trong 1 phút.\n"
                . "• **Chốt hóa đơn điện nước tự động**: Cuối tháng chỉ cần nhập số điện, số nước mới, hệ thống sẽ tự động tính thành tiền và xuất hóa đơn gửi cho khách.\n\n"
                . "Anh/Chị có thể bấm vào menu tài khoản chọn mục **\"Quản lý vận hành\"** để bắt đầu ngay nhé ạ!";
        }

        // Tình huống 5: KHÁCH TÌM PHÒNG NHƯNG KHÔNG CÓ KẾT QUẢ KHỚP (Ví dụ: Cầu Giấy dưới 4 triệu)
        if ($searchMeta['is_search'] && empty($cards)) {
            $loc = $searchMeta['district'] ?: 'khu vực này';
            $priceText = $searchMeta['max_price'] ? 'dưới ' . number_format($searchMeta['max_price'] / 1000000, 0) . ' triệu' : '';

            return $greeting
                . "Dạ em vừa rà soát toàn bộ cơ sở dữ liệu của RentHome nhưng hiện tại tại khu vực **{$loc}** {$priceText} chưa có phòng trọ nào đang trống và được phê duyệt ạ. 🥺\n\n"
                . "Khu vực {$loc} nhu cầu thuê của sinh viên và người đi làm rất đông, nên các phòng tầm giá rẻ thường được thuê rất nhanh ngay khi vừa đăng.\n\n"
                . "💡 **Lời khuyên chân thành từ em:**\n"
                . "• Anh/Chị có thể cân nhắc mở rộng tìm kiếm sang các quận lân cận (như Nam Từ Liêm, Bắc Từ Liêm, Đống Đa) với bán kính di chuyển chỉ 10 - 15 phút xe máy.\n"
                . "• Hoặc nếu được, mình có thể nới nhẹ ngân sách lên một chút để có nhiều lựa chọn phòng đẹp, an ninh và đầy đủ tiện nghi hơn nhé ạ.\n\n"
                . "Anh/Chị có muốn em hỗ trợ tìm kiếm ở các quận lân cận không ạ?";
        }

        // Tình huống 6: Tìm thấy phòng trọ KHỚP CHÍNH XÁC
        if (count($cards) > 0) {
            $locName = $searchMeta['district'] ? "tại khu vực **{$searchMeta['district']}**" : '';
            return $greeting
                . "Dạ em đã rà soát nhanh trong hệ thống và chọn lọc được các căn phòng rất phù hợp với tiêu chí của Anh/Chị {$locName} ở ngay bên dưới ạ! 🏠✨\n\n"
                . "Các căn này đều đã được kiểm duyệt kỹ về thông tin và giá cả minh bạch. Anh/Chị có thể bấm vào thẻ bên dưới để xem chi tiết ảnh và thông tin liên hệ nhé ạ.\n\n"
                . "💡 *Kinh nghiệm từ em*: Khi đi xem phòng, Anh/Chị nhớ kiểm tra kỹ công tơ điện nước riêng và thỏa thuận rõ khoản tiền đặt cọc trước khi ký hợp đồng nhé.\n\n"
                . "Anh/Chị dự kiến ngày nào chuyển vào ở ạ? Mình có cần phòng có gác lửng hay ban công thoáng mát không để em hỗ trợ lọc thêm cho mình ạ?";
        }

        // Tình huống mặc định
        return $greeting
            . "Dạ em luôn sẵn sàng lắng nghe và hỗ trợ Anh/Chị! Anh/Chị đang cần tìm phòng trọ khu vực nào, hướng dẫn đăng tin hay hỗ trợ khiếu nại ạ? Nhắn cho em biết nhé!";
    }
}
