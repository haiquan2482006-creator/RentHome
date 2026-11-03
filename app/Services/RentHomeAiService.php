<?php

namespace App\Services;

use App\Models\Post;
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
     * Nhận tin nhắn chat từ người dùng và xử lý chuyên sâu:
     * 1. Lọc tòa nhà theo yêu cầu (Chung cư mini, căn hộ, phòng trọ, KTX, nhà riêng, biệt thự liền kề, mặt bằng kinh doanh).
     * 2. Lọc mức giá (dưới, trên, khoảng giá, tầm giá).
     * 3. Tìm bài đăng trong cơ sở dữ liệu và hiển thị thẻ phòng trực quan.
     * 4. Hướng dẫn khách thuê nhà (FAQ thủ tục, hợp đồng, tiền cọc, giá điện nước, lưu ý khi đi xem phòng).
     */
    public function ask(string $message, array $history = [], $currentUser = null, array $uploadedImageIds = []): array
    {
        $cleanMsg = trim($message);
        $userSalutation = $this->resolveUserSalutation($currentUser);
        $qLower = mb_strtolower($cleanMsg, 'UTF-8');

        // 1. NẾU ĐANG CHỜ TIÊU CHÍ TÌM PHÒNG (KHU VỰC & MỨC GIÁ)
        if (session()->get('ai_search_waiting_criteria', false) === true) {
            session()->forget('ai_search_waiting_criteria');
            $searchResult = $this->searchPostsInDatabase($cleanMsg);
            $cards = $searchResult['cards'];
            $meta = $searchResult['meta'];

            if (!empty($meta['needs_clarification'])) {
                return [
                    'reply' => $this->generateSoftConsultantReply($cleanMsg, [], $meta, $userSalutation),
                    'cards' => [],
                ];
            }

            if (!empty($this->geminiApiKey)) {
                try {
                    $geminiReply = $this->callGemini($cleanMsg, $history, $cards, $meta, $currentUser, $userSalutation);
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

            $consultantReply = $this->generateSoftConsultantReply($cleanMsg, $cards, $meta, $userSalutation);
            return [
                'reply' => $consultantReply,
                'cards' => $cards,
            ];
        }

        // 2. NHẬN DIỆN CÂU HỎI THẮC MẮC CỤ THỂ (HƯỚNG DẪN KHÁCH THUÊ NHÀ, FAQ)
        $specificFaqReply = $this->matchSpecificFaqQuestion($cleanMsg, $userSalutation, $currentUser);
        if ($specificFaqReply !== null) {
            return [
                'reply' => $specificFaqReply,
                'cards' => [],
            ];
        }

        // 3. HƯỚNG DẪN KHI NGƯỜI DÙNG YÊU CẦU ĐĂNG BÀI QUA CHATBOT (HƯỚNG DẪN ĐĂNG TRÊN WEBSITE)
        $isPostingRequest = str_contains($qLower, 'đăng bài') || str_contains($qLower, 'dang bai') ||
                            str_contains($qLower, 'đăng tin') || str_contains($qLower, 'dang tin') ||
                            str_contains($qLower, 'lên bài') || str_contains($qLower, 'tạo bài');
        if ($isPostingRequest) {
            return [
                'reply' => "Dạ chào **{$userSalutation}**! RentHome hỗ trợ Chủ nhà / Quản lý tòa nhà đăng tin cho thuê trực tiếp trên website tại mục **[Đăng tin cho thuê](/dang-tin)** (hoàn toàn miễn phí 100%, kiểm duyệt nhanh chóng trong 15 - 30 phút).\n\n"
                         . "🤖 **Chatbot RentHome hiện chuyên sâu phục vụ 4 chức năng chính:**\n"
                         . "• 🏢 **Lọc tòa nhà & loại hình:** Chung cư mini, phòng trọ, KTX, nhà riêng, biệt thự liền kề, mặt bằng kinh doanh...\n"
                         . "• 💰 **Lọc mức giá:** Dưới mức giá, trên mức giá, khoảng giá phù hợp với ngân sách.\n"
                         . "• 🔍 **Tìm kiếm bài đăng:** Tìm phòng theo khu vực quận/huyện, tỉnh thành trên Toàn quốc.\n"
                         . "• 📖 **Hướng dẫn thuê nhà:** Giải đáp hợp đồng, tiền cọc, giá điện nước và lưu ý khi đi xem phòng.\n\n"
                         . "👉 {$userSalutation} hãy nhắn khu vực hoặc tầm giá bạn muốn tìm phòng để mình hỗ trợ ngay nhé!",
                'cards' => [],
            ];
        }

        // 4. HƯỚNG DẪN KHI NGƯỜI DÙNG CÓ NHU CẦU KHIẾU NẠI / BÁO CÁO (HƯỚNG DẪN KÊNH CHÍNH THỨC)
        $isComplaintRequest = str_contains($qLower, 'khiếu nại') || str_contains($qLower, 'khieu nai') ||
                              str_contains($qLower, 'tố cáo') || str_contains($qLower, 'to cao');
        if ($isComplaintRequest) {
            return [
                'reply' => "Dạ chào **{$userSalutation}**! Để bảo vệ quyền lợi người thuê và xử lý nhanh chóng, RentHome tiếp nhận khiếu nại qua các kênh chính thức sau:\n\n"
                         . "• 🚨 **Báo cáo trực tiếp trên bài đăng:** Bấm nút **\"Báo cáo bài đăng\"** tại trang chi tiết phòng để Ban Kiểm duyệt đối chất và tạm khóa bài đăng vi phạm.\n"
                         . "• 📞 **Hotline hỗ trợ khẩn cấp:** Liên hệ tổng đài **1900 8888** để được chuyên viên hỗ trợ can thiệp xử lý ngay.\n\n"
                         . "👉 Nếu bạn cần tư vấn pháp lý về hợp đồng thuê nhà hoặc giải quyết tranh chấp đòi lại tiền cọc (theo Điều 328 BLDS 2015), bạn cứ nhắn cho mình nhé!",
                'cards' => [],
            ];
        }

        // 5. NHẬN DIỆN YÊU CẦU TRỢ GIÚP / TƯ VẤN / GIẢI ĐÁP CHUNG (FAQ / CẦN TRỢ GIÚP / HỖ TRỢ)
        if ($this->isGeneralFaqPrompt($cleanMsg)) {
            return [
                'reply' => $this->generateFaqIntroReply($userSalutation),
                'cards' => [],
            ];
        }

        // 6. TÌM KIẾM PHÒNG TRONG DATABASE (LỌC TÒA NHÀ THEO YÊU CẦU, LỌC GIÁ, TÌM BÀI ĐĂNG)
        $searchResult = $this->searchPostsInDatabase($cleanMsg);
        $cards = $searchResult['cards'];
        $meta = $searchResult['meta'];

        // Nếu người dùng hỏi tìm phòng chung chung thiếu cả địa điểm lẫn mức giá -> Hỏi lại chu đáo kèm nút bấm
        if (!empty($meta['needs_clarification'])) {
            return [
                'reply' => $this->generateSoftConsultantReply($cleanMsg, [], $meta, $userSalutation),
                'cards' => [],
            ];
        }

        // 7. NẾU CÓ GEMINI API KEY, ƯU TIÊN GEMINI PHẢN HỒI THEO KIẾN THỨC CHUẨN THỰC TẾ
        if (!empty($this->geminiApiKey)) {
            try {
                $geminiReply = $this->callGemini($cleanMsg, $history, $cards, $meta, $currentUser, $userSalutation);
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

        // 8. KỊCH BẢN TƯ VẤN CHUYÊN VIÊN BẤT ĐỘNG SẢN THỰC TẾ (FALLBACK)
        $consultantReply = $this->generateSoftConsultantReply($cleanMsg, $cards, $meta, $userSalutation);

        return [
            'reply' => $consultantReply,
            'cards' => $cards,
        ];
    }

    /**
     * Xác định tên hiển thị xưng hô thân mật, đúng tên người dùng
     */
    protected function resolveUserSalutation($currentUser): string
    {
        if (!$currentUser) {
            return 'bạn';
        }
        $name = $currentUser->account_name ?: ($currentUser->username ?? 'bạn');
        return trim((string)$name) ?: 'bạn';
    }

    /**
     * Kiểm tra xem tin nhắn có phải là lời yêu cầu vào chế độ FAQ / Giải đáp thắc mắc chung hay không
     */
    protected function isGeneralFaqPrompt(string $msg): bool
    {
        $q = mb_strtolower(trim($msg), 'UTF-8');

        // Bỏ qua nếu tin nhắn chứa ý định đăng bài / tạo bài cụ thể
        if (str_contains($q, 'đăng bài') || str_contains($q, 'đăng tin') || str_contains($q, 'tạo bài') || str_contains($q, 'lên bài')) {
            return false;
        }

        // Khớp tuyệt đối hoặc tương đối các cụm từ trợ giúp / hỗ trợ / tư vấn / FAQ phổ biến
        $patterns = [
            '/^(?:faq|f\.a\.q|hoi dap|hỏi\s*đáp|giai dap|giải\s*đáp|giải\s*đáp\s*thắc\s*mắc|cần\s*giải\s*đáp|cần\s*giải\s*đáp\s*thắc\s*mắc|tôi\s*cần\s*giải\s*đáp|tôi\s*cần\s*giải\s*đáp\s*thắc\s*mắc|câu\s*hỏi\s*thường\s*gặp|thắc\s*mắc|hỗ\s*trợ\s*giải\s*đáp|trợ\s*giúp|cần\s*trợ\s*giúp|tôi\s*cần\s*trợ\s*giúp|hỗ\s*trợ|cần\s*hỗ\s*trợ|tôi\s*cần\s*hỗ\s*trợ|tư\s*vấn|cần\s*tư\s*vấn|tôi\s*cần\s*tư\s*vấn|giúp\s*tôi|giúp\s*mình|cần\s*giúp\s*đỡ|hỗ\s*trợ\s*tôi|hỗ\s*trợ\s*mình|trợ\s*giúp\s*tôi|trợ\s*giúp\s*mình|tôi\s*cần\s*giúp|bạn\s*giúp\s*tôi\s*với|tôi\s*cần\s*bạn\s*giúp)$/ui',
            '/^(?:cho\s*mình|cho\s*tôi|em\s*cho\s*anh|em\s*cho\s*chị)?\s*(?:hỏi\s*chút|hỏi\s*xíu|hỏi\s*câu\s*này|có\s*vài\s*thắc\s*mắc|có\s*thắc\s*mắc|giải\s*đáp\s*giúp\s*mình|giải\s*đáp\s*cho\s*tôi|tư\s*vấn\s*giúp\s*tôi|tư\s*vấn\s*cho\s*tôi|hỗ\s*trợ\s*giúp\s*tôi|trợ\s*giúp\s*cho\s*tôi)$/ui',
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $q)) {
                return true;
            }
        }

        // Nếu là câu ngắn (dưới 40 ký tự) chứa từ khóa trợ giúp/tư vấn/giải đáp
        $shortHelpKeywords = [
            'trợ giúp', 'cần trợ giúp', 'hỗ trợ', 'cần hỗ trợ', 'tư vấn', 'cần tư vấn',
            'giải đáp', 'cần giải đáp', 'hỏi đáp', 'faq', 'thắc mắc'
        ];

        if (mb_strlen($q, 'UTF-8') <= 40) {
            foreach ($shortHelpKeywords as $shk) {
                if (str_contains($q, $shk)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Tạo câu chào mừng FAQ theo đúng yêu cầu:
     * "Xin chào (tên người dùng) bạn cần mình giải đáp điều gì ạ? 💡✨"
     * kèm hướng dẫn lĩnh vực và các nút bấm câu hỏi nhanh.
     */
    protected function generateFaqIntroReply(string $userName): string
    {
        return "Xin chào **{$userName}**! Tôi là **Chuyên gia Tư vấn Cấp cao RentHome**. Bạn cần giải đáp thắc mắc hoặc tư vấn về thủ tục thuê nhà ạ? 💡✨\n\n"
             . "Tôi sẵn sàng tư vấn và hướng dẫn khách thuê chi tiết, chuẩn xác theo quy định pháp luật Việt Nam và chính sách RentHome về:\n"
             . "• 📝 **Hợp đồng & Tiền cọc:** Quy định đặt cọc (Điều 328 BLDS 2015), điều khoản hợp đồng bắt buộc, cách lấy lại cọc...\n"
             . "• ⚡ **Điện nước & Chi phí:** Quy định giá điện nước phòng trọ, chủ trọ có được tự ý tăng giá không...\n"
             . "• 🤝 **Phí môi giới & Xem phòng:** RentHome kết nối trực tiếp chính chủ với **0 VNĐ phí môi giới**, hướng dẫn xem phòng...\n"
             . "• 📋 **Giấy tờ & Thủ tục thuê:** Cần chuẩn bị CCCD, thủ tục đăng ký tạm trú (Luật Cư trú 2020)...\n"
             . "• 🏢 **Lọc tòa nhà & Mức giá:** Tra cứu căn hộ, chung cư mini, phòng trọ theo khoảng ngân sách và địa điểm...\n\n"
             . "👉 **{$userName} hãy nhắn trực tiếp câu hỏi cụ thể**, hoặc bấm nhanh vào các câu hỏi thường gặp bên dưới:\n"
             . "<div style=\"margin-top: 10px; display: flex; flex-direction: column; gap: 6px;\">\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Làm sao để thuê nhà trên RentHome?')\" style=\"text-align: left; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">🏠 Làm sao để thuê nhà? Quy trình 5 bước thuê nhà an toàn?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Thuê nhà có phải đặt cọc không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📝 Thuê nhà có phải đặt cọc không? Cọc bao nhiêu?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Hợp đồng thuê nhà làm như thế nào?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📑 Hợp đồng thuê nhà làm như thế nào? Lưu ý gì?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Chủ nhà không trả tiền cọc thì làm thế nào?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">⚖️ Chủ trọ không trả tiền cọc thì làm thế nào?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Quy định về giá điện nước phòng trọ như thế nào?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">⚡ Quy định về giá điện nước phòng trọ?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Chủ trọ có được tự ý tăng giá phòng không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📈 Chủ trọ có được tự ý tăng giá phòng không?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Khi đi thuê phòng cần chuẩn bị giấy tờ gì?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📋 Khi đi thuê phòng cần chuẩn bị giấy tờ gì?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('RentHome có thu phí môi giới không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">🤝 Thuê phòng có mất phí môi giới không?</button>\n"
             . "</div>";
    }

    /**
     * Khớp chính xác câu hỏi cụ thể và giải đáp theo cấu trúc chuẩn chuyên gia tư vấn cấp cao (4 phần)
     */
    protected function matchSpecificFaqQuestion(string $msg, string $userName, $currentUser = null): ?string
    {
        $q = mb_strtolower(trim($msg), 'UTF-8');

        // Chuẩn hóa teencode & từ ngữ tiếng lóng phổ biến
        $normalized = ' ' . $q . ' ';
        $normalized = preg_replace('/\b(k|ko|khong|kh|hong|hông|khum|hok)\b/ui', 'không', $normalized);
        $normalized = preg_replace('/\b(đc|dc)\b/ui', 'được', $normalized);
        $normalized = preg_replace('/\b(ntn|sao)\b/ui', 'thế nào', $normalized);
        $normalized = preg_replace('/\b(p|phg|ptro)\b/ui', 'phòng', $normalized);
        $normalized = trim($normalized);

        // 0.A CHỦ ĐỀ ĐẶC BIỆT: HƯỚNG DẪN CÁC BƯỚC THUÊ NHÀ / QUY TRÌNH THUÊ NHÀ TRÊN RENTHOME
        // (Ví dụ: "tôi muốn thuê nhà thì làm sao để có thể thuê nhà", "cách để thuê nhà", "làm sao để thuê nhà", "các bước thuê nhà")
        $isHowToRentGuide = (
            preg_match('/(?:làm sao|làm như thế nào|làm thế nào|làm cách nào|cách nào|bước nào|cần làm gì|cần làm những gì|bắt đầu từ đâu|hướng dẫn|các bước|quy trình|thủ tục)\s*.*(?:để|được|có thể)?\s*(?:thuê nhà|thuê phòng|thuê trọ|thuê căn hộ)/ui', $q) ||
            preg_match('/(?:muốn thuê|cần thuê|đi thuê)\s*(?:nhà|phòng|trọ|căn hộ)?\s*(?:thì|phải)?\s*.*(?:làm sao|làm như thế nào|làm thế nào|làm cách nào|bước nào|cần làm gì|cần làm những gì|bắt đầu từ đâu|làm gì|làm những gì)/ui', $q) ||
            preg_match('/^(?:hướng dẫn|cách|quy trình|các bước)\s*(?:để\s*)?(?:thuê|tìm thuê)\s*(?:nhà|phòng|trọ|căn hộ)/ui', $q) ||
            preg_match('/(?:làm sao|làm thế nào|làm cách nào)\s*(?:để\s*)?(?:có thể\s*)?(?:thuê|tìm thuê)\s*(?:được\s*)?(?:nhà|phòng|trọ|căn hộ)/ui', $q) ||
            preg_match('/cách thuê nhà trên (?:web|website|renthome)/ui', $q) ||
            preg_match('/(?:biết|cho tôi biết|chỉ tôi)?\s*cách\s*(?:để\s*)?(?:thuê|tìm thuê)\s*(?:nhà|phòng|trọ|căn hộ)/ui', $q) ||
            str_contains($q, 'cách để thuê nhà') || str_contains($q, 'cách để thuê phòng') || str_contains($q, 'cách thuê nhà') || str_contains($q, 'cách thuê phòng')
        );

        if ($isHowToRentGuide) {
            return "🎯 **Quy trình các bước thuê nhà trên RentHome:**\n\n"
                 . "Để thuê nhà hoặc phòng trọ trên hệ thống RentHome, bạn thực hiện theo các bước sau nhé:\n\n"
                 . "1. **Vào trang chi tiết bài đăng muốn chọn:** Tìm kiếm và bấm vào bài đăng phòng trọ / căn hộ phù hợp với nhu cầu của bạn.\n"
                 . "2. **Điền thông tin yêu cầu liên hệ, hoặc gọi điện trao đổi với người đăng:** Bạn có thể bấm gửi yêu cầu liên hệ hoặc gọi điện / nhắn tin trực tiếp cho chủ bài đăng để trao đổi chi tiết và hẹn xem phòng.\n"
                 . "3. **Chờ phản hồi từ người đăng:** Chủ bài đăng sẽ tiếp nhận thông tin và phản hồi lại cho bạn.\n"
                 . "4. **Nếu người đăng chấp nhận, chờ hóa đơn và hợp đồng điện tử:** Sau khi hai bên thống nhất, người đăng sẽ khởi tạo và gửi hóa đơn cùng hợp đồng điện tử trực tiếp trên hệ thống cho bạn xác nhận.\n\n"
                 . "👉 Bây giờ, {$userName} đang muốn tìm phòng/nhà ở khu vực quận/huyện nào và khoảng mức giá bao nhiêu? Hãy nhắn cho mình để mình hỗ trợ lọc danh sách các bài đăng tốt nhất gửi bạn ngay nhé!";
        }

        // Bỏ qua giải đáp FAQ tĩnh nếu người dùng đang có nhu cầu tìm phòng cụ thể:
        // (Có động từ tìm phòng HOẶC có địa điểm quận/huyện đi kèm mức giá / diện tích)
        $hasSearchIntent = preg_match('/\b(tìm|tim|kiếm|kiem|cần thuê|can thue|muốn thuê|muon thue|gợi ý phòng|có phòng nào|co phong nao|những phòng nào|còn phòng nào)\b/ui', $q);
        $hasLocationKeyword = preg_match('/\b(cầu giấy|đống đa|ba đình|hai bà trưng|thanh xuân|hoàng mai|hà đông|nam từ liêm|bắc từ liêm|tây hồ|long biên|gia lâm|quận 1|quận 2|quận 3|quận 4|quận 5|quận 7|quận 10|bình thạnh|gò vấp|tân bình|thủ đức|hà nội|hồ chí minh|sài gòn|đà nẵng)\b/ui', $q);
        $hasPriceKeyword = preg_match('/(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|k|nghìn)\b/ui', $q);
        $hasAreaKeyword = preg_match('/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)\b/ui', $q) || preg_match('/\bdiện\s*tích\b/ui', $q);

        if ($hasSearchIntent || ($hasLocationKeyword && ($hasPriceKeyword || $hasAreaKeyword)) || $hasAreaKeyword) {
            return null;
        }

        // Chỉ bỏ qua FAQ nếu câu chat mang tính tường thuật sự việc thực tế cá nhân (để chuyển luồng khiếu nại xử lý)
        // Nếu câu chat là hỏi về quy trình/cách thức/thủ tục (chứa "thế nào", "như nào", "ở đâu", "làm sao", "quy trình", "hướng dẫn")
        // thì giữ nguyên để Chủ đề 12 ($isFaqReport) và các mục FAQ khác xử lý bình thường.
        $isFaqInquiry = str_contains($q, 'thế nào') || str_contains($q, 'như nào') || str_contains($q, 'như thế nào') ||
                        str_contains($q, 'ở đâu') || str_contains($q, 'làm sao') || str_contains($q, 'quy trình') ||
                        str_contains($q, 'hướng dẫn') || str_contains($q, 'cách nào') || str_contains($q, 'làm cách nào');

        $isPersonalIncident = str_contains($q, 'tôi bị') || str_contains($q, 'mình bị') || str_contains($q, 'em bị') ||
                              str_contains($q, 'vừa bị lừa') || str_contains($q, 'bị lừa') || str_contains($q, 'chủ này lừa') ||
                              str_contains($q, 'chủ trọ lừa') || str_contains($q, 'chỗ này lừa') || str_contains($q, 'bài này lừa') ||
                              str_contains($q, 'web này lừa') || str_contains($q, 'nó lừa') || str_contains($q, 'thằng này lừa') ||
                              str_contains($q, 'bị bùng cọc') || str_contains($q, 'bùng cọc của') || str_contains($q, 'quịt tiền của') ||
                              str_contains($q, 'quịt cọc của');

        if ($isPersonalIncident && !$isFaqInquiry) {
            return null;
        }

        // 0. CHỦ ĐỀ: BẢO MẬT THÔNG TIN HỆ THỐNG / ADMIN LÀ AI / AI DO AI TẠO RA / THÔNG TIN DỰ ÁN
        $isSecurityOrIdentity = str_contains($q, 'admin') || str_contains($q, 'quản trị viên') ||
                                str_contains($q, 'tạo ra') || str_contains($q, 'người tạo') ||
                                str_contains($q, 'ai tạo') || str_contains($q, 'ai làm') ||
                                str_contains($q, 'ai viết') || str_contains($q, 'ai lập trình') ||
                                str_contains($q, 'ai phát triển') || str_contains($q, 'ai sinh ra') ||
                                str_contains($q, 'người phát triển') || str_contains($q, 'người lập trình') ||
                                str_contains($q, 'cha đẻ') || str_contains($q, 'tác giả') ||
                                str_contains($q, 'sáng lập') || str_contains($q, 'đứng sau') ||
                                str_contains($q, 'chủ sở hữu') || str_contains($q, 'ai sở hữu') ||
                                str_contains($q, 'công nghệ') || str_contains($q, 'mã nguồn') ||
                                str_contains($q, 'source code') || str_contains($q, 'mã code') ||
                                str_contains($q, 'database') || str_contains($q, 'cơ sở dữ liệu') ||
                                str_contains($q, 'framework') || str_contains($q, 'kiến trúc') ||
                                str_contains($q, 'backend') || str_contains($q, 'frontend') ||
                                str_contains($q, 'dự án') ||
                                (str_contains($q, 'bạn là ai') && !str_contains($q, 'phòng')) ||
                                (str_contains($q, 'renthome') && (str_contains($q, 'là gì') || str_contains($q, 'chức năng') || str_contains($q, 'tính năng') || str_contains($q, 'giới thiệu') || str_contains($q, 'làm gì') || str_contains($q, 'có gì') || $q === 'renthome' || $q === 'về renthome')) ||
                                ((str_contains($q, 'hệ thống này') || str_contains($q, 'website này') || str_contains($q, 'web này') || str_contains($q, 'trang web này') || str_contains($q, 'phần mềm này') || str_contains($q, 'app này') || str_contains($q, 'nền tảng này')) &&
                                 (str_contains($q, 'là gì') || str_contains($q, 'chức năng') || str_contains($q, 'tính năng') || str_contains($q, 'làm gì') || str_contains($q, 'có gì') || str_contains($q, 'hoạt động') || str_contains($q, 'giới thiệu')));

        if ($isSecurityOrIdentity) {
            $isStrictSecret = str_contains($q, 'admin') || str_contains($q, 'quản trị viên') ||
                              str_contains($q, 'tạo ra') || str_contains($q, 'người tạo') ||
                              str_contains($q, 'ai tạo') || str_contains($q, 'ai làm') ||
                              str_contains($q, 'ai viết') || str_contains($q, 'ai lập trình') ||
                              str_contains($q, 'ai phát triển') || str_contains($q, 'ai sinh ra') ||
                              str_contains($q, 'người phát triển') || str_contains($q, 'người lập trình') ||
                              str_contains($q, 'cha đẻ') || str_contains($q, 'tác giả') ||
                              str_contains($q, 'sáng lập') || str_contains($q, 'đứng sau') ||
                              str_contains($q, 'chủ sở hữu') || str_contains($q, 'công nghệ') ||
                              str_contains($q, 'mã nguồn') || str_contains($q, 'code') ||
                              str_contains($q, 'database') || str_contains($q, 'cơ sở dữ liệu') ||
                              str_contains($q, 'framework') || str_contains($q, 'kiến trúc') ||
                              str_contains($q, 'backend') || str_contains($q, 'dự án');

            if ($isStrictSecret) {
                return "Chào **{$userName}** nha! Mình là **Trợ lý ảo RentHome**, do đội ngũ kỹ sư của nền tảng phát triển để đồng hành hỗ trợ bạn tìm phòng ưng ý và giải đáp mọi thủ tục thuê trọ. 😊\n\n"
                     . "Chuyện \"bếp núc\" công nghệ hay thông tin cá nhân của các anh kỹ sư thì mình xin phép được giữ bí mật nội bộ một chút nhé ạ! Đổi lại, chuyện tìm phòng ở đâu đẹp, giá thuê hợp lý, hợp đồng ra sao, cách giữ cọc an toàn hay quy định giá điện nước chuẩn thì mình nắm rất rõ trong lòng bàn tay luôn. 🏠✨\n\n"
                     . "Hôm nay {$userName} đang cần mình hỗ trợ tìm phòng ở khu vực nào, hay cần tư vấn thủ tục gì nè?";
            }

            // Giới thiệu chung về RentHome
            return "Chào **{$userName}**! **RentHome** là nền tảng kết nối trực tiếp và minh bạch giữa người thuê và chính chủ nhà trọ, giúp loại bỏ hoàn toàn các khâu trung gian ăn chênh lệch giá. 🏠✨\n\n"
                 . "**Các tiện ích nổi bật RentHome hỗ trợ bạn:**\n"
                 . "• **Dành cho Người tìm phòng (Tài khoản cá nhân):** Tìm kiếm thông minh theo khu vực, mức giá, diện tích; kết nối trực tiếp chính chủ xem phòng với **0 VNĐ phí môi giới** *(Lưu ý: Tài khoản cá nhân chỉ dùng để thuê nhà, không được đăng bài)*.\n"
                 . "• **Dành cho Doanh nghiệp & Chủ cơ sở:** Đăng tin cho thuê hoàn toàn **miễn phí 100%**, tiếp cận nhanh chóng hàng ngàn khách thuê có nhu cầu thực tế.\n"
                 . "• **Tư vấn & Hỗ trợ pháp lý:** Hướng dẫn hợp đồng thuê, quy định tiền cọc (Điều 328 BLDS 2015), định mức điện nước và hỗ trợ giải quyết sự cố, phản ánh vi phạm.\n\n"
                 . "👉 {$userName} có thể thử ngay bằng cách nhắn ví dụ: *\"Tìm phòng Cầu Giấy tầm 4 triệu\"* hoặc *\"Quy định về tiền cọc thế nào\"* nhé!";
        }

        // 1. CHỦ ĐỀ: TIỀN ĐẶT CỌC / THUÊ NHÀ CÓ PHẢI CỌC KHÔNG / CỌC BAO NHIÊU / MẤT CỌC
        if (str_contains($q, 'cọc') || str_contains($q, 'coc')) {
            // Trường hợp A: Chủ nhà không trả cọc / Quịt cọc / Lấy lại cọc / Mất cọc
            $isDepositRefundDispute = str_contains($normalized, 'không trả') || str_contains($normalized, 'ko trả') ||
                                     str_contains($normalized, 'k trả') || str_contains($q, 'quịt') ||
                                     str_contains($q, 'bùng') || str_contains($q, 'mất cọc') ||
                                     str_contains($q, 'lấy lại') || str_contains($q, 'đòi lại') ||
                                     str_contains($q, 'chiếm đoạt') || str_contains($q, 'tranh chấp') ||
                                     str_contains($q, 'không chịu trả');

            if ($isDepositRefundDispute) {
                return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                     . "Theo Khoản 2 Điều 328 Bộ luật Dân sự 2015, nếu người thuê thực hiện đúng hợp đồng (báo trước đủ thời hạn, không vi phạm thời gian thuê, bàn giao phòng nguyên vẹn), chủ trọ **bắt buộc phải hoàn trả 100% tiền đặt cọc**. Mọi hành vi tự ý chiếm giữ cọc mà không có căn cứ văn bản là vi phạm hợp đồng và có dấu hiệu chiếm đoạt tài sản trái phép.\n\n"
                     . "⚖️ **2. Chi tiết & Phân tích pháp lý:**\n"
                     . "• **Căn cứ pháp luật:** Nếu bên nhận đặt cọc từ chối thực hiện nghĩa vụ hợp đồng, phải trả lại cho bên đặt cọc tài sản đặt cọc và một khoản tiền tương đương giá trị đặt cọc (trừ khi có thỏa thuận khác).\n"
                     . "• **Đánh giá rủi ro:** Chủ trọ thường viện cớ hao mòn tự nhiên của thiết bị phòng hoặc ép buộc người thuê phải ở tiếp để từ chối hoàn cọc. Người thuê cần có chứng cứ đối chiếu bằng văn bản để bác bỏ các yêu sách vô lý.\n\n"
                     . "📋 **3. Kế hoạch hành động thu hồi tiền cọc:**\n"
                     . "• **Bước 1 (Tập hợp chứng cứ):** Chuẩn bị Hợp đồng thuê/Biên nhận cọc đã ký, tin nhắn thông báo trả phòng đúng thời hạn cam kết, biên lai đóng tiền điện nước và video/ảnh chụp hiện trạng phòng lúc bàn giao.\n"
                     . "• **Bước 2 (Gửi thông báo đối chiếu chính thức):** Gặp trực tiếp hoặc gửi văn bản/tin nhắn yêu cầu chủ trọ đối chiếu từng điều khoản hợp đồng và ấn định hạn chót hoàn cọc (ví dụ: trong vòng 24 - 48 giờ).\n"
                     . "• **Bước 3 (Kích hoạt hòa giải RentHome):** Gửi mã bài đăng hoặc SĐT chủ trọ cho Ban Quản trị RentHome qua Hotline `1900 8888` hoặc mục Khiếu nại. RentHome sẽ lập hồ sơ cảnh cáo và tạm khóa tài khoản chủ trọ vi phạm để thúc đẩy hoàn trả.\n"
                     . "• **Bước 4 (Trình báo cơ quan chức năng):** Nếu chủ trọ cố tình thách thức, trốn tránh, mang toàn bộ hồ sơ (hợp đồng, CCCD, lịch sử chuyển tiền) đến Công an phường/UBND nơi có phòng trọ để cán bộ khu vực can thiệp xử lý theo pháp luật.\n\n"
                     . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                     . "Luôn yêu cầu chủ nhà ký vào \"Biên bản bàn giao trả phòng\" xác nhận tình trạng phòng và tài sản nguyên vẹn trước khi dọn đi để triệt tiêu mọi lý do khấu trừ tiền cọc.\n\n"
                     . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                     . "• *Nếu chủ trọ dọa trừ tiền vì tường bẩn, hao mòn tự nhiên?* -> Theo luật, hao mòn tự nhiên theo thời gian không thuộc trách nhiệm bồi thường của bên thuê (trừ khi cố ý đập phá hoặc làm hư hỏng nặng).\n"
                      . "• *Nếu chủ trọ chặn số, trốn tránh không gặp?* -> Gửi ngay thông tin cho RentHome để khóa số điện thoại trên hệ thống và mang đơn tố giác ra Công an phường nơi có nhà trọ.";
            }

            // Trường hợp B: Thuê nhà có phải cọc không / Mức cọc bao nhiêu / Quy định đặt cọc thông thường
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "**CÓ.** Đặt cọc là biện pháp bảo đảm thực hiện hợp đồng phổ biến và hoàn toàn hợp pháp theo Điều 328 Bộ luật Dân sự 2015. Mức đặt cọc thông thường trên thị trường là **tương đương 1 tháng tiền phòng** (hoặc từ 1 - 2 tháng đối với căn hộ đầy đủ nội thất cao cấp).\n\n"
                 . "⚖️ **2. Chi tiết & Mục đích tiền đặt cọc:**\n"
                 . "• **Mục đích:** Đảm bảo người thuê giữ đúng cam kết thời gian ở, thanh toán đầy đủ hóa đơn điện nước/dịch vụ hàng tháng và giữ gìn tài sản bàn giao trong phòng.\n"
                 . "• **Thời điểm hoàn trả:** Khi kết thúc hợp đồng thuê, bàn giao phòng nguyên vẹn và thanh toán xong mọi chi phí, chủ trọ có trách nhiệm hoàn trả 100% tiền cọc cho bạn.\n\n"
                 . "📋 **3. Kế hoạch hành động khi đặt cọc an toàn:**\n"
                 . "• **Bước 1:** Chỉ đặt cọc sau khi đã trực tiếp đến xem phòng thực tế, gặp chính chủ và kiểm tra CCCD chủ nhà.\n"
                 . "• **Bước 2:** Bắt buộc phải có **Giấy biên nhận cọc** hoặc Hợp đồng đặt cọc bằng văn bản, ghi rõ: số tiền, thời hạn giữ phòng, ngày vào ở chính thức và điều kiện hoàn cọc.\n"
                 . "• **Bước 3:** Chuyển khoản ngân hàng có ghi rõ nội dung: *\"[Họ tên] đặt cọc giữ phòng số... tại địa chỉ...\"* để lưu chứng từ điện tử.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Tuyệt đối không chuyển tiền cọc giữ chỗ khi chỉ mới xem ảnh qua mạng mà chưa gặp người thật việc thật hoặc người nhận từ chối cung cấp thông tin CCCD.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Nếu sau khi cọc mà chủ trọ đổi ý không cho thuê nữa?* -> Theo luật, chủ trọ phải hoàn trả lại toàn bộ tiền cọc và đền bù thêm một khoản tương đương giá trị tiền cọc (gấp đôi cọc).\n"
                 . "• *Nếu tôi đổi ý không muốn thuê nữa thì có lấy lại được cọc không?* -> Tiền cọc sẽ thuộc về chủ nhà theo quy định pháp luật, trừ khi bạn và chủ trọ có thỏa thuận riêng bằng văn bản.";
        }

        // 2. CHỦ ĐỀ: PHÍ MÔI GIỚI ĐỐI VỚI NGƯỜI THUÊ / PHÍ XEM PHÒNG
        if (str_contains($q, 'môi giới') || str_contains($q, 'hoa hồng') ||
            ((str_contains($q, 'thu phí') || str_contains($q, 'mất phí') || str_contains($q, 'mất tiền') || str_contains($q, 'tốn phí') || str_contains($q, 'tốn tiền')) &&
             (str_contains($q, 'thuê') || str_contains($q, 'xem phòng') || str_contains($q, 'dẫn xem') || str_contains($q, 'người thuê') || str_contains($q, 'khách')))) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "**HOÀN TOÀN KHÔNG (0 VNĐ).** RentHome là nền tảng kết nối trực tiếp 100% giữa người thuê và chính chủ nhà trọ; người thuê không phải trả bất kỳ khoản phí môi giới hay phí dẫn xem phòng nào.\n\n"
                 . "🔍 **2. Chi tiết & Phân tích cơ chế:**\n"
                 . "• Người thuê được tra cứu, xem hình ảnh thực tế, tiện ích và lấy SĐT/Zalo chủ trọ để liên hệ xem phòng hoàn toàn miễn phí.\n"
                 . "• Hệ thống loại bỏ hoàn toàn các khâu trung gian ăn chênh lệch giá, bảo đảm người thuê tiếp cận đúng giá gốc từ chính chủ.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Bước 1:** Lựa chọn bài đăng phòng trọ ưng ý trên website RentHome.\n"
                 . "• **Bước 2:** Bấm nút **\"Gọi điện thoại\"** hoặc **\"Nhắn tin Zalo\"** tại bài đăng để liên hệ trực tiếp chủ trọ hẹn giờ xem phòng.\n"
                 . "• **Bước 3:** Đến xem phòng và làm việc trực tiếp với chủ trọ.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Cảnh giác cao độ với bất kỳ đối tượng nào xưng là nhân viên đòi thu \"tiền dẫn xem phòng\". RentHome nghiêm cấm hành vi này. Nếu gặp đối tượng đòi tiền, hãy bấm nút \"Báo cáo bài đăng\" hoặc gọi ngay Hotline `1900 8888` để BQT xử lý.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Làm sao để biết chắc chắn đó là chính chủ nhà?* -> Yêu cầu xuất trình CCCD và giấy chứng nhận quyền sở hữu hoặc hợp đồng ủy quyền quản lý trước khi đặt bút ký hợp đồng.\n"
                 . "• *Nếu đến nơi chủ nhà báo giá cao hơn bài đăng?* -> Bấm nút \"Báo cáo bài đăng\" ngay tại bài viết để BQT can thiệp đối chất và cảnh cáo chủ trọ gian lận.";
        }

        // 3. CHỦ ĐỀ: THỜI GIAN DUYỆT BÀI ĐĂNG
        if ((str_contains($q, 'duyệt') || str_contains($q, 'kiểm duyệt')) &&
            (str_contains($q, 'bao lâu') || str_contains($q, 'thời gian') || str_contains($q, 'mất bao lâu') || str_contains($q, 'khi nào') || str_contains($q, 'lâu không') || str_contains($q, 'sao chưa') || str_contains($q, 'chờ duyệt'))) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Thời gian kiểm duyệt bài đăng tiêu chuẩn từ **15 đến 30 phút** trong khung giờ làm việc (08:00 - 22:00 hàng ngày, bao gồm cả Thứ 7 và Chủ Nhật). Các bài gửi sau 22:00 sẽ được ưu tiên duyệt sớm trước 08:30 sáng hôm sau.\n\n"
                 . "🔍 **2. Chi tiết & Tiêu chuẩn kiểm duyệt:**\n"
                 . "• Nhằm bảo đảm an toàn cho người thuê trọ, Ban Kiểm duyệt thẩm định 3 tiêu chuẩn nghiêm ngặt:\n"
                 . "  1. **Hình ảnh thực tế:** Hình ảnh phòng sáng rõ, đúng góc chụp thực tế, không dùng ảnh mạng giả mạo.\n"
                 . "  2. **Giá thuê & Địa chỉ:** Mức giá hợp lý so với thị trường và địa chỉ có thật, không chứa link quảng cáo độc hại.\n"
                 . "  3. **Thông tin liên hệ:** Số điện thoại chính chủ đang hoạt động để bảo vệ người thuê trọ.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• Khi bạn gửi bài đăng qua AI Chat hoặc form website, bài viết tự động vào hàng đợi kiểm duyệt (`pending`) và chuyển ngay đến Kiểm duyệt viên ca trực để phê duyệt nhanh nhất.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Đính kèm từ 2 - 4 tấm ảnh thực tế góc rộng, ghi rõ địa chỉ ngõ/phố và công khai giá điện nước trong phần mô tả để bài đăng được kiểm duyệt tự động thông qua nhanh nhất.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Tại sao bài đăng của tôi bị từ chối duyệt?* -> Thường do ảnh mờ, dùng ảnh mạng tải từ Google, thiếu thông tin giá điện nước hoặc để số điện thoại ảo.\n"
                 . "• *Bài đăng sau khi duyệt sẽ xuất hiện ở đâu?* -> Hiển thị ngay tại Trang chủ, trang Tìm kiếm và được Trợ lý AI ưu tiên gợi ý cho khách tìm phòng.";
        }

        // 3B. CHỦ ĐỀ: TÀI KHOẢN CÁ NHÂN CÓ ĐƯỢC ĐĂNG BÀI / ĐĂNG TIN KHÔNG? AI ĐƯỢC ĐĂNG BÀI?
        $isPersonalAccountPostingFaq = (
            (str_contains($q, 'cá nhân') || str_contains($q, 'ca nhan')) &&
            (str_contains($q, 'đăng bài') || str_contains($q, 'dang bai') || str_contains($q, 'đăng tin') || str_contains($q, 'dang tin') || str_contains($q, 'cho thuê') || str_contains($q, 'lên bài') || str_contains($q, 'tạo bài') || str_contains($q, 'có quyền') || str_contains($q, 'được làm gì') || str_contains($q, 'thuê nhà'))
        ) || (
            (str_contains($q, 'ai được') || str_contains($q, 'ai có quyền') || str_contains($q, 'ai mới được') || str_contains($q, 'ai có thể') || str_contains($q, 'đối tượng nào') || str_contains($q, 'tài khoản nào')) &&
            (str_contains($q, 'đăng bài') || str_contains($q, 'dang bai') || str_contains($q, 'đăng tin') || str_contains($q, 'dang tin'))
        ) || (
            str_contains($q, 'tài khoản cá nhân') && (str_contains($q, 'đăng') || str_contains($q, 'thuê') || str_contains($q, 'quyền'))
        );

        if ($isPersonalAccountPostingFaq) {
            return "🎯 **1. Kết luận chính xác:**\n"
                 . "**KHÔNG ĐƯỢC PHÉP.** Trên nền tảng RentHome, **Tài khoản cá nhân chỉ được tìm và thuê nhà, KHÔNG ĐƯỢC đăng bài hay đăng tin cho thuê**.\n\n"
                 . "⚖️ **2. Quy định phân quyền tài khoản RentHome:**\n"
                 . "• 👤 **Tài khoản Cá nhân (Người tìm thuê):** Được tìm kiếm phòng trọ, căn hộ, liên hệ chính chủ, đặt lịch xem phòng, ký hợp đồng và thanh toán tiền thuê. Quyền đăng tin cho thuê bị khóa đối với tài khoản cá nhân nhằm loại bỏ triệt để tin ảo, môi giới tự do hoặc lừa đảo.\n"
                 . "• 🏢 **Tài khoản Doanh nghiệp (Chủ nhà / Chủ cơ sở):** Được tạo cơ sở, tòa nhà, quản lý phòng và đăng tin cho thuê.\n"
                 . "• 🔑 **Quản lý tòa nhà (Nhân sự phân quyền):** Được Doanh nghiệp phân công quản lý các cơ sở cụ thể để đăng bài và vận hành phòng.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• Nếu bạn là Chủ cơ sở / Chủ nhà muốn đăng tin: Vui lòng đăng ký tài khoản **Doanh nghiệp**, hoặc liên hệ Ban Quản trị RentHome qua Hotline `1900 8888` để được hỗ trợ nâng cấp tài khoản.\n"
                 . "• Nếu {$userName} đang cần tìm thuê phòng: Bạn chỉ cần nhắn khu vực (quận/huyện, tỉnh thành) và mức giá mong muốn, mình sẽ lọc danh sách các căn tốt nhất gửi bạn ngay nhé!\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Tại sao tài khoản cá nhân không được đăng bài?* -> Nhằm đảm bảo 100% bài đăng trên RentHome đều là cơ sở có thật, được xác thực bởi Doanh nghiệp/Chủ tòa nhà uy tín và qua Ban Kiểm duyệt thẩm định.\n"
                 . "• *Muốn chuyển đổi từ tài khoản cá nhân sang tài khoản cho thuê?* -> Liên hệ Hotline `1900 8888` hoặc đăng xuất và đăng ký tài khoản mới với vai trò Doanh nghiệp.";
        }

        // 4. CHỦ ĐỀ: PHÍ ĐĂNG TIN TRÊN RENTHOME
        if ((str_contains($q, 'đăng tin') || str_contains($q, 'đăng bài') || str_contains($q, 'tạo bài')) &&
            (str_contains($q, 'mất phí') || str_contains($q, 'chi phí') || str_contains($q, 'bao nhiêu tiền') || str_contains($q, 'có tốn phí') || str_contains($q, 'tính phí') || str_contains($q, 'miễn phí') || str_contains($q, 'mất tiền') || str_contains($q, 'tốn tiền'))) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "**HOÀN TOÀN MIỄN PHÍ 100% (0 VNĐ)** dành cho tài khoản **Doanh nghiệp & Quản lý tòa nhà**.\n\n"
                 . "*(Lưu ý quan trọng: Tài khoản cá nhân chỉ được tìm và thuê nhà, KHÔNG ĐƯỢC đăng bài hay đăng tin cho thuê)*.\n\n"
                 . "🔍 **2. Chi tiết & Phân tích chính sách:**\n"
                 . "• Miễn phí toàn bộ việc đăng tin trên website dành cho Chủ nhà / Doanh nghiệp.\n"
                 . "• Không phát sinh bất kỳ khoản phí khởi tạo, phí duy trì bài đăng hay hoa hồng chiết khấu nào khi tìm được khách thuê.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Dành cho Doanh nghiệp & Quản lý tòa nhà:** Bạn có thể đăng bài trực tiếp tại mục **[Đăng tin](/dang-tin)** trên website.\n"
                 . "• **Dành cho Khách thuê:** Sử dụng khung chat này để tìm kiếm phòng, lọc giá và hướng dẫn thủ tục thuê nhà với 0 VNĐ phí môi giới.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Tài khoản cá nhân có đăng bài được không?* -> Không, tài khoản cá nhân chỉ được dùng để thuê nhà.\n"
                 . "• *Làm sao để đăng bài nếu là chủ nhà?* -> Đăng ký tài khoản Doanh nghiệp hoặc liên hệ Hotline `1900 8888`.";
        }

        // 4B. CHỦ ĐỀ: HƯỚNG DẪN CÁCH ĐĂNG BÀI / QUY TRÌNH ĐĂNG BÀI TRÊN RENTHOME
        $isGuideToPost = (str_contains($q, 'đăng bài') || str_contains($q, 'đăng tin') || str_contains($q, 'lên bài') || str_contains($q, 'tạo bài') || str_contains($q, 'tạo tin')) &&
                         (str_contains($q, 'hướng dẫn') || str_contains($q, 'cách') || str_contains($q, 'làm sao') || str_contains($q, 'thế nào') || str_contains($q, 'như nào') || str_contains($q, 'như thế nào') || str_contains($q, 'quy trình') || str_contains($q, 'thủ tục') || str_contains($q, 'ở đâu') || str_contains($q, 'bước nào'));

        if ($isGuideToPost) {
            if ($currentUser && $currentUser->isIndividual()) {
                return "Chào **{$userName}**! Tài khoản của bạn hiện tại là **Tài khoản Cá nhân (Người tìm thuê)**.\n\n"
                     . "🚫 Theo quy định của RentHome, **Tài khoản cá nhân chỉ được tìm và thuê nhà, KHÔNG ĐƯỢC đăng bài hay đăng tin cho thuê**.\n\n"
                     . "📌 **Lưu ý phân quyền:**\n"
                     . "• Chức năng đăng bài dành riêng cho **Tài khoản Doanh nghiệp (Chủ nhà / Chủ cơ sở)** và **Quản lý tòa nhà**.\n"
                     . "• Nếu bạn là Chủ nhà muốn đăng tin: Vui lòng liên hệ BQT RentHome qua Hotline `1900 8888` để đăng ký tài khoản Doanh nghiệp.\n"
                     . "• Nếu {$userName} đang cần **tìm phòng thuê**, bạn cứ nhắn khu vực và mức giá mong muốn để mình hỗ trợ tìm phòng ngay nhé!";
            }

            return "Chào **{$userName}**! Để đăng bài cho thuê phòng/nhà trên RentHome hoàn toàn miễn phí 100% *(chỉ áp dụng cho tài khoản Doanh nghiệp & Quản lý tòa nhà; tài khoản cá nhân chỉ được thuê nhà)*, bạn thực hiện trực tiếp trên website theo các bước sau nhé: 📝✨\n\n"
                 . "• **Bước 1:** Đăng nhập tài khoản Doanh nghiệp hoặc Quản lý tòa nhà.\n"
                 . "• **Bước 2:** Bấm vào mục **[Đăng tin cho thuê](/dang-tin)** trên thanh menu website.\n"
                 . "• **Bước 3:** Điền đầy đủ địa chỉ, loại hình, giá thuê, diện tích, hình ảnh thực tế và bấm Gửi duyệt.\n\n"
                 . "💡 *Lưu ý*: Chatbot RentHome hiện chuyên phục vụ các chức năng cốt lõi: **Lọc tòa nhà / loại hình BĐS**, **Lọc giá**, **Lọc diện tích (m²)**, **Tìm bài đăng** và **Hướng dẫn thủ tục thuê nhà**; chức năng đăng tin được thực hiện trực tiếp qua giao diện website để bài được kiểm duyệt nhanh nhất!";
        }

        // 5. CHỦ ĐỀ: CHỦ TRỌ CÓ ĐƯỢC TỰ Ý TĂNG GIÁ PHÒNG KHÔNG
        if (str_contains($q, 'tăng giá') || str_contains($q, 'tăng tiền') || ((str_contains($q, 'giá phòng') || str_contains($q, 'tiền phòng') || str_contains($q, 'tiền nhà')) && (str_contains($q, 'tăng') || str_contains($q, 'lên')))) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "**KHÔNG ĐƯỢC PHÉP.** Trong thời hạn hợp đồng thuê nhà đang còn hiệu lực, chủ trọ không có quyền đơn phương tăng giá thuê nếu hợp đồng không có điều khoản cho phép điều chỉnh giá định kỳ.\n\n"
                 . "⚖️ **2. Chi tiết & Căn cứ pháp lý:**\n"
                 . "• Theo quy định của Luật Nhà ở và Bộ luật Dân sự, giá thuê là cam kết cố định trong suốt thời hạn hợp đồng. Mọi hành vi đơn phương ép tăng giá, đe dọa đuổi người thuê khi không đóng thêm tiền là hành vi vi phạm nghĩa vụ hợp đồng.\n"
                 . "• Chủ trọ chỉ có quyền tăng giá trong 2 trường hợp: 1) Hợp đồng cũ hết hạn và hai bên tiến hành ký hợp đồng mới; 2) Trong hợp đồng ban đầu có ghi rõ điều khoản thỏa thuận trước về lộ trình điều chỉnh giá.\n\n"
                 . "📋 **3. Kế hoạch hành động khi bị ép tăng giá:**\n"
                 . "• **Bước 1 (Đối chiếu hợp đồng):** Mang hợp đồng thuê phòng ra chỉ rõ điều khoản cam kết giá thuê cố định trong thời hạn hợp đồng.\n"
                 . "• **Bước 2 (Phản hồi văn bản):** Từ chối thanh toán khoản tiền tăng thêm trái thỏa thuận bằng tin nhắn hoặc văn bản lưu lại làm bằng chứng.\n"
                 . "• **Bước 3 (Yêu cầu can thiệp):** Nếu chủ trọ cố tình gây khó dễ, gửi khiếu nại tới BQT RentHome qua Hotline `1900 8888` hoặc báo Công an/UBND phường nơi có nhà trọ để can thiệp bảo vệ quyền lợi hợp pháp.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Khi ký hợp đồng ban đầu, luôn gạch bỏ hoặc làm rõ các điều khoản mở mập mờ kiểu *\"giá thuê có thể thay đổi tùy biến động thị trường\"*.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Nếu chủ trọ dọa cắt điện nước hoặc đuổi ra khỏi nhà?* -> Hành vi này vi phạm pháp luật; hãy báo ngay Công an phường sở tại vì chỉ có cơ quan chức năng mới có thẩm quyền giải quyết theo trình tự luật định.\n"
                 . "• *Khi nào chủ trọ mới được điều chỉnh giá hợp pháp?* -> Chỉ khi kết thúc hợp đồng hiện tại hoặc có phụ lục hợp đồng được cả 2 bên cùng tự nguyện ký kết.";
        }

        // 6. CHỦ ĐỀ: QUY ĐỊNH VỀ GIÁ ĐIỆN NƯỚC PHÒNG TRỌ
        $isElectricityWater = str_contains($q, 'điện nước') || str_contains($q, 'giá điện') || str_contains($q, 'giá nước') ||
                              str_contains($q, 'tiền điện') || str_contains($q, 'tiền nước') || str_contains($q, 'khối nước') ||
                              (str_contains($q, 'số điện') && !str_contains($q, 'số điện thoại') && !str_contains($q, 'sđt') && !str_contains($q, 'sdt'));

        if ($isElectricityWater) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Giá điện phòng trọ được Nhà nước quản lý theo Thông tư 25/2018/TT-BCT và Thông tư 09/2023/TT-BCT của Bộ Công Thương. Hành vi chủ trọ tự ý thu tiền điện của người thuê cao hơn mức giá quy định sẽ bị **phạt tiền từ 7.000.000đ đến 10.000.000đ** (Nghị định 17/2022/NĐ-CP).\n\n"
                 . "⚖️ **2. Chi tiết & Định mức chi phí:**\n"
                 . "• **Điện sinh hoạt:** Người thuê có hợp đồng từ 12 tháng trở lên và có đăng ký tạm trú được cấp định mức điện sinh hoạt theo bậc thang Nhà nước. Nếu không đủ điều kiện, chủ trọ phải tính theo giá bán lẻ điện sinh hoạt bậc 3 (khoảng 2.300 - 2.400đ/kWh chưa thuế).\n"
                 . "• **Thực tế thị trường:** Để bù chi phí điện chiếu sáng hành lang, máy bơm nước, camera, wifi..., các bên thường thỏa thuận mức giá điện khoán từ 3.000đ - 4.000đ/kWh. Mức giá này phải được ghi công khai và thống nhất trong hợp đồng trước khi vào ở.\n"
                 . "• **Nước sinh hoạt:** Thường tính theo đồng hồ riêng (20.000đ - 30.000đ/khối) hoặc khoán theo đầu người (70.000đ - 100.000đ/người/tháng).\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Bước 1:** Kiểm tra công tơ điện và đồng hồ nước riêng của phòng trước khi dọn vào ở.\n"
                 . "• **Bước 2:** Yêu cầu chủ trọ ghi rõ ràng đơn giá điện và nước trong Hợp đồng thuê phòng.\n"
                 . "• **Bước 3:** Chụp ảnh chỉ số công tơ ban đầu và định kỳ chụp lại chỉ số vào ngày chốt tiền hàng tháng.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Chủ động đề nghị chủ trọ hỗ trợ đăng ký tạm trú để được hưởng định mức giá điện sinh hoạt của Nhà nước, giúp tiết kiệm từ 30% - 50% tiền điện hàng tháng.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Làm sao kiểm tra công tơ điện có bị câu trộm?* -> Tắt toàn bộ thiết bị và aptomat trong phòng, nếu kim/số công tơ vẫn nhảy thì hệ thống bị rò rỉ hoặc câu chung dây.\n"
                 . "• *Nếu bị thu giá điện 4.500 - 5.000đ/kWh?* -> Bạn có quyền chụp hóa đơn và gửi đơn phản ánh lên Sở Công Thương hoặc UBND phường để can thiệp xử phạt.";
        }

        // 7. CHỦ ĐỀ: HỢP ĐỒNG THUÊ TRỌ / LÀM HỢP ĐỒNG NHƯ THẾ NÀO / LƯU Ý KHI KÝ KẾT
        if (str_contains($q, 'hợp đồng') || str_contains($q, 'hop dong')) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Hợp đồng thuê nhà/phòng trọ bắt buộc phải lập thành **văn bản giấy (in thành tối thiểu 2 bản, mỗi bên giữ 1 bản có chữ ký của cả 2 bên)**. Theo Luật Nhà ở, hợp đồng thuê trọ cá nhân không bắt buộc phải công chứng nhưng có giá trị pháp lý ràng buộc đầy đủ giữa các bên.\n\n"
                 . "⚖️ **2. Chi tiết & 6 điều khoản cốt lõi bắt buộc phải có:**\n"
                 . "1. **Thông tin nhân thân:** Họ tên, số CCCD, địa chỉ thường trú, SĐT của bên thuê và bên cho thuê.\n"
                 . "2. **Đối tượng thuê & Tài sản:** Địa chỉ phòng, diện tích, tầng, kèm danh mục trang thiết bị bàn giao (điều hòa, nóng lạnh, giường tủ, khóa...).\n"
                 . "3. **Giá thuê & Kỳ thanh toán:** Số tiền cụ thể bằng số và chữ (VNĐ/tháng), ngày đóng tiền hàng tháng, cam kết không tự ý tăng giá trong thời hạn hợp đồng.\n"
                 . "4. **Tiền đặt cọc & Điều kiện hoàn trả:** Số tiền cọc (thường là 1 tháng) và điều kiện hoàn cọc (thời hạn báo trước, nguyên vẹn tài sản).\n"
                 . "5. **Chi phí dịch vụ phát sinh:** Đơn giá điện (VNĐ/kWh), nước (VNĐ/khối hoặc người), wifi, phí vệ sinh, rác, gửi xe.\n"
                 . "6. **Thời hạn hợp đồng & Chấm dứt:** Thời gian thuê (6 tháng/1 năm), quy định báo trước bao nhiêu ngày khi muốn chuyển đi.\n\n"
                 . "📋 **3. Kế hoạch hành động lập hợp đồng:**\n"
                 . "• **Bước 1 (Kiểm tra chủ thể):** Yêu cầu chủ nhà xuất trình CCCD và giấy tờ chứng minh quyền sở hữu hoặc quyền đại diện cho thuê trước khi đặt bút ký.\n"
                 . "• **Bước 2 (Rà soát điều khoản):** Đọc kỹ từng điều khoản, gạch bỏ hoặc yêu cầu sửa các điều khoản mập mờ về phạt cọc hoặc điều chỉnh giá giữa chừng.\n"
                 . "• **Bước 3 (Ký kết & Lưu trữ):** Hai bên ký nháy vào từng trang và ký đầy đủ ở trang cuối. Mỗi bên giữ 1 bản gốc có chữ ký sống.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Tuyệt đối không chấp nhận thỏa thuận miệng. Hãy chụp ảnh hoặc scan hợp đồng lưu vào điện thoại/Google Drive để luôn có sẵn chứng từ đối chiếu khi xảy ra tranh chấp.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Muốn chuyển đi trước thời hạn hợp đồng thì có lấy lại được cọc không?* -> Thỏa thuận điều khoản báo trước 30 ngày để được nhận lại 100% tiền cọc hoặc chỉ chịu phạt mức tối thiểu.\n"
                 . "• *Hợp đồng viết tay có giá trị trước pháp luật không?* -> Có, chỉ cần có đầy đủ chữ ký của hai bên và thông tin CCCD thật thì hợp đồng viết tay vẫn có giá trị khởi kiện.";
        }

        // 8. CHỦ ĐỀ: GIẤY TỜ & THỦ TỤC KHI ĐI THUÊ PHÒNG
        if (str_contains($q, 'giấy tờ') || str_contains($q, 'thủ tục thuê') || str_contains($q, 'cần chuẩn bị gì') || str_contains($q, 'hồ sơ thuê')) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Người thuê chỉ cần chuẩn bị: Căn cước công dân (CCCD gắn chip) hoặc VNeID cấp độ 2, tiền đặt cọc (thường là 1 tháng) và tiền phòng tháng đầu tiên.\n\n"
                 . "🔍 **2. Chi tiết & Thủ tục cần nắm:**\n"
                 . "• **Giấy tờ tùy thân:** Bản gốc CCCD để đối chiếu khi ký hợp đồng và 1 bản sao/ảnh chụp gửi chủ trọ để thực hiện đăng ký tạm trú theo Luật Cư trú 2020.\n"
                 . "• **Chi phí ban đầu:** Chuẩn bị sẵn tiền cọc giữ phòng và tiền phòng tháng đầu tiên.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Bước 1:** Đối chiếu CCCD và ký Hợp đồng thuê phòng bằng văn bản (2 bản).\n"
                 . "• **Bước 2:** Thanh toán tiền cọc, nhận Giấy biên nhận cọc có chữ ký chủ nhà.\n"
                 . "• **Bước 3:** Chốt số công tơ điện nước ban đầu và nhận bàn giao chìa khóa/thẻ từ.\n"
                 . "• **Bước 4:** Cung cấp thông tin để chủ trọ thực hiện đăng ký tạm trú trong vòng 30 ngày.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Lưu ảnh chụp hợp đồng và biên nhận cọc vào điện thoại/Google Drive để tra cứu ngay khi cần thiết.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Chủ trọ có quyền giữ bản gốc CCCD của người thuê không?* -> Tuyệt đối không, chủ trọ chỉ có quyền xem đối chiếu hoặc giữ bản photo/ảnh chụp để khai báo tạm trú.\n"
                 . "• *Người nước ngoài thuê phòng cần thêm giấy tờ gì?* -> Cần Hộ chiếu còn hạn, Thị thực (Visa) hoặc Thẻ tạm trú hợp pháp tại Việt Nam.";
        }

        // 9. CHỦ ĐỀ: CÁCH LIÊN HỆ CHỦ NHÀ / LẤY SỐ ĐIỆN THOẠI XEM PHÒNG
        if (str_contains($q, 'số điện thoại') || str_contains($q, 'sđt') || str_contains($q, 'sdt') || str_contains($q, 'liên hệ chủ') || str_contains($q, 'gọi chủ') || str_contains($q, 'lấy sđt') || str_contains($q, 'hẹn xem phòng')) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Để liên hệ chính chủ trên RentHome, bạn chỉ cần bấm nút **\"Gọi điện thoại\"** hoặc **\"Nhắn tin Zalo\"** ngay tại bài đăng phòng. Số điện thoại chính chủ hiển thị ngay lập tức, hoàn toàn miễn phí.\n\n"
                 . "🔍 **2. Chi tiết:**\n"
                 . "• 100% kết nối trực tiếp giữa người thuê và chính chủ nhà, không qua trung gian môi giới, xem phòng không mất phí.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Bước 1:** Lựa chọn bài đăng phòng trọ ưng ý trên website.\n"
                 . "• **Bước 2:** Bấm nút Gọi điện hoặc Nhắn Zalo để liên hệ trực tiếp chủ trọ hẹn giờ xem phòng.\n"
                 . "• **Bước 3:** Đến xem phòng trực tiếp vào ban ngày để kiểm tra ánh sáng, an ninh và môi trường xung quanh.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Tuyệt đối không chuyển tiền cọc giữ chỗ online khi chưa đến xem phòng thực tế và chưa gặp chính chủ.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Nếu gọi điện mà số thuê bao hoặc không liên lạc được?* -> Bấm nút Báo cáo để Kiểm duyệt viên liên hệ cập nhật số mới hoặc tạm ẩn bài đăng.\n"
                 . "• *Nên đến xem phòng vào khung giờ nào tốt nhất?* -> Nên đi xem vào buổi trưa hoặc chiều để kiểm tra độ nóng bức, tiếng ồn và môi trường khu dân cư.";
        }

        // 10. CHỦ ĐỀ: THỦ TỤC ĐĂNG KÝ TẠM TRÚ
        if (str_contains($q, 'tạm trú') || str_contains($q, 'đăng ký tạm trú')) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Theo Luật Cư trú 2020, trách nhiệm đăng ký tạm trú thuộc về **Chủ nhà trọ phối hợp cùng người thuê** trong thời hạn 30 ngày kể từ ngày dọn vào ở.\n\n"
                 . "🔍 **2. Chi tiết hình thức:**\n"
                 . "• Đăng ký trực tuyến qua Cổng Dịch vụ công Quản lý Cư trú (dichvucong.dancuquocgia.gov.vn) hoặc qua ứng dụng VNeID.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Bước 1:** Người thuê cung cấp ảnh chụp 2 mặt CCCD và thông tin cư trú cho chủ trọ.\n"
                 . "• **Bước 2:** Chủ trọ thực hiện kê khai tạm trú trực tuyến hoặc tại Công an xã/phường sở tại.\n"
                 . "• **Bước 3:** Kiểm tra thông tin cư trú được cập nhật trên ứng dụng VNeID.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Đăng ký tạm trú đầy đủ để bảo đảm quyền lợi cư trú hợp pháp, an ninh khu vực và làm căn cứ hưởng giá điện sinh hoạt bậc thang.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Nếu chủ trọ không chịu đăng ký tạm trú cho khách thuê?* -> Chủ trọ sẽ bị xử phạt hành chính từ 500.000đ - 1.000.000đ; bạn có thể tự mình kê khai tạm trú trực tuyến trên cổng Dịch vụ công.\n"
                 . "• *Đăng ký tạm trú mất bao nhiêu tiền phí?* -> Lệ phí Nhà nước chỉ khoảng 15.000đ khi nộp trực tiếp hoặc 7.000đ khi nộp trực tuyến qua cổng Dịch vụ công.";
        }

        // 11. CHỦ ĐỀ: QUẢN LÝ TÒA NHÀ & XUẤT HÓA ĐƠN TRÊN RENTHOME
        if (str_contains($q, 'quản lý tòa nhà') || str_contains($q, 'quản lý phòng') || str_contains($q, 'xuất hóa đơn') || str_contains($q, 'chốt chỉ số') || str_contains($q, 'vận hành dãy trọ')) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "RentHome cung cấp trọn bộ công cụ Quản lý vận hành toàn diện cho Chủ trọ/Doanh nghiệp: theo dõi phòng trống/đang thuê theo thời gian thực, quản lý hợp đồng điện tử, chốt số điện nước tự động và xuất hóa đơn gửi khách chỉ với 1 cú nhấp chuột.\n\n"
                 . "🔍 **2. Chi tiết tính năng:**\n"
                 . "• Quản lý công nợ, doanh thu, kỳ hạn hợp đồng, tự động nhân đơn giá điện nước tránh sai sót thủ công.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• Bấm vào menu tài khoản chọn **\"Quản lý vận hành\"** để bắt đầu thiết lập dãy trọ/tòa nhà.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Sử dụng tính năng chốt chỉ số điện nước tự động hàng tháng để tăng tính minh bạch và chuyên nghiệp trong mắt khách thuê.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Khách thuê có nhận được hóa đơn điện tử không?* -> Có, hóa đơn chi tiết gồm tiền phòng, số điện, số nước và các dịch vụ khác được xuất tự động gửi cho khách.\n"
                 . "• *Hệ thống có nhắc hạn hợp đồng sắp hết không?* -> Có, hệ thống tự động đánh dấu các phòng sắp hết hạn để chủ trọ chủ động gia hạn hoặc tìm khách mới.";
        }

        // 12. CHỦ ĐỀ: BÁO CÁO TIN GIẢ, LỪA ĐẢO HOẶC KHIẾU NẠI CHỦ TRỌ (FAQ HƯỚNG DẪN)
        $isFaqReport = ((str_contains($q, 'tin giả') || str_contains($q, 'báo cáo') || str_contains($q, 'tố cáo') || str_contains($q, 'lừa đảo')) &&
                        (str_contains($q, 'thế nào') || str_contains($q, 'ở đâu') || str_contains($q, 'làm sao') || str_contains($q, 'cách') || str_contains($q, 'quy trình') || str_contains($q, 'hướng dẫn') || str_contains($q, 'như nào'))) &&
                       !str_contains($q, 'tôi muốn') && !str_contains($q, 'tôi cần') && !str_contains($q, 'tôi bị');

        if ($isFaqReport) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "Người dùng có thể báo cáo tin vi phạm trực tiếp bằng nút **\"Báo cáo bài đăng\"** tại bài viết hoặc gửi tin nhắn khiếu nại qua Trợ lý AI. Ban Kiểm duyệt RentHome sẽ tiếp nhận và xử lý trong vòng 15 - 30 phút.\n\n"
                 . "🔍 **2. Chi tiết quy trình:**\n"
                 . "• Hệ thống kích hoạt cảnh báo bảo mật tới Ban Quản trị; tạm khóa bài đăng vi phạm để đối chất và xử lý nghiêm.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• Bấm Báo cáo trên bài đăng hoặc nhắn nội dung sự cố tại khung chat này; trường hợp khẩn cấp liên hệ Hotline `1900 8888`.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Lưu lại tin nhắn trao đổi, biên lai chuyển tiền làm chứng cứ xác minh để BQT can thiệp bảo vệ quyền lợi tối đa.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Làm sao nhận biết bẫy lừa cọc phòng trọ phổ biến?* -> Chủ phòng giục chuyển cọc gấp vì \"đang có nhiều người muốn lấy\", không cho xem phòng trực tiếp hoặc giá rẻ bất thường so với khu vực.\n"
                 . "• *RentHome xử lý chủ trọ lừa đảo thế nào?* -> Khóa vĩnh viễn số điện thoại, tài khoản, đưa vào danh sách đen và chuyển hồ sơ sang cơ quan Công an nếu có dấu hiệu chiếm đoạt tài sản.";
        }

        return null;
    }

    /**
     * Tìm kiếm phòng trong Database: Khớp chính xác hoặc tìm các căn phù hợp với túi tiền
     * CHỈ KÍCH HOẠT KHI NGƯỜI DÙNG THỰC SỰ CÓ Ý ĐỊNH TÌM KIẾM PHÒNG (KHÔNG PHẢI HỎI ĐÁP / FAQ)
     */
    protected function searchPostsInDatabase(string $query): array
    {
        $qLower = mb_strtolower(trim($query), 'UTF-8');
        $cards = [];
        $meta = [
            'is_search' => false,
            'is_exact_match' => false,
            'location_requested' => null,
            'property_type_requested' => null,
            'target_price' => null,
            'price_label' => '',
            'price_operator' => null,
            'price_min' => null,
            'price_max' => null,
            'has_alternatives' => false,
            'is_nationwide' => false,
            'needs_clarification' => false,
        ];

        // 1. NHẬN DIỆN TÌM KIẾM TOÀN QUỐC (KHẮP CẢ NƯỚC / Ở ĐÂU CŨNG ĐƯỢC)
        $isNationwide = (bool) preg_match('/\b(toàn quốc|toan quoc|cả nước|ca nuoc|khắp cả nước|khap ca nuoc|khắp nơi|ở đâu cũng được|o dau cung duoc|bất kỳ đâu|mọi tỉnh thành)\b/ui', $qLower);
        if ($isNationwide) {
            $meta['location_requested'] = 'Toàn quốc';
            $meta['is_nationwide'] = true;
        }

        // 2. NHẬN DIỆN ĐỊA ĐIỂM (TỈNH / THÀNH PHỐ / QUẬN / HUYỆN) - ĐA ĐỊA ĐIỂM 63 TỈNH THÀNH
        $matchedProvinces = [];
        $matchedDistricts = [];

        $provincePatterns = [
            // Miền Bắc
            'Hà Nội' => '/\b(hà\s*nội|ha\s*noi|hn|thủ\s*đô)\b/ui',
            'Hải Phòng' => '/\b(hải\s*phòng|hai\s*phong|thành\s*phố\s*hoa\s*phượng\s*đỏ)\b/ui',
            'Quảng Ninh' => '/\b(quảng\s*ninh|quang\s*ninh|hạ\s*long|ha\s*long|cẩm\s*phả|móng\s*cái)\b/ui',
            'Bắc Ninh' => '/\b(bắc\s*ninh|bac\s*ninh|từ\s*sơn)\b/ui',
            'Bắc Giang' => '/\b(bắc\s*giang|bac\s*giang)\b/ui',
            'Hải Dương' => '/\b(hải\s*dương|hai\s*duong|chí\s*linh)\b/ui',
            'Hưng Yên' => '/\b(hưng\s*yên|hung\s*yen|phố\s*nối)\b/ui',
            'Nam Định' => '/\b(nam\s*định|nam\s*dinh)\b/ui',
            'Thái Bình' => '/\b(thái\s*bình|thai\s*binh)\b/ui',
            'Vĩnh Phúc' => '/\b(vĩnh\s*phúc|vinh\s*phuc|vĩnh\s*yên|phúc\s*yên)\b/ui',
            'Phú Thọ' => '/\b(phú\s*thọ|phu\s*tho|việt\s*trì)\b/ui',
            'Thái Nguyên' => '/\b(thái\s*nguyên|thai\s*nguyen|sông\s*công)\b/ui',
            'Ninh Bình' => '/\b(ninh\s*bình|ninh\s*binh|tam\s*điệp)\b/ui',
            'Hà Nam' => '/\b(hà\s*nam|ha\s*nam|phủ\s*lý)\b/ui',
            'Lào Cai' => '/\b(lào\s*cai|lao\s*cai|sa\s*pa|sapa)\b/ui',
            'Hòa Bình' => '/\b(hòa\s*bình|hoa\s*binh)\b/ui',
            'Lạng Sơn' => '/\b(lạng\s*sơn|lang\s*son)\b/ui',
            'Tuyên Quang' => '/\b(tuyên\s*quang|tuyen\s*quang)\b/ui',
            'Yên Bái' => '/\b(yên\s*bái|yen\s*bai)\b/ui',
            'Sơn La' => '/\b(sơn\s*la|son\s*la|mộc\s*châu)\b/ui',
            'Điện Biên' => '/\b(điện\s*biên|dien\s*bien)\b/ui',
            'Lai Châu' => '/\b(lai\s*châu|lai\s*chau)\b/ui',
            'Hà Giang' => '/\b(hà\s*giang|ha\s*giang)\b/ui',
            'Cao Bằng' => '/\b(cao\s*bằng|cao\s*bang)\b/ui',
            'Bắc Kạn' => '/\b(bắc\s*kạn|bac\s*kan)\b/ui',

            // Miền Trung & Tây Nguyên
            'Đà Nẵng' => '/\b(đà\s*nẵng|da\s*nang|đn)\b/ui',
            'Thừa Thiên Huế' => '/\b(thừa\s*thiên\s*huế|thua\s*thien\s*hue|huế|hue)\b/ui',
            'Nghệ An' => '/\b(nghệ\s*an|nghe\s*an|tp\s*vinh|thành\s*phố\s*vinh)\b/ui',
            'Thanh Hóa' => '/\b(thanh\s*hóa|thanh\s*hoa|sầm\s*sơn)\b/ui',
            'Quảng Nam' => '/\b(quảng\s*nam|quang\s*nam|hội\s*an|tam\s*kỳ)\b/ui',
            'Quảng Ngãi' => '/\b(quảng\s*ngãi|quang\s*ngai)\b/ui',
            'Bình Định' => '/\b(bình\s*định|binh\s*dinh|quy\s*nhơn|quy\s*nhon)\b/ui',
            'Khánh Hòa' => '/\b(khánh\s*hòa|khanh\s*hoa|nha\s*trang|cam\s*ranh)\b/ui',
            'Phú Yên' => '/\b(phú\s*yên|phu\s*yen|tuy\s*hòa)\b/ui',
            'Lâm Đồng' => '/\b(lâm\s*đồng|lam\s*dong|đà\s*lạt|da\s*lat|bảo\s*lộc)\b/ui',
            'Đắk Lắk' => '/\b(đắk\s*lắk|dak\s*lak|đắc\s*lắc|buôn\s*ma\s*thuột|buon\s*ma\s*thuot|bmt)\b/ui',
            'Gia Lai' => '/\b(gia\s*lai|pleiku)\b/ui',
            'Kon Tum' => '/\b(kon\s*tum)\b/ui',
            'Đắk Nông' => '/\b(đắk\s*nông|dak\s*nong|đắc\s*nông|gia\s*nghĩa)\b/ui',
            'Quảng Trị' => '/\b(quảng\s*trị|quang\s*tri|đông\s*hà)\b/ui',
            'Quảng Bình' => '/\b(quảng\s*bình|quang\s*binh|đồng\s*hới)\b/ui',
            'Hà Tĩnh' => '/\b(hà\s*tĩnh|ha\s*tinh)\b/ui',
            'Ninh Thuận' => '/\b(ninh\s*thuận|ninh\s*thuan|phan\s*rang)\b/ui',
            'Bình Thuận' => '/\b(bình\s*thuận|binh\s*thuan|phan\s*thiết)\b/ui',

            // Miền Nam
            'Hồ Chí Minh' => '/\b(hcm|tp\.?\s*hcm|tphcm|sài\s*gòn|sai\s*gon|hồ\s*chí\s*minh|ho\s*chi\s*minh|tp\s*hồ\s*chí\s*minh)\b/ui',
            'Bình Dương' => '/\b(bình\s*dương|binh\s*duong|thủ\s*dầu\s*một|dĩ\s*an|thuận\s*an|bến\s*cát|tân\s*uyên)\b/ui',
            'Đồng Nai' => '/\b(đồng\s*nai|dong\s*nai|biên\s*hòa|bien\s*hoa|long\s*khánh)\b/ui',
            'Bà Rịa - Vũng Tàu' => '/\b(bà\s*rịa\s*-\s*vũng\s*tàu|bà\s*rịa\s*vũng\s*tàu|ba\s*ria\s*vung\s*tau|vũng\s*tàu|vung\s*tau|bà\s*rịa)\b/ui',
            'Long An' => '/\b(long\s*an|tân\s*an)\b/ui',
            'Tiền Giang' => '/\b(tiền\s*giang|tien\s*giang|mỹ\s*tho)\b/ui',
            'Cần Thơ' => '/\b(cần\s*thơ|can\s*tho|ninh\s*kiều)\b/ui',
            'An Giang' => '/\b(an\s*giang|long\s*xuyên|châu\s*đốc)\b/ui',
            'Kiên Giang' => '/\b(kiên\s*giang|kien\s*giang|phú\s*quốc|phu\s*quoc|rạch\s*giá)\b/ui',
            'Cà Mau' => '/\b(cà\s*mau|ca\s*mau)\b/ui',
            'Tây Ninh' => '/\b(tây\s*ninh|tay\s*ninh)\b/ui',
            'Bình Phước' => '/\b(bình\s*phước|binh\s*phuoc|đồng\s*xoài)\b/ui',
            'Bến Tre' => '/\b(bến\s*tre|ben\s*tre)\b/ui',
            'Vĩnh Long' => '/\b(vĩnh\s*long|vinh\s*long)\b/ui',
            'Trà Vinh' => '/\b(trà\s*vinh|tra\s*vinh)\b/ui',
            'Hậu Giang' => '/\b(hậu\s*giang|hau\s*giang|vị\s*thanh)\b/ui',
            'Sóc Trăng' => '/\b(sóc\s*trăng|soc\s*trang)\b/ui',
            'Bạc Liêu' => '/\b(bạc\s*liêu|bac\s*lieu)\b/ui',
            'Đồng Tháp' => '/\b(đồng\s*tháp|dong\s*thap|cao\s*lãnh|sa\s*đéc)\b/ui',
        ];

        // Quét độc lập từng tỉnh thành, không dùng if..elseif
        foreach ($provincePatterns as $pName => $pattern) {
            if (preg_match($pattern, $qLower)) {
                $matchedProvinces[] = $pName;
            }
        }

        // Nhận diện quận huyện chi tiết
        $districtMap = [
            // Hà Nội
            'cầu giấy' => ['district' => 'Cầu Giấy', 'province' => 'Hà Nội'],
            'đống đa' => ['district' => 'Đống Đa', 'province' => 'Hà Nội'],
            'ba đình' => ['district' => 'Ba Đình', 'province' => 'Hà Nội'],
            'hai bà trưng' => ['district' => 'Hai Bà Trưng', 'province' => 'Hà Nội'],
            'thanh xuân' => ['district' => 'Thanh Xuân', 'province' => 'Hà Nội'],
            'hoàng mai' => ['district' => 'Hoàng Mai', 'province' => 'Hà Nội'],
            'hà đông' => ['district' => 'Hà Đông', 'province' => 'Hà Nội'],
            'nam từ liêm' => ['district' => 'Nam Từ Liêm', 'province' => 'Hà Nội'],
            'bắc từ liêm' => ['district' => 'Bắc Từ Liêm', 'province' => 'Hà Nội'],
            'tây hồ' => ['district' => 'Tây Hồ', 'province' => 'Hà Nội'],
            'long biên' => ['district' => 'Long Biên', 'province' => 'Hà Nội'],
            'gia lâm' => ['district' => 'Gia Lâm', 'province' => 'Hà Nội'],
            'sơn tây' => ['district' => 'Sơn Tây', 'province' => 'Hà Nội'],
            'hoài đức' => ['district' => 'Hoài Đức', 'province' => 'Hà Nội'],
            'thanh trì' => ['district' => 'Thanh Trì', 'province' => 'Hà Nội'],
            'đông anh' => ['district' => 'Đông Anh', 'province' => 'Hà Nội'],

            // TP. Hồ Chí Minh
            'quận 1' => ['district' => 'Quận 1', 'province' => 'Hồ Chí Minh'],
            'q1' => ['district' => 'Quận 1', 'province' => 'Hồ Chí Minh'],
            'quận 2' => ['district' => 'Quận 2', 'province' => 'Hồ Chí Minh'],
            'q2' => ['district' => 'Quận 2', 'province' => 'Hồ Chí Minh'],
            'quận 3' => ['district' => 'Quận 3', 'province' => 'Hồ Chí Minh'],
            'q3' => ['district' => 'Quận 3', 'province' => 'Hồ Chí Minh'],
            'quận 4' => ['district' => 'Quận 4', 'province' => 'Hồ Chí Minh'],
            'q4' => ['district' => 'Quận 4', 'province' => 'Hồ Chí Minh'],
            'quận 5' => ['district' => 'Quận 5', 'province' => 'Hồ Chí Minh'],
            'q5' => ['district' => 'Quận 5', 'province' => 'Hồ Chí Minh'],
            'quận 6' => ['district' => 'Quận 6', 'province' => 'Hồ Chí Minh'],
            'q6' => ['district' => 'Quận 6', 'province' => 'Hồ Chí Minh'],
            'quận 7' => ['district' => 'Quận 7', 'province' => 'Hồ Chí Minh'],
            'q7' => ['district' => 'Quận 7', 'province' => 'Hồ Chí Minh'],
            'quận 8' => ['district' => 'Quận 8', 'province' => 'Hồ Chí Minh'],
            'q8' => ['district' => 'Quận 8', 'province' => 'Hồ Chí Minh'],
            'quận 9' => ['district' => 'Quận 9', 'province' => 'Hồ Chí Minh'],
            'q9' => ['district' => 'Quận 9', 'province' => 'Hồ Chí Minh'],
            'quận 10' => ['district' => 'Quận 10', 'province' => 'Hồ Chí Minh'],
            'q10' => ['district' => 'Quận 10', 'province' => 'Hồ Chí Minh'],
            'quận 11' => ['district' => 'Quận 11', 'province' => 'Hồ Chí Minh'],
            'q11' => ['district' => 'Quận 11', 'province' => 'Hồ Chí Minh'],
            'quận 12' => ['district' => 'Quận 12', 'province' => 'Hồ Chí Minh'],
            'q12' => ['district' => 'Quận 12', 'province' => 'Hồ Chí Minh'],
            'bình thạnh' => ['district' => 'Bình Thạnh', 'province' => 'Hồ Chí Minh'],
            'gò vấp' => ['district' => 'Gò Vấp', 'province' => 'Hồ Chí Minh'],
            'tân bình' => ['district' => 'Tân Bình', 'province' => 'Hồ Chí Minh'],
            'tân phú' => ['district' => 'Tân Phú', 'province' => 'Hồ Chí Minh'],
            'phú nhuận' => ['district' => 'Phú Nhuận', 'province' => 'Hồ Chí Minh'],
            'thủ đức' => ['district' => 'Thủ Đức', 'province' => 'Hồ Chí Minh'],
            'bình tân' => ['district' => 'Bình Tân', 'province' => 'Hồ Chí Minh'],
            'nhà bè' => ['district' => 'Nhà Bè', 'province' => 'Hồ Chí Minh'],
            'hóc môn' => ['district' => 'Hóc Môn', 'province' => 'Hồ Chí Minh'],
            'củ chi' => ['district' => 'Củ Chi', 'province' => 'Hồ Chí Minh'],
            'bình chánh' => ['district' => 'Bình Chánh', 'province' => 'Hồ Chí Minh'],

            // Đà Nẵng
            'hải châu' => ['district' => 'Hải Châu', 'province' => 'Đà Nẵng'],
            'thanh khê' => ['district' => 'Thanh Khê', 'province' => 'Đà Nẵng'],
            'sơn trà' => ['district' => 'Sơn Trà', 'province' => 'Đà Nẵng'],
            'ngũ hành sơn' => ['district' => 'Ngũ Hành Sơn', 'province' => 'Đà Nẵng'],
            'liên chiểu' => ['district' => 'Liên Chiểu', 'province' => 'Đà Nẵng'],
            'cẩm lệ' => ['district' => 'Cẩm Lệ', 'province' => 'Đà Nẵng'],

            // Bình Dương
            'thủ dầu một' => ['district' => 'Thủ Dầu Một', 'province' => 'Bình Dương'],
            'dĩ an' => ['district' => 'Dĩ An', 'province' => 'Bình Dương'],
            'thuận an' => ['district' => 'Thuận An', 'province' => 'Bình Dương'],
            'bến cát' => ['district' => 'Bến Cát', 'province' => 'Bình Dương'],
            'tân uyên' => ['district' => 'Tân Uyên', 'province' => 'Bình Dương'],
        ];

        foreach ($districtMap as $kw => $info) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/ui', $qLower)) {
                $matchedDistricts[] = $info['district'];
                if (!$isNationwide) {
                    $matchedProvinces[] = $info['province'];
                }
            }
        }

        $matchedProvinces = array_values(array_unique($matchedProvinces));
        $matchedDistricts = array_values(array_unique($matchedDistricts));

        if ($isNationwide) {
            $meta['location_requested'] = 'Toàn quốc';
        } elseif (!empty($matchedDistricts) && count($matchedProvinces) === 1) {
            $meta['location_requested'] = implode(', ', $matchedDistricts) . ', ' . $matchedProvinces[0];
        } elseif (count($matchedProvinces) >= 2) {
            $meta['location_requested'] = implode(' và ', $matchedProvinces);
        } elseif (count($matchedProvinces) === 1) {
            $meta['location_requested'] = $matchedProvinces[0];
        }

        // 3. NHẬN DIỆN MỨC GIÁ VÀ TOÁN TỬ GIÁ (DƯỚI / TRÊN / KHOẢNG / TẦM)
        $targetPrice = null;
        $priceMin = null;
        $priceMax = null;
        $priceOperator = null; // 'under', 'over', 'range', 'around'

        // 3.1. Khoảng giá: "từ 3 đến 5 triệu", "3 - 5 triệu", "3-5tr", "500k - 1 triệu", "500k - 1tr"
        if (preg_match('/(?:từ\s*)?(\d+(?:[.,]\d+)?)\s*(k|nghìn|triệu|tr|tỷ)?\s*(?:-|đến|tới|\.\.\.)\s*(\d+(?:[.,]\d+)?)\s*(triệu|tr|tỷ|k|nghìn)/ui', $qLower, $mPrice)) {
            $unit2 = mb_strtolower($mPrice[4], 'UTF-8');
            $unit1 = !empty($mPrice[2]) ? mb_strtolower($mPrice[2], 'UTF-8') : $unit2;

            $multiplier1 = (in_array($unit1, ['triệu', 'tr'])) ? 1000000 : ((in_array($unit1, ['tỷ'])) ? 1000000000 : 1000);
            $multiplier2 = (in_array($unit2, ['triệu', 'tr'])) ? 1000000 : ((in_array($unit2, ['tỷ'])) ? 1000000000 : 1000);

            $val1 = (float) str_replace(',', '.', $mPrice[1]) * $multiplier1;
            $val2 = (float) str_replace(',', '.', $mPrice[3]) * $multiplier2;

            $priceMin = (int) min($val1, $val2);
            $priceMax = (int) max($val1, $val2);
            $priceOperator = 'range';
            $unit1Display = in_array($unit1, ['tr', 'triệu']) ? 'triệu' : $unit1;
            $unit2Display = in_array($unit2, ['tr', 'triệu']) ? 'triệu' : $unit2;
            $meta['price_label'] = ($unit1 !== $unit2) ? "{$mPrice[1]} {$unit1Display} - {$mPrice[3]} {$unit2Display}" : "{$mPrice[1]} - {$mPrice[3]} {$unit2Display}";
            $targetPrice = (int) (($priceMin + $priceMax) / 2);
        }
        // 3.2. Dưới / Tối đa / Đổ lại / Trở xuống / Quay đầu: "dưới 400 triệu", "tối đa 400tr", "400 triệu trở xuống", "<= 4 triệu"
        elseif (
            preg_match('/(?:dưới|nhỏ hơn|bé hơn|thấp hơn|tối đa|không quá|không vượt quá|dưới mức|ít hơn|<=|<)\s*(\d+(?:[.,]\d+)?)\s*(triệu|tr|tỷ|k|nghìn)/ui', $qLower, $mPrice) ||
            preg_match('/(\d+(?:[.,]\d+)?)\s*(triệu|tr|tỷ|k|nghìn)\s*(?:trở xuống|đổ lại|quay đầu|trở lại)/ui', $qLower, $mPrice)
        ) {
            $unit = mb_strtolower($mPrice[2], 'UTF-8');
            $multiplier = (in_array($unit, ['triệu', 'tr'])) ? 1000000 : ((in_array($unit, ['tỷ'])) ? 1000000000 : 1000);
            $val = (int) ((float) str_replace(',', '.', $mPrice[1]) * $multiplier);

            $priceMin = 0;
            $priceMax = $val;
            $targetPrice = $val;
            $priceOperator = 'under';
            $unitDisplay = in_array($unit, ['tr', 'triệu']) ? 'triệu' : $unit;
            $meta['price_label'] = 'dưới ' . $mPrice[1] . ' ' . $unitDisplay;
        }
        // 3.3. Trên / Tối thiểu / Trở lên: "trên 50 triệu", "từ 50 triệu trở lên", "tối thiểu 5 triệu", ">= 5 triệu"
        elseif (
            preg_match('/(?:trên|lớn hơn|cao hơn|tối thiểu|ít nhất|>=|>)\s*(\d+(?:[.,]\d+)?)\s*(triệu|tr|tỷ|k|nghìn)/ui', $qLower, $mPrice) ||
            preg_match('/(?:từ\s*)?(\d+(?:[.,]\d+)?)\s*(triệu|tr|tỷ|k|nghìn)\s*(?:trở lên|đổ lên)/ui', $qLower, $mPrice)
        ) {
            $unit = mb_strtolower($mPrice[2], 'UTF-8');
            $multiplier = (in_array($unit, ['triệu', 'tr'])) ? 1000000 : ((in_array($unit, ['tỷ'])) ? 1000000000 : 1000);
            $val = (int) ((float) str_replace(',', '.', $mPrice[1]) * $multiplier);

            $priceMin = $val;
            $priceMax = PHP_INT_MAX;
            $targetPrice = $val;
            $priceOperator = 'over';
            $unitDisplay = in_array($unit, ['tr', 'triệu']) ? 'triệu' : $unit;
            $meta['price_label'] = 'trên ' . $mPrice[1] . ' ' . $unitDisplay;
        }
        // 3.4. Giá cụ thể / Khoảng tầm thông thường: "400 triệu", "tầm 5 triệu", "khoảng 3 triệu"
        elseif (
            preg_match('/(?:tầm|khoảng|cỡ|xấp xỉ|quanh)?\s*(\d+(?:[.,]\d+)?)\s*(triệu|tr|tỷ)(?!\s*m2)/ui', $qLower, $mPrice)
        ) {
            $unit = mb_strtolower($mPrice[2], 'UTF-8');
            $multiplier = (in_array($unit, ['triệu', 'tr'])) ? 1000000 : 1000000000;
            $val = (int) ((float) str_replace(',', '.', $mPrice[1]) * $multiplier);

            $priceMin = (int) ($val * 0.85);
            $priceMax = (int) ($val * 1.15);
            $targetPrice = $val;
            $priceOperator = 'around';
            $unitDisplay = in_array($unit, ['tr', 'triệu']) ? 'triệu' : $unit;
            $meta['price_label'] = $mPrice[1] . ' ' . $unitDisplay;
        }
        elseif (
            preg_match('/(?:tầm|khoảng|cỡ|xấp xỉ|quanh)?\s*(\d+(?:[.,]\d+)?)\s*(?:k|nghìn)\s*(?:đ|vnd|đồng)?/ui', $qLower, $mPrice)
        ) {
            $val = (int) ((float) str_replace(',', '.', $mPrice[1]) * 1000);

            $priceMin = (int) ($val * 0.85);
            $priceMax = (int) ($val * 1.15);
            $targetPrice = $val;
            $priceOperator = 'around';
            $meta['price_label'] = $mPrice[1] . 'k';
        }

        $meta['target_price'] = $targetPrice;
        $meta['price_min'] = $priceMin;
        $meta['price_max'] = $priceMax;
        $meta['price_operator'] = $priceOperator;

        // 4. NHẬN DIỆN DIỆN TÍCH (m2)
        $targetArea = null;
        $areaMin = null;
        $areaMax = null;
        $areaOperator = null; // 'range', 'under', 'over', 'around'

        // 4.1. Khoảng diện tích: "từ 30 đến 45 m2", "30 - 45 m2", "diện tích 30 - 45", "30 đến 45 mét vuông"
        if (
            preg_match('/(?:diện\s*tích|rộng|dt)\s*(?:từ\s*)?(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)?\s*(?:-|đến|tới|\.\.\.)\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)?/ui', $qLower, $mArea) ||
            preg_match('/(?:từ\s*)?(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)?\s*(?:-|đến|tới|\.\.\.)\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)/ui', $qLower, $mArea)
        ) {
            $val1 = (float) str_replace(',', '.', $mArea[1]);
            $val2 = (float) str_replace(',', '.', $mArea[2]);
            $areaMin = min($val1, $val2);
            $areaMax = max($val1, $val2);
            $targetArea = ($areaMin + $areaMax) / 2;
            $areaOperator = 'range';
            $meta['area_label'] = "{$areaMin} - {$areaMax} m²";
        }
        // 4.2. Dưới diện tích / Tối đa / Đổ lại / Trở xuống: "dưới 35 m2", "tối đa 40m2", "diện tích dưới 40", "35m2 trở xuống", "<= 35 m2"
        elseif (
            preg_match('/(?:diện\s*tích|rộng|dt)\s*(?:dưới|nhỏ hơn|bé hơn|thấp hơn|tối đa|không quá|không vượt quá|dưới mức|ít hơn|<=|<)\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)?/ui', $qLower, $mArea) ||
            preg_match('/(?:dưới|nhỏ hơn|bé hơn|thấp hơn|tối đa|không quá|không vượt quá|dưới mức|ít hơn|<=|<)\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)/ui', $qLower, $mArea) ||
            preg_match('/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)\s*(?:trở xuống|đổ lại|quay đầu|trở lại)/ui', $qLower, $mArea)
        ) {
            $val = (float) str_replace(',', '.', $mArea[1]);
            $areaMin = 0;
            $areaMax = $val;
            $targetArea = $val;
            $areaOperator = 'under';
            $meta['area_label'] = "dưới {$val} m²";
        }
        // 4.3. Trên diện tích / Tối thiểu / Trở lên: "trên 35 m2", "lớn hơn 40m2", "diện tích trên 40", "từ 35 m2 trở lên", ">= 35 m2"
        elseif (
            preg_match('/(?:diện\s*tích|rộng|dt)\s*(?:trên|lớn hơn|rộng hơn|cao hơn|tối thiểu|ít nhất|>=|>)\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)?/ui', $qLower, $mArea) ||
            preg_match('/(?:trên|lớn hơn|rộng hơn|cao hơn|tối thiểu|ít nhất|>=|>)\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)/ui', $qLower, $mArea) ||
            preg_match('/(?:từ\s*)?(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)\s*(?:trở lên|đổ lên)/ui', $qLower, $mArea)
        ) {
            $val = (float) str_replace(',', '.', $mArea[1]);
            $areaMin = $val;
            $areaMax = PHP_INT_MAX;
            $targetArea = $val;
            $areaOperator = 'over';
            $meta['area_label'] = "trên {$val} m²";
        }
        // 4.4. Diện tích cụ thể / Khoảng tầm thông thường: "với diện tích 35 m2", "diện tích 35 m2", "tầm 35m2", "khoảng 35 m2", "35 m2", "35 mét vuông", "diện tích 35"
        elseif (
            preg_match('/(?:diện\s*tích|rộng|dt)\s*(?:là|với|tầm|khoảng|cỡ|xấp xỉ)?\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)?/ui', $qLower, $mArea) ||
            preg_match('/(?:tầm|khoảng|cỡ|xấp xỉ|quanh)?\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét\s*vuông|met\s*vuong)/ui', $qLower, $mArea)
        ) {
            $val = (float) str_replace(',', '.', $mArea[1]);
            $targetArea = $val;
            $areaMin = max(0, $val - max(3, $val * 0.15));
            $areaMax = $val + max(3, $val * 0.15);
            $areaOperator = 'around';
            $meta['area_label'] = "{$val} m²";
        }

        $meta['target_area'] = $targetArea;
        $meta['area_min'] = $areaMin;
        $meta['area_max'] = $areaMax;
        $meta['area_operator'] = $areaOperator;

        // 5. NHẬN DIỆN ĐỘNG TỪ TÌM KIẾM
        $hasSearchVerb = (bool) preg_match('/\b(tìm|tim|kiếm|kiem|cần thuê|can thue|muốn thuê|muon thue|thuê ở|thuê tại|gợi ý|cho tôi|những căn|các căn|có phòng|co phong|phòng nào|phong nao|còn phòng|con phong)\b/ui', $qLower);

        // 6. NHẬN DIỆN LOẠI HÌNH BẤT ĐỘNG SẢN (7 LOẠI HÌNH CHUẨN CỦA RENTHOME)
        $typeDefinitions = [
            'can_ho' => [
                'name' => 'Chung cư mini / Căn hộ dịch vụ',
                'pattern' => '/\b(tòa\s*nhà|toa\s*nha|chung\s*cư\s*mini|chung\s*cu\s*mini|chung\s*cư|chung\s*cu|căn\s*hộ\s*dịch\s*vụ|can\s*ho\s*dich\s*vu|căn\s*hộ|can\s*ho|ccmn|studio|condo|apartment)\b/ui',
                'db_keys' => ['can_ho', 'can_ho_dich_vu', 'chung_cu', 'chung_cu_mini', 'ch'],
                'keywords' => ['chung cư', 'căn hộ', 'ccmn', 'tòa nhà', 'chdv', 'apartment', 'studio', 'condo'],
            ],
            'nha_tro' => [
                'name' => 'Nhà trọ / Khu trọ',
                'pattern' => '/\b(nhà\s*trọ|nha\s*tro|khu\s*trọ|khu\s*tro|phòng\s*trọ|phong\s*tro|dãy\s*trọ|day\s*tro|xóm\s*trọ|xom\s*tro|phòng\s*cho\s*thuê|phong\s*cho\s*thue|phòng\s*khép\s*kín|phong\s*khep\s*kin)\b/ui',
                'db_keys' => ['nha_tro', 'phong_tro', 'pt', 'khu_tro'],
                'keywords' => ['nhà trọ', 'khu trọ', 'phòng trọ', 'dãy trọ', 'xóm trọ', 'phòng cho thuê'],
            ],
            'ky_tuc_xa' => [
                'name' => 'Ký túc xá / Sleepbox',
                'pattern' => '/\b(ký\s*túc\s*xá|ky\s*tuc\s*xa|sleepbox|sleep\s*box|ktx|hộp\s*ngủ|hop\s*ngu|giường\s*tầng|giuong\s*tang|dorm)\b/ui',
                'db_keys' => ['ky_tuc_xa', 'sleepbox', 'ktx', 'box'],
                'keywords' => ['ký túc xá', 'sleepbox', 'ktx', 'hộp ngủ', 'giường tầng', 'dorm'],
            ],
            'dat_phan_lo' => [
                'name' => 'Đất phân lô / Đất nền',
                'pattern' => '/\b(đất\s*phân\s*lô|dat\s*phan\s*lo|lô\s*đất|lo\s*dat|thửa\s*đất|thua\s*dat|đất\s*thổ\s*cư|dat\s*tho\s*cu|thổ\s*cư|tho\s*cu|đất\s*dự\s*án|dat\s*du\s*an)\b/ui',
                'db_keys' => ['dat_phan_lo', 'dat_phanlo', 'dat_nen_du_an'],
                'keywords' => ['đất phân lô', 'lô đất', 'thửa đất', 'thổ cư', 'đất dự án'],
            ],
            'nha_rieng' => [
                'name' => 'Nhà ở / Nhà riêng lẻ',
                'pattern' => '/\b(nhà\s*riêng\s*lẻ|nha\s*rieng\s*le|nhà\s*riêng|nha\s*rieng|nhà\s*nguyên\s*căn|nha\s*nguyen\s*can|nguyên\s*căn|nguyen\s*can|nhà\s*phố|nha\s*pho|nhà\s*mặt\s*phố|nha\s*mat\s*pho|nhà\s*ngõ|nha\s*ngo|nhà\s*cấp\s*4|nha\s*cap\s*4|nhà\s*ở\s*riêng\s*lẻ|nhà\s*liền\s*thổ)\b/ui',
                'db_keys' => ['nha_rieng', 'nha_nguyen_can', 'nha_o', 'nnc'],
                'keywords' => ['nhà riêng', 'nhà riêng lẻ', 'nhà nguyên căn', 'nhà phố', 'nhà mặt phố'],
            ],
            'cum_nha_lien_ke' => [
                'name' => 'Cụm nhà liền kề / Dự án',
                'pattern' => '/\b(cụm\s*nhà\s*liền\s*kề|cum\s*nha\s*lien\s*ke|nhà\s*liền\s*kề|nha\s*lien\s*ke|liền\s*kề|lien\s*ke|biệt\s*thự|biet\s*thu|villa|shophouse|nhà\s*dự\s*án)\b/ui',
                'db_keys' => ['cum_nha_lien_ke', 'lien_ke', 'nha_lien_ke', 'biet_thu', 'villa', 'shophouse'],
                'keywords' => ['cụm nhà liền kề', 'nhà liền kề', 'liền kề', 'dự án', 'biệt thự', 'villa', 'shophouse'],
            ],
            'dat_nen' => [
                'name' => 'Mặt bằng kinh doanh',
                'pattern' => '/\b(mặt\s*bằng\s*kinh\s*doanh|mat\s*bang\s*kinh\s*doanh|mặt\s*bằng|mat\s*bang|cửa\s*hàng|cua\s*hang|shop|kiot|kiosk|quán\s*ăn|văn\s*phòng|van\s*phong|mặt\s*sàn|mat\s*san|đất\s*kinh\s*doanh)\b/ui',
                'db_keys' => ['dat_nen', 'mat_bang', 'mat_bang_kinh_doanh', 'van_phong', 'cua_hang'],
                'keywords' => ['mặt bằng kinh doanh', 'mặt bằng', 'cửa hàng', 'shop', 'kiot', 'kiosk', 'văn phòng', 'kinh doanh', 'đất kinh doanh'],
            ],
        ];

        $matchedTypes = [];
        foreach ($typeDefinitions as $key => $def) {
            if (preg_match($def['pattern'], $qLower)) {
                $matchedTypes[$key] = $def;
            }
        }

        // Hỗ trợ trường hợp người dùng chỉ nói "tìm phòng" hoặc "tìm nhà", "căn nhà" chung chung (không nêu loại hình cụ thể)
        $isGenericRoomSearch = empty($matchedTypes) && (bool) preg_match('/\b(phòng|phong|nhà|nha|căn\s*nhà|can\s*nha|ngôi\s*nhà|ngoi\s*nha|căn|can)\b/ui', $qLower);

        if (!empty($matchedTypes)) {
            $typeNames = array_map(fn($d) => $d['name'], $matchedTypes);
            $meta['property_type_requested'] = implode(', ', $typeNames);
        } elseif ($isGenericRoomSearch) {
            $meta['property_type_requested'] = null;
        }

        // 7. BỔ SUNG CƠ CHẾ HỎI LẠI (CLARIFICATION) KHI THÔNG TIN QUÁ CHUNG CHUNG
        // Chỉ hỏi lại khi người dùng tìm kiếm chung chung mà KHÔNG CÓ loại hình cụ thể,
        // HOÀN TOÀN THIẾU ĐỊA ĐIỂM (chưa có tỉnh/thành hay quận/huyện, không phải toàn quốc) VÀ THIẾU MỨC GIÁ / DIỆN TÍCH
        if ($hasSearchVerb && empty($matchedTypes) && empty($matchedProvinces) && empty($matchedDistricts) && !$isNationwide && empty($targetPrice) && empty($targetArea) && empty($areaOperator)) {
            $meta['is_search'] = true;
            $meta['needs_clarification'] = true;
            session()->put('ai_search_waiting_criteria', true);
            session()->forget('ai_auto_posting_draft');
            return ['cards' => [], 'meta' => $meta];
        }

        // 8. KIỂM TRA TỪ KHÓA CÂU HỎI THẮC MẮC VS THÔNG TIN TÌM KIẾM
        $hasValidSearchCriteria = (!empty($matchedProvinces) || !empty($matchedDistricts) || !empty($targetPrice) || !empty($targetArea) || !empty($areaOperator) || $isNationwide);

        $questionKeywords = [
            'cọc', 'tiền cọc', 'đặt cọc', 'môi giới', 'duyệt bài', 'mất phí', 'tăng giá',
            'điện nước', 'tạm trú', 'hợp đồng', 'khiếu nại', 'lừa đảo', 'quy định', 'thủ tục',
            'có phải', 'có cần', 'có nên', 'được không', 'được k', 'được ko', 'thế nào',
            'như thế nào', 'làm sao', 'tại sao', 'vì sao', 'bao lâu', 'mấy tháng'
        ];

        $hasQuestionKeyword = false;
        foreach ($questionKeywords as $qk) {
            if (str_contains($qLower, $qk)) {
                $hasQuestionKeyword = true;
                break;
            }
        }

        if ($hasQuestionKeyword && !$hasValidSearchCriteria) {
            return ['cards' => [], 'meta' => $meta];
        }

        // Chỉ coi là tìm kiếm nếu: có động từ tìm kiếm RÕ RÀNG HOẶC (có địa điểm / mức giá / diện tích / loại hình / toàn quốc)
        if (!$hasSearchVerb && empty($matchedProvinces) && empty($matchedDistricts) && !$isNationwide && !$targetPrice && !$targetArea && empty($areaOperator) && empty($matchedTypes) && !$isGenericRoomSearch) {
            return ['cards' => [], 'meta' => $meta];
        }

        $meta['is_search'] = true;

        // 9. TRUY VẤN CƠ SỞ DỮ LIỆU
        $baseQuery = Post::with('building')->where('status', 'approved');

        // Lọc địa điểm: Toàn quốc vs Đa tỉnh thành vs Đơn tỉnh thành
        if (!$isNationwide) {
            if (count($matchedProvinces) >= 2) {
                // Đa tỉnh thành
                $baseQuery->where(function ($query) use ($matchedProvinces) {
                    foreach ($matchedProvinces as $p) {
                        $query->orWhere('province', 'like', "%{$p}%")
                              ->orWhere('address', 'like', "%{$p}%");
                    }
                });
            } elseif (count($matchedProvinces) === 1) {
                $p = $matchedProvinces[0];
                $baseQuery->where(function ($query) use ($p) {
                    $query->where('province', 'like', "%{$p}%")
                          ->orWhere('address', 'like', "%{$p}%");
                });
            } elseif (!empty($matchedDistricts)) {
                $baseQuery->where(function ($query) use ($matchedDistricts) {
                    foreach ($matchedDistricts as $d) {
                        $query->orWhere('district', 'like', "%{$d}%")
                              ->orWhere('address', 'like', "%{$d}%");
                    }
                });
            }
        }

        // Lấy danh sách bài đăng thô
        $posts = $baseQuery->latest()->take(30)->get();

        if ($posts->isEmpty()) {
            return ['cards' => [], 'meta' => $meta];
        }

        // 10. PHÂN LOẠI THEO LOẠI HÌNH BẤT ĐỘNG SẢN (NẾU CÓ)
        $candidates = $posts;
        $isExactType = true;
        if (!empty($matchedTypes)) {
            $allowedDbKeys = [];
            $allTextKeywords = [];
            foreach ($matchedTypes as $def) {
                $allowedDbKeys = array_merge($allowedDbKeys, $def['db_keys']);
                $allTextKeywords = array_merge($allTextKeywords, $def['keywords']);
            }
            $allowedDbKeys = array_unique($allowedDbKeys);

            $filteredByType = $posts->filter(function ($p) use ($allowedDbKeys, $allTextKeywords) {
                // 1. Kiểm tra raw property_type hoặc accessor property_type của Post
                $propType = $p->property_type ?? ($p->getAttributes()['property_type'] ?? null);
                if ($propType && in_array(strtolower(trim($propType)), $allowedDbKeys, true)) {
                    return true;
                }

                // 2. Kiểm tra type và business_type của Tòa nhà (nếu có liên kết)
                if ($p->building) {
                    $bType = $p->building->type ?? null;
                    if ($bType && in_array(strtolower(trim($bType)), $allowedDbKeys, true)) {
                        return true;
                    }
                    $bBiz = mb_strtolower($p->building->business_type ?? '', 'UTF-8');
                    foreach ($allTextKeywords as $kw) {
                        if (str_contains($bBiz, mb_strtolower($kw, 'UTF-8'))) {
                            return true;
                        }
                    }
                    $bName = mb_strtolower($p->building->name ?? '', 'UTF-8');
                    foreach ($allTextKeywords as $kw) {
                        if (str_contains($bName, mb_strtolower($kw, 'UTF-8'))) {
                            return true;
                        }
                    }
                }

                // 3. Kiểm tra title và description của bài đăng
                $text = mb_strtolower(($p->title ?? '') . ' ' . ($p->description ?? ''), 'UTF-8');
                foreach ($allTextKeywords as $kw) {
                    if (str_contains($text, mb_strtolower($kw, 'UTF-8'))) {
                        return true;
                    }
                }

                return false;
            });

            if ($filteredByType->count() > 0) {
                $candidates = $filteredByType;
            } else {
                $isExactType = false;
            }
        }

        $matchesPrice = function ($postPrice) use ($priceMin, $priceMax) {
            if ($priceMin === null && $priceMax === null) {
                return true;
            }
            if ($postPrice <= 0) {
                return false;
            }
            if ($priceMin !== null && $postPrice < $priceMin) {
                return false;
            }
            if ($priceMax !== null && $postPrice > $priceMax) {
                return false;
            }
            return true;
        };

        $matchesArea = function ($postArea) use ($areaOperator, $targetArea, $areaMin, $areaMax) {
            if ($areaOperator === null && $targetArea === null) {
                return true;
            }
            $pArea = (float) $postArea;
            if ($pArea <= 0) {
                return false;
            }
            if ($areaOperator === 'under') {
                return $pArea <= $areaMax;
            }
            if ($areaOperator === 'over') {
                return $pArea >= $areaMin;
            }
            if ($areaOperator === 'range') {
                return $pArea >= $areaMin && $pArea <= $areaMax;
            }
            // 'around' hoặc số diện tích cụ thể:
            // Khớp chính xác hoặc chênh lệch rất nhỏ (<= 1.5m2 hoặc 5%)
            return abs($pArea - $targetArea) <= max(1.5, $targetArea * 0.05);
        };

        $sortMatches = function ($collection) use ($priceOperator, $targetPrice, $areaOperator, $targetArea) {
            if ($targetArea && $targetPrice) {
                return $collection->sortBy(function ($p) use ($targetArea, $targetPrice) {
                    $areaDiff = abs((float)$p->area - $targetArea) / max(1, $targetArea);
                    $priceDiff = abs((float)$p->price - $targetPrice) / max(1, $targetPrice);
                    return $areaDiff + $priceDiff;
                });
            }
            if ($targetArea !== null || $areaOperator !== null) {
                if ($areaOperator === 'under') {
                    return $collection->sortByDesc(fn($p) => (float)$p->area);
                }
                if ($areaOperator === 'over') {
                    return $collection->sortBy(fn($p) => (float)$p->area);
                }
                return $collection->sortBy(fn($p) => abs((float)$p->area - $targetArea));
            }
            if ($priceOperator === 'under') {
                return $collection->sortByDesc('price');
            }
            if ($priceOperator === 'over') {
                return $collection->sortBy('price');
            }
            if ($targetPrice) {
                return $collection->sortBy(function ($p) use ($targetPrice) {
                    return abs($p->price - $targetPrice);
                });
            }
            return $collection;
        };

        $sortAlternatives = function ($collection) use ($priceOperator, $targetPrice, $areaOperator, $targetArea) {
            if ($targetArea) {
                return $collection->sortBy(fn($p) => abs((float)$p->area - $targetArea));
            }
            if ($priceOperator === 'under') {
                return $collection->sortBy('price');
            }
            if ($priceOperator === 'over') {
                return $collection->sortByDesc('price');
            }
            if ($targetPrice) {
                return $collection->sortBy(function ($p) use ($targetPrice) {
                    return abs($p->price - $targetPrice);
                });
            }
            return $collection;
        };

        // 11. XỬ LÝ TRƯỜNG HỢP TÌM NHIỀU TỈNH THÀNH (ĐA ĐỊA ĐIỂM)
        if (count($matchedProvinces) >= 2) {
            $cardsPerProvince = (int) ceil(4 / count($matchedProvinces));
            foreach ($matchedProvinces as $prov) {
                $provPosts = $candidates->filter(function ($p) use ($prov) {
                    $provText = mb_strtolower(($p->province ?? '') . ' ' . ($p->address ?? ''), 'UTF-8');
                    $provCheck = mb_strtolower($prov, 'UTF-8');
                    return str_contains($provText, $provCheck);
                });

                if ($targetArea !== null || $areaOperator !== null || $priceOperator !== null) {
                    $provMatches = $provPosts->filter(function ($p) use ($matchesArea, $matchesPrice, $priceOperator, $targetArea, $areaOperator) {
                        $areaOk = ($targetArea !== null || $areaOperator !== null) ? $matchesArea($p->area) : true;
                        $priceOk = ($priceOperator !== null) ? $matchesPrice($p->price) : true;
                        return $areaOk && $priceOk;
                    });
                    $chosen = ($provMatches->count() > 0 ? $sortMatches($provMatches) : $sortAlternatives($provPosts))->take($cardsPerProvince);
                } else {
                    $chosen = $provPosts->take($cardsPerProvince);
                }

                foreach ($chosen as $p) {
                    $cards[] = $this->transformPostToCard($p, true);
                }
            }

            if (!empty($cards)) {
                $meta['is_exact_match'] = true;
                return ['cards' => array_slice($cards, 0, 4), 'meta' => $meta];
            }
        }

        // 12. XỬ LÝ TRƯỜNG HỢP TOÀN QUỐC
        if ($isNationwide) {
            $maxCards = 4;
            $hasFilter = ($targetArea !== null || $areaOperator !== null || $priceOperator !== null);

            if ($hasFilter) {
                $exactMatches = $candidates->filter(function ($p) use ($matchesArea, $matchesPrice, $priceOperator, $targetArea, $areaOperator) {
                    $areaOk = ($targetArea !== null || $areaOperator !== null) ? $matchesArea($p->area) : true;
                    $priceOk = ($priceOperator !== null) ? $matchesPrice($p->price) : true;
                    return $areaOk && $priceOk;
                });
                $chosenExact = ($exactMatches->count() > 0 ? $sortMatches($exactMatches) : $sortAlternatives($candidates))->take($maxCards);
            } else {
                $chosenExact = $candidates->take($maxCards);
            }

            $chosenIds = [];
            foreach ($chosenExact as $p) {
                $isPostMatch = ($targetArea !== null || $areaOperator !== null) ? $matchesArea($p->area) : true;
                if ($priceOperator !== null && !$matchesPrice($p->price)) {
                    $isPostMatch = false;
                }
                $badge = null;
                if (!$isPostMatch && $targetArea) {
                    $badge = $p->area ? '📐 Gần ' . $targetArea . ' m²' : '💡 Gợi ý tham khảo';
                }
                $cards[] = $this->transformPostToCard($p, $isPostMatch, $badge);
                $chosenIds[] = (string) $p->_id;
            }

            // Nếu số bài khớp đúng loại hình ít hơn 4 bài (ví dụ loại hình đó chỉ mới có 1 bài trên hệ thống),
            // bổ sung thêm các bài đăng nổi bật khác trên Toàn quốc để người dùng luôn có đủ 3 - 4 lựa chọn phong phú:
            if (count($cards) < $maxCards) {
                $needed = $maxCards - count($cards);
                $remainingPosts = $posts->filter(function ($p) use ($chosenIds) {
                    return !in_array((string)$p->_id, $chosenIds, true);
                })->take($needed);

                if ($remainingPosts->count() > 0) {
                    $meta['has_alternatives'] = true;
                    foreach ($remainingPosts as $p) {
                        $cards[] = $this->transformPostToCard($p, false);
                    }
                }
            }

            $meta['is_exact_match'] = true;
            return ['cards' => $cards, 'meta' => $meta];
        }

        // 14. LỌC THEO DIỆN TÍCH VÀ / HOẶC GIÁ TIỀN (TRƯỜNG HỢP ĐƠN ĐỊA ĐIỂM HOẶC CHUNG)
        if ($targetArea !== null || $areaOperator !== null) {
            // Lọc các căn khớp diện tích (và giá nếu có)
            $exactMatches = $candidates->filter(function ($p) use ($matchesArea, $matchesPrice, $priceOperator) {
                $areaOk = $matchesArea($p->area);
                $priceOk = ($priceOperator !== null) ? $matchesPrice($p->price) : true;
                return $areaOk && $priceOk;
            });

            $maxCards = 3;
            $chosenIds = [];

            if ($exactMatches->count() > 0) {
                $meta['is_exact_match'] = $isExactType;
                $sortedExact = $sortMatches($exactMatches);

                foreach ($sortedExact->take($maxCards) as $p) {
                    $cards[] = $this->transformPostToCard($p, $isExactType);
                    $chosenIds[] = (string) $p->_id;
                }

                // Nếu số căn khớp đúng diện tích ít hơn 3 (ví dụ chỉ có 1 căn đúng 35m2),
                // lấy thêm các căn có diện tích gần nhất theo yêu cầu để người dùng có đủ lựa chọn:
                if (count($cards) < $maxCards) {
                    $needed = $maxCards - count($cards);
                    $remaining = $candidates->filter(function ($p) use ($chosenIds, $matchesPrice, $priceOperator) {
                        if (in_array((string)$p->_id, $chosenIds, true)) return false;
                        if ($priceOperator !== null && !$matchesPrice($p->price)) return false;
                        return true;
                    })->sortBy(function ($p) use ($targetArea) {
                        return abs((float)$p->area - $targetArea);
                    })->take($needed);

                    if ($remaining->count() > 0) {
                        $meta['has_alternatives'] = true;
                        foreach ($remaining as $p) {
                            $nearBadge = $p->area ? '📐 Gần ' . $targetArea . ' m²' : '💡 Gợi ý tham khảo';
                            $cards[] = $this->transformPostToCard($p, false, $nearBadge);
                        }
                    }
                }
            } else {
                // Không có căn nào đúng số m2 yêu cầu -> Lấy các căn có diện tích gần nhất
                $meta['is_exact_match'] = false;
                $meta['has_alternatives'] = true;

                $sortedNearby = $candidates->filter(function ($p) use ($matchesPrice, $priceOperator) {
                    if ($priceOperator !== null && !$matchesPrice($p->price)) return false;
                    return true;
                })->sortBy(function ($p) use ($targetArea) {
                    return abs((float)$p->area - $targetArea);
                })->take($maxCards);

                foreach ($sortedNearby as $p) {
                    $nearBadge = $p->area ? '📐 Gần ' . $targetArea . ' m²' : '💡 Gợi ý tham khảo';
                    $cards[] = $this->transformPostToCard($p, false, $nearBadge);
                }
            }
        } elseif ($priceOperator !== null) {
            $exactMatches = $candidates->filter(function ($p) use ($matchesPrice) {
                return $matchesPrice($p->price);
            });

            if ($exactMatches->count() > 0) {
                $meta['is_exact_match'] = $isExactType;
                $sorted = $sortMatches($exactMatches);
                foreach ($sorted->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, $isExactType);
                }
            } else {
                $meta['is_exact_match'] = false;
                $meta['has_alternatives'] = true;
                $sorted = $sortAlternatives($candidates);
                foreach ($sorted->take(2) as $p) {
                    $cards[] = $this->transformPostToCard($p, false);
                }
            }
        } else {
            // Không yêu cầu giá lẫn diện tích
            $meta['is_exact_match'] = $isExactType;
            if ($isExactType) {
                $chosenIds = [];
                // Lấy các bài khớp đúng loại hình
                foreach ($candidates->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, true);
                    $chosenIds[] = (string) $p->_id;
                }
                // Nếu ít hơn 3 bài, bổ sung thêm các bài đăng khác cùng khu vực hoặc nổi bật để người dùng có thêm lựa chọn
                if (count($cards) < 3) {
                    $needed = 3 - count($cards);
                    $remaining = $posts->filter(function ($p) use ($chosenIds) {
                        return !in_array((string)$p->_id, $chosenIds, true);
                    })->take($needed);
                    if ($remaining->count() > 0) {
                        $meta['has_alternatives'] = true;
                        foreach ($remaining as $p) {
                            $cards[] = $this->transformPostToCard($p, false);
                        }
                    }
                }
            } else {
                // Không có bài khớp đúng loại hình, gợi ý các bài cùng khu vực
                $meta['has_alternatives'] = true;
                foreach ($candidates->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, false);
                }
            }
        }

        return ['cards' => $cards, 'meta' => $meta];
    }

    /**
     * Chuyển đổi Post Model sang Card UI đẹp mắt
     */
    protected function transformPostToCard($post, bool $isMatch, ?string $customBadge = null): array
    {
        $areaStr = $post->area ? $post->area . ' m²' : 'Đang cập nhật';
        $priceStr = $this->formatCurrency($post->price);
        $img = $post->display_image ?: url('/images/default-room.jpg');

        $locationParts = array_filter([$post->address, $post->ward, $post->district, $post->province]);
        $addressStr = !empty($locationParts) ? implode(', ', $locationParts) : ($post->province ?? 'Toàn quốc');

        return [
            'id' => (string) $post->_id,
            'title' => $post->title ?? 'Phòng trọ cho thuê',
            'price' => $priceStr,
            'area' => $areaStr,
            'address' => $addressStr,
            'image' => $img,
            'url' => url('/baidang/' . $post->_id),
            'is_match' => $isMatch,
            'badge' => $customBadge ?? ($isMatch ? '⭐ Khớp nhu cầu' : '💡 Gợi ý tham khảo'),
        ];
    }

    /**
     * Định dạng tiền tệ VND đẹp mắt (tự động đổi sang tỷ hoặc triệu)
     */
    protected function formatCurrency($amount): string
    {
        $num = (float) $amount;
        if ($num >= 1000000000) {
            return rtrim(rtrim(number_format($num / 1000000000, 1, '.', ''), '0'), '.') . ' tỷ/tháng';
        }
        if ($num >= 1000000) {
            return rtrim(rtrim(number_format($num / 1000000, 1, '.', ''), '0'), '.') . ' triệu/tháng';
        }
        return number_format($num, 0, ',', '.') . ' đ/tháng';
    }

    /**
     * Lời chào mở đầu theo thời gian trong ngày
     */
    protected function getTimeBasedGreeting(string $userName): string
    {
        return "Xin chào **{$userName}**! ";
    }

    /**
     * Gọi Google Gemini API để tư vấn tự nhiên với cơ sở tri thức thật 100%
     */
    protected function callGemini(string $message, array $history, array $cards, array $meta, $currentUser, string $userName): ?string
    {
        if (empty($this->geminiApiKey)) {
            return null;
        }

        $systemInstruction = "Bạn là Trợ lý AI của Nền tảng Bất động sản Cho thuê RentHome. Bạn đồng hành hỗ trợ người thuê tìm phòng và tư vấn mọi thủ tục liên quan đến thuê nhà tại Việt Nam.\n\n"
                           . "### QUY TẮC BẮT BUỘC VỀ PHÂN QUYỀN TÀI KHOẢN TRÊN RENTHOME:\n"
                           . "- **Tài khoản cá nhân (Khách thuê):** CHỈ ĐƯỢC tìm kiếm phòng, hỏi đáp thủ tục, ký hợp đồng và thuê nhà. TUYỆT ĐỐI KHÔNG ĐƯỢC đăng bài hay đăng tin cho thuê phòng/nhà.\n"
                           . "- **Tài khoản Doanh nghiệp & Quản lý tòa nhà (Chủ nhà / Chủ cơ sở):** Mới có quyền tạo tòa nhà, quản lý phòng và đăng tin cho thuê (miễn phí 100%, kiểm duyệt 15 - 30 phút).\n"
                           . "- Khi người dùng hỏi: 'tài khoản cá nhân có được đăng bài không?', hoặc người dùng tài khoản cá nhân yêu cầu đăng bài: BẮT BUỘC phải khẳng định rõ ràng là TÀI KHOẢN CÁ NHÂN KHÔNG ĐƯỢC ĐĂNG BÀI HAY ĐĂNG TIN, chỉ được tìm và thuê nhà. Để đăng bài cần tài khoản Doanh nghiệp hoặc Quản lý tòa nhà.\n\n"
                           . "### 1. Giọng văn & Phong thái (Tone of Voice)\n"
                           . "- Điềm tĩnh, nhạy bén, đồng cảm và am hiểu sâu sắc thị trường bất động sản cho thuê tại Việt Nam.\n"
                           . "- Xưng hô tự nhiên là \"mình\" hoặc \"RentHome\", gọi người dùng là \"{$userName}\". Tránh giọng điệu hành chính, răn đe, giáo điều hay máy móc.\n\n"
                           . "### 2. Bảo mật danh tính & Công nghệ bên dưới (Duyên dáng & Khéo léo)\n"
                           . "- Khi được hỏi về Admin, người sáng lập, người tạo ra AI hoặc công nghệ bên dưới (framework, ngôn ngữ lập trình, cơ sở dữ liệu, model AI...):\n"
                           . "  + Hãy phản hồi một cách khiêm tốn, hóm hỉnh và khéo léo: Tự giới thiệu là trợ lý ảo do đội ngũ kỹ sư RentHome phát triển để hỗ trợ người thuê tìm phòng và giải đáp thủ tục; chuyện \"bếp núc\" công nghệ hay thông tin cá nhân của các anh kỹ sư thì được giữ bí mật nội bộ, nhưng chuyện phòng ốc, hợp đồng, cọc và giá điện nước thì nắm rất rõ trong lòng bàn tay. Sau đó khéo léo hỏi thăm người dùng đang cần hỗ trợ tìm phòng hay đăng bài thế nào.\n"
                           . "  + Tuyệt đối không nhắc tên các công nghệ: PHP, Laravel, MongoDB, Google, Gemini API, DeepMind, v.v.\n\n"
                           . "### 3. Nguyên tắc phản hồi linh hoạt theo ngữ cảnh\n"
                           . "- TUYỆT ĐỐI KHÔNG ÉP BUỘC CẤU TRÚC 5 PHẦN CỨNG NHẮC CHO MỌI CÂU HỎI. Không dùng các icon đầu mục đao to búa lớn (như 🎯 1, ⚖️ 2, 🔮 Lường trước...).\n"
                           . "- Định dạng Markdown tự nhiên, sạch sẽ, phân đoạn mạch lạc, dễ đọc trên điện thoại.\n"
                           . "- Đối với câu hỏi Chào hỏi, Tìm kiếm phòng hoặc Câu hỏi phụ ngắn:\n"
                           . "  + Trả lời tự nhiên, ngắn gọn, đi thẳng vào vấn đề và gợi ý ấm áp.\n"
                           . "  + Nếu có danh sách phòng gửi kèm trong dữ liệu, hãy giới thiệu các căn phù hợp, nêu bật ưu điểm chính (vị trí, mức giá, tiện nghi) và hướng dẫn người dùng bấm vào xem chi tiết hoặc liên hệ chính chủ.\n"
                           . "- Chỉ áp dụng cấu trúc chuyên sâu cho các câu hỏi Pháp lý, Tranh chấp, Đòi cọc hoặc Hợp đồng:\n"
                           . "  + Trình bày mạch lạc theo cấu trúc: Kết luận rõ ràng -> Căn cứ pháp lý thực tế (Điều 328 BLDS 2015 về cọc, Luật Nhà ở, Thông tư 25/2018/TT-BCT về điện nước...) -> Kế hoạch hành động cụ thể từng bước -> Lưu ý quan trọng để bảo vệ quyền lợi.\n\n"
                           . "### 4. Phạm vi hoạt động & Chỉ dẫn tư vấn tìm kiếm thông minh\n"
                           . "- **Phạm vi nền tảng Toàn quốc (63 tỉnh thành):** RentHome là nền tảng hoạt động trên phạm vi Toàn quốc (63 tỉnh thành Việt Nam), không chỉ riêng Hà Nội hay TP.HCM. Sẵn sàng tìm kiếm tại bất kỳ địa phương nào (Hải Phòng, Đà Nẵng, Cần Thơ, Bình Dương, Nha Trang, Huế, Vũng Tàu, Bắc Ninh...) hoặc trên Toàn quốc khi người dùng yêu cầu.\n"
                           . "- **Hỏi lại khi thiếu dữ liệu:** Nếu người dùng yêu cầu tìm phòng/nhà một cách chung chung mà chưa đưa ra địa điểm hay mức giá mong muốn, AI phải hỏi lại một cách lịch thiệp, chu đáo về khu vực và tầm giá trước khi gợi ý phòng.\n"
                           . "- **Tìm nhiều khu vực cùng lúc:** Nếu người dùng tìm kiếm cùng lúc nhiều tỉnh thành (ví dụ: \"Hà Nội và HCM\"), hãy phân tích, so sánh và tư vấn các lựa chọn ở cả hai khu vực một cách cân đối, rành mạch và rõ ràng.\n\n"
                           . "### 5. Cơ sở tri thức chuẩn của RentHome\n"
                           . "- Người thuê (Tài khoản cá nhân): Miễn phí 100% (0 VNĐ phí môi giới, kết nối trực tiếp chính chủ). Chỉ có quyền tìm và thuê nhà, KHÔNG ĐƯỢC đăng bài.\n"
                           . "- Chủ nhà (Tài khoản Doanh nghiệp / Quản lý tòa nhà): Đăng tin miễn phí 100%, thời gian kiểm duyệt bài 15 - 30 phút.\n"
                           . "- Tiền cọc: Phổ biến là 1 tháng tiền phòng; hoàn trả 100% khi hết hợp đồng và bàn giao đủ tài sản (Điều 328 BLDS 2015).\n"
                           . "- Giá điện nước: Thực hiện đúng quy định của Nhà nước, xử phạt 7 - 10 triệu đồng nếu chủ trọ thu sai giá theo Nghị định 17/2022/NĐ-CP.\n"
                           . "- Khiếu nại / Phản ánh vi phạm: Hỗ trợ tiếp nhận xác minh và can thiệp qua Hotline 1900 8888 hoặc chat.";

        $contents = [];
        foreach (array_slice($history, -4) as $h) {
            $contents[] = [
                'role' => ($h['role'] === 'user') ? 'user' : 'model',
                'parts' => [['text' => $h['text']]],
            ];
        }

        $promptText = "Người dùng ({$userName}) vừa gửi tin nhắn: \"{$message}\"\n\n";
        if (!empty($cards)) {
            $promptText .= "Danh sách phòng trọ tìm thấy trong hệ thống:\n";
            foreach ($cards as $c) {
                $promptText .= "- {$c['title']} | Giá: {$c['price']} | Diện tích: {$c['area']} | Địa chỉ: {$c['address']}\n";
            }
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $promptText]],
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}";

        $res = Http::timeout(10)->post($url, [
            'system_instruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 1200,
            ],
        ]);

        if ($res->successful()) {
            $json = $res->json();
            return $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        Log::warning('Gemini API Non-200: ' . $res->status() . ' - ' . $res->body());
        return null;
    }

    /**
     * Kịch bản tư vấn nhẹ nhàng, chu đáo khi không gọi được Gemini
     */
    protected function generateSoftConsultantReply(string $message, array $cards, array $meta, string $userName): string
    {
        $greeting = $this->getTimeBasedGreeting($userName);
        $q = mb_strtolower($message, 'UTF-8');

        // Khách chào hỏi thông thường
        if (preg_match('/^(xin chào|chào|hello|hi|alo|chào em|em ơi|bạn ơi)/u', trim($q))) {
            return $greeting
                . "Tôi là **Chuyên gia Tư vấn Cấp cao RentHome**. Tôi sẵn sàng hỗ trợ bạn về các chức năng cốt lõi: 💡\n\n"
                . "• 🏢 **Lọc tòa nhà & loại hình:** Chung cư mini, căn hộ dịch vụ, phòng trọ, KTX, nhà riêng, biệt thự liền kề, mặt bằng kinh doanh...\n"
                . "• 💰 **Lọc mức giá:** Dưới mức giá, trên mức giá hoặc khoảng giá phù hợp với ngân sách.\n"
                . "• 📐 **Lọc diện tích (m²):** Tìm kiếm căn đúng số m² mong muốn hoặc các căn có diện tích gần tương đương.\n"
                . "• 🔍 **Tìm bài đăng:** Tra cứu các phòng/nhà đang còn trống tại 63 tỉnh thành trên Toàn quốc.\n"
                . "• 📖 **Hướng dẫn thuê nhà:** Tư vấn các bước liên hệ, xem phòng, hợp đồng điện tử và nhận hóa đơn.\n\n"
                . "👉 {$userName} hãy nhắn trực tiếp khu vực, mức giá hoặc diện tích mong muốn để tôi hỗ trợ ngay nhé!";
        }

        // 1. Trường hợp người dùng tìm kiếm chung chung cần hỏi lại khu vực & mức giá (Clarification)
        if (!empty($meta['needs_clarification'])) {
            $loaiHinh = $meta['property_type_requested'] ?? 'phòng / căn hộ';
            return "Chào **{$userName}**! Mình rất sẵn lòng hỗ trợ bạn tìm {$loaiHinh} ưng ý ạ. 🏠✨\n\n"
                 . "Để mình lọc chính xác nhất các căn phù hợp và tránh mất thời gian của bạn, bạn cho mình biết thêm nhé:\n"
                 . "1. 📍 **Bạn muốn tìm ở khu vực nào?** (Ví dụ: Hà Nội, TP.HCM, Đà Nẵng hay quận/huyện, tỉnh thành cụ thể nào?)\n"
                 . "2. 💰 **Khoảng mức giá bạn dự định thuê là bao nhiêu triệu/tháng?** (Hoặc diện tích khoảng bao nhiêu m²?)\n\n"
                 . "👉 Bạn cứ nhắn trực tiếp khu vực, tầm giá hoặc diện tích, mình sẽ lọc danh sách các căn tốt nhất gửi bạn ngay nhé!\n\n"
                 . "<div style=\"display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px;\">\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Cầu Giấy, Hà Nội tầm 3 - 5 triệu')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📍 Cầu Giấy, Hà Nội tầm 3 - 5 triệu</button>\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Quận 1, TP.HCM tầm 5 - 8 triệu')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📍 Quận 1, TP.HCM tầm 5 - 8 triệu</button>\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Hải Châu, Đà Nẵng tầm 3 - 5 triệu')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📍 Hải Châu, Đà Nẵng tầm 3 - 5 triệu</button>\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Tìm phòng / nhà trên Toàn quốc')\" style=\"background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">🌐 Tìm phòng / nhà trên Toàn quốc</button>\n"
                 . "</div>";
        }

        // Tình huống tìm kiếm không có căn nào đúng tiêu chí
        if ($meta['is_search'] && !$meta['is_exact_match']) {
            $isNationwide = !empty($meta['is_nationwide']) || ($meta['location_requested'] === 'Toàn quốc');
            $loc = $meta['location_requested'] ? "tại khu vực **{$meta['location_requested']}**" : "";
            $type = !empty($meta['property_type_requested']) ? "loại hình **{$meta['property_type_requested']}**" : "";
            $priceText = '';
            if (!empty($meta['price_label'])) {
                $op = $meta['price_operator'] ?? 'around';
                if ($op === 'under' || $op === 'over') {
                    $priceText = "mức giá **{$meta['price_label']}**";
                } elseif ($op === 'range') {
                    $priceText = "khoảng giá **{$meta['price_label']}**";
                } else {
                    $priceText = "tầm giá **{$meta['price_label']}**";
                }
            }
            $areaText = '';
            if (!empty($meta['area_label'])) {
                $opArea = $meta['area_operator'] ?? 'around';
                if ($opArea === 'under' || $opArea === 'over') {
                    $areaText = "diện tích **{$meta['area_label']}**";
                } elseif ($opArea === 'range') {
                    $areaText = "khoảng diện tích **{$meta['area_label']}**";
                } else {
                    $areaText = "diện tích khoảng **{$meta['area_label']}**";
                }
            }

            $criteriaList = array_filter([$loc, $type, $priceText, $areaText]);
            $criteriaStr = !empty($criteriaList) ? ' (' . implode(', ', $criteriaList) . ')' : '';

            $reply = $greeting
                . "Hiện tại trong cơ sở dữ liệu của RentHome tạm thời chưa có căn nào khớp hoàn toàn tiêu chí{$criteriaStr} đang trống ạ.\n\n";

            if ($meta['has_alternatives'] && count($cards) > 0) {
                if (!empty($meta['target_area'])) {
                    $leadText = "Hiện tại trong cơ sở dữ liệu chưa có căn đúng chính xác **{$meta['area_label']}**, nhưng mình đã chọn lọc các căn có diện tích gần số m² này nhất ở ngay bên dưới để {$userName} tham khảo nhé: 🏠✨\n\n";
                } elseif ($isNationwide) {
                    $leadText = "Hệ thống đã chọn lọc các căn nổi bật trên **Toàn quốc** phù hợp với nhu cầu của {$userName} ở ngay bên dưới: 🏠✨\n\n";
                } else {
                    $leadText = "Tuy nhiên, để {$userName} không mất thời gian chờ đợi, **tôi xin phép gửi bạn một số lựa chọn khác đang có sẵn ở bên dưới** để tham khảo trước nhé ạ:\n\n";
                }

                $reply = $greeting . $leadText
                       . "💡 *Gợi ý*: Bạn có thể mở rộng tìm kiếm sang các loại hình hoặc quận/huyện lân cận để có thêm nhiều lựa chọn đẹp hơn nhé.\n\n"
                       . "{$userName} có muốn tôi hỗ trợ tìm kiếm thêm ở khu vực nào khác không?";
            } else {
                $reply .= "💡 {$userName} có thể cân nhắc mở rộng tiêu chí tìm kiếm sang các quận lân cận, hoặc nhắn lại cho tôi để được hỗ trợ lọc thêm nhé!";
            }

            return $reply;
        }

        // Tình huống tìm thấy phòng khớp tiêu chí thực sự
        if ($meta['is_search'] && $meta['is_exact_match'] && count($cards) > 0) {
            $isNationwide = !empty($meta['is_nationwide']) || ($meta['location_requested'] === 'Toàn quốc');
            $loc = $meta['location_requested'] ? "tại khu vực **{$meta['location_requested']}**" : '';
            $type = !empty($meta['property_type_requested']) ? "loại hình **{$meta['property_type_requested']}**" : '';
            $priceText = '';
            if (!empty($meta['price_label'])) {
                $op = $meta['price_operator'] ?? 'around';
                if ($op === 'under' || $op === 'over') {
                    $priceText = "mức giá **{$meta['price_label']}**";
                } elseif ($op === 'range') {
                    $priceText = "khoảng giá **{$meta['price_label']}**";
                } else {
                    $priceText = "tầm giá **{$meta['price_label']}**";
                }
            }
            $areaText = '';
            if (!empty($meta['area_label'])) {
                $opArea = $meta['area_operator'] ?? 'around';
                if ($opArea === 'under' || $opArea === 'over') {
                    $areaText = "diện tích **{$meta['area_label']}**";
                } elseif ($opArea === 'range') {
                    $areaText = "khoảng diện tích **{$meta['area_label']}**";
                } else {
                    $areaText = "diện tích khoảng **{$meta['area_label']}**";
                }
            }
            $criteriaList = array_filter([$loc, $type, $priceText, $areaText]);
            $criteriaStr = !empty($criteriaList) ? ' (' . implode(', ', $criteriaList) . ')' : '';

            if (!empty($meta['has_alternatives']) && !empty($meta['target_area'])) {
                $introHeading = "Mình đã chọn lọc được căn có diện tích đúng **{$meta['area_label']}** và một số căn có diện tích gần tương đương ở ngay bên dưới cho {$userName}: 🏠✨";
            } elseif ($isNationwide) {
                $introHeading = "Mình đã chọn lọc các căn nổi bật trên **Toàn quốc** phù hợp với nhu cầu của {$userName} ở ngay bên dưới: 🏠✨";
            } else {
                $introHeading = "Mình đã chọn lọc được các căn rất phù hợp với đúng tiêu chí của {$userName}{$criteriaStr} ở ngay bên dưới: 🏠✨";
            }

            return $greeting
                . $introHeading . "\n\n"
                . "Các căn này đều đã được kiểm duyệt kỹ về thông tin và giá cả minh bạch. Bạn có thể bấm vào thẻ bên dưới để xem chi tiết ảnh và thông tin liên hệ nhé ạ.\n\n"
                . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                . "• *Làm sao để hẹn xem phòng trực tiếp?* -> Bạn bấm vào thẻ phòng bên dưới, chọn **\"Gọi điện thoại\"** hoặc **\"Nhắn tin Zalo\"** để hẹn trực tiếp chính chủ xem phòng hoàn toàn miễn phí.\n"
                . "• *Khi đi xem phòng cần kiểm tra những gì?* -> Nên kiểm tra áp lực nước, đồng hồ điện riêng, an ninh khóa vân tay/thẻ từ và hỏi rõ các phụ phí (vệ sinh, rác, thang máy, wifi).\n\n"
                . "{$userName} có cần tôi lọc thêm phòng có ban công, thang máy hay cho nuôi thú cưng không ạ?";
        }

        // Mặc định
        return $greeting
            . "Tôi là **Chuyên gia Tư vấn Cấp cao RentHome**, luôn túc trực hỗ trợ bạn 24/7.\n\n"
            . "{$userName} đang cần **tìm kiếm phòng trọ**, **hỏi đáp thủ tục thuê nhà (FAQ)** về hợp đồng, tiền cọc, điện nước? Hãy nhắn ngay cho tôi nhé!";
    }
}