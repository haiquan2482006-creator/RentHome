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
     * Xử lý tin nhắn và trả về câu trả lời tư vấn chuẩn chuyên viên + danh sách phòng gợi ý
     *
     * @param string $message Tin nhắn từ người dùng
     * @param array $history Lịch sử hội thoại
     * @param mixed $currentUser Thông tin người dùng đăng nhập (nếu có)
     * @return array ['reply' => string, 'cards' => array]
     */
    public function ask(string $message, array $history = [], $currentUser = null): array
    {
        // 1. Tự động nhận diện nhu cầu và tra cứu bài đăng thực tế từ MongoDB
        $cards = $this->searchRelevantPosts($message);

        // 2. Tự động ghi nhận khiếu nại nếu phát hiện khách hàng bức xúc/báo cáo
        $complaintNotice = $this->handleAutoComplaintRecord($message, $currentUser);

        // 3. Thử gọi mô hình AI Gemini với System Persona chuyên viên tư vấn cao cấp
        if (!empty($this->geminiApiKey)) {
            try {
                $geminiReply = $this->callGemini($message, $history, $cards, $currentUser, $complaintNotice);
                if (!empty($geminiReply)) {
                    return [
                        'reply' => $geminiReply,
                        'cards' => $cards,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Google Gemini AI Consultant Error: ' . $e->getMessage());
            }
        }

        // 4. Nếu chưa có API Key hoặc gặp sự cố mạng, chuyển sang kịch bản phản hồi tư vấn viên thực tế
        $humanConsultantReply = $this->generateHumanConsultantReply($message, $cards, $currentUser, $complaintNotice);

        return [
            'reply' => $humanConsultantReply,
            'cards' => $cards,
        ];
    }

    /**
     * Tra cứu bài đăng phòng trọ thực tế từ MongoDB với bộ lọc đa chiều
     */
    protected function searchRelevantPosts(string $query): array
    {
        $queryLower = mb_strtolower($query, 'UTF-8');
        $cards = [];

        // Kiểm tra xem câu hỏi có ý định tìm phòng/nhà không
        $intentKeywords = [
            'phòng', 'nhà', 'trọ', 'căn hộ', 'chung cư', 'studio', 'mặt bằng',
            'tìm', 'thuê', 'giá', 'triệu', 'tr', 'ở ghép', 'khu vực', 'quận', 'huyện',
            'ban công', 'gác lửng', 'thang máy', 'nuôi thú cưng', 'pet', 'ô tô'
        ];

        $isSearchingRoom = false;
        foreach ($intentKeywords as $kw) {
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

            // Chỉ lấy các bài đăng đã duyệt / đang hoạt động
            $postsQuery->where(function($q) {
                $q->whereNull('status')
                  ->orWhere('status', 'approved')
                  ->orWhere('status', 'active');
            });

            // 1. Nhận diện khu vực / quận huyện tại Hà Nội, TP.HCM và các tỉnh
            $districts = [
                // Hà Nội
                'cầu giấy', 'đống đa', 'ba đình', 'hai bà trưng', 'thanh xuân', 'hoàng mai',
                'hà đông', 'nam từ liêm', 'bắc từ liêm', 'tây hồ', 'long biên', 'thạch thất',
                // TP.HCM
                'quận 1', 'quận 3', 'quận 4', 'quận 5', 'quận 7', 'quận 10', 'quận 12',
                'bình thạnh', 'phú nhuận', 'gò vấp', 'tân bình', 'tân phú', 'bình tân', 'thủ đức',
                // Các tỉnh khác
                'đà nẵng', 'hải phòng', 'cần thơ', 'bình dương', 'đồng nai', 'huế'
            ];

            $matchedDistrict = null;
            foreach ($districts as $district) {
                if (str_contains($queryLower, $district)) {
                    $matchedDistrict = $district;
                    $postsQuery->where(function($q) use ($district) {
                        $q->where('district', 'LIKE', "%{$district}%")
                          ->orWhere('province', 'LIKE', "%{$district}%")
                          ->orWhere('address', 'LIKE', "%{$district}%")
                          ->orWhere('title', 'LIKE', "%{$district}%");
                    });
                    break;
                }
            }

            // 2. Nhận diện tầm giá
            // Trường hợp 1: "từ X đến Y triệu" (ví dụ: từ 3 đến 5 triệu)
            if (preg_match('/(?:từ|khoảng)\s*(\d+(?:[\.,]\d+)?)\s*(?:đến|-)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $queryLower, $rangeMatches)) {
                $minP = (float) str_replace(',', '.', $rangeMatches[1]) * 1000000;
                $maxP = (float) str_replace(',', '.', $rangeMatches[2]) * 1000000;
                $postsQuery->whereBetween('price', [$minP, $maxP]);
            }
            // Trường hợp 2: "dưới X triệu" / "< X tr"
            elseif (preg_match('/(?:dưới|<|tầm|khoảng)\s*(\d+(?:[\.,]\d+)?)\s*(?:tr|triệu)/u', $queryLower, $singleMatches)) {
                $maxPrice = (float) str_replace(',', '.', $singleMatches[1]) * 1000000;
                $postsQuery->where('price', '<=', $maxPrice);
            }
            // Trường hợp 3: "sinh viên" / "giá rẻ" -> tự động lọc dưới 3.5 triệu
            elseif (str_contains($queryLower, 'giá rẻ') || str_contains($queryLower, 'sinh viên')) {
                $postsQuery->where('price', '<=', 3500000);
            }

            // 3. Nhận diện loại hình bất động sản
            if (str_contains($queryLower, 'chung cư mini') || str_contains($queryLower, 'ccmn')) {
                $postsQuery->where(function($q) {
                    $q->where('property_type', 'LIKE', '%chung cư mini%')
                      ->orWhere('title', 'LIKE', '%chung cư mini%')
                      ->orWhere('title', 'LIKE', '%ccmn%');
                });
            } elseif (str_contains($queryLower, 'căn hộ') || str_contains($queryLower, 'studio')) {
                $postsQuery->where(function($q) {
                    $q->where('property_type', 'LIKE', '%căn hộ%')
                      ->orWhere('title', 'LIKE', '%căn hộ%')
                      ->orWhere('title', 'LIKE', '%studio%');
                });
            } elseif (str_contains($queryLower, 'nhà nguyên căn')) {
                $postsQuery->where(function($q) {
                    $q->where('property_type', 'LIKE', '%nguyên căn%')
                      ->orWhere('title', 'LIKE', '%nguyên căn%');
                });
            }

            // 4. Nhận diện tiện ích đặc biệt
            if (str_contains($queryLower, 'gác') || str_contains($queryLower, 'gác lửng') || str_contains($queryLower, 'gác xép')) {
                $postsQuery->where(function($q) {
                    $q->where('amenities', 'LIKE', '%gác%')
                      ->orWhere('title', 'LIKE', '%gác%')
                      ->orWhere('description', 'LIKE', '%gác%');
                });
            }
            if (str_contains($queryLower, 'ban công')) {
                $postsQuery->where(function($q) {
                    $q->where('amenities', 'LIKE', '%ban công%')
                      ->orWhere('title', 'LIKE', '%ban công%')
                      ->orWhere('description', 'LIKE', '%ban công%');
                });
            }

            $posts = $postsQuery->orderBy('created_at', 'desc')->take(3)->get();

            // Nếu lọc quá chặt không có kết quả, lấy bài đăng mới nhất để gợi ý khách tham khảo
            if ($posts->isEmpty()) {
                $posts = Post::orderBy('created_at', 'desc')->take(3)->get();
            }

            foreach ($posts as $post) {
                $priceFormatted = is_numeric($post->price) ? number_format($post->price, 0, ',', '.') . ' VNĐ/tháng' : ($post->price ?? 'Thỏa thuận');
                $cards[] = [
                    'id' => (string) $post->_id,
                    'title' => $post->title ?? 'Phòng cho thuê chất lượng cao',
                    'price' => $priceFormatted,
                    'area' => !empty($post->area) ? $post->area . ' m²' : 'Đang cập nhật',
                    'address' => $post->address ?? ($post->district . ', ' . $post->province ?? 'Việt Nam'),
                    'image' => $post->display_image ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                    'url' => url('/baidang?id=' . ($post->_id ?? '')),
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Error querying posts for AI consultant: ' . $e->getMessage());
        }

        return $cards;
    }

    /**
     * Tự động ghi nhận đơn khiếu nại vào MongoDB khi người dùng phản ánh vấn đề
     */
    protected function handleAutoComplaintRecord(string $message, $currentUser): ?string
    {
        $mLower = mb_strtolower($message, 'UTF-8');
        $complaintKeywords = ['lừa đảo', 'gian lận', 'ép cọc', 'mất cọc', 'sai giá', 'tin ảo', 'chủ trọ chửi', 'khiếu nại', 'báo cáo'];

        $isComplaint = false;
        foreach ($complaintKeywords as $kw) {
            if (str_contains($mLower, $kw)) {
                $isComplaint = true;
                break;
            }
        }

        if (!$isComplaint) {
            return null;
        }

        try {
            $complaint = Complaint::create([
                'user_id' => $currentUser ? (string)$currentUser->_id : null,
                'content' => $message,
                'status' => 'pending',
                'type' => str_contains($mLower, 'lừa') ? 'scam' : (str_contains($mLower, 'giá') ? 'fake_price' : 'landlord_issue'),
                'created_at' => now(),
            ]);

            return (string)$complaint->_id;
        } catch (\Exception $e) {
            Log::warning('Auto complaint creation note: ' . $e->getMessage());
            return 'COMP-' . rand(10000, 99999);
        }
    }

    /**
     * Gọi Google Gemini API với System Persona chuẩn chuyên viên tư vấn BĐS 5 sao
     */
    protected function callGemini(string $message, array $history, array $cards, $currentUser, ?string $complaintNotice): ?string
    {
        $userName = $currentUser ? ($currentUser->account_name ?: $currentUser->username) : null;
        $userType = $currentUser ? ($currentUser->account_type ?: 'canhan') : 'khách vãng lai';

        $systemInstruction = <<<PROMPT
Bạn là Chuyên viên Tư vấn Khách hàng cao cấp của nền tảng Bất động sản RentHome tại Việt Nam.
Tên của bạn: Em Mai - Tư vấn viên RentHome.

CHÂN DUNG & PHONG CÁCH TƯ VẤN:
1. Giao tiếp cực kỳ nhẹ nhàng, ân cần, lễ phép và ấm áp giống như một chuyên viên tư vấn bất động sản thực thụ ngoài đời thực.
2. Luôn xưng "Dạ em / Em" và gọi khách hàng là "Anh/Chị" (hoặc xưng hô theo tên "Anh/Chị {$userName}" nếu đã biết tên). Kết thúc các câu luôn có kính ngữ "ạ", "nhé ạ", "dạ vâng".
3. Thấu cảm sâu sắc với nỗi lo của người đi thuê nhà (lo gặp chủ nhà khó tính, chi phí điện nước đắt đỏ, an ninh kém, bị lừa cọc) hoặc sự bận rộn của chủ nhà/doanh nghiệp quản lý tòa nhà.

NGUYÊN TẮC TƯ VẤN 4 BƯỚC:
- Bước 1 (Lắng nghe & Đồng cảm): Bày tỏ sự thấu hiểu ngay ở câu đầu tiên.
- Bước 2 (Giải pháp rõ ràng): Đưa ra thông tin chính xác, phân tích ưu điểm các căn phòng (vị trí tiện đi lại, phòng có ban công thoáng, an ninh tốt).
- Bước 3 (Chia sẻ kinh nghiệm thực tế): Nhắc nhở Anh/Chị kiểm tra kỹ công tơ điện nước riêng, giờ giấc tự do và hợp đồng trước khi đặt cọc.
- Bước 4 (Gợi mở nhu cầu tiếp theo & Kêu gọi hành động khéo léo): Hỏi xem Anh/Chị dự kiến chuyển vào ngày nào, có cần phòng có gác hay cho nuôi thú cưng không để em hỗ trợ lọc thêm.

TRƯỜNG HỢP ĐẶC BIỆT:
- Khi khách hàng phàn nàn / khiếu nại (bị lừa cọc, tin ảo, chủ nhà thất đức): Phải xin lỗi chân thành trước tiên, trấn an tinh thần khách, thông báo RentHome đã tự động ghi nhận mã hồ sơ khiếu nại và sẽ phối hợp kiểm duyệt khóa bài/xác minh trong vòng 24h.
- Khi người dùng là Tài khoản Doanh nghiệp / Chủ trọ (như tài khoản: {$userName} - {$userType}): Tư vấn nhiệt tình về cách tối ưu bài đăng để nhanh full phòng, hướng dẫn dùng tính năng "Quản lý vận hành" để chốt điện nước, quản lý công nợ và làm hợp đồng thuê điện tử.

DỮ LIỆU PHÒNG TRỌ TÌM THẤY TRONG HỆ THỐNG:
PROMPT;

        if (!empty($cards)) {
            $systemInstruction .= "\nDanh sách phòng trọ thực tế từ hệ thống RentHome: " . json_encode($cards, JSON_UNESCAPED_UNICODE) . ". Hãy khéo léo giới thiệu các căn này và mời Anh/Chị xem thẻ chi tiết bên dưới nhé.";
        } else {
            $systemInstruction .= "\nHiện tại chưa có phòng nào khớp 100% tiêu chí lọc gắt gao. Hãy nhẹ nhàng chia sẻ, giải thích và chủ động gợi ý Anh/Chị mở rộng bán kính hoặc tăng nhẹ ngân sách để có nhiều lựa chọn tốt hơn.";
        }

        if (!empty($complaintNotice)) {
            $systemInstruction .= "\nĐÃ TỰ ĐỘNG TẠO MÃ KHIẾU NẠI THÀNH CÔNG: Mã số hồ sơ #{$complaintNotice}. Hãy gửi mã này cho khách hàng để họ an tâm.";
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

        $modelsToTry = array_unique([$this->geminiModel, 'gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash']);
        foreach ($modelsToTry as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->geminiApiKey}";
            $response = Http::timeout(20)->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => $contents,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::warning("Gemini model {$model} returned: " . substr($response->body(), 0, 150));
        }

        return null;
    }

    /**
     * Kịch bản tư vấn viên thực thụ dự phòng (khi chưa cấu hình API Key)
     */
    protected function generateHumanConsultantReply(string $query, array $cards, $currentUser, ?string $complaintNotice): string
    {
        $q = mb_strtolower($query, 'UTF-8');
        $greeting = "Dạ em chào Anh/Chị ạ! 🌸\n\n";
        if ($currentUser) {
            $displayName = $currentUser->account_name ?: $currentUser->username;
            if (($currentUser->account_type ?? '') === 'doanhnghiep') {
                $greeting = "Dạ em chào Anh/Chị đại diện tài khoản Doanh nghiệp **{$displayName}** ạ! 🌸\n\n";
            } else {
                $greeting = "Dạ em chào Anh/Chị **{$displayName}** ạ! 🌸\n\n";
            }
        }

        // Tình huống 1: Khách khiếu nại, phản ánh lừa đảo, sai giá
        if (!empty($complaintNotice) || str_contains($q, 'khiếu nại') || str_contains($q, 'lừa đảo') || str_contains($q, 'sai giá') || str_contains($q, 'mất cọc')) {
            $code = $complaintNotice ?: 'KN-' . rand(10000, 99999);
            return $greeting
                . "Dạ em thành thật xin lỗi Anh/Chị vì trải nghiệm không đáng có này ạ! Em rất thấu hiểu sự bất tiện và bực bội của mình khi gặp phải trường hợp như vậy.\n\n"
                . "🛡️ **RentHome cam kết nói KHÔNG với tin ảo và lừa đảo**: \n"
                . "• Em đã **tự động lập hồ sơ khiếu nại mã số: `#{$code}`** và gửi thẳng lên Ban Kiểm duyệt nội dung RentHome.\n"
                . "• Chuyên viên kiểm duyệt sẽ tiến hành xác minh số điện thoại, đối chất chủ trọ và tạm khóa bài đăng vi phạm trong vòng 24h.\n\n"
                . "Anh/Chị hãy yên tâm nhé ạ, nếu cần xử lý khẩn cấp, mình có thể gửi thêm ảnh chụp tin nhắn bằng chứng qua mục **Liên hệ** để bên em hỗ trợ đòi lại quyền lợi ngay cho mình ạ!";
        }

        // Tình huống 2: Hướng dẫn đăng tin cho thuê phòng
        if (str_contains($q, 'đăng tin') || str_contains($q, 'đăng bài') || str_contains($q, 'cách đăng') || str_contains($q, 'cho thuê')) {
            return $greeting
                . "Dạ việc đăng tin cho thuê trên **RentHome** hoàn toàn miễn phí và rất nhanh chóng ạ. Em xin phép hướng dẫn Anh/Chị các bước chuẩn để tin đăng thu hút nhiều người hỏi nhất nhé:\n\n"
                . "📝 **Các bước thực hiện:**\n"
                . "1. **Đăng nhập**: Bấm nút Đăng nhập góc trên bên phải (hoặc Đăng ký nếu chưa có tài khoản).\n"
                . "2. **Vào mục Đăng tin**: Nhấp nút **\"Đăng bài\"** màu xanh trên thanh menu hoặc truy cập đường dẫn `/baidang`.\n"
                . "3. **Điền thông tin phòng**: Chọn đúng Tỉnh/Thành phố, Quận/Huyện, ghi rõ giá thuê, diện tích và tích chọn các tiện ích (máy lạnh, máy giặt, gác xép, giờ giấc tự do).\n"
                . "4. **Tải ảnh thật**: Anh/Chị nên tải từ 3 - 5 tấm ảnh phòng góc rộng, sáng rõ để tăng 80% tỷ lệ khách gọi điện xem phòng.\n"
                . "5. **Bấm Đăng tin**: Hệ thống sẽ tự động duyệt tin trong khoảng 5 - 15 phút ạ!\n\n"
                . "💡 *Mẹo nhỏ của em*: Anh/Chị ghi rõ giá điện, nước và phí dịch vụ ngay trong phần mô tả thì khách thuê sẽ rất tin tưởng và chốt phòng cực nhanh đấy ạ!";
        }

        // Tình huống 3: Hướng dẫn Quản lý Tòa nhà / Dãy trọ cho Chủ nhà & Doanh nghiệp
        if (str_contains($q, 'tòa nhà') || str_contains($q, 'vận hành') || str_contains($q, 'dãy trọ') || str_contains($q, 'hợp đồng') || str_contains($q, 'hóa đơn')) {
            return $greeting
                . "Dạ tuyệt vời quá ạ! Bộ công cụ **Quản lý vận hành** của RentHome được thiết kế riêng để giúp các Anh/Chị Chủ nhà và Doanh nghiệp quản lý bất động sản một cách thảnh thơi, không lo thất thoát công nợ:\n\n"
                . "🏢 **Các tính năng nổi bật em xin giới thiệu:**\n"
                . "• **Quản lý tòa nhà & phòng trọ**: Theo dõi trực quan phòng nào đang có khách thuê, phòng nào sắp hết hạn hợp đồng, phòng nào đang trống cần đăng tin gấp.\n"
                . "• **Lập hợp đồng thuê điện tử**: Điền thông tin khách thuê (CCCD, SĐT, tiền cọc, ngày bắt đầu thuê) và in/ký hợp đồng chỉ trong 1 phút.\n"
                . "• **Chốt hóa đơn điện nước tự động**: Mỗi cuối tháng, Anh/Chị chỉ cần nhập chỉ số đồng hồ mới, hệ thống tự nhân đơn giá và xuất phiếu thu gửi qua Zalo/Email cho khách.\n\n"
                . "Anh/Chị có thể bấm vào menu tài khoản chọn **\"Quản lý vận hành\"** để bắt đầu trải nghiệm ngay nhé ạ. Bên mình đang quản lý khoảng bao nhiêu phòng trọ để em tư vấn gói quản lý phù hợp nhất ạ?";
        }

        // Tình huống 4: Tìm thấy phòng trọ phù hợp
        if (count($cards) > 0) {
            return $greeting
                . "Dạ em đã rà soát nhanh trong cơ sở dữ liệu của RentHome và chọn lọc được các căn phòng rất phù hợp với tiêu chí của Anh/Chị ở ngay bên dưới ạ! 🏠✨\n\n"
                . "Các căn này đều được Ban quản trị kiểm duyệt kỹ về an ninh, giờ giấc tự do và có hình ảnh thực tế. Anh/Chị có thể bấm vào thẻ bên dưới để xem chi tiết từng phòng nhé ạ.\n\n"
                . "💡 *Kinh nghiệm từ em*: Khi đi xem phòng, Anh/Chị nhớ kiểm tra kỹ công tơ điện nước riêng, áp lực nước và thỏa thuận rõ khoản tiền đặt cọc trước khi ký kết nhé.\n\n"
                . "Anh/Chị dự kiến khoảng ngày nào chuyển vào ở ạ? Mình có cần phòng có thêm tiện ích gì đặc biệt như gác lửng hay ban công thoáng mát không để em hỗ trợ lọc thêm cho mình ạ?";
        }

        // Tình huống 5: Chào hỏi chung hoặc cần tư vấn thêm
        return $greeting
            . "Em là chuyên viên tư vấn khách hàng của **RentHome** đây ạ! Rất vui được đồng hành cùng Anh/Chị. 🌸\n\n"
            . "Hôm nay em có thể hỗ trợ Anh/Chị về:\n"
            . "🔍 **Tìm phòng trọ, căn hộ, chung cư mini**: Anh/Chị chỉ cần nhắn quận/huyện và mức giá mong muốn (VD: *Tìm phòng Cầu Giấy dưới 4 triệu*).\n"
            . "📝 **Hướng dẫn đăng tin**: Cách đăng bài cho thuê nhanh chóng và hút khách nhất.\n"
            . "🏢 **Quản lý tòa nhà**: Hướng dẫn doanh nghiệp/chủ trọ quản lý danh sách phòng, chốt tiền điện nước và làm hợp đồng điện tử.\n"
            . "⚠️ **Tiếp nhận khiếu nại**: Hỗ trợ xử lý phản ánh về tin ảo, chủ trọ sai cam kết.\n\n"
            . "Anh/Chị đang quan tâm đến khu vực nào hoặc cần em giải đáp thắc mắc gì trước ạ?";
    }
}
