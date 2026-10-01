<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Complaint;
use App\Models\Image;
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
     * Nhận tin nhắn chat từ người dùng và xử lý thông minh:
     * 1. Nhận diện FAQ / Giải đáp thắc mắc: Chào hỏi tên người dùng, gợi ý câu hỏi, trả lời 100% sự thật chuẩn xác.
     * 2. Nhận diện luồng Tự động đăng tin / Sửa bài / Hủy bài / Xác nhận đăng bài.
     * 3. Nhận diện khiếu nại, phản ánh tin giả, lừa đảo.
     * 4. Tìm kiếm phòng trọ trong Database và tư vấn chuyên nghiệp.
     */
    public function ask(string $message, array $history = [], $currentUser = null, array $uploadedImageIds = []): array
    {
        $cleanMsg = trim($message);
        $userSalutation = $this->resolveUserSalutation($currentUser);
        $qLower = mb_strtolower($cleanMsg, 'UTF-8');

        // 0.0 NẾU ĐANG CHỜ TIÊU CHÍ TÌM PHÒNG (KHU VỰC & MỨC GIÁ): TRÁNH BỊ LUỒNG ĐĂNG BÀI BẮT NHẦM
        if (session()->get('ai_search_waiting_criteria', false) === true) {
            $isExplicitPostCommand = (
                $qLower === 'đăng bài' || $qLower === 'dang bai' ||
                str_starts_with($qLower, 'tôi muốn đăng') || str_starts_with($qLower, 'muốn đăng') ||
                str_starts_with($qLower, 'cần đăng') || str_starts_with($qLower, 'hãy giúp tôi đăng') ||
                str_starts_with($qLower, 'giúp tôi đăng')
            );
            $isExplicitComplaintCommand = (
                $qLower === 'khiếu nại' || $qLower === 'tố cáo' ||
                str_starts_with($qLower, 'tôi muốn khiếu nại') || str_starts_with($qLower, 'muốn khiếu nại')
            );

            if (!$isExplicitPostCommand && !$isExplicitComplaintCommand) {
                // Người dùng đang trả lời thông tin tìm phòng (ví dụ: "Quận 1, TP.HCM tầm 5 - 8 triệu")
                // Xóa cờ chờ và chuyển thẳng xuống tìm kiếm phòng trong Database, TUYỆT ĐỐI KHÔNG nhảy vào handleAutoPostingFlow()
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
            } else {
                session()->forget('ai_search_waiting_criteria');
            }
        }

        // 0. ƯU TIÊN TIẾP NHẬN KHI ĐANG TRONG TIẾN TRÌNH KHIẾU NẠI HOẶC NGƯỜI DÙNG NÊU Ý ĐỊNH KHIẾU NẠI RÕ RÀNG
        if (session()->has('ai_complaint_pending') || 
            session()->has('ai_complaint_confirming') ||
            str_starts_with($qLower, 'tôi muốn khiếu nại') || 
            str_starts_with($qLower, 'tôi cần khiếu nại') || 
            str_starts_with($qLower, 'muốn khiếu nại') || 
            $qLower === 'khiếu nại' || 
            $qLower === 'tố cáo' ||
            str_contains($qLower, 'khiếu nại chủ trọ') ||
            str_contains($qLower, 'khiếu nại bài đăng')) {
            $complaintResult = $this->handleComplaintFlow($cleanMsg, $currentUser, $userSalutation, $uploadedImageIds);
            if ($complaintResult !== null) {
                return $complaintResult;
            }
        }

        // 1. NHẬN DIỆN CÂU HỎI THẮC MẮC CỤ THỂ (SỰ THẬT 100%, ĐÚNG TRỌNG TÂM, KHÔNG ẢO - 4 PHẦN CHUYÊN GIA)
        $specificFaqReply = $this->matchSpecificFaqQuestion($cleanMsg, $userSalutation);
        if ($specificFaqReply !== null) {
            if (session()->has('ai_auto_posting_draft')) {
                $draft = session()->get('ai_auto_posting_draft');
                if (empty($draft['price']) && empty($draft['area'])) {
                    session()->forget('ai_auto_posting_draft');
                }
            }

            return [
                'reply' => $specificFaqReply,
                'cards' => [],
            ];
        }

        // 2. NHẬN DIỆN YÊU CẦU TRỢ GIÚP / TƯ VẤN / GIẢI ĐÁP CHUNG (FAQ / CẦN TRỢ GIÚP / HỖ TRỢ)
        if ($this->isGeneralFaqPrompt($cleanMsg)) {
            if (session()->has('ai_auto_posting_draft')) {
                $draft = session()->get('ai_auto_posting_draft');
                if (empty($draft['price']) && empty($draft['area'])) {
                    session()->forget('ai_auto_posting_draft');
                }
            }

            return [
                'reply' => $this->generateFaqIntroReply($userSalutation),
                'cards' => [],
            ];
        }

        // 3. XỬ LÝ LUỒNG TỰ ĐỘNG ĐĂNG BÀI QUA AI (ĐĂNG / SỬA / HỦY / XÁC NHẬN)
        $postingResult = $this->handleAutoPostingFlow($cleanMsg, $currentUser, $uploadedImageIds, $userSalutation);
        if ($postingResult !== null) {
            return $postingResult;
        }

        // 4. XỬ LÝ LUỒNG KHIẾU NẠI & BÁO CÁO SỰ CỐ THÔNG MINH
        $complaintResult = $this->handleComplaintFlow($cleanMsg, $currentUser, $userSalutation, $uploadedImageIds);
        if ($complaintResult !== null) {
            return $complaintResult;
        }

        // 5. TÌM KIẾM PHÒNG TRONG DATABASE (CHỈ KHI CÓ NHU CẦU TÌM KIẾM THỰC SỰ)
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

        // 6. NẾU CÓ GEMINI API KEY, ƯU TIÊN GEMINI PHẢN HỒI THEO KIẾN THỨC CHUẨN THỰC TẾ
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

        // 7. KỊCH BẢN TƯ VẤN CHUYÊN VIÊN BẤT ĐỘNG SẢN THỰC THẾ (FALLBACK)
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
        return "Xin chào **{$userName}**! Tôi là **Chuyên gia Tư vấn Cấp cao RentHome**. Bạn cần giải đáp thắc mắc hoặc tư vấn chuyên sâu về vấn đề gì ạ? 💡✨\n\n"
             . "Tôi sẵn sàng phân tích và giải đáp chuẩn xác, thấu đáo theo quy định pháp luật Việt Nam và cơ chế vận hành của RentHome về:\n"
             . "• 📝 **Đăng bài cho thuê:** Hướng dẫn cách đăng bài, đăng tin miễn phí qua web & AI Chat...\n"
             . "• 💰 **Chi phí & Thời gian:** Đăng tin có mất phí không, thời gian duyệt bài bao lâu...\n"
             . "• 🤝 **Phí môi giới & Thuê phòng:** RentHome có thu phí người thuê không, kết nối chính chủ...\n"
             . "• 📝 **Hợp đồng & Tiền cọc:** Thuê nhà có phải cọc không, mức cọc, quy trình lập hợp đồng, lấy lại tiền cọc (Bộ luật Dân sự 2015)...\n"
             . "• ⚡ **Điện nước & Giá phòng:** Quy định giá điện nước, chủ trọ có được tự ý tăng giá không...\n"
             . "• 🏢 **Quản lý vận hành:** Quản lý tòa nhà, phòng trọ, chốt số điện nước, xuất hóa đơn...\n"
             . "• 🚨 **An toàn & Khiếu nại:** Báo cáo tin giả, xử lý tranh chấp phòng trọ...\n\n"
             . "👉 **{$userName} hãy nhắn trực tiếp câu hỏi cụ thể**, hoặc bấm nhanh vào các câu hỏi thường gặp bên dưới:\n"
             . "<div style=\"margin-top: 10px; display: flex; flex-direction: column; gap: 6px;\">\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Hướng dẫn cách đăng bài')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">📝 Hướng dẫn cách đăng bài cho thuê trên RentHome?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('RentHome hỗ trợ những tính năng gì?')\" style=\"text-align: left; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dbeafe'\" onmouseout=\"this.style.background='#eff6ff'\">🏠 RentHome hỗ trợ những tính năng gì cho người thuê và chủ nhà?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Thuê nhà có phải đặt cọc không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">📝 Thuê nhà có phải đặt cọc không? Cọc bao nhiêu?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Hợp đồng thuê nhà làm như thế nào?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">📑 Hợp đồng thuê nhà làm như thế nào? Lưu ý gì?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Thời gian duyệt bài đăng là bao lâu?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">⏳ Thời gian duyệt bài đăng là bao lâu?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Đăng tin trên RentHome có mất phí không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">💰 Đăng tin trên RentHome có mất phí không?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('RentHome có thu phí môi giới không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">🤝 Thuê phòng có mất phí môi giới không?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Chủ nhà không trả tiền cọc thì làm thế nào?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">⚖️ Chủ trọ không trả tiền cọc thì làm thế nào?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Chủ trọ có được tự ý tăng giá phòng không?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">📈 Chủ trọ có được tự ý tăng giá phòng không?</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Quy định về giá điện nước phòng trọ như thế nào?')\" style=\"text-align: left; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;\" onmouseover=\"this.style.background='#dcfce7'\" onmouseout=\"this.style.background='#f0fdf4'\">⚡ Quy định về giá điện nước phòng trọ?</button>\n"
             . "</div>";
    }

    /**
     * Khớp chính xác câu hỏi cụ thể và giải đáp theo cấu trúc chuẩn chuyên gia tư vấn cấp cao (4 phần)
     */
    protected function matchSpecificFaqQuestion(string $msg, string $userName): ?string
    {
        $q = mb_strtolower(trim($msg), 'UTF-8');

        // Chuẩn hóa teencode & từ ngữ tiếng lóng phổ biến
        $normalized = ' ' . $q . ' ';
        $normalized = preg_replace('/\b(k|ko|khong|kh|hong|hông|khum|hok)\b/ui', 'không', $normalized);
        $normalized = preg_replace('/\b(đc|dc)\b/ui', 'được', $normalized);
        $normalized = preg_replace('/\b(ntn|sao)\b/ui', 'thế nào', $normalized);
        $normalized = preg_replace('/\b(p|phg|ptro)\b/ui', 'phòng', $normalized);
        $normalized = trim($normalized);

        // Bỏ qua giải đáp FAQ tĩnh nếu người dùng đang có nhu cầu tìm phòng cụ thể:
        // (Có động từ tìm phòng HOẶC có địa điểm quận/huyện đi kèm mức giá / diện tích)
        $hasSearchIntent = preg_match('/\b(tìm|tim|kiếm|kiem|cần thuê|can thue|muốn thuê|muon thue|gợi ý phòng|có phòng nào|co phong nao|những phòng nào|còn phòng nào)\b/ui', $q);
        $hasLocationKeyword = preg_match('/\b(cầu giấy|đống đa|ba đình|hai bà trưng|thanh xuân|hoàng mai|hà đông|nam từ liêm|bắc từ liêm|tây hồ|long biên|gia lâm|quận 1|quận 2|quận 3|quận 4|quận 5|quận 7|quận 10|bình thạnh|gò vấp|tân bình|thủ đức|hà nội|hồ chí minh|sài gòn|đà nẵng)\b/ui', $q);
        $hasPriceKeyword = preg_match('/(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|k|nghìn)\b/ui', $q);

        if ($hasSearchIntent || ($hasLocationKeyword && $hasPriceKeyword)) {
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
                     . "Hôm nay {$userName} đang cần mình hỗ trợ tìm phòng ở khu vực nào, hay đang muốn đăng bài cho thuê phòng thế nào nè?";
            }

            // Giới thiệu chung về RentHome
            return "Chào **{$userName}**! **RentHome** là nền tảng kết nối trực tiếp và minh bạch giữa người thuê và chính chủ nhà trọ, giúp loại bỏ hoàn toàn các khâu trung gian ăn chênh lệch giá. 🏠✨\n\n"
                 . "**Các tiện ích nổi bật RentHome hỗ trợ bạn:**\n"
                 . "• **Dành cho Người tìm phòng:** Tìm kiếm thông minh theo khu vực, mức giá, diện tích; kết nối trực tiếp chính chủ xem phòng với **0 VNĐ phí môi giới**.\n"
                 . "• **Dành cho Chủ trọ:** Đăng tin cho thuê hoàn toàn **miễn phí 100%**, tiếp cận nhanh chóng hàng ngàn khách thuê có nhu cầu thực tế.\n"
                 . "• **Tư vấn & Hỗ trợ pháp lý:** Hướng dẫn hợp đồng thuê, quy định tiền cọc (Điều 328 BLDS 2015), định mức điện nước và hỗ trợ giải quyết sự cố, phản ánh vi phạm.\n\n"
                 . "👉 {$userName} có thể thử ngay bằng cách nhắn ví dụ: *\"Tìm phòng Cầu Giấy tầm 4 triệu\"* hoặc *\"Tôi muốn đăng tin cho thuê phòng\"* nhé!";
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

        // 4. CHỦ ĐỀ: PHÍ ĐĂNG TIN TRÊN RENTHOME
        if ((str_contains($q, 'đăng tin') || str_contains($q, 'đăng bài') || str_contains($q, 'tạo bài')) &&
            (str_contains($q, 'mất phí') || str_contains($q, 'chi phí') || str_contains($q, 'bao nhiêu tiền') || str_contains($q, 'có tốn phí') || str_contains($q, 'tính phí') || str_contains($q, 'miễn phí') || str_contains($q, 'mất tiền') || str_contains($q, 'tốn tiền'))) {
            return "🎯 **1. Kết luận / Giải pháp nhanh:**\n"
                 . "**HOÀN TOÀN MIỄN PHÍ 100% (0 VNĐ).** Bất kỳ cá nhân, chủ trọ hay doanh nghiệp cho thuê nào đều được đăng tin miễn phí không giới hạn trên RentHome.\n\n"
                 . "🔍 **2. Chi tiết & Phân tích chính sách:**\n"
                 . "• Miễn phí toàn bộ cả hình thức tự đăng bài trên website và đăng bài tự động qua Trợ lý AI Chat.\n"
                 . "• Không phát sinh bất kỳ khoản phí khởi tạo, phí duy trì bài đăng hay hoa hồng chiết khấu nào khi tìm được khách thuê.\n\n"
                 . "📋 **3. Kế hoạch hành động:**\n"
                 . "• **Cách 1 (Qua AI Chat):** Bạn chỉ cần nhắn thông tin phòng (địa chỉ, giá, diện tích...) và gửi kèm ảnh tại khung chat này, AI sẽ tự động soạn tin chuẩn SEO và đăng lên hệ thống giúp bạn.\n"
                 . "• **Cách 2 (Qua Website):** Truy cập mục **\"Đăng bài\"** trên thanh điều hướng để điền form chi tiết.\n\n"
                 . "💡 **4. Lưu ý & Khuyến nghị chuyên gia:**\n"
                 . "Cung cấp đầy đủ tiện ích có sẵn (điều hòa, nóng lạnh, giờ tự do) và hình ảnh thật của phòng để tin đăng tiếp cận khách thuê nhanh hơn gấp 3 lần.\n\n"
                 . "🔮 **Lường trước băn khoăn tiếp theo của bạn:**\n"
                 . "• *Tôi có thể đăng tối đa bao nhiêu bài?* -> Không giới hạn số lượng bài đăng, bạn có thể tạo nhiều bài cho từng phòng khác nhau.\n"
                 . "• *Khi có khách thuê rồi thì xử lý bài đăng thế nào?* -> Vào trang Quản lý bài đăng chọn \"Đã cho thuê\" để ẩn bài, tránh bị làm phiền.";
        }

        // 4B. CHỦ ĐỀ: HƯỚNG DẪN CÁCH ĐĂNG BÀI / QUY TRÌNH ĐĂNG BÀI TRÊN RENTHOME
        $isGuideToPost = (str_contains($q, 'đăng bài') || str_contains($q, 'đăng tin') || str_contains($q, 'lên bài') || str_contains($q, 'tạo bài') || str_contains($q, 'tạo tin')) &&
                         (str_contains($q, 'hướng dẫn') || str_contains($q, 'cách') || str_contains($q, 'làm sao') || str_contains($q, 'thế nào') || str_contains($q, 'như nào') || str_contains($q, 'như thế nào') || str_contains($q, 'quy trình') || str_contains($q, 'thủ tục') || str_contains($q, 'ở đâu') || str_contains($q, 'bước nào'));

        if ($isGuideToPost) {
            return "Chào **{$userName}**! Để đăng bài cho thuê phòng/nhà trên RentHome hoàn toàn miễn phí 100%, bạn có thể chọn 1 trong 2 cách sau nhé: 📝✨\n\n"
                 . "🔹 **Cách 1: Tự đăng qua Website** (Chủ động điền form)\n"
                 . "• Bấm vào nút **'Đăng tin'** ở thanh menu trên cùng.\n"
                 . "• Điền địa chỉ, giá thuê, diện tích, tải ảnh thực tế và bấm Xác nhận.\n\n"
                 . "🔹 **Cách 2: Đăng tự động qua Trợ lý AI** (Nhanh chóng & Tiện lợi nhất)\n"
                 . "• Bạn không cần điền form, chỉ cần nhắn thông tin phòng và gửi ảnh trực tiếp tại khung chat này, mình sẽ tự động soạn tin chuẩn SEO và lên bài giúp bạn luôn!\n\n"
                 . "👉 Nếu bạn muốn mình hỗ trợ đăng bài tự động ngay bây giờ, hãy nhắn: **'Hãy giúp tôi đăng bài'** hoặc bấm vào nút bên dưới nhé!\n"
                 . "<div style=\"margin-top: 8px;\"><button type=\"button\" onclick=\"sendQuickPrompt('Hãy giúp tôi đăng bài')\" style=\"background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;\">🚀 Hãy giúp tôi đăng bài</button></div>";
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
     * Xử lý trọn vẹn luồng Tự động đăng tin qua AI Chat
     */
    protected function handleAutoPostingFlow(string $message, $currentUser, array $uploadedImageIds = [], string $userName = 'bạn'): ?array
    {
        $q = mb_strtolower(trim($message), 'UTF-8');
        $draft = session()->get('ai_auto_posting_draft', null);

        // 1. Kiểm tra ý định HỦY đăng bài
        $cancelKeywords = ['hủy', 'hủy bài', 'hủy đăng', 'không đăng nữa', 'thôi không đăng', 'dừng lại', 'bỏ qua', 'cancel'];
        foreach ($cancelKeywords as $ck) {
            if ($q === $ck || preg_match('/\b' . preg_quote($ck, '/') . '\b/u', $q)) {
                if ($draft !== null) {
                    session()->forget('ai_auto_posting_draft');
                    return [
                        'reply' => "Dạ mình đã **hủy bản nháp đăng tin** thành công rồi ạ! 🗑️\n\n"
                                 . "Bất cứ lúc nào {$userName} có nhu cầu đăng bài mới, tìm kiếm phòng trọ hay cần giải đáp thắc mắc (FAQ), cứ nhắn cho mình biết nhé!",
                        'cards' => [],
                    ];
                }
            }
        }

        // 2. Kiểm tra ý định XÁC NHẬN ĐĂNG BÀI
        $confirmKeywords = ['đăng bài ngay', 'xác nhận đăng', 'đồng ý đăng', 'đăng luôn', 'tôi muốn đăng bài này', 'ok đăng đi', 'duyệt đăng'];
        $isConfirm = false;
        if ($draft !== null) {
            foreach ($confirmKeywords as $ck) {
                if ($q === $ck || str_contains($q, $ck)) {
                    $isConfirm = true;
                    break;
                }
            }
        }

        if ($isConfirm && $draft !== null) {
            return $this->finalizeAndCreatePost($draft, $currentUser, $uploadedImageIds, $userName);
        }

        // 3. Kiểm tra ý định bắt đầu đăng bài: "Tôi cần đăng bài", "Tạo cho tôi bài đăng", "Đăng tin cho thuê"...
        $startPostingKeywords = [
            'đăng bài', 'dang bai', 'tôi muốn đăng bài', 'muốn đăng bài', 'cần đăng bài',
            'hãy giúp tôi đăng bài', 'giúp tôi đăng bài', 'tạo bài đăng', 'đăng tin',
            'tôi muốn đăng tin', 'muốn đăng tin', 'cần đăng tin', 'tôi cần đăng bài',
            'đăng phòng', 'tôi có phòng cho thuê', 'có phòng muốn cho thuê', 'bắt đầu đăng bài',
            'hỗ trợ tôi đăng bài', 'đăng bài giúp tôi', 'tạo cho tôi bài đăng',
            'đăng tin cho thuê', 'đăng giúp tôi', 'lên bài giúp tôi', 'tạo tin đăng',
            'bắt đầu đăng tin'
        ];
        $isStartPosting = false;
        if ($q === 'đăng bài' || $q === 'dang bai') {
            $isStartPosting = true;
        } else {
            foreach ($startPostingKeywords as $spk) {
                if ($q === $spk || str_contains($q, $spk)) {
                    $isStartPosting = true;
                    break;
                }
            }
        }

        // 4. Kiểm tra xem tin nhắn có thông số bài đăng cụ thể hay không (giá và diện tích)
        $hasPostingInfo = $this->containsPostingDetails($q);
        $hasBothPriceAndArea = (bool) preg_match('/(?:giá|thuê|\b)(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|nghìn|k|đ|vnd|đồng)/ui', $q) 
                            && (bool) preg_match('/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét vuông)/ui', $q);

        // Trường hợp người dùng có draft nhưng lại nhắn tìm phòng hoặc hỏi câu khác không liên quan đến phòng
        if ($draft !== null && !$hasPostingInfo && !$isStartPosting) {
            // Nếu draft chỉ mới khởi tạo rỗng, hủy draft để người dùng thoải mái tìm phòng / hỏi đáp
            if (empty($draft['price']) && empty($draft['area'])) {
                session()->forget('ai_auto_posting_draft');
                $draft = null;
            }
        }

        // Nếu câu chat là yêu cầu bắt đầu đăng bài mà CHƯA có thông số phòng cụ thể:
        // BẤT KỂ trước đó có $draft hay không, HÃY RESET HOÀN TOÀN:
        if ($isStartPosting && !$hasBothPriceAndArea && !$hasPostingInfo) {
            session()->forget('ai_auto_posting_draft');
            session()->put('ai_auto_posting_draft', [
                'property_type' => 'phong_tro',
                'property_type_name' => 'Phòng trọ',
                'ready_to_publish' => false,
                'images' => $uploadedImageIds,
            ]);

            return [
                'reply' => "Dạ tuyệt vời quá! Em rất sẵn lòng hỗ trợ **{$userName}** soạn và đăng bài tự động lên RentHome hoàn toàn miễn phí ạ! 📝✨\n\n"
                         . "Để bài đăng đầy đủ, thu hút và được duyệt nhanh nhất trong 15 - 30 phút, {$userName} vui lòng gửi cho em các thông tin sau nhé:\n"
                         . "1. 📍 **Địa chỉ phòng:** (Số nhà, ngõ/đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố)\n"
                         . "2. 💰 **Giá thuê:** (Ví dụ: 3.5 triệu/tháng hoặc 3tr5/tháng)\n"
                         . "3. 📐 **Diện tích:** (Ví dụ: 25m2, 30m2...)\n"
                         . "4. 🛋️ **Tiện ích:** (Điều hòa, nóng lạnh, máy giặt, ban công, giờ tự do...)\n"
                         . "5. 📸 **Hình ảnh:** (Bạn bấm vào nút Kẹp ghim / Máy ảnh bên dưới để gửi từ 1 - 5 ảnh thực tế của phòng)\n\n"
                         . "👉 {$userName} cứ nhắn một câu đầy đủ hoặc gửi ảnh kèm mô tả, em sẽ tự động gom lại thành bài đăng hoàn chỉnh ngay ạ!\n\n"
                         . "<div style=\"display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;\">\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Căn hộ / Chung cư')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 6px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer;\">🏢 Căn hộ / Chung cư</button>\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Nhà nguyên căn')\" style=\"background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 6px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer;\">🏡 Nhà nguyên căn</button>\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Phòng trọ')\" style=\"background: #fefce8; border: 1px solid #fef08a; color: #854d0e; padding: 6px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer;\">🏠 Phòng trọ</button>\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Mặt bằng / Đất nền')\" style=\"background: #faf5ff; border: 1px solid #e9d5ff; color: #6b21a8; padding: 6px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer;\">🏬 Mặt bằng kinh doanh</button>\n"
                         . "</div>",
                'cards' => [],
            ];
        }

        // Nếu người dùng cung cấp thông tin phòng hoặc đang sửa bản nháp hiện tại
        if ($hasPostingInfo || ($draft !== null && (count($uploadedImageIds) > 0 || str_contains($q, 'sửa') || str_contains($q, 'thay đổi')))) {
            $updatedDraft = $this->extractPostingDetails($message, $q, $draft ?? []);
            if (!empty($uploadedImageIds)) {
                $existingImages = $updatedDraft['images'] ?? [];
                $updatedDraft['images'] = array_values(array_unique(array_merge($existingImages, $uploadedImageIds)));
            }

            session()->put('ai_auto_posting_draft', $updatedDraft);

            return [
                'reply' => $this->generatePostSummaryHtml($updatedDraft, $userName),
                'cards' => [],
            ];
        }

        return null;
    }

    /**
     * Nhận diện xem tin nhắn có chứa các thông số của bài đăng phòng trọ hay không
     */
    protected function containsPostingDetails(string $q): bool
    {
        // 1. Nếu có từ khóa tìm kiếm -> BẮT BUỘC trả về false để nhường quyền cho luồng Tìm phòng
        if (preg_match('/\b(tìm|tim|kiếm|kiem|cần thuê|can thue|muốn thuê|muon thue|gợi ý phòng|cho thuê không)\b/ui', $q)) {
            return false;
        }

        // 2. LOẠI TRỪ CÁC CÂU HỎI HƯỚNG DẪN / CÁCH THỨC / LÀM SAO / THỦ TỤC ĐĂNG BÀI
        // Nếu $q chứa các từ khóa: hướng dẫn, cách, làm sao, như thế nào, ở đâu, thủ tục, quy trình... đi cùng với đăng bài/đăng tin
        if (preg_match('/\b(hướng dẫn|cách|làm sao|như thế nào|thế nào|như nào|ở đâu|thủ tục|quy trình)\b/ui', $q) &&
            preg_match('/\b(đăng bài|đăng tin|tạo bài|tạo tin|lên bài)\b/ui', $q)) {
            return false;
        }

        // 3. LOẠI TRỪ CÁC CÂU CHỌN KHU VỰC KÈM MỨC GIÁ TÌM PHÒNG (Ví dụ: "Quận 1, TP.HCM tầm 5 - 8 triệu", "Cầu Giấy tầm 4 triệu", "tìm phòng 3tr")
        $hasBudgetSearch = (bool) preg_match('/\b(tầm|khoảng|khoang|dưới|duoi|từ|tu)\s*\d+/ui', $q);
        $hasArea = (bool) preg_match('/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét vuông)/ui', $q);

        if ($hasBudgetSearch && !$hasArea) {
            return false;
        }

        // 4. Nhận diện các thông số thực tế của bài đăng
        $hasPrice = (bool) preg_match('/(?:giá|thuê)\s*(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|nghìn|k|đ|vnd|đồng)/ui', $q)
                 || (bool) preg_match('/(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|k)\b/ui', $q);

        $hasEdit = (bool) preg_match('/(?:sửa|đổi|thay|chỉnh)\s*(?:thành|lại|từ|diện tích|giá|địa chỉ|mô tả|loại hình)/ui', $q);
        
        $hasExplicitPost = (
            str_contains($q, 'tôi có phòng cho thuê') ||
            str_contains($q, 'có phòng cho thuê') ||
            str_contains($q, 'chính chủ cho thuê') ||
            str_contains($q, 'đăng bài này giúp tôi') ||
            str_contains($q, 'đăng tin này giúp tôi') ||
            str_contains($q, 'thông tin phòng:') ||
            str_contains($q, 'thông tin phòng')
        );

        $hasTypeSelection = str_starts_with($q, 'loại hình:');

        // BẮT BUỘC có cả GIÁ TIỀN VÀ DIỆN TÍCH đi cùng nhau (ví dụ: "phòng 4tr 25m2"), hoặc từ chỉ định sửa đổi, hoặc câu khẳng định cho thuê
        if ($hasPrice && $hasArea) {
            return true;
        }

        if ($hasEdit || $hasExplicitPost || $hasTypeSelection) {
            return true;
        }

        return false;
    }

    /**
     * Trích xuất các trường thông tin bài đăng từ tin nhắn của người dùng
     */
    protected function extractPostingDetails(string $rawMessage, string $q, array $draft): array
    {
        // 1. Loại hình bất động sản (chỉ gán khi người dùng nêu rõ hoặc chọn, không gán mặc định)
        if (preg_match('/\b(căn hộ|chung cư|chung cu|ccmn|studio|condo|apartment)\b/ui', $q)) {
            $draft['property_type'] = 'ch';
            $draft['property_type_name'] = 'Căn hộ / Chung cư';
        } elseif (preg_match('/\b(nhà nguyên căn|nguyên căn|nha nguyen can|nhà riêng|nha rieng|nhà phố|nha pho)\b/ui', $q)) {
            $draft['property_type'] = 'nnc';
            $draft['property_type_name'] = 'Nhà nguyên căn';
        } elseif (preg_match('/\b(mặt bằng|mat bang|đất nền|dat nen|đất|dat|cửa hàng|kiot|văn phòng|van phong)\b/ui', $q)) {
            $draft['property_type'] = 'dat_nen';
            $draft['property_type_name'] = 'Mặt bằng / Đất nền';
        } elseif (preg_match('/\b(phòng trọ|phong tro|nhà trọ|nha tro|phòng khép kín|phong khep kin|phòng cho thuê|phong cho thue)\b/ui', $q)) {
            $draft['property_type'] = 'phong_tro';
            $draft['property_type_name'] = 'Phòng trọ';
        }

        // 2. Trích xuất mức giá (VND) - Hỗ trợ cả sửa: "sửa giá từ 4tr thành 3.5tr"
        if (preg_match('/(?:sửa|thay|đổi)\s*(?:giá|mức giá)?\s*(?:từ\s*\d+(?:[.,]\d+)?\s*(?:triệu|tr)?\s*)?thành\s*(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|k|nghìn|đ|vnd|đồng)?/ui', $q, $m)) {
            $val = (float) str_replace(',', '.', $m[1]);
            if (str_contains($m[0], 'k') || str_contains($m[0], 'nghìn') || $val >= 500) {
                $draft['price'] = (int) ($val * 1000);
            } else {
                $draft['price'] = (int) ($val * 1000000);
            }
        } elseif (preg_match('/(?:giá|thuê|khoảng|tầm)?\s*(\d+(?:[.,]\d+)?)\s*(?:triệu|tr)(?!\s*m2)/ui', $q, $m)) {
            $val = (float) str_replace(',', '.', $m[1]);
            $draft['price'] = (int) ($val * 1000000);
        } elseif (preg_match('/(?:giá|thuê)?\s*(\d+(?:[.,]\d+)?)\s*(?:k|nghìn)\s*(?:đ|vnd|đồng)?/ui', $q, $m)) {
            $val = (float) str_replace(',', '.', $m[1]);
            $draft['price'] = (int) ($val * 1000);
        }

        // 3. Trích xuất diện tích (m2) - Hỗ trợ cả sửa: "sửa diện tích từ 80m2 thành 70m2"
        if (preg_match('/(?:sửa|thay|đổi)\s*(?:diện tích|dt)?\s*(?:từ\s*\d+(?:[.,]\d+)?\s*(?:m2|m²)?\s*)?thành\s*(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét vuông)?/ui', $q, $m)) {
            $draft['area'] = (float) str_replace(',', '.', $m[1]);
        } elseif (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét vuông)/ui', $q, $m)) {
            $draft['area'] = (float) str_replace(',', '.', $m[1]);
        }

        // 4. Trích xuất Quận/Huyện & Tỉnh/Thành phố (Không hardcode Hà Nội/HCM, xác định theo thông tin người dùng)
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
            'hoài đức' => ['district' => 'Hoài Đức', 'province' => 'Hà Nội'],
            'thanh trì' => ['district' => 'Thanh Trì', 'province' => 'Hà Nội'],
            'đông anh' => ['district' => 'Đông Anh', 'province' => 'Hà Nội'],
            'sơn tây' => ['district' => 'Sơn Tây', 'province' => 'Hà Nội'],

            // TP. Hồ Chí Minh
            'quận 1' => ['district' => 'Quận 1', 'province' => 'Hồ Chí Minh'],
            'quận 2' => ['district' => 'Quận 2', 'province' => 'Hồ Chí Minh'],
            'quận 3' => ['district' => 'Quận 3', 'province' => 'Hồ Chí Minh'],
            'quận 4' => ['district' => 'Quận 4', 'province' => 'Hồ Chí Minh'],
            'quận 5' => ['district' => 'Quận 5', 'province' => 'Hồ Chí Minh'],
            'quận 6' => ['district' => 'Quận 6', 'province' => 'Hồ Chí Minh'],
            'quận 7' => ['district' => 'Quận 7', 'province' => 'Hồ Chí Minh'],
            'quận 8' => ['district' => 'Quận 8', 'province' => 'Hồ Chí Minh'],
            'quận 9' => ['district' => 'Quận 9', 'province' => 'Hồ Chí Minh'],
            'quận 10' => ['district' => 'Quận 10', 'province' => 'Hồ Chí Minh'],
            'quận 11' => ['district' => 'Quận 11', 'province' => 'Hồ Chí Minh'],
            'quận 12' => ['district' => 'Quận 12', 'province' => 'Hồ Chí Minh'],
            'bình thạnh' => ['district' => 'Bình Thạnh', 'province' => 'Hồ Chí Minh'],
            'gò vấp' => ['district' => 'Gò Vấp', 'province' => 'Hồ Chí Minh'],
            'tân bình' => ['district' => 'Tân Bình', 'province' => 'Hồ Chí Minh'],
            'tân phú' => ['district' => 'Tân Phú', 'province' => 'Hồ Chí Minh'],
            'phú nhuận' => ['district' => 'Phú Nhuận', 'province' => 'Hồ Chí Minh'],
            'thủ đức' => ['district' => 'Thủ Đức', 'province' => 'Hồ Chí Minh'],
            'bình tân' => ['district' => 'Bình Tân', 'province' => 'Hồ Chí Minh'],

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

        $foundProvince = null;
        $commonProvinces = [
            'Hồ Chí Minh' => '/\b(hcm|tp\.?\s*hcm|tphcm|sài\s*gòn|sai\s*gon|hồ\s*chí\s*minh|ho\s*chi\s*minh)\b/ui',
            'Hà Nội' => '/\b(hà\s*nội|ha\s*noi|hn|thủ\s*đô)\b/ui',
            'Đà Nẵng' => '/\b(đà\s*nẵng|da\s*nang)\b/ui',
            'Hải Phòng' => '/\b(hải\s*phòng|hai\s*phong)\b/ui',
            'Cần Thơ' => '/\b(cần\s*thơ|can\s*tho)\b/ui',
            'Bình Dương' => '/\b(bình\s*dương|binh\s*duong)\b/ui',
            'Đồng Nai' => '/\b(đồng\s*nai|dong\s*nai|biên\s*hòa)\b/ui',
            'Bà Rịa - Vũng Tàu' => '/\b(vũng\s*tàu|bà\s*rịa)\b/ui',
            'Quảng Ninh' => '/\b(quảng\s*ninh|hạ\s*long)\b/ui',
            'Bắc Ninh' => '/\b(bắc\s*ninh)\b/ui',
            'Bắc Giang' => '/\b(bắc\s*giang)\b/ui',
            'Thừa Thiên Huế' => '/\b(thừa\s*thiên\s*huế|huế)\b/ui',
            'Khánh Hòa' => '/\b(khánh\s*hòa|nha\s*trang)\b/ui',
            'Lâm Đồng' => '/\b(lâm\s*đồng|đà\s*lạt)\b/ui',
        ];

        foreach ($commonProvinces as $pName => $pattern) {
            if (preg_match($pattern, $q)) {
                $foundProvince = $pName;
                break;
            }
        }

        foreach ($districtMap as $kw => $info) {
            if (str_contains($q, $kw)) {
                $draft['district'] = $info['district'];
                if (!$foundProvince) {
                    $foundProvince = $info['province'];
                }
                break;
            }
        }

        if ($foundProvince) {
            $draft['province'] = $foundProvince;
        }

        // Địa chỉ chi tiết nếu có nhắc tên đường/phố/ngõ
        if (preg_match('/(?:ở|tại|địa chỉ|ngõ|đường|phố)\s+([^,.\n]+)/ui', $rawMessage, $m)) {
            $potentialAddr = trim($m[1]);
            // Làm sạch nếu dính các từ khóa giá hoặc diện tích
            $cleanedAddr = preg_replace('/(?:giá|diện tích|\d+\s*(?:tr|triệu|m2|m²)).*/ui', '', $potentialAddr);
            $cleanedAddr = trim($cleanedAddr);
            if (mb_strlen($cleanedAddr, 'UTF-8') >= 3 && !in_array(mb_strtolower($cleanedAddr, 'UTF-8'), ['hà nội', 'hồ chí minh', 'phòng trọ', 'căn hộ'])) {
                $draft['address'] = $cleanedAddr;
            }
        }

        if (empty($draft['address']) && !empty($draft['district'])) {
            $draft['address'] = $draft['district'] . (!empty($draft['province']) ? (', ' . $draft['province']) : '');
        }

        // 5. Số phòng ngủ & phòng tắm
        if (preg_match('/(\d+)\s*(?:pn|phòng ngủ)/ui', $q, $m)) {
            $draft['bedrooms'] = (int) $m[1];
        }
        if (preg_match('/(\d+)\s*(?:wc|vệ sinh|phòng tắm)/ui', $q, $m)) {
            $draft['bathrooms'] = (int) $m[1];
        }

        // 6. Tiện ích
        $amenityMap = [
            'dieu_hoa' => ['điều hòa', 'máy lạnh', 'máy điều hòa'],
            'nong_lanh' => ['nóng lạnh', 'bình nóng lạnh', 'máy nước nóng'],
            'may_giat' => ['máy giặt'],
            'tu_lanh' => ['tủ lạnh'],
            'giuong_tu' => ['giường', 'tủ', 'full đồ', 'đầy đủ đồ', 'nội thất'],
            'wifi' => ['wifi', 'mạng', 'internet'],
            'ban_cong' => ['ban công', 'cửa sổ thoáng', 'thoáng mát'],
            'thang_may' => ['thang máy'],
            'cho_de_xe' => ['chỗ để xe', 'để xe', 'nhà xe'],
            'khong_chung_chu' => ['không chung chủ', 'giờ tự do', 'tự do'],
            've_sinh_khep_kin' => ['khép kín', 'vệ sinh riêng'],
        ];

        $existingAmenities = $draft['amenities'] ?? [];
        foreach ($amenityMap as $code => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($q, $kw) && !in_array($code, $existingAmenities)) {
                    $existingAmenities[] = $code;
                    break;
                }
            }
        }
        $draft['amenities'] = $existingAmenities;

        // 7. Mô tả phòng chi tiết
        if (preg_match('/(?:mô tả|chi tiết|ghi chú|nội dung)\s*:\s*([^.\n]+)/ui', $rawMessage, $m)) {
            $draft['description'] = trim($m[1]);
        } elseif (empty($draft['description'])) {
            $draft['description'] = $this->generatePostDescription($draft);
        }

        return $draft;
    }

    /**
     * Tự động sinh nội dung mô tả phòng chuyên nghiệp, hấp dẫn chuẩn SEO
     */
    protected function generatePostDescription(array $draft): string
    {
        $typeName = $draft['property_type_name'] ?? 'Phòng trọ';
        $district = $draft['district'] ?? 'trung tâm';
        $area = isset($draft['area']) ? $draft['area'] . 'm²' : 'rộng rãi';
        $amenities = $draft['amenities'] ?? [];

        $amenityLabels = [
            'dieu_hoa' => 'Điều hòa mát lạnh',
            'nong_lanh' => 'Bình nóng lạnh',
            'may_giat' => 'Máy giặt tiện lợi',
            'tu_lanh' => 'Tủ lạnh',
            'giuong_tu' => 'Giường tủ đầy đủ',
            'wifi' => 'Wifi tốc độ cao',
            'ban_cong' => 'Ban công thoáng mát',
            'thang_may' => 'Thang máy di chuyển',
            'cho_de_xe' => 'Khu để xe an ninh',
            'khong_chung_chu' => 'Giờ giấc tự do, không chung chủ',
            've_sinh_khep_kin' => 'Vệ sinh khép kín sạch sẽ',
        ];

        $amenityTexts = [];
        foreach ($amenities as $am) {
            if (isset($amenityLabels[$am])) {
                $amenityTexts[] = '• ' . $amenityLabels[$am];
            }
        }
        $amenityBlock = !empty($amenityTexts) ? implode("\n", $amenityTexts) : "• Đầy đủ trang thiết bị cơ bản, sạch sẽ dọn vào ở ngay";

        return "Cho thuê {$typeName} chất lượng cao tại {$district}. Diện tích {$area} thoáng mát, thiết kế hiện đại.\n\n"
             . "🌟 **Trang bị tiện nghi nổi bật:**\n"
             . "{$amenityBlock}\n\n"
             . "An ninh cực tốt, camera giám sát 24/7, khu dân trí cao, di chuyển thuận tiện tới các tuyến đường lớn, trường học và chợ.";
    }

    /**
     * Tạo bảng tóm tắt nội dung bài đăng kèm 3 nút: [Đăng bài ngay], [Sửa thông tin], [Hủy đăng bài]
     */
    protected function generatePostSummaryHtml(array $draft, string $userName): string
    {
        $typeName = $draft['property_type_name'] ?? 'Phòng trọ';
        $priceStr = isset($draft['price']) ? number_format($draft['price'], 0, ',', '.') . ' VNĐ/tháng' : '<span style="color: #ef4444;">Chưa có (cần bổ sung)</span>';
        $areaStr = isset($draft['area']) ? $draft['area'] . ' m²' : '<span style="color: #ef4444;">Chưa có (cần bổ sung)</span>';
        $districtStr = $draft['district'] ?? ($draft['province'] ?? '<span style="color: #ef4444;">Chưa rõ (cần bổ sung)</span>');
        $addressStr = $draft['address'] ?? $districtStr;

        $amenityLabels = [
            'dieu_hoa' => 'Điều hòa', 'nong_lanh' => 'Nóng lạnh', 'may_giat' => 'Máy giặt',
            'tu_lanh' => 'Tủ lạnh', 'giuong_tu' => 'Giường tủ', 'wifi' => 'Wifi',
            'ban_cong' => 'Ban công', 'thang_may' => 'Thang máy', 'cho_de_xe' => 'Chỗ để xe',
            'khong_chung_chu' => 'Giờ tự do', 've_sinh_khep_kin' => 'Khép kín',
        ];
        $amenities = $draft['amenities'] ?? [];
        $amenityNames = [];
        foreach ($amenities as $am) {
            $amenityNames[] = $amenityLabels[$am] ?? $am;
        }
        $amenityStr = !empty($amenityNames) ? implode(', ', $amenityNames) : 'Cơ bản';

        $imgCount = isset($draft['images']) ? count($draft['images']) : 0;
        $imgStr = $imgCount > 0 ? "<b>{$imgCount} ảnh đã đính kèm 📸</b>" : "<i>Chưa có ảnh (sẽ dùng ảnh minh họa)</i>";

        $desc = $draft['description'] ?? $this->generatePostDescription($draft);
        $shortDesc = mb_strlen($desc, 'UTF-8') > 120 ? mb_substr($desc, 0, 117, 'UTF-8') . '...' : $desc;

        // Kiểm tra xem đã đủ điều kiện đăng chưa
        $hasType = !empty($draft['property_type']) && !empty($draft['property_type_name']);
        $typeName = $hasType ? $draft['property_type_name'] : '<span style="color: #ef4444; font-weight: 700;">Chưa chọn (bấm chọn bên dưới)</span>';
        $priceStr = isset($draft['price']) ? number_format($draft['price'], 0, ',', '.') . ' VNĐ/tháng' : '<span style="color: #ef4444; font-weight: 700;">Chưa có (cần bổ sung)</span>';
        $areaStr = isset($draft['area']) ? $draft['area'] . ' m²' : '<span style="color: #ef4444; font-weight: 700;">Chưa có (cần bổ sung)</span>';
        $hasLocation = !empty($draft['district']) || !empty($draft['province']) || !empty($draft['address']);
        $districtStr = $draft['district'] ?? ($draft['province'] ?? '<span style="color: #ef4444; font-weight: 700;">Chưa rõ (cần bổ sung)</span>');
        $addressStr = $draft['address'] ?? $districtStr;

        $missingFields = [];
        if (!$hasType) $missingFields[] = 'Loại hình BĐS';
        if (empty($draft['price'])) $missingFields[] = 'Giá thuê';
        if (empty($draft['area'])) $missingFields[] = 'Diện tích';
        if (!$hasLocation) $missingFields[] = 'Khu vực / Địa chỉ';

        $isReady = empty($missingFields);
        $draft['ready_to_publish'] = $isReady;

        $notice = $isReady
            ? "<div style=\"margin-top: 10px; color: #15803d; font-weight: 600;\">✅ Thông tin đã rất đầy đủ và chuẩn xác! Bạn có thể bấm <b>\"🚀 Đăng bài ngay\"</b> để hoàn tất.</div>"
            : "<div style=\"margin-top: 10px; color: #b45309; font-weight: 600;\">⚠️ Vui lòng bổ sung thêm: <b>" . implode(', ', $missingFields) . "</b> để bài viết sẵn sàng đăng nhé!</div>";

        $tableTitle = $hasType ? "🏠 BẢNG TỔNG HỢP: " . mb_strtoupper($draft['property_type_name'], 'UTF-8') : "🏠 BẢNG TỔNG HỢP BÀI ĐĂNG";

        $typeQuickButtons = "";
        if (!$hasType) {
            $typeQuickButtons = "<div style=\"margin: 10px 0;\">\n"
                              . "  <div style=\"font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 4px;\">👇 Bấm chọn nhanh loại hình BĐS:</div>\n"
                              . "  <div style=\"display: flex; gap: 6px; flex-wrap: wrap;\">\n"
                              . "    <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Căn hộ / Chung cư')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏢 Căn hộ / Chung cư</button>\n"
                              . "    <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Nhà nguyên căn')\" style=\"background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏡 Nhà nguyên căn</button>\n"
                              . "    <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Phòng trọ')\" style=\"background: #fefce8; border: 1px solid #fef08a; color: #854d0e; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏠 Phòng trọ</button>\n"
                              . "    <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Mặt bằng / Đất nền')\" style=\"background: #faf5ff; border: 1px solid #e9d5ff; color: #6b21a8; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏬 Mặt bằng kinh doanh</button>\n"
                              . "  </div>\n"
                              . "</div>\n";
        }

        $areaPrompt = !empty($draft['area']) ? $draft['area'] : '80';

        return "Dạ em đã tổng hợp và soạn sẵn nội dung bài đăng cho **{$userName}** như sau ạ: 📋✨\n\n"
             . "<div style=\"background: #ffffff; border: 1.5px solid #10b981; border-radius: 12px; padding: 12px 14px; box-shadow: 0 4px 12px rgba(16,185,129,0.12); margin: 6px 0;\">\n"
             . "  <div style=\"font-size: 14.5px; font-weight: 700; color: #047857; border-bottom: 1px dashed #cbd5e1; padding-bottom: 6px; margin-bottom: 8px;\">{$tableTitle}</div>\n"
             . "  <div style=\"display: grid; grid-template-columns: 1fr; gap: 4px; font-size: 13px; color: #334155;\">\n"
             . "    <div>🏷️ <b>Loại hình:</b> {$typeName}</div>\n"
             . "    <div>💰 <b>Mức giá:</b> {$priceStr}</div>\n"
             . "    <div>📐 <b>Diện tích:</b> {$areaStr}</div>\n"
             . "    <div>📍 <b>Khu vực:</b> {$addressStr}</div>\n"
             . "    <div>🛋️ <b>Tiện ích:</b> {$amenityStr}</div>\n"
             . "    <div>🖼️ <b>Hình ảnh:</b> {$imgStr}</div>\n"
             . "    <div style=\"margin-top: 4px;\">📄 <b>Mô tả:</b> <i>{$shortDesc}</i></div>\n"
             . "  </div>\n"
             . "  {$notice}\n"
             . "</div>\n\n"
             . $typeQuickButtons
             . "💡 *Bạn có thể nói: \"Sửa diện tích từ {$areaPrompt}m2 thành 70m2\", \"Đổi giá thành 4 triệu\" hoặc dùng các nút thao tác bên dưới:*\n\n"
             . "<div style=\"display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;\">\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Đăng bài ngay')\" style=\"background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 2px 6px rgba(16,185,129,0.3);\">🚀 Đăng bài ngay</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Tôi muốn sửa thông tin bài đăng')\" style=\"background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;\">✏️ Sửa thông tin</button>\n"
             . "  <button type=\"button\" onclick=\"sendQuickPrompt('Hủy đăng bài')\" style=\"background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;\">❌ Hủy đăng bài</button>\n"
             . "</div>";
    }

    /**
     * Tạo bài đăng chính thức vào MongoDB và gửi thông báo qua Telegram/Email cho BQT
     */
    protected function finalizeAndCreatePost(array $draft, $currentUser, array $uploadedImageIds = [], string $userName = 'bạn'): array
    {
        // Kiểm tra tính đầy đủ của bài đăng
        $missing = [];
        if (empty($draft['property_type'])) $missing[] = 'Loại hình BĐS';
        if (empty($draft['price'])) $missing[] = 'Giá thuê';
        if (empty($draft['area'])) $missing[] = 'Diện tích';
        if (empty($draft['district']) && empty($draft['province']) && empty($draft['address'])) $missing[] = 'Khu vực / Địa chỉ';

        if (!empty($missing)) {
            $typeButtons = empty($draft['property_type']) ? "<div style=\"display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;\">\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Căn hộ / Chung cư')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏢 Căn hộ / Chung cư</button>\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Nhà nguyên căn')\" style=\"background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏡 Nhà nguyên căn</button>\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Phòng trọ')\" style=\"background: #fefce8; border: 1px solid #fef08a; color: #854d0e; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏠 Phòng trọ</button>\n"
                         . "  <button type=\"button\" onclick=\"sendQuickPrompt('Loại hình: Mặt bằng / Đất nền')\" style=\"background: #faf5ff; border: 1px solid #e9d5ff; color: #6b21a8; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;\">🏬 Mặt bằng kinh doanh</button>\n"
                         . "</div>" : "";

            return [
                'reply' => "Dạ bài đăng vẫn còn thiếu thông tin quan trọng: **" . implode(', ', $missing) . "**.\n\n"
                         . "{$userName} vui lòng nhắn bổ sung (hoặc bấm chọn nút nhanh bên dưới) để em hoàn thiện và đăng bài ngay nhé ạ! 📝✨\n\n"
                         . $typeButtons,
                'cards' => [],
            ];
        }

        try {
            $title = $this->generateCatchyPostTitle($draft);
            $desc = $draft['description'] ?? $this->generatePostDescription($draft);

            // Xử lý ảnh: ưu tiên ảnh người dùng upload, nếu không có lấy ảnh mẫu có sẵn
            $images = $draft['images'] ?? [];
            if (!empty($uploadedImageIds)) {
                $images = array_values(array_unique(array_merge($images, $uploadedImageIds)));
            }
            if (empty($images)) {
                $sampleImg = Image::first();
                if ($sampleImg) {
                    $images[] = (string) $sampleImg->_id;
                }
            }

            $authorId = $currentUser ? (string)$currentUser->_id : null;
            if (!$authorId) {
                $firstUser = \App\Models\User::first();
                $authorId = $firstUser ? (string)$firstUser->_id : '60c72b2f9b1d8b2bad6e8a10';
            }

            $province = $draft['province'] ?? 'Toàn quốc';
            $district = $draft['district'] ?? ($draft['province'] ?? 'Chưa cập nhật');
            $address = $draft['address'] ?? ($draft['district'] ? ($draft['district'] . (!empty($draft['province']) ? ', ' . $draft['province'] : '')) : $province);

            $post = Post::create([
                'user_id' => $authorId,
                'title' => $title,
                'description' => $desc,
                'price' => (int) $draft['price'],
                'price_unit' => 'tháng',
                'area' => (float) $draft['area'],
                'province' => $province,
                'district' => $district,
                'ward' => $draft['ward'] ?? '',
                'address' => $address,
                'property_type' => $draft['property_type'] ?? 'phong_tro',
                'bedrooms' => $draft['bedrooms'] ?? 1,
                'bathrooms' => $draft['bathrooms'] ?? 1,
                'amenities' => $draft['amenities'] ?? [],
                'images' => $images,
                'status' => 'pending', // Chờ ban quản trị kiểm duyệt 15-30p
            ]);

            // Gửi thông báo khẩn cấp kèm ảnh tới Admin qua Telegram Bot và Email
            try {
                $notificationService = app(SupportNotificationService::class);
                $notificationService->notifyNewPostCreated($post, $currentUser);
            } catch (\Exception $e) {
                Log::error('SupportNotificationService notifyNewPostCreated error: ' . $e->getMessage());
            }

            // Xóa session draft sau khi tạo thành công
            session()->forget('ai_auto_posting_draft');

            $postUrl = url('/baidang/' . $post->_id);

            $reply = "🎉 **Chúc mừng {$userName}! Bài đăng của bạn đã được tạo thành công!** 🚀✨\n\n"
                   . "<div style=\"background: #f0fdf4; border: 1.5px solid #22c55e; border-radius: 12px; padding: 14px; margin: 8px 0; color: #14532d;\">\n"
                   . "  <div style=\"font-weight: 700; font-size: 14.5px; margin-bottom: 6px;\">✅ BÀI ĐĂNG ĐÃ VÀO HÀNG ĐỢI DUYỆT</div>\n"
                   . "  <div>📝 <b>Tiêu đề:</b> {$title}</div>\n"
                   . "  <div>💰 <b>Mức giá:</b> " . number_format($post->price, 0, ',', '.') . " VNĐ/tháng</div>\n"
                   . "  <div>📐 <b>Diện tích:</b> {$post->area} m²</div>\n"
                   . "  <div>📍 <b>Địa chỉ:</b> {$post->address}</div>\n"
                   . "  <div>⏳ <b>Trạng thái:</b> <span style=\"background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 11.5px;\">Chờ duyệt (15 - 30 phút)</span></div>\n"
                   . "</div>\n\n"
                   . "🛡️ **Thông tin thêm cho {$userName}:**\n"
                   . "• Bài đăng đã được gửi tới Ban Kiểm duyệt để ưu tiên duyệt trong vòng 15 - 30 phút.\n"
                   . "• Bạn có thể xem ngay bài đăng tại đây: [Xem bài đăng vừa tạo]({$postUrl})\n\n"
                   . "Cảm ơn {$userName} đã tin tưởng đồng hành cùng RentHome! Bạn cần mình giải đáp thêm điều gì nữa không ạ?";

            return [
                'reply' => $reply,
                'cards' => [],
                'post_created' => [
                    'id' => (string) $post->_id,
                    'title' => $title,
                    'url' => $postUrl,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('AI Create Post Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $currentUser ? (string)$currentUser->_id : null,
            ]);
            return [
                'reply' => "Hệ thống đang bận cập nhật dữ liệu một chút, {$userName} vui lòng thử lại sau ít phút hoặc nhắn lại giúp mình nhé!",
                'cards' => [],
            ];
        }
    }

    /**
     * Sinh tiêu đề bài đăng hấp dẫn, chuẩn phong cách BĐS
     */
    protected function generateCatchyPostTitle(array $draft): string
    {
        $typeName = $draft['property_type_name'] ?? 'Phòng trọ';
        $location = !empty($draft['district'])
            ? ($draft['district'] . (!empty($draft['province']) ? ', ' . $draft['province'] : ''))
            : ($draft['province'] ?? 'Việt Nam');
        $area = isset($draft['area']) ? $draft['area'] . 'm²' : '';
        $room = !empty($draft['bedrooms']) ? "({$draft['bedrooms']}PN)" : '';

        $amenities = $draft['amenities'] ?? [];
        $extra = '';
        if (in_array('khong_chung_chu', $amenities)) {
            $extra = ' - Giờ tự do, không chung chủ';
        } elseif (in_array('giuong_tu', $amenities) || in_array('dieu_hoa', $amenities)) {
            $extra = ' - Đầy đủ tiện nghi, sạch đẹp';
        }

        return trim("Cho thuê {$typeName} {$room} tại {$location}" . ($area ? " - {$area}" : "") . $extra);
    }

    /**
     * Xử lý trọn vẹn luồng Khiếu nại / Báo cáo sự cố thông minh:
     * - Tránh tạo mã #KN bừa bãi khi người dùng chỉ than phiền hoặc nhắc đến "lừa đảo", "bùng cọc".
     * - Bổ sung bước xác nhận ý định (Confirmation Step) khi chưa có Link bài, chưa có SĐT và chưa xác nhận.
     * - Chỉ chính thức insert vào model Complaint và gửi thông báo tới BQT khi người dùng đã xác nhận hoặc cung cấp rõ link bài/SĐT đối tượng.
     */
    protected function handleComplaintFlow(string $message, $currentUser, string $userName, array $uploadedImageIds = []): ?array
    {
        $q = mb_strtolower(trim($message), 'UTF-8');
        $isPending = session()->get('ai_complaint_pending', false);
        $isConfirming = session()->get('ai_complaint_confirming', false);

        if (!empty($uploadedImageIds)) {
            session()->put('ai_complaint_image', $uploadedImageIds[0]);
        }

        // Kiểm tra xem tin nhắn có chứa SĐT (9-11 số) hoặc link URL hay không
        $hasLink = (bool) preg_match('#https?://[^\s,]+#i', $message) || (bool) preg_match('/\b[a-zA-Z0-9-]+\.(?:com|vn|net|org)(?:\/[^\s,]*)?/i', $message);
        $hasPhone = (bool) preg_match('/(?:(?:\+?84|0)(?:[\s.-]?\d){8,10}|\b\d(?:[\s.-]?\d){8,10}\b)/', $message);
        $hasTargetData = $hasLink || $hasPhone;

        // 1. Kiểm tra ý định HỦY / TỪ CHỐI khiếu nại (Tránh False Cancel)
        // Nếu tin nhắn có chứa SĐT hoặc link URL thì tuyệt đối KHÔNG được coi là hủy đơn (kể cả khi câu bắt đầu bằng "Không")
        // Chỉ coi là từ chối/hủy khi câu ngắn dưới 25 ký tự và mang nghĩa từ chối thuần túy
        $msgLen = mb_strlen(trim($q), 'UTF-8');
        $pureDeclineKeywords = [
            'không', 'ko', 'k', 'thôi', 'thoi', 'hủy', 'cancel', 'bỏ qua', 'không cần', 'ko cần', 'k cần',
            'chỉ hỏi thôi', 'mình chỉ hỏi thôi', 'hỏi thôi', 'không khiếu nại', 'không muốn',
            'thôi không cần', 'dạ thôi', 'dạ không', 'ko đâu', 'không đâu', 'thôi bạn', 'không bạn',
            'ko bạn ơi', 'không bạn ơi', 'thôi bạn ơi', 'thôi khỏi', 'khỏi cần', 'thôi em', 'thôi ad'
        ];

        $isCancelOrDecline = false;
        if (!$hasTargetData && $msgLen < 25) {
            if (in_array($q, $pureDeclineKeywords) ||
                (preg_match('/^(không|ko|thôi|dạ thôi|dạ không)\b/ui', $q) && (str_contains($q, 'cần') || str_contains($q, 'muốn') || str_contains($q, 'khiếu nại') || in_array($q, ['không', 'ko', 'thôi', 'dạ thôi', 'dạ không', 'không nha', 'không nhé', 'không ạ', 'thôi nha', 'thôi nhé', 'thôi ạ']))) ||
                str_contains($q, 'không cần') ||
                str_contains($q, 'hủy khiếu nại') ||
                str_contains($q, 'chỉ hỏi thôi') ||
                str_contains($q, 'nói vậy thôi')) {
                $isCancelOrDecline = true;
            }
        }

        if (($isPending || $isConfirming) && $isCancelOrDecline) {
            session()->forget('ai_complaint_pending');
            session()->forget('ai_complaint_confirming');
            session()->forget('ai_complaint_draft_content');
            session()->forget('ai_complaint_image');
            return [
                'reply' => "Dạ RentHome đã ghi nhận chia sẻ của **{$userName}** nha. Nếu bạn cần hỗ trợ tìm phòng an toàn, tư vấn hợp đồng, giải đáp thủ tục cọc hay bất kỳ vấn đề nào khác, cứ nhắn cho mình nhé! 💡",
                'cards' => [],
            ];
        }

        // 2. Kiểm tra nếu đang ở bước XÁC NHẬN Ý ĐỊNH (isConfirming)
        if ($isConfirming) {
            // Nhận diện ý định tìm phòng (chứa các từ tìm kiếm phòng, khu vực, giá tiền) hoặc hỏi FAQ khác
            $hasSearchVerb = (bool) preg_match('/\b(tìm|tim|kiếm|kiem|cần thuê|can thue|muốn thuê|muon thue|gợi ý|có phòng|co phong|phòng nào|phong nao|còn phòng|con phong)\b/ui', $q);
            $hasLocation = (bool) preg_match('/\b(cầu giấy|đống đa|ba đình|hai bà trưng|thanh xuân|hoàng mai|hà đông|nam từ liêm|bắc từ liêm|tây hồ|long biên|gia lâm|quận 1|quận 2|quận 3|quận 4|quận 5|quận 7|quận 10|bình thạnh|gò vấp|tân bình|thủ đức|hà nội|hồ chí minh|sài gòn|đà nẵng|bình dương|bắc giang)\b/ui', $q);
            $hasPrice = (bool) preg_match('/(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|k|nghìn)\b/ui', $q);
            $hasFaqTopic = (bool) preg_match('/\b(duyệt bài|mất phí|bao lâu|điện nước|tăng giá|hợp đồng|phí môi giới|đăng tin|quản lý tòa nhà|faq|thắc mắc|tạm trú|giấy tờ|thủ tục)\b/ui', $q)
                || $this->isGeneralFaqPrompt($message)
                || ($this->matchSpecificFaqQuestion($message, $userName) !== null);

            $isSwitchingTopic = ($hasSearchVerb || ($hasLocation && ($hasPrice || str_contains($q, 'phòng'))) || $hasFaqTopic);

            $affirmativeKeywords = [
                'có', 'co', 'có nha', 'có nhé', 'có ạ', 'có bạn', 'có em', 'có ad',
                'đồng ý', 'dong y', 'ok', 'oke', 'okie', 'okay', 'được', 'được nha', 'được chứ',
                'lập hồ sơ', 'lập giúp', 'lập giúp mình', 'lập giúp tôi', 'giúp mình', 'giúp tôi',
                'tôi muốn', 'mình muốn', 'xác nhận', 'xác nhận lập', 'lập đi', 'tạo đơn', 'tạo hồ sơ'
            ];

            $isAffirmative = false;
            if (!$isSwitchingTopic) {
                if (in_array($q, $affirmativeKeywords) ||
                    preg_match('/^(có|co)\s*(ạ|nhé|nha|ơi|bạn|ad|admin|anh|em|lập|tạo|muốn|giúp)?$/ui', $q) ||
                    str_starts_with($q, 'đồng ý') ||
                    str_contains($q, 'lập hồ sơ') ||
                    str_contains($q, 'giúp tôi lập') ||
                    str_contains($q, 'giúp mình lập') ||
                    str_contains($q, 'xác nhận lập')) {
                    $isAffirmative = true;
                }
            } else {
                if (str_contains($q, 'lập hồ sơ khiếu nại') || str_contains($q, 'xác nhận khiếu nại')) {
                    $isAffirmative = true;
                    $isSwitchingTopic = false;
                }
            }

            // Bóc tách xem tin nhắn này hoặc tin nhắn cũ có link / sđt không
            $draftContent = session()->get('ai_complaint_draft_content', '');
            $combinedText = trim($draftContent . "\n" . $message);
            $complaintImages = !empty($uploadedImageIds) ? $uploadedImageIds : (array)session()->get('ai_complaint_image', []);
            $parsedDetails = $this->parseComplaintDetails($combinedText, $complaintImages);

            $hasTargetInfo = ($parsedDetails['link'] !== 'Chưa cung cấp link bài đăng/website')
                          || ($parsedDetails['phone'] !== 'Chưa cung cấp SĐT đối tượng');

            // XÓA TRẠNG THÁI SESSION RÒ RỈ ("Ghost" Confirmation):
            // Nếu người dùng nhắn tin mang ý định tìm phòng hoặc hỏi FAQ khác thay vì trả lời Có/Không khiếu nại,
            // tự động xóa session 'ai_complaint_confirming' và 'ai_complaint_draft_content' để không nhận nhầm chữ "ok" ở lượt chat sau
            if ($isSwitchingTopic && !$isAffirmative && !$hasTargetInfo) {
                session()->forget('ai_complaint_confirming');
                session()->forget('ai_complaint_draft_content');
                return null;
            }

            // Nếu người dùng đồng ý VÀ đã có link bài hoặc SĐT đối tượng -> Chính thức lập hồ sơ #KN!
            if ($isAffirmative && $hasTargetInfo) {
                session()->forget('ai_complaint_confirming');
                session()->forget('ai_complaint_draft_content');
                return $this->executeCreateComplaint($combinedText, $currentUser, $userName, $complaintImages, $parsedDetails);
            }

            // Nếu người dùng đồng ý nhưng VẪN CHƯA CÓ Link bài hoặc SĐT đối tượng -> Chuyển sang pending và yêu cầu bổ sung
            if ($isAffirmative && !$hasTargetInfo) {
                session()->forget('ai_complaint_confirming');
                session()->put('ai_complaint_pending', true);
                session()->put('ai_complaint_draft_content', $combinedText);

                return [
                    'reply' => "Dạ RentHome đã ghi nhận mong muốn lập hồ sơ của **{$userName}**! 🛡️\n\n"
                             . "Để Ban Quản trị có đầy đủ căn cứ xác minh và can thiệp khóa bài/chủ trọ vi phạm, bạn vui lòng gửi giúp mình:\n"
                             . "1. 🔗 **Link bài đăng** hoặc **Mã bài đăng** cần khiếu nại.\n"
                             . "2. 📞 **Số điện thoại** của đối tượng/chủ trọ.\n"
                             . "3. 📸 **Ảnh chụp bằng chứng (nếu có):** Tin nhắn, biên lai cọc...\n\n"
                             . "👉 Ngay khi nhận được thông tin, mình sẽ lập tức xuất mã hồ sơ `#KN` và chuyển thẳng tới BQT xử lý trong 15 – 30 phút ạ!",
                    'cards' => [],
                ];
            }

            // Nếu người dùng gửi trực tiếp Link hoặc SĐT trong lúc đang confirm -> Lập hồ sơ luôn!
            if ($hasTargetInfo) {
                session()->forget('ai_complaint_confirming');
                session()->forget('ai_complaint_draft_content');
                return $this->executeCreateComplaint($combinedText, $currentUser, $userName, $complaintImages, $parsedDetails);
            }
        }

        // 3. Các từ khóa người dùng NÊU Ý ĐỊNH KHIẾU NẠI RÕ RÀNG
        $complaintIntentKeywords = [
            'tôi muốn khiếu nại', 'tôi cần khiếu nại', 'muốn khiếu nại', 'cần khiếu nại',
            'hỗ trợ khiếu nại', 'cho tôi khiếu nại', 'làm sao để khiếu nại', 'khiếu nại ở đâu',
            'tôi muốn tố cáo', 'tôi cần tố cáo', 'tôi muốn báo cáo', 'tôi cần báo cáo',
            'khiếu nại chủ trọ', 'khiếu nại bài đăng', 'báo cáo bài đăng', 'tố cáo chủ trọ',
            'phản ánh chủ trọ', 'phản ánh bài đăng'
        ];

        $isIntentOnly = false;
        foreach ($complaintIntentKeywords as $cik) {
            if ($q === $cik || str_starts_with($q, $cik) || str_contains($q, $cik)) {
                if (mb_strlen($q, 'UTF-8') <= 45) {
                    $isIntentOnly = true;
                    break;
                }
            }
        }
        if ($q === 'khiếu nại' || $q === 'tố cáo' || $q === 'báo cáo sự cố' || $q === 'phản ánh') {
            $isIntentOnly = true;
        }

        // Nếu người dùng NÊU Ý ĐỊNH khiếu nại chủ động (chưa có nội dung sự việc)
        if ($isIntentOnly && !$isPending) {
            session()->put('ai_complaint_pending', true);
            return [
                'reply' => "Chào **{$userName}**! Ban Quản trị RentHome luôn sẵn sàng tiếp nhận và bảo vệ quyền lợi của khách thuê 24/7. 🛡️\n\n"
                         . "Để hệ thống lập hồ sơ khiếu nại chính xác và chuyển ngay cho Ban Kiểm duyệt xử lý, {$userName} vui lòng gửi thông tin chi tiết sự cố nhé:\n"
                         . "1. 🔗 **Link bài đăng hoặc Website** cần phản ánh.\n"
                         . "2. 📞 **Số điện thoại hoặc Mã phòng** của đối tượng/chủ trọ.\n"
                         . "3. ⚠️ **Nội dung sự việc cụ thể:** (Ví dụ: đòi tiền cọc sai thỏa thuận, phòng không đúng ảnh/giá thực tế, chủ trọ có thái độ xấu...).\n"
                         . "4. 📸 **Bằng chứng kèm theo (nếu có):** Ảnh chụp tin nhắn, biên lai chuyển tiền hoặc hiện trạng phòng.\n\n"
                         . "👉 {$userName} hãy gửi nội dung chi tiết ngay tại đây, tôi sẽ lập tức tạo mã hồ sơ `#KN` và chuyển Ban Quản trị can thiệp xử lý trong 15 – 30 phút ạ!",
                'cards' => [],
            ];
        }

        // 4. Nếu người dùng ĐANG TRONG TRẠNG THÁI PENDING (đã được yêu cầu gửi chi tiết)
        if ($isPending) {
            if (mb_strlen($q, 'UTF-8') < 8 && !preg_match('/\d+/u', $q) && empty($uploadedImageIds)) {
                return [
                    'reply' => "Dạ {$userName} vui lòng mô tả rõ hơn một chút về sự việc hoặc cung cấp Số điện thoại/Link bài đăng của chủ trọ để hệ thống có căn cứ lập hồ sơ khiếu nại giúp bạn nhé!",
                    'cards' => [],
                ];
            }

            session()->forget('ai_complaint_pending');
            $complaintImages = !empty($uploadedImageIds) ? $uploadedImageIds : (array)session()->pull('ai_complaint_image', []);
            $draftContent = session()->pull('ai_complaint_draft_content', '');
            $combinedText = trim($draftContent . "\n" . $message);
            $parsedDetails = $this->parseComplaintDetails($combinedText, $complaintImages);

            return $this->executeCreateComplaint($combinedText, $currentUser, $userName, $complaintImages, $parsedDetails);
        }

        // 5. NẾU TIN NHẮN TỰ NHIÊN CÓ CHỨA CÁC TỪ KHÓA VỀ SỰ CỐ / LỪA ĐẢO / BÙNG CỌC...
        $hasComplaintSubstance = str_contains($q, 'lừa') || str_contains($q, 'lừa đảo') || str_contains($q, 'bùng cọc') || 
                                 str_contains($q, 'treo đầu dê') || str_contains($q, 'ảnh ảo') ||
                                 str_contains($q, 'chửi bới') || str_contains($q, 'đánh đập') ||
                                 str_contains($q, 'sai địa chỉ') || str_contains($q, 'chủ trọ dọa') ||
                                 str_contains($q, 'giả mạo') || str_contains($q, 'quịt tiền') ||
                                 str_contains($q, 'gian lận') || str_contains($q, 'chiếm đoạt') ||
                                 (str_contains($q, 'khiếu nại') && mb_strlen($q, 'UTF-8') > 45);

        if ($hasComplaintSubstance) {
            // Kiểm tra xem tin nhắn có chứa Link hoặc SĐT rõ ràng không
            $complaintImages = !empty($uploadedImageIds) ? $uploadedImageIds : (array)session()->get('ai_complaint_image', []);
            $parsedDetails = $this->parseComplaintDetails($message, $complaintImages);

            $hasLinkOrPhone = ($parsedDetails['link'] !== 'Chưa cung cấp link bài đăng/website')
                           || ($parsedDetails['phone'] !== 'Chưa cung cấp SĐT đối tượng');

            // TRƯỜNG HỢP A: ĐÃ CÓ RÕ LINK BÀI HOẶC SĐT ĐỐI TƯỢNG CẦN XỬ LÝ -> Lập hồ sơ khiếu nại ngay
            if ($hasLinkOrPhone) {
                return $this->executeCreateComplaint($message, $currentUser, $userName, $complaintImages, $parsedDetails);
            }

            // TRƯỜNG HỢP B: CHƯA CÓ LINK BÀI, CHƯA CÓ SĐT VÀ CHƯA XÁC NHẬN Ý ĐỊNH
            // -> Đồng cảm trước và hỏi xác nhận, TUYỆT ĐỐI KHÔNG TẠO MÃ #KN BỪA BÃI
            session()->put('ai_complaint_confirming', true);
            session()->put('ai_complaint_draft_content', $message);

            return [
                'reply' => "Nghe có vẻ **{$userName}** đang gặp trải nghiệm không tốt/lo ngại về vấn đề này. Bạn có muốn RentHome lập hồ sơ khiếu nại chính thức để BQT xác minh và can thiệp khóa bài vi phạm không ạ?\n\n"
                         . "👉 Nếu có, {$userName} vui lòng gửi cho mình **Link bài đăng** hoặc **Số điện thoại** của đối tượng/chủ trọ kèm nội dung cụ thể, mình sẽ tạo mã hồ sơ `#KN` và chuyển Ban Quản trị xử lý ngay nhé!",
                'cards' => [],
            ];
        }

        return null;
    }

    /**
     * Tạo hồ sơ khiếu nại chính thức vào MongoDB và bắn thông báo tới BQT
     */
    protected function executeCreateComplaint(string $content, $currentUser, string $userName, array $complaintImages, array $parsedDetails): array
    {
        $firstImageId = !empty($complaintImages) ? (is_array($complaintImages) ? reset($complaintImages) : $complaintImages) : null;
        $complaintCode = 'KN-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $authorId = $currentUser ? (string)($currentUser->_id ?? ($currentUser->id ?? null)) : null;

        try {
            $complaint = Complaint::create([
                'user_id' => $authorId,
                'complaint_code' => $complaintCode,
                'content' => $content,
                'image' => $firstImageId,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            try {
                $notificationService = app(SupportNotificationService::class);
                $notificationService->notifyComplaintCreated($complaint, $currentUser, $firstImageId, $parsedDetails);
            } catch (\Exception $e) {
                Log::error('SupportNotificationService notifyComplaintCreated error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Create Complaint Error: ' . $e->getMessage());
        }

        return [
            'reply' => $this->generateComplaintReply($complaintCode, $userName, $parsedDetails),
            'cards' => [],
        ];
    }

    /**
     * Bóc tách thông tin khiếu nại của người dùng thành các phần riêng biệt:
     * 1. Link bài đăng / Website
     * 2. Số điện thoại / Mã phòng bị phản ánh (ở trên, riêng biệt không gộp chung với link)
     * 3. Nội dung sự việc cụ thể
     * 4. Bằng chứng kèm theo (kèm ảnh thumbnail nhỏ hiển thị trực tiếp bên dưới nếu có)
     */
    protected function parseComplaintDetails(string $message, $imageInput = null): array
    {
        $raw = trim($message);

        // 1. Trích xuất liên kết URL (YouTube, Facebook, Website, Link bài viết...)
        $links = [];
        if (preg_match_all('#https?://[^\s,]+#i', $raw, $matches)) {
            $links = array_values(array_unique($matches[0]));
        }

        // 2. Trích xuất số điện thoại (hỗ trợ mọi định dạng: 01..., 03..., 05..., 07..., 08..., 09..., +84...)
        $phones = [];
        if (preg_match_all('/(?:sđt|sdt|đt|dt|phone|tel|số\s*điện\s*thoại|zalo|số)?[:\s]*(?:là\s*)?((?:\+?84|0)(?:[\s.-]?\d){8,10})\b/ui', $raw, $matches)) {
            foreach ($matches[1] as $p) {
                $cleanP = preg_replace('/\D/', '', $p);
                if (strlen($cleanP) >= 9 && strlen($cleanP) <= 11) {
                    $phones[] = $cleanP;
                }
            }
        }
        if (empty($phones)) {
            if (preg_match_all('/(?:\b|\D)((?:0|\+84)(?:[\s.-]?\d){8,10})\b/', $raw, $matches)) {
                foreach ($matches[1] as $p) {
                    $cleanP = preg_replace('/\D/', '', $p);
                    if (strlen($cleanP) >= 9 && strlen($cleanP) <= 11) {
                        $phones[] = $cleanP;
                    }
                }
            }
        }
        $phones = array_values(array_unique($phones));

        // 3. Trích xuất mã bài đăng hoặc mã phòng (nếu có)
        $postCodes = [];
        if (preg_match_all('/(?:mã\s*(?:phòng|bài|tin|post)?[:\s]*#?|#)([a-zA-Z0-9]{4,})/ui', $raw, $matches)) {
            foreach ($matches[1] as $c) {
                if (!in_array($c, $links) && !in_array($c, $phones)) {
                    $postCodes[] = $c;
                }
            }
        }
        $postCodes = array_values(array_unique($postCodes));

        // 4. Làm sạch để lấy nội dung sự việc cụ thể (loại bỏ link, sđt, mã phòng đã bóc tách)
        $cleanDesc = $raw;
        foreach ($links as $l) {
            $cleanDesc = str_replace($l, '', $cleanDesc);
        }
        $cleanDesc = preg_replace('/(?:sđt|sdt|đt|dt|phone|tel|số\s*điện\s*thoại|zalo|số)?[:\s]*(?:là\s*)?(?:\+?84|0)(?:[\s.-]?\d){8,10}\b/ui', '', $cleanDesc);
        foreach ($phones as $p) {
            $cleanDesc = str_replace($p, '', $cleanDesc);
        }
        foreach ($postCodes as $c) {
            $cleanDesc = preg_replace('/(?:mã\s*(?:phòng|bài|tin|post)?[:\s]*#?|#)' . preg_quote($c, '/') . '/ui', '', $cleanDesc);
        }

        $cleanDesc = trim(preg_replace('/^[\s,\.\-–—:;]+|[\s,\.\-–—:;]+$/u', '', trim($cleanDesc)));
        $cleanDesc = preg_replace('/\s{2,}/u', ' ', $cleanDesc);

        // Chuẩn hóa kết quả theo từng phần riêng biệt (link riêng, sdt riêng không trùng)
        $linkStr = !empty($links) ? implode("\n", $links) : 'Chưa cung cấp link bài đăng/website';

        $phoneParts = [];
        if (!empty($phones)) {
            $phoneParts = array_merge($phoneParts, $phones);
        }
        if (!empty($postCodes)) {
            $phoneParts = array_merge($phoneParts, array_map(fn($c) => "Mã phòng/bài: #{$c}", $postCodes));
        }
        $phoneStr = !empty($phoneParts) ? implode(', ', $phoneParts) : 'Chưa cung cấp SĐT đối tượng';

        $issueDesc = !empty($cleanDesc) ? $cleanDesc : 'Người dùng báo cáo vi phạm qua liên kết/bằng chứng đính kèm';

        // 5. Xử lý ảnh bằng chứng hiển thị nhỏ trực tiếp dưới mục 4
        $imageIds = [];
        if (is_array($imageInput)) {
            $imageIds = array_values(array_filter($imageInput));
        } elseif (!empty($imageInput)) {
            $imageIds = [(string)$imageInput];
        }

        $imageHtml = '';
        $hasImage = !empty($imageIds);
        $evidence = $hasImage ? 'Đã đính kèm ảnh bằng chứng xác minh' : 'Không có ảnh đính kèm (cung cấp qua liên kết/nội dung)';

        if ($hasImage) {
            $thumbImgs = [];
            foreach ($imageIds as $imgId) {
                try {
                    $imgStr = (string)$imgId;
                    if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
                        $imgDoc = \App\Models\Image::find($imgStr);
                        if ($imgDoc && !empty($imgDoc->base64_data)) {
                            $mime = $imgDoc->mime_type ?: 'image/jpeg';
                            $thumbImgs[] = "data:{$mime};base64,{$imgDoc->base64_data}";
                        }
                    } elseif (str_starts_with($imgStr, 'data:image') || str_starts_with($imgStr, 'http')) {
                        $thumbImgs[] = $imgStr;
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Complaint thumb error: " . $e->getMessage());
                }
            }

            if (!empty($thumbImgs)) {
                $imageHtml = '<div style="display: flex; gap: 8px; margin-top: 6px; flex-wrap: wrap;">';
                foreach ($thumbImgs as $src) {
                    $imageHtml .= '<img src="' . $src . '" alt="Ảnh bằng chứng" style="width: 68px; height: 68px; object-fit: cover; border-radius: 8px; border: 1.5px solid #10b981; box-shadow: 0 2px 6px rgba(0,0,0,0.12); cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform=\'scale(1.06)\'" onmouseout="this.style.transform=\'scale(1)\'" onclick="window.open(this.src, \'_blank\')" title="Bấm để xem ảnh phóng to">';
                }
                $imageHtml .= '</div>';
            }
        }

        return [
            'link' => $linkStr,
            'phone' => $phoneStr,
            'issue_desc' => $issueDesc,
            'evidence' => $evidence,
            'image_html' => $imageHtml,
            'has_image' => $hasImage,
        ];
    }

    protected function generateComplaintReply(string $code, string $userName, array $parsedDetails = []): string
    {
        $link = $parsedDetails['link'] ?? 'Chưa cung cấp link bài đăng/website';
        $phone = $parsedDetails['phone'] ?? 'Chưa cung cấp SĐT đối tượng';
        $issue = $parsedDetails['issue_desc'] ?? 'Đang xác minh';
        $evidence = $parsedDetails['evidence'] ?? 'Không có ảnh đính kèm';
        $imageHtml = $parsedDetails['image_html'] ?? '';

        $evidenceSection = $evidence . ($imageHtml ? "\n" . $imageHtml : "");

        return "Chào **{$userName}**! Mình thành thật xin lỗi bạn vì trải nghiệm không mong muốn này ạ! \n\n"
             . "🛡️ **RentHome đã tiếp nhận và lập hồ sơ khiếu nại khẩn cấp `#{$code}`:**\n\n"
             . "<div style=\"background: #ffffff; border: 1.5px solid #ef4444; border-radius: 12px; padding: 12px 14px; margin: 8px 0; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.08); font-size: 13px; color: #1e293b;\">\n"
             . "  <div style=\"font-weight: 700; color: #b91c1c; margin-bottom: 8px; font-size: 13.5px; border-bottom: 1px dashed #fca5a5; padding-bottom: 6px;\">📋 CHI TIẾT HỒ SƠ KHIẾU NẠI</div>\n"
             . "  <div style=\"margin-bottom: 6px;\">🔗 <b>Link bài đăng:</b> <span style=\"word-break: break-all;\">{$link}</span></div>\n"
             . "  <div style=\"margin-bottom: 6px;\">📞 <b>Số điện thoại:</b> {$phone}</div>\n"
             . "  <div style=\"margin-bottom: 6px;\">⚠️ <b>Nội dung sự việc:</b> {$issue}</div>\n"
             . "  <div>📸 <b>Bằng chứng:</b> {$evidenceSection}</div>\n"
             . "</div>\n\n"
             . "• Toàn bộ hồ sơ đã được chuyển thẳng tới **Ban Kiểm duyệt & Quản trị viên** xử lý khẩn cấp.\n"
             . "• Bộ phận kiểm duyệt sẽ tiến hành xác minh, đối chất chủ bài đăng và xử lý tạm khóa/gỡ bỏ vi phạm trong vòng 15 – 30 phút.\n\n"
             . "{$userName} hoàn toàn yên tâm nhé ạ, quyền lợi và sự an toàn của khách thuê luôn là ưu tiên số 1 của RentHome!\n"
             . "Hotline hỗ trợ trực tiếp 24/7: `1900 8888`.";
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

        // 3. NHẬN DIỆN MỨC GIÁ
        $targetPrice = null;
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:triệu|tr)(?!\s*m2)/ui', $qLower, $matches)) {
            $num = (float) str_replace(',', '.', $matches[1]);
            $targetPrice = (int) ($num * 1000000);
            $meta['target_price'] = $targetPrice;
            $meta['price_label'] = $matches[1] . ' triệu';
        } elseif (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:k|nghìn)\s*(?:đ|vnd|đồng)?/ui', $qLower, $matches)) {
            $num = (float) str_replace(',', '.', $matches[1]);
            $targetPrice = (int) ($num * 1000);
            $meta['target_price'] = $targetPrice;
            $meta['price_label'] = $matches[1] . 'k';
        }

        // 4. NHẬN DIỆN DIỆN TÍCH (m2)
        $targetArea = null;
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:m2|m²|mét vuông)/ui', $qLower, $mArea)) {
            $targetArea = (float) str_replace(',', '.', $mArea[1]);
            $meta['target_area'] = $targetArea;
            $meta['area_label'] = $mArea[1] . ' m²';
        }

        // 5. NHẬN DIỆN ĐỘNG TỪ TÌM KIẾM
        $hasSearchVerb = (bool) preg_match('/\b(tìm|tim|kiếm|kiem|cần thuê|can thue|muốn thuê|muon thue|thuê ở|thuê tại|gợi ý|cho tôi|những căn|các căn|có phòng|co phong|phòng nào|phong nao|còn phòng|con phong)\b/ui', $qLower);

        // 6. NHẬN DIỆN LOẠI HÌNH BẤT ĐỘNG SẢN
        $propertyType = null;
        if (preg_match('/\b(căn hộ|chung cư|chung cu|ccmn|apartment|condo|studio)\b/ui', $qLower)) {
            $propertyType = 'căn hộ';
            $meta['property_type_requested'] = 'căn hộ / chung cư';
        } elseif (preg_match('/\b(phòng trọ|phong tro|phòng|phong|nhà trọ|nha tro)\b/ui', $qLower)) {
            $propertyType = 'phòng';
            $meta['property_type_requested'] = 'phòng trọ';
        } elseif (preg_match('/\b(biệt thự|villa)\b/ui', $qLower)) {
            $propertyType = 'biệt thự';
            $meta['property_type_requested'] = 'biệt thự';
        } elseif (preg_match('/\b(nhà nguyên căn|nhà ở|nha o|nhà|căn nhà)\b/ui', $qLower)) {
            $propertyType = 'nhà';
            $meta['property_type_requested'] = 'nhà nguyên căn';
        } elseif (preg_match('/\b(đất|mặt bằng|mat bang|đất nền|thổ cư)\b/ui', $qLower)) {
            $propertyType = 'đất';
            $meta['property_type_requested'] = 'mặt bằng / đất';
        }

        // 7. BỔ SUNG CƠ CHẾ HỎI LẠI (CLARIFICATION) KHI THÔNG TIN QUÁ CHUNG CHUNG
        // Nếu phát hiện người dùng có ý định tìm kiếm (có động từ tìm kiếm hoặc có loại hình bất động sản)
        // nhưng HOÀN TOÀN THIẾU ĐỊA ĐIỂM (chưa có tỉnh/thành hay quận/huyện, không phải toàn quốc) VÀ THIẾU MỨC GIÁ / DIỆN TÍCH:
        if (($hasSearchVerb || $propertyType) && empty($matchedProvinces) && empty($matchedDistricts) && !$isNationwide && empty($targetPrice) && empty($targetArea)) {
            $meta['is_search'] = true;
            $meta['needs_clarification'] = true;
            session()->put('ai_search_waiting_criteria', true);
            session()->forget('ai_auto_posting_draft');
            return ['cards' => [], 'meta' => $meta];
        }

        // 8. KIỂM TRA TỪ KHÓA CÂU HỎI THẮC MẮC VS THÔNG TIN TÌM KIẾM
        $hasValidSearchCriteria = (!empty($matchedProvinces) || !empty($matchedDistricts) || !empty($targetPrice) || !empty($targetArea) || $isNationwide);

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
        if (!$hasSearchVerb && empty($matchedProvinces) && empty($matchedDistricts) && !$isNationwide && !$targetPrice && !$targetArea && !$propertyType) {
            return ['cards' => [], 'meta' => $meta];
        }

        $meta['is_search'] = true;

        // 9. TRUY VẤN CƠ SỞ DỮ LIỆU
        $baseQuery = Post::query()->where('status', 'approved');

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
        if ($propertyType) {
            $filteredByType = $posts->filter(function ($p) use ($propertyType) {
                $text = mb_strtolower(($p->title ?? '') . ' ' . ($p->description ?? ''), 'UTF-8');
                return str_contains($text, $propertyType);
            });
            if ($filteredByType->count() > 0) {
                $candidates = $filteredByType;
            } else {
                $isExactType = false;
            }
        }

        // 11. XỬ LÝ TRƯỜNG HỢP TÌM NHIỀU TỈNH THÀNH (ĐA ĐỊA ĐIỂM)
        if (count($matchedProvinces) >= 2) {
            $cardsPerProvince = (int) ceil(4 / count($matchedProvinces));
            foreach ($matchedProvinces as $prov) {
                $provPosts = $candidates->filter(function ($p) use ($prov) {
                    $provText = mb_strtolower(($p->province ?? '') . ' ' . ($p->address ?? ''), 'UTF-8');
                    $provCheck = mb_strtolower($prov, 'UTF-8');
                    return str_contains($provText, $provCheck);
                });

                if ($targetPrice) {
                    $provMatches = $provPosts->filter(function ($p) use ($targetPrice) {
                        return $p->price >= $targetPrice * 0.75 && $p->price <= $targetPrice * 1.25;
                    });
                    $chosen = ($provMatches->count() > 0 ? $provMatches : $provPosts)->take($cardsPerProvince);
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
            if ($targetPrice) {
                $exactMatches = $candidates->filter(function ($p) use ($targetPrice) {
                    return $p->price >= $targetPrice * 0.80 && $p->price <= $targetPrice * 1.20;
                });
                $chosen = ($exactMatches->count() > 0 ? $exactMatches : $candidates)->take(4);
            } else {
                $chosen = $candidates->take(4);
            }
            foreach ($chosen as $p) {
                $cards[] = $this->transformPostToCard($p, true);
            }
            $meta['is_exact_match'] = true;
            return ['cards' => $cards, 'meta' => $meta];
        }

        // 14. LỌC THEO DIỆN TÍCH VÀ / HOẶC GIÁ TIỀN (TRƯỜNG HỢP ĐƠN ĐỊA ĐIỂM)
        if ($targetArea && $targetPrice) {
            $exactMatches = $candidates->filter(function ($p) use ($targetArea, $targetPrice) {
                $areaMatch = abs($p->area - $targetArea) <= ($targetArea * 0.20);
                $priceMatch = ($p->price >= $targetPrice * 0.80) && ($p->price <= $targetPrice * 1.20);
                return $areaMatch && $priceMatch;
            });

            if ($exactMatches->count() > 0) {
                $meta['is_exact_match'] = $isExactType;
                $sorted = $exactMatches->sortBy(function ($p) use ($targetArea, $targetPrice) {
                    return abs($p->area - $targetArea) + (abs($p->price - $targetPrice) / 1000000);
                });
                foreach ($sorted->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, $isExactType);
                }
            } else {
                $meta['is_exact_match'] = false;
                $meta['has_alternatives'] = true;
                $sorted = $candidates->sortBy(function ($p) use ($targetArea) {
                    return abs($p->area - $targetArea);
                });
                foreach ($sorted->take(2) as $p) {
                    $cards[] = $this->transformPostToCard($p, false);
                }
            }
        } elseif ($targetArea) {
            $exactMatches = $candidates->filter(function ($p) use ($targetArea) {
                return abs($p->area - $targetArea) <= max(3, $targetArea * 0.15);
            });

            if ($exactMatches->count() > 0) {
                $meta['is_exact_match'] = $isExactType;
                $sorted = $exactMatches->sortBy(function ($p) use ($targetArea) {
                    return abs($p->area - $targetArea);
                });
                foreach ($sorted->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, $isExactType);
                }
            } else {
                $meta['is_exact_match'] = false;
                $meta['has_alternatives'] = true;
                $sorted = $candidates->sortBy(function ($p) use ($targetArea) {
                    return abs($p->area - $targetArea);
                });
                foreach ($sorted->take(2) as $p) {
                    $cards[] = $this->transformPostToCard($p, false);
                }
            }
        } elseif ($targetPrice) {
            $minPrice = $targetPrice * 0.85;
            $maxPrice = $targetPrice * 1.15;
            $exactMatches = $candidates->filter(function ($p) use ($minPrice, $maxPrice) {
                return $p->price >= $minPrice && $p->price <= $maxPrice;
            });

            if ($exactMatches->count() > 0) {
                $meta['is_exact_match'] = $isExactType;
                foreach ($exactMatches->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, $isExactType);
                }
            } else {
                $meta['is_exact_match'] = false;
                $meta['has_alternatives'] = true;
                $sorted = $candidates->sortBy(function ($p) use ($targetPrice) {
                    return abs($p->price - $targetPrice);
                });
                foreach ($sorted->take(2) as $p) {
                    $cards[] = $this->transformPostToCard($p, false);
                }
            }
        } else {
            // Không yêu cầu giá lẫn diện tích
            $meta['is_exact_match'] = $isExactType;
            if ($isExactType) {
                // Chỉ lấy các bài khớp đúng loại hình
                foreach ($candidates->take(3) as $p) {
                    $cards[] = $this->transformPostToCard($p, true);
                }
            } else {
                // Không có bài khớp đúng loại hình, gợi ý các bài cùng khu vực
                $meta['has_alternatives'] = true;
                foreach ($candidates->take(2) as $p) {
                    $cards[] = $this->transformPostToCard($p, false);
                }
            }
        }

        return ['cards' => $cards, 'meta' => $meta];
    }

    /**
     * Chuyển đổi Post Model sang Card UI đẹp mắt
     */
    protected function transformPostToCard($post, bool $isMatch): array
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
            'badge' => $isMatch ? '⭐ Khớp nhu cầu' : '💡 Gợi ý tham khảo',
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

        $systemInstruction = "Bạn là Trợ lý AI của Nền tảng Bất động sản Cho thuê RentHome. Bạn đồng hành hỗ trợ người thuê tìm phòng, chủ nhà đăng tin và tư vấn mọi thủ tục liên quan đến thuê nhà tại Việt Nam.\n\n"
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
                           . "- Người thuê: Miễn phí 100% (0 VNĐ phí môi giới, kết nối trực tiếp chính chủ).\n"
                           . "- Chủ nhà: Đăng tin miễn phí 100%, thời gian kiểm duyệt bài 15 - 30 phút.\n"
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
                . "Tôi là **Chuyên gia Tư vấn Cấp cao RentHome**. Tôi có thể hỗ trợ gì cho bạn hôm nay ạ? 💡\n\n"
                . "Bạn có thể lựa chọn nhanh:\n"
                . "🔍 **Tìm phòng trọ, căn hộ**: Nhắn khu vực & tầm giá (VD: *Tìm phòng Cầu Giấy tầm 4 triệu*).\n"
                . "📝 **Đăng tin cho thuê**: Gửi ảnh và thông tin phòng để AI đăng tự động hoàn toàn miễn phí.\n"
                . "💡 **Giải đáp thắc mắc (FAQ)**: Gõ 'FAQ' hoặc hỏi trực tiếp về cọc, hợp đồng, chi phí...\n"
                . "🏢 **Quản lý vận hành**: Hướng dẫn tạo tòa nhà, chốt số điện nước và tự động xuất hóa đơn.\n\n"
                . "{$userName} hãy nhắn trực tiếp nhu cầu để tôi hỗ trợ ngay nhé!";
        }

        // 1. Trường hợp người dùng tìm kiếm chung chung cần hỏi lại khu vực & mức giá (Clarification)
        if (!empty($meta['needs_clarification'])) {
            $loaiHinh = $meta['property_type_requested'] ?? 'phòng / căn hộ';
            return "Chào **{$userName}**! Mình rất sẵn lòng hỗ trợ bạn tìm {$loaiHinh} ưng ý ạ. 🏠✨\n\n"
                 . "Để mình lọc chính xác nhất các căn phù hợp và tránh mất thời gian của bạn, bạn cho mình biết thêm nhé:\n"
                 . "1. 📍 **Bạn muốn tìm ở khu vực nào?** (Ví dụ: Hà Nội, TP.HCM, Đà Nẵng hay quận/huyện, tỉnh thành cụ thể nào?)\n"
                 . "2. 💰 **Khoảng mức giá bạn dự định thuê là bao nhiêu triệu/tháng?**\n\n"
                 . "👉 Bạn cứ nhắn trực tiếp khu vực và tầm giá, mình sẽ lọc danh sách các căn tốt nhất gửi bạn ngay nhé!\n\n"
                 . "<div style=\"display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px;\">\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Cầu Giấy, Hà Nội tầm 3 - 5 triệu')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📍 Cầu Giấy, Hà Nội tầm 3 - 5 triệu</button>\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Quận 1, TP.HCM tầm 5 - 8 triệu')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📍 Quận 1, TP.HCM tầm 5 - 8 triệu</button>\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Hải Châu, Đà Nẵng tầm 3 - 5 triệu')\" style=\"background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">📍 Hải Châu, Đà Nẵng tầm 3 - 5 triệu</button>\n"
                 . "  <button type=\"button\" onclick=\"sendQuickPrompt('Tìm căn hộ trên Toàn quốc')\" style=\"background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;\">🌐 Tìm căn hộ trên Toàn quốc</button>\n"
                 . "</div>";
        }

        // Tình huống tìm kiếm không có căn nào đúng tiêu chí
        if ($meta['is_search'] && !$meta['is_exact_match']) {
            $isNationwide = !empty($meta['is_nationwide']) || ($meta['location_requested'] === 'Toàn quốc');
            $loc = $meta['location_requested'] ? "tại khu vực **{$meta['location_requested']}**" : "";
            $type = !empty($meta['property_type_requested']) ? "loại hình **{$meta['property_type_requested']}**" : "";
            $price = $meta['price_label'] ? "trong tầm giá **{$meta['price_label']}**" : "";
            $area = !empty($meta['area_label']) ? "diện tích khoảng **{$meta['area_label']}**" : "";

            $criteriaList = array_filter([$loc, $type, $price, $area]);
            $criteriaStr = !empty($criteriaList) ? ' (' . implode(', ', $criteriaList) . ')' : '';

            $reply = $greeting
                . "Hiện tại trong cơ sở dữ liệu của RentHome tạm thời chưa có căn nào khớp hoàn toàn tiêu chí{$criteriaStr} đang trống ạ.\n\n";

            if ($meta['has_alternatives'] && count($cards) > 0) {
                $leadText = $isNationwide
                    ? "Hệ thống đã chọn lọc các căn nổi bật trên **Toàn quốc** phù hợp với nhu cầu của {$userName} ở ngay bên dưới: 🏠✨\n\n"
                    : "Tuy nhiên, để {$userName} không mất thời gian chờ đợi, **tôi xin phép gửi bạn một số lựa chọn khác đang có sẵn ở bên dưới** để tham khảo trước nhé ạ:\n\n";

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
            $price = $meta['price_label'] ? "tầm giá **{$meta['price_label']}**" : '';
            $area = !empty($meta['area_label']) ? "diện tích **{$meta['area_label']}**" : '';
            $criteriaList = array_filter([$loc, $type, $price, $area]);
            $criteriaStr = !empty($criteriaList) ? ' (' . implode(', ', $criteriaList) . ')' : '';

            $introHeading = $isNationwide
                ? "Mình đã chọn lọc các căn nổi bật trên **Toàn quốc** phù hợp với nhu cầu của {$userName} ở ngay bên dưới: 🏠✨"
                : "Mình đã chọn lọc được các căn rất phù hợp với đúng tiêu chí của {$userName}{$criteriaStr} ở ngay bên dưới: 🏠✨";

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
            . "{$userName} đang cần **tìm kiếm phòng trọ**, **đăng tin cho thuê** hay cần **giải đáp thắc mắc (FAQ)** về hợp đồng, tiền cọc, điện nước? Hãy nhắn ngay cho tôi nhé!";
    }
}