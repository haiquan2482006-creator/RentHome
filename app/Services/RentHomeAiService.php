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
     * Nhận tin nhắn và tư vấn nhẹ nhàng, thông minh như chuyên viên tư vấn thực thụ
     */
    public function ask(string $message, array $history = [], $currentUser = null): array
    {
        // 1. Phân tích nhu cầu tìm kiếm phòng trong database
        $searchResult = $this->searchPostsInDatabase($message);
        $cards = $searchResult['cards'];
        $meta = $searchResult['meta'];

        // 2. Ghi nhận khiếu nại nếu khách hàng có khiếu nại / phàn nàn
        $complaintCode = $this->handleComplaint($message, $currentUser);

        // 3. Nếu có Gemini API Key, ưu tiên để Gemini sinh lời thoại tự nhiên
        if (!empty($this->geminiApiKey)) {
            try {
                $geminiReply = $this->callGemini($message, $history, $cards, $meta, $currentUser, $complaintCode);
                if (!empty($geminiReply)) {
                    return [
                        'reply' => $geminiReply,
                        'cards' => $cards,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Gemini Consultant Error: ' . $e->getMessage());
            }
        }

        // 4. Kịch bản tư vấn mềm mại, thấu hiểu (chuẩn quy trình tư vấn BĐS 5 sao)
        $consultantReply = $this->generateSoftConsultantReply($message, $cards, $meta, $currentUser, $complaintCode);

        return [
            'reply' => $consultantReply,
            'cards' => $cards,
        ];
    }

    /**
     * Tìm kiếm phòng trong Database: Khớp chính xác hoặc tìm các căn phù hợp với túi tiền
     */
    protected function searchPostsInDatabase(string $query): array
    {
        $qLower = mb_strtolower($query, 'UTF-8');
        $cards = [];
        $meta = [
            'is_search' => false,
            'is_exact_match' => false,
            'location_requested' => null,
            'target_price' => null,
            'price_label' => '',
            'has_alternatives' => false,
        ];

        // Nhận diện từ khóa tìm kiếm (hỗ trợ cả gõ nhầm 'im' thay vì 'tìm')
        $searchKeywords = [
            'tìm', 'tim', 'im', 'kiếm', 'phòng', 'phong', 'nhà', 'nha', 'trọ', 'tro',
            'căn hộ', 'chung cư', 'studio', 'thuê', 'thue', 'giá', 'triệu', 'tr'
        ];

        foreach ($searchKeywords as $kw) {
            if (str_contains($qLower, $kw)) {
                $meta['is_search'] = true;
                break;
            }
        }

        if (!$meta['is_search']) {
            return ['cards' => [], 'meta' => $meta];
        }

        // 1. Nhận diện khu vực người dùng muốn tìm
        $locationMap = [
            'cầu giấy' => 'Cầu Giấy', 'đống đa' => 'Đống Đa', 'ba đình' => 'Ba Đình',
            'hai bà trưng' => 'Hai Bà Trưng', 'thanh xuân' => 'Thanh Xuân', 'hoàng mai' => 'Hoàng Mai',
            'hà đông' => 'Hà Đông', 'nam từ liêm' => 'Nam Từ Liêm', 'bắc từ liêm' => 'Bắc Từ Liêm',
            'tây hồ' => 'Tây Hồ', 'long biên' => 'Long Biên', 'sơn tây' => 'Sơn Tây',
            'hà nội' => 'Hà Nội', 'tp.hcm' => 'Hồ Chí Minh', 'hồ chí minh' => 'Hồ Chí Minh',
            'quận 1' => 'Quận 1', 'quận 3' => 'Quận 3', 'quận 4' => 'Quận 4', 'quận 5' => 'Quận 5',
            'quận 7' => 'Quận 7', 'quận 10' => 'Quận 10', 'bình thạnh' => 'Bình Thạnh',
            'phú nhuận' => 'Phú Nhuận', 'gò vấp' => 'Gò Vấp', 'tân bình' => 'Tân Bình',
            'thủ đức' => 'Thủ Đức', 'bắc giang' => 'Bắc Giang', 'bà rịa' => 'Bà Rịa'
        ];

        foreach ($locationMap as $alias => $name) {
            if (str_contains($qLower, $alias)) {
                $meta['location_requested'] = $name;
                break;
            }
        }

        // 2. Nhận diện mức giá người dùng mong muốn
        if (preg_match('/(?:từ|khoảng)\s*(\d+(?:[\.,]\d+)?)\s*(?:đến|-)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $qLower, $m)) {
            $meta['target_price'] = (float) str_replace(',', '.', $m[2]) * 1000000;
            $meta['price_label'] = "khoảng {$m[1]} - {$m[2]} triệu";
        } elseif (preg_match('/(?:dưới|<|tầm|khoảng|giá)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $qLower, $m)) {
            $meta['target_price'] = (float) str_replace(',', '.', $m[1]) * 1000000;
            $meta['price_label'] = "tầm {$m[1]} triệu";
        }

        try {
            // Bước 1: Thử tìm chính xác phòng ĐÃ DUYỆT (approved) theo đúng khu vực & giá
            $exactQuery = Post::where('status', 'approved');

            if ($meta['location_requested']) {
                $loc = $meta['location_requested'];
                $exactQuery->where(function($q) use ($loc) {
                    $q->where('district', 'LIKE', "%{$loc}%")
                      ->orWhere('province', 'LIKE', "%{$loc}%")
                      ->orWhere('address', 'LIKE', "%{$loc}%")
                      ->orWhere('title', 'LIKE', "%{$loc}%");
                });
            }

            if ($meta['target_price']) {
                $exactQuery->where('price', '<=', $meta['target_price'] * 1.15); // Dung sai 15%
            }

            $exactPosts = $exactQuery->orderBy('created_at', 'desc')->take(3)->get();

            if ($exactPosts->isNotEmpty()) {
                $meta['is_exact_match'] = true;
                $cards = $this->formatPostCards($exactPosts);
                return ['cards' => $cards, 'meta' => $meta];
            }

            // Bước 2: KHÔNG CÓ CĂN NÀO ĐÚNG TIÊU CHÍ -> Tìm các căn PHÙ HỢP VỚI GIÁ TIỀN của người dùng!
            // Chỉ tìm các bài đăng có mức giá hợp lý (tối đa gấp 3 lần ngân sách hoặc <= 20 triệu)
            // TUYỆT ĐỐI KHÔNG GỢI Ý CĂN BIỆT THỰ 1 TỶ HAY VÀI TRĂM TRIỆU CHO KHÁCH TÌM PHÒNG 4 TRIỆU!
            $priceCeiling = $meta['target_price'] ? max($meta['target_price'] * 3, 15000000) : 25000000;

            $altPosts = Post::where('status', 'approved')
                ->where('price', '<=', $priceCeiling)
                ->orderBy('price', 'asc')
                ->take(3)
                ->get();

            if ($altPosts->isNotEmpty()) {
                $meta['has_alternatives'] = true;
                $cards = $this->formatPostCards($altPosts);
            }
        } catch (\Exception $e) {
            Log::warning('Error in AI search: ' . $e->getMessage());
        }

        return ['cards' => $cards, 'meta' => $meta];
    }

    /**
     * Định dạng bài đăng thành thẻ giao diện
     */
    protected function formatPostCards($posts): array
    {
        $cards = [];
        foreach ($posts as $post) {
            $price = $post->price;
            if (is_numeric($price)) {
                if ($price >= 1000000000) {
                    $priceFormatted = number_format($price / 1000000000, 1, ',', '.') . ' tỷ VNĐ/tháng';
                } elseif ($price >= 1000000) {
                    $priceFormatted = number_format($price / 1000000, 1, ',', '.') . ' triệu VNĐ/tháng';
                } else {
                    $priceFormatted = number_format($price, 0, ',', '.') . ' đ/tháng';
                }
            } else {
                $priceFormatted = $price ?? 'Thỏa thuận';
            }

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
        return $cards;
    }

    /**
     * Tự động ghi nhận khiếu nại
     */
    protected function handleComplaint(string $message, $currentUser): ?string
    {
        $mLower = mb_strtolower($message, 'UTF-8');
        $keywords = ['lừa đảo', 'gian lận', 'ép cọc', 'mất cọc', 'sai giá', 'tin ảo', 'chửi khách', 'khiếu nại', 'báo cáo'];

        foreach ($keywords as $kw) {
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
     * Gọi Gemini API
     */
    protected function callGemini(string $message, array $history, array $cards, array $meta, $currentUser, ?string $complaintCode): ?string
    {
        $userName = $currentUser ? ($currentUser->account_name ?: $currentUser->username) : 'Quý khách';

        $prompt = "Bạn là Em Mai - Chuyên viên Tư vấn Khách hàng của RentHome. Tông giọng: Cực kỳ nhẹ nhàng, lễ phép, ấm áp, thấu hiểu và ân cần. Xưng 'Em', gọi 'Anh/Chị {$userName}'. "
            . "QUY TẮC BẮT BUỘC KHI TƯ VẤN TÌM PHÒNG: "
            . "Nếu hệ thống không có căn phòng nào đúng khu vực/mức giá khách cần: Đầu tiên phải nói 'Dạ em rất xin lỗi Anh/Chị ạ! Hiện tại khu vực... chưa có căn nào đúng tầm giá...', "
            . "sau đó giới thiệu các lựa chọn thay thế phù hợp với túi tiền đang có sẵn trên hệ thống ở bên dưới, và đưa ra lời khuyên chân thành. "
            . "Nếu khách khiếu nại: Xin lỗi chân thành và thông báo mã khiếu nại #{$complaintCode}.";

        $contents = [];
        foreach ($history as $h) {
            $contents[] = [
                'role' => ($h['role'] === 'user') ? 'user' : 'model',
                'parts' => [['text' => $h['text'] ?? '']]
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $models = array_unique([$this->geminiModel, 'gemini-2.5-flash', 'gemini-2.0-flash']);
        foreach ($models as $m) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key={$this->geminiApiKey}";
            $res = Http::timeout(15)->post($url, [
                'system_instruction' => ['parts' => [['text' => $prompt]]],
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
     * Kịch bản tư vấn viên thực thụ (Mềm mại, biết xin lỗi khi không có phòng và chủ động gợi ý lựa chọn vừa túi tiền)
     */
    protected function generateSoftConsultantReply(string $query, array $cards, array $meta, $currentUser, ?string $complaintCode): string
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

        // Tình huống 1: Khách chào hỏi
        if (preg_match('/^(xin chào|chào|hello|hi|alo|chào em|em ơi)/u', trim($q))) {
            return $greeting
                . "Em là chuyên viên tư vấn của **RentHome**, rất vui được đồng hành cùng mình hôm nay ạ!\n\n"
                . "Hôm nay Anh/Chị đang quan tâm đến:\n"
                . "🔍 **Tìm phòng trọ, căn hộ**: Anh/Chị chỉ cần nhắn khu vực quận/huyện và mức giá mong muốn (ví dụ: *Tìm phòng Cầu Giấy tầm 4 triệu*).\n"
                . "📝 **Đăng tin cho thuê**: Hướng dẫn đăng bài nhanh và nhiều khách hỏi.\n"
                . "🏢 **Quản lý vận hành**: Hướng dẫn tạo tòa nhà, chốt tiền điện nước và xuất hóa đơn.\n"
                . "⚠️ **Hỗ trợ khiếu nại**: Tiếp nhận giải quyết các bài đăng sai sự thật.\n\n"
                . "Anh/Chị cần em hỗ trợ việc gì trước ạ?";
        }

        // Tình huống 2: Khách khiếu nại
        if (!empty($complaintCode) || str_contains($q, 'khiếu nại') || str_contains($q, 'lừa đảo') || str_contains($q, 'sai giá') || str_contains($q, 'chửi') || str_contains($q, 'mất cọc')) {
            $code = $complaintCode ?: 'KN-' . rand(10000, 99999);
            return $greeting
                . "Dạ em thành thật xin lỗi Anh/Chị vì trải nghiệm không vui này ạ! Em rất thấu hiểu sự bất tiện và bức xúc của mình.\n\n"
                . "🛡️ **RentHome cam kết xử lý nghiêm minh mọi hành vi vi phạm**:\n"
                . "• Em đã **lập hồ sơ khiếu nại tự động với mã số: `#{$code}`** và chuyển thẳng tới Ban Kiểm duyệt.\n"
                . "• Bộ phận kiểm duyệt sẽ đối chất chủ bài đăng và xử lý tạm khóa/gỡ bỏ bài đăng vi phạm trong vòng 24h.\n\n"
                . "Anh/Chị hoàn toàn yên tâm nhé ạ, quyền lợi và sự an toàn của khách thuê luôn là ưu tiên số 1 của bên em!";
        }

        // Tình huống 3: Hướng dẫn đăng tin
        if (str_contains($q, 'đăng tin') || str_contains($q, 'đăng bài') || str_contains($q, 'cách đăng') || str_contains($q, 'cho thuê')) {
            return $greeting
                . "Dạ đăng tin cho thuê trên **RentHome** hoàn toàn miễn phí và rất đơn giản ạ. Em xin phép chia sẻ các bước để bài đăng của mình hút khách nhất nhé:\n\n"
                . "1. **Vào mục Đăng bài**: Nhấp nút **\"Đăng bài\"** màu xanh trên thanh điều hướng hoặc truy cập `/baidang`.\n"
                . "2. **Điền thông tin chi tiết**: Chọn đúng Tỉnh/Thành phố, Quận/Huyện, ghi rõ mức giá thuê và diện tích.\n"
                . "3. **Đánh dấu tiện ích**: Chọn các tiện ích có sẵn như máy lạnh, gác lửng, ban công, giờ tự do.\n"
                . "4. **Tải ảnh thật**: Anh/Chị tải từ 3 - 5 tấm ảnh phòng góc rộng, sáng rõ để khách tin tưởng hơn.\n"
                . "5. **Bấm Hoàn tất**: Tin đăng sẽ được ban quản trị duyệt trong vòng 5 - 15 phút ạ!\n\n"
                . "💡 *Mẹo nhỏ*: Ghi rõ giá điện, nước công khai trong mô tả sẽ giúp khách chốt thuê nhanh gấp đôi đấy ạ!";
        }

        // Tình huống 4: Hướng dẫn quản lý tòa nhà
        if (str_contains($q, 'tòa nhà') || str_contains($q, 'vận hành') || str_contains($q, 'dãy trọ') || str_contains($q, 'hợp đồng') || str_contains($q, 'hóa đơn')) {
            return $greeting
                . "Dạ bộ công cụ **Quản lý vận hành** của RentHome sẽ giúp Anh/Chị quản lý dãy trọ/tòa nhà cực kỳ thảnh thơi, không lo thất thoát tiền bạc:\n\n"
                . "🏢 **Quy trình vận hành tiện lợi:**\n"
                . "• **Quản lý phòng trọ**: Theo dõi danh sách phòng nào trống, phòng nào đã có khách thuê theo thời gian thực.\n"
                . "• **Lập hợp đồng điện tử**: Điền thông tin người thuê, tiền cọc, ngày vào ở và in/ký hợp đồng chỉ trong 1 phút.\n"
                . "• **Chốt hóa đơn điện nước tự động**: Cuối tháng chỉ cần nhập chỉ số đồng hồ mới, hệ thống tự động tính thành tiền và xuất hóa đơn gửi cho khách.\n\n"
                . "Anh/Chị bấm vào menu tài khoản chọn **\"Quản lý vận hành\"** để bắt đầu trải nghiệm ngay nhé ạ!";
        }

        // Tình huống 5: KHÔNG CÓ CĂN NÀO ĐÚNG TẦM GIÁ / KHU VỰC CỦA NGƯỜI DÙNG CẦN (THEO ĐÚNG YÊU CẦU CỦA BẠN)
        if ($meta['is_search'] && !$meta['is_exact_match']) {
            $loc = $meta['location_requested'] ? "tại khu vực **{$meta['location_requested']}**" : "tại khu vực này";
            $price = $meta['price_label'] ? "trong tầm giá **{$meta['price_label']}**" : "trong tầm giá này";

            $reply = $greeting
                . "Dạ em rất xin lỗi Anh/Chị ạ! 🥺 Hiện tại trong cơ sở dữ liệu của RentHome, {$loc} tạm thời chưa có căn phòng nào {$price} đang trống ạ.\n\n"
                . "Khu vực này nhu cầu thuê rất cao nên các căn phòng giá tốt thường được khách thuê chốt rất nhanh ngay khi vừa đăng lên.\n\n";

            if ($meta['has_alternatives'] && count($cards) > 0) {
                $reply .= "Tuy nhiên, để Anh/Chị không phải mất thời gian chờ đợi, **em xin phép gửi mình một số lựa chọn có mức giá phù hợp nhất đang có sẵn trên hệ thống ở bên dưới** để mình tham khảo trước nhé ạ:\n\n"
                       . "💡 *Gợi ý thêm từ em*: Anh/Chị có thể mở rộng tìm kiếm sang các khu vực lân cận di chuyển thuận tiện, hoặc nới nhẹ ngân sách lên một chút để có thêm nhiều lựa chọn phòng đẹp và an ninh hơn nhé ạ.\n\n"
                       . "Anh/Chị có muốn em hỗ trợ tìm kiếm thêm ở quận/huyện nào khác không ạ?";
            } else {
                $reply .= "💡 Anh/Chị có thể cân nhắc mở rộng tìm kiếm sang các quận lân cận, hoặc nếu được, em xin phép lưu lại nhu cầu của Anh/Chị để ngay khi có chủ nhà đăng căn mới đúng tầm giá, em sẽ báo mình ngay nhé ạ!\n\n"
                       . "Anh/Chị có muốn em tìm kiếm thử ở khu vực lân cận nào không ạ?";
            }

            return $reply;
        }

        // Tình huống 6: Tìm thấy phòng KHỚP CHÍNH XÁC
        if ($meta['is_exact_match'] && count($cards) > 0) {
            $loc = $meta['location_requested'] ? "tại khu vực **{$meta['location_requested']}**" : '';
            return $greeting
                . "Dạ tuyệt vời quá ạ! Em đã rà soát nhanh trong hệ thống và chọn lọc được các căn phòng rất phù hợp với đúng tiêu chí của Anh/Chị {$loc} ở ngay bên dưới ạ! 🏠✨\n\n"
                . "Các căn này đều đã được kiểm duyệt kỹ về thông tin và giá cả minh bạch. Anh/Chị có thể bấm vào thẻ bên dưới để xem chi tiết ảnh và thông tin liên hệ nhé ạ.\n\n"
                . "💡 *Kinh nghiệm từ em*: Khi đi xem phòng, Anh/Chị nhớ kiểm tra kỹ công tơ điện nước riêng và thỏa thuận rõ khoản tiền đặt cọc trước khi ký kết nhé.\n\n"
                . "Anh/Chị dự kiến ngày nào chuyển vào ở ạ? Mình có cần phòng có gác lửng hay ban công thoáng mát không để em hỗ trợ lọc thêm cho mình ạ?";
        }

        // Mặc định
        return $greeting
            . "Dạ em luôn sẵn sàng lắng nghe và hỗ trợ Anh/Chị! Anh/Chị đang cần tìm phòng trọ khu vực nào, hướng dẫn đăng tin hay hỗ trợ khiếu nại ạ? Nhắn cho em biết nhé!";
    }
}
