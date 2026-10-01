<?php

namespace App\Services;

use App\Models\Complaint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportNotificationService
{
    protected string $adminEmail;
    protected string $telegramBotToken;
    protected ?string $telegramChatId;

    public function __construct()
    {
        $this->adminEmail = config('services.admin_email', env('ADMIN_EMAIL', 'haiquan2482006@gmail.com'));
        $this->telegramBotToken = env('TELEGRAM_BOT_TOKEN', '8904631603:AAFOYpEZglwsQGjaEuS-wuAm-B_i_nDsk58');
        $this->telegramChatId = env('TELEGRAM_CHAT_ID');
    }

    /**
     * Gửi yêu cầu trợ giúp / báo cáo sự cố tới Admin qua Gmail và Telegram
     */
    public function sendSupportTicket(array $data): array
    {
        $title = $data['title'] ?? '🚨 <b>[RENTHOME] YÊU CẦU TRỢ GIÚP / BÁO CÁO SỰ CỐ</b>';
        $complaintCode = $data['complaint_code'] ?? null;
        $name = $data['name'] ?? 'Khách hàng';
        $contact = $data['contact'] ?? 'Chưa cung cấp';
        $issue = $data['issue'] ?? 'Yêu cầu hỗ trợ sự cố';
        $username = $data['username'] ?? '';
        $time = now('Asia/Ho_Chi_Minh')->format('H:i:s d/m/Y');
        $imageItem = $data['image'] ?? null;
        $skipDbCreate = $data['skip_db_create'] ?? false;
        $parsedDetails = $data['parsed_details'] ?? null;

        $emailSent = false;
        $telegramSent = false;

        // Xây dựng nội dung chi tiết theo từng mục riêng (Link riêng, SĐT riêng, Nội dung riêng, Bằng chứng riêng)
        if (!empty($parsedDetails)) {
            $link = $parsedDetails['link'] ?? 'Chưa cung cấp link bài đăng/website';
            $phone = $parsedDetails['phone'] ?? 'Chưa cung cấp SĐT đối tượng';
            $issueDesc = $parsedDetails['issue_desc'] ?? $issue;
            $evidence = $parsedDetails['evidence'] ?? ($imageItem ? 'Đã đính kèm ảnh bằng chứng xác minh' : 'Không có ảnh đính kèm');

            $linkSafe = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
            $phoneSafe = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
            $issueDescSafe = htmlspecialchars($issueDesc, ENT_QUOTES, 'UTF-8');
            $evidenceSafe = htmlspecialchars($evidence, ENT_QUOTES, 'UTF-8');

            $emailBodyDetails = "📋 CHI TIẾT HỒ SƠ:\n"
                              . "1. 🔗 Link bài đăng / Website:\n{$link}\n\n"
                              . "2. 📞 Số điện thoại bị phản ánh:\n{$phone}\n\n"
                              . "3. ⚠️ Nội dung sự việc cụ thể:\n{$issueDesc}\n\n"
                              . "4. 📸 Bằng chứng kèm theo:\n{$evidence}";

            $teleBodyDetails = "📋 <b>CHI TIẾT HỒ SƠ:</b>\n\n"
                             . "1. 🔗 <b>Link bài đăng / Website:</b>\n"
                             . "{$linkSafe}\n\n"
                             . "2. 📞 <b>Số điện thoại bị phản ánh:</b>\n"
                             . "<code>{$phoneSafe}</code>\n\n"
                             . "3. ⚠️ <b>Nội dung sự việc cụ thể:</b>\n"
                             . "{$issueDescSafe}\n\n"
                             . "4. 📸 <b>Bằng chứng kèm theo:</b>\n"
                             . "{$evidenceSafe}";
        } else {
            $issueSafe = htmlspecialchars($issue, ENT_QUOTES, 'UTF-8');
            $emailBodyDetails = "• Nội dung chi tiết:\n{$issue}";
            $teleBodyDetails = "📝 <b>Nội dung sự cố:</b>\n{$issueSafe}";
        }

        // 1. GỬI EMAIL VỀ GMAIL CỦA ADMIN
        try {
            $subject = $complaintCode ? "[RentHome] Hồ sơ khiếu nại khẩn cấp #{$complaintCode} từ: {$name}" : "[RentHome] Yêu cầu trợ giúp / Báo cáo sự cố từ: {$name}";
            $body = "KÍNH GỬI BAN QUẢN TRỊ RENTHOME,\n\n"
                  . "Hệ thống AI vừa tiếp nhận một hồ sơ khiếu nại / yêu cầu hỗ trợ từ người dùng:\n\n"
                  . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                  . ($complaintCode ? "• Mã hồ sơ khiếu nại: #{$complaintCode}\n" : "")
                  . "• Người gửi: {$name}" . ($username ? " (Tài khoản: {$username})" : "") . "\n"
                  . "• Thông tin liên hệ: {$contact}\n"
                  . "• Thời gian tiếp nhận: {$time}\n"
                  . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                  . "{$emailBodyDetails}\n\n"
                  . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                  . "Vui lòng xác minh, đối chất và can thiệp xử lý kịp thời trong vòng 15 – 30 phút!\n\n"
                  . "--\nRentHome AI Support System";

            Mail::raw($body, function ($message) use ($subject) {
                $message->to($this->adminEmail)
                        ->subject($subject);
            });
            $emailSent = true;
            Log::info("Support ticket email sent to {$this->adminEmail} successfully.");
        } catch (\Exception $e) {
            Log::error("Failed to send support email: " . $e->getMessage());
        }

        // 2. GỬI TIN NHẮN TỚI TELEGRAM BOT (DÙNG HTML ĐỂ KHÔNG BỊ LỖI KHI CÓ KÝ TỰ _ , * HOẶC URL)
        try {
            $chatIds = $this->resolveTelegramChatIds();
            if (!empty($chatIds)) {
                $nameSafe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                $userSafe = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
                $contactSafe = htmlspecialchars($contact, ENT_QUOTES, 'UTF-8');

                $codeHeader = $complaintCode ? "🔖 <b>Mã hồ sơ:</b> <code>#{$complaintCode}</code>\n" : "";

                $teleMessage = "{$title}\n"
                             . "━━━━━━━━━━━━━━━━━━━━━━\n"
                             . $codeHeader
                             . "👤 <b>Người gửi:</b> {$nameSafe}" . ($userSafe ? " (<code>{$userSafe}</code>)" : "") . "\n"
                             . "📞 <b>Liên hệ:</b> <code>{$contactSafe}</code>\n"
                             . "⏰ <b>Thời gian:</b> {$time}\n"
                             . "━━━━━━━━━━━━━━━━━━━━━━\n"
                             . "{$teleBodyDetails}\n"
                             . "━━━━━━━━━━━━━━━━━━━━━━";

                // Kiểm tra xem có ảnh bằng chứng đính kèm không
                $imgBinary = $imageItem ? $this->resolveSingleImageBinary($imageItem) : null;

                foreach ($chatIds as $cid) {
                    $sent = false;

                    // Nếu có ảnh, gửi kèm ảnh qua sendPhoto
                    if ($imgBinary) {
                        try {
                            $res = Http::timeout(15)
                                ->attach('photo', $imgBinary['binary'], $imgBinary['filename'])
                                ->post("https://api.telegram.org/bot{$this->telegramBotToken}/sendPhoto", [
                                    'chat_id' => $cid,
                                    'caption' => mb_substr($teleMessage, 0, 1024, 'UTF-8'),
                                    'parse_mode' => 'HTML',
                                ]);
                            if ($res->successful()) {
                                $sent = true;
                                $telegramSent = true;
                            } else {
                                Log::warning("Telegram sendPhoto failed: " . $res->body());
                            }
                        } catch (\Exception $e) {
                            Log::warning("Telegram sendPhoto exception: " . $e->getMessage());
                        }
                    }

                    // Fallback hoặc khi không có ảnh: gửi tin nhắn text qua sendMessage (HTML mode)
                    if (!$sent) {
                        $res = Http::timeout(10)->post("https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage", [
                            'chat_id' => $cid,
                            'text' => $teleMessage,
                            'parse_mode' => 'HTML',
                        ]);
                        if ($res->successful()) {
                            $telegramSent = true;
                        } else {
                            Log::error("Telegram sendMessage failed: " . $res->body());
                        }
                    }
                }
            } else {
                Log::info("No Telegram chat_id found in getUpdates yet. Admin needs to send /start to bot.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send Telegram notification: " . $e->getMessage());
        }

        // 3. LƯU BẢN GHI VÀO MONGODB (Collection complaints) NẾU CHƯA ĐƯỢC TẠO
        if (!$skipDbCreate) {
            try {
                Complaint::create([
                    'user_id' => auth()->id() ?? null,
                    'post_id' => null,
                    'complaint_code' => $data['complaint_code'] ?? null,
                    'content' => "[Hỗ trợ / Báo cáo sự cố] Người gửi: {$name} | SĐT/Email: {$contact}\nNội dung: {$issue}",
                    'image' => $imageItem,
                    'status' => 'pending',
                    'type' => 'support_report',
                ]);
            } catch (\Exception $e) {
                Log::warning("Failed to store complaint to MongoDB: " . $e->getMessage());
            }
        }

        return [
            'email_sent' => $emailSent,
            'telegram_sent' => $telegramSent,
        ];
    }

    /**
     * Thông báo tới Admin qua Telegram và Gmail khi có bài đăng mới được tạo qua AI Chat
     */
    public function notifyNewPostCreated($post, $user = null): array
    {
        $postId = (string)($post->_id ?? $post->id);
        $title = $post->title ?? 'Bài đăng cho thuê mới';
        $price = number_format($post->price ?? 0, 0, ',', '.') . ' VNĐ/tháng';
        $area = ($post->area ?? 0) . ' m²';

        // Chuẩn hóa và làm sạch địa chỉ, loại bỏ trùng lặp và tiền tố 'ở'
        $addrParts = [];
        foreach ([$post->address, $post->ward, $post->district, $post->province] as $p) {
            if (!empty($p)) {
                foreach (explode(',', (string)$p) as $sub) {
                    $subClean = trim(preg_replace('/^(?:ở|tại|khu\s*vực|gần)\s+/ui', '', trim($sub)));
                    if (!empty($subClean)) {
                        $lower = mb_strtolower($subClean, 'UTF-8');
                        $exists = false;
                        foreach ($addrParts as $ap) {
                            if (mb_strtolower($ap, 'UTF-8') === $lower) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $addrParts[] = $subClean;
                        }
                    }
                }
            }
        }
        $address = !empty($addrParts) ? implode(', ', $addrParts) : ($post->province ?? 'Hà Nội');

        // Tên loại hình bất động sản
        $propTypeLabels = [
            'phong_tro' => 'Phòng trọ',
            'ch' => 'Căn hộ / Chung cư',
            'nnc' => 'Nhà nguyên căn',
            'nha_o' => 'Nhà ở',
            'villa' => 'Biệt thự / Villa',
            'dat_nen' => 'Mặt bằng / Đất nền',
        ];
        $typeName = $propTypeLabels[$post->property_type ?? ''] ?? ($post->property_type ?? 'Phòng trọ');

        // Số phòng ngủ & phòng tắm
        $roomParts = [];
        if (!empty($post->bedrooms)) $roomParts[] = "{$post->bedrooms} PN";
        if (!empty($post->bathrooms)) $roomParts[] = "{$post->bathrooms} WC";
        $roomStr = !empty($roomParts) ? implode(', ', $roomParts) : '';

        // Tiện ích
        $amenities = $post->amenities ?? [];
        $amenityMap = [
            'dieu_hoa' => 'Điều hòa', 'nong_lanh' => 'Nóng lạnh', 'may_giat' => 'Máy giặt',
            'tu_lanh' => 'Tủ lạnh', 'giuong_tu' => 'Giường tủ', 'wifi' => 'Wifi',
            'ban_cong' => 'Ban công', 'thang_may' => 'Thang máy', 'cho_de_xe' => 'Chỗ để xe',
            'khong_chung_chu' => 'Giờ tự do', 've_sinh_khep_kin' => 'Khép kín',
        ];
        $amenityNames = [];
        foreach ($amenities as $am) {
            $amenityNames[] = $amenityMap[$am] ?? $am;
        }
        $amenityStr = !empty($amenityNames) ? implode(', ', array_slice($amenityNames, 0, 5)) : 'Cơ bản';

        // Mô tả chi tiết
        $rawDesc = trim((string)($post->description ?? ''));
        $descSnippet = mb_strlen($rawDesc, 'UTF-8') > 150 ? mb_substr($rawDesc, 0, 147, 'UTF-8') . '...' : ($rawDesc ?: 'Đã tự động tạo mô tả chuẩn SEO');

        // Hình ảnh
        $imgCount = is_array($post->images) ? count($post->images) : (!empty($post->images) ? 1 : 0);
        $imgInfo = $imgCount > 0 ? "Đã đính kèm {$imgCount} ảnh thực tế 📸" : "Chưa có ảnh (dùng ảnh hệ thống)";

        $authorName = $user ? ($user->account_name ?: ($user->username ?? 'Thành viên')) : 'Người dùng RentHome';
        $time = now('Asia/Ho_Chi_Minh')->format('H:i:s d/m/Y');
        $viewUrl = url("/baidang/{$postId}");
        $modUrl = url("/moderator/qlpheduyet");

        $emailSent = false;
        $telegramSent = false;

        // 1. GỬI TIN NHẮN TỚI TELEGRAM BOT
        try {
            $chatIds = $this->resolveTelegramChatIds();
            if (!empty($chatIds)) {
                $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                $safeTypeName = htmlspecialchars($typeName, ENT_QUOTES, 'UTF-8');
                $safePrice = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
                $safeArea = htmlspecialchars($area, ENT_QUOTES, 'UTF-8');
                $safeAddress = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
                $safeRoomStr = htmlspecialchars($roomStr, ENT_QUOTES, 'UTF-8');
                $safeAmenityStr = htmlspecialchars($amenityStr, ENT_QUOTES, 'UTF-8');
                $safeImgInfo = htmlspecialchars($imgInfo, ENT_QUOTES, 'UTF-8');
                $safeAuthor = htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8');
                $safePostId = htmlspecialchars($postId, ENT_QUOTES, 'UTF-8');
                $safeTime = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');
                $safeDesc = htmlspecialchars($descSnippet, ENT_QUOTES, 'UTF-8');

                $teleMsg = "🏡 <b>[RENTHOME] BÀI ĐĂNG MỚI TẠO QUA AI CHAT (CHỜ DUYỆT)</b>\n"
                         . "━━━━━━━━━━━━━━━━━━━━━━\n"
                         . "📝 <b>Tiêu đề:</b> {$safeTitle}\n"
                         . "🏷️ <b>Loại hình:</b> {$safeTypeName}\n"
                         . "💰 <b>Mức giá:</b> <code>{$safePrice}</code>\n"
                         . "📐 <b>Diện tích:</b> {$safeArea}\n"
                         . "📍 <b>Khu vực:</b> {$safeAddress}\n"
                         . ($safeRoomStr ? "🛏️ <b>Số phòng:</b> {$safeRoomStr}\n" : "")
                         . "🛋️ <b>Tiện ích:</b> {$safeAmenityStr}\n"
                         . "🖼️ <b>Hình ảnh:</b> {$safeImgInfo}\n"
                         . "👤 <b>Người đăng:</b> {$safeAuthor}\n"
                         . "🆔 <b>ID Bài đăng:</b> <code>{$safePostId}</code>\n"
                         . "⏰ <b>Thời gian:</b> {$safeTime}\n\n"
                         . "📄 <b>Mô tả chi tiết:</b>\n<i>{$safeDesc}</i>\n"
                         . "━━━━━━━━━━━━━━━━━━━━━━\n"
                         . "🔗 <b>Xem bài đăng:</b> <a href=\"{$viewUrl}\">Bấm vào đây để xem</a>\n"
                         . "⚡ <b>Kiểm duyệt:</b> <a href=\"{$modUrl}\">Vào trang Quản lý kiểm duyệt</a>";

                // Chuẩn bị caption an toàn <= 1000 ký tự cho Telegram Media/Photo
                $caption = $teleMsg;
                if (mb_strlen($caption, 'UTF-8') > 1000) {
                    $maxDescLen = max(50, 1000 - (mb_strlen($caption, 'UTF-8') - mb_strlen($safeDesc, 'UTF-8')));
                    $trimmedDesc = mb_substr($safeDesc, 0, $maxDescLen, 'UTF-8') . '...';
                    $caption = "🏡 <b>[RENTHOME] BÀI ĐĂNG MỚI TẠO QUA AI CHAT (CHỜ DUYỆT)</b>\n"
                             . "━━━━━━━━━━━━━━━━━━━━━━\n"
                             . "📝 <b>Tiêu đề:</b> {$safeTitle}\n"
                             . "🏷️ <b>Loại hình:</b> {$safeTypeName}\n"
                             . "💰 <b>Mức giá:</b> <code>{$safePrice}</code>\n"
                             . "📐 <b>Diện tích:</b> {$safeArea}\n"
                             . "📍 <b>Khu vực:</b> {$safeAddress}\n"
                             . ($safeRoomStr ? "🛏️ <b>Số phòng:</b> {$safeRoomStr}\n" : "")
                             . "🛋️ <b>Tiện ích:</b> {$safeAmenityStr}\n"
                             . "🖼️ <b>Hình ảnh:</b> {$safeImgInfo}\n"
                             . "👤 <b>Người đăng:</b> {$safeAuthor}\n"
                             . "🆔 <b>ID Bài đăng:</b> <code>{$safePostId}</code>\n"
                             . "⏰ <b>Thời gian:</b> {$safeTime}\n\n"
                             . "📄 <b>Mô tả chi tiết:</b>\n<i>{$trimmedDesc}</i>\n"
                             . "━━━━━━━━━━━━━━━━━━━━━━\n"
                             . "🔗 <b>Xem bài đăng:</b> <a href=\"{$viewUrl}\">Bấm vào đây để xem</a>\n"
                             . "⚡ <b>Kiểm duyệt:</b> <a href=\"{$modUrl}\">Vào trang Quản lý kiểm duyệt</a>";
                }

                $imageBinaries = $this->extractPostImageBinaries($post, 5);

                foreach ($chatIds as $cid) {
                    $sent = false;

                    // A. Nếu có nhiều ảnh (từ 2 trở lên): Gửi album qua sendMediaGroup
                    if (count($imageBinaries) >= 2) {
                        try {
                            $media = [];
                            $req = Http::timeout(20);
                            foreach ($imageBinaries as $idx => $ib) {
                                $attachKey = "photo{$idx}";
                                $item = [
                                    'type' => 'photo',
                                    'media' => "attach://{$attachKey}",
                                ];
                                if ($idx === 0) {
                                    $item['caption'] = $caption;
                                    $item['parse_mode'] = 'HTML';
                                }
                                $media[] = $item;
                                $req->attach($attachKey, $ib['binary'], $ib['filename']);
                            }
                            $res = $req->post("https://api.telegram.org/bot{$this->telegramBotToken}/sendMediaGroup", [
                                'chat_id' => $cid,
                                'media' => json_encode($media),
                            ]);
                            if ($res->successful()) {
                                $sent = true;
                                $telegramSent = true;
                            }
                        } catch (\Exception $e) {
                            Log::warning("sendMediaGroup failed: " . $e->getMessage());
                        }
                    }

                    // B. Nếu có 1 ảnh (hoặc sendMediaGroup thất bại): Gửi qua sendPhoto
                    if (!$sent && count($imageBinaries) >= 1) {
                        try {
                            $firstImg = $imageBinaries[0];
                            $res = Http::timeout(15)
                                ->attach('photo', $firstImg['binary'], $firstImg['filename'])
                                ->post("https://api.telegram.org/bot{$this->telegramBotToken}/sendPhoto", [
                                    'chat_id' => $cid,
                                    'caption' => $caption,
                                    'parse_mode' => 'HTML',
                                ]);
                            if ($res->successful()) {
                                $sent = true;
                                $telegramSent = true;
                            }
                        } catch (\Exception $e) {
                            Log::warning("sendPhoto failed: " . $e->getMessage());
                        }
                    }

                    // C. Fallback: Nếu không có ảnh hoặc gửi ảnh lỗi, gửi tin nhắn thông thường qua sendMessage
                    if (!$sent) {
                        $res = Http::timeout(10)->post("https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage", [
                            'chat_id' => $cid,
                            'text' => $teleMsg,
                            'parse_mode' => 'HTML',
                        ]);
                        if ($res->successful()) {
                            $telegramSent = true;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to send Telegram post notification: " . $e->getMessage());
        }

        // 2. GỬI EMAIL VỀ GMAIL CỦA ADMIN
        try {
            $subject = "[RentHome] Bài đăng mới tạo qua AI Chat: {$title}";
            $body = "KÍNH GỬI BAN QUẢN TRỊ RENTHOME,\n\n"
                  . "Người dùng vừa tạo một bài đăng cho thuê mới thông qua Trợ lý ảo AI RentHome và đang chờ kiểm duyệt:\n\n"
                  . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                  . "• Tiêu đề: {$title}\n"
                  . "• Loại hình: {$typeName}\n"
                  . "• Mức giá: {$price}\n"
                  . "• Diện tích: {$area}\n"
                  . "• Địa chỉ: {$address}\n"
                  . ($roomStr ? "• Số phòng: {$roomStr}\n" : "")
                  . "• Tiện nghi: {$amenityStr}\n"
                  . "• Hình ảnh: {$imgInfo}\n"
                  . "• Người đăng: {$authorName}\n"
                  . "• Mã bài đăng: {$postId}\n"
                  . "• Thời gian tạo: {$time}\n\n"
                  . "• Mô tả chi tiết:\n{$rawDesc}\n"
                  . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                  . "👉 Xem chi tiết bài đăng: {$viewUrl}\n"
                  . "👉 Trang kiểm duyệt bài viết: {$modUrl}\n\n"
                  . "--\nRentHome AI Auto-Post System";

            Mail::raw($body, function ($message) use ($subject) {
                $message->to($this->adminEmail)
                        ->subject($subject);
            });
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error("Failed to send post creation email: " . $e->getMessage());
        }

        return [
            'email_sent' => $emailSent,
            'telegram_sent' => $telegramSent,
        ];
    }

    /**
     * Tìm danh sách Chat ID đã nhắn tin với Telegram Bot
     */
    protected function resolveTelegramChatIds(): array
    {
        $chatIds = [];

        if (!empty($this->telegramChatId)) {
            $rawIds = explode(',', (string) $this->telegramChatId);
            foreach ($rawIds as $rawId) {
                $rawId = trim($rawId);
                if ($rawId !== '' && !in_array($rawId, $chatIds)) {
                    $chatIds[] = $rawId;
                }
            }
        }

        $cached = cache()->get('telegram_admin_chat_ids', []);
        if (!empty($cached)) {
            $chatIds = array_unique(array_merge($chatIds, (array) $cached));
        }

        try {
            $response = Http::timeout(6)->get("https://api.telegram.org/bot{$this->telegramBotToken}/getUpdates");
            if ($response->successful()) {
                $updates = $response->json('result', []);
                foreach ($updates as $u) {
                    $cid = $u['message']['chat']['id'] ?? ($u['channel_post']['chat']['id'] ?? ($u['my_chat_member']['chat']['id'] ?? null));
                    if ($cid && !in_array((string)$cid, array_map('strval', $chatIds))) {
                        $chatIds[] = (string)$cid;
                    }
                }
                if (!empty($chatIds)) {
                    cache()->put('telegram_admin_chat_ids', $chatIds, now()->addDays(30));
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error fetching telegram updates: " . $e->getMessage());
        }

        return $chatIds;
    }

    /**
     * Trích xuất dữ liệu nhị phân (binary) của các ảnh bài đăng để gửi trực tiếp lên Telegram
     */
    protected function extractPostImageBinaries($post, int $max = 5): array
    {
        $images = $post->images ?? [];
        if (!is_array($images)) {
            $images = !empty($images) ? [$images] : [];
        }

        $binaries = [];
        $count = 0;
        foreach ($images as $imgItem) {
            if ($count >= $max) break;
            $bin = $this->resolveSingleImageBinary($imgItem);
            if ($bin) {
                $binaries[] = $bin;
                $count++;
            }
        }
        return $binaries;
    }

    /**
     * Lấy nội dung binary của 1 item ảnh (từ Mongo Model Image, File Storage hoặc URL)
     */
    protected function resolveSingleImageBinary($imgItem): ?array
    {
        if (empty($imgItem)) return null;
        $imgStr = (string)$imgItem;

        // 1. Nếu là MongoDB ID của Model Image (24 ký tự hex)
        if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
            try {
                $imgDoc = \App\Models\Image::find($imgStr);
                if ($imgDoc && !empty($imgDoc->base64_data)) {
                    $decoded = base64_decode($imgDoc->base64_data);
                    if (!empty($decoded)) {
                        return [
                            'binary' => $decoded,
                            'filename' => "room_{$imgStr}.jpg",
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Error reading image from DB: " . $e->getMessage());
            }
        }

        // 2. Nếu là Data URI base64
        if (str_starts_with($imgStr, 'data:')) {
            if (preg_match('#^data:[^;]+;base64,(.+)$#', $imgStr, $dm)) {
                $decoded = base64_decode($dm[1]);
                if (!empty($decoded)) {
                    return [
                        'binary' => $decoded,
                        'filename' => 'room_image.jpg',
                    ];
                }
            }
        }

        // 3. Nếu là file trong storage hoặc public
        $possiblePaths = [
            public_path('storage/' . $imgStr),
            storage_path('app/public/' . $imgStr),
            public_path($imgStr),
        ];
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_file($path)) {
                $content = @file_get_contents($path);
                if (!empty($content)) {
                    return [
                        'binary' => $content,
                        'filename' => basename($path),
                    ];
                }
            }
        }

        // 4. Nếu là URL ngoài (Unsplash, Cloudinary, etc.)
        if (str_starts_with($imgStr, 'http://') || str_starts_with($imgStr, 'https://')) {
            try {
                $res = Http::timeout(6)->get($imgStr);
                if ($res->successful() && !empty($res->body())) {
                    return [
                        'binary' => $res->body(),
                        'filename' => 'room_remote.jpg',
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Error downloading external image: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Thông báo tới Admin khi có khiếu nại mới từ khách hàng
     */
    public function notifyComplaintCreated($complaint, $user = null, $image = null, array $parsedDetails = []): array
    {
        $authorName = $user ? ($user->account_name ?: ($user->username ?? 'Thành viên')) : 'Người dùng RentHome';
        $contact = $user ? ($user->phone ?? ($user->email ?? 'Chưa rõ')) : 'Chưa rõ';
        $code = $complaint->complaint_code ?? ('KN-' . substr(md5(uniqid()), 0, 6));
        $content = $complaint->content ?? 'Khiếu nại bài đăng/chủ trọ';
        $img = $image ?? ($complaint->image ?? null);

        return $this->sendSupportTicket([
            'title' => '🚨 <b>[RENTHOME] TIẾP NHẬN HỒ SƠ KHIẾU NẠI & TỐ CÁO</b>',
            'complaint_code' => $code,
            'name' => $authorName,
            'contact' => $contact,
            'issue' => $content,
            'username' => $user->username ?? '',
            'image' => $img,
            'parsed_details' => $parsedDetails,
            'skip_db_create' => true,
        ]);
    }
}

