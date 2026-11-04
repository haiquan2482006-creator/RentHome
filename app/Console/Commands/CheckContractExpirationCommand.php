<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Models\Post;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class CheckContractExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra các hợp đồng thuê sắp hết hạn (trong vòng 10 ngày) và gửi thông báo cho khách thuê và người đăng bài / nhân sự phụ trách';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->startOfDay();
        $this->info("Bắt đầu quét kiểm tra hợp đồng sắp hết hạn vào ngày: " . $now->format('d/m/Y') . "...");

        // Lấy tất cả hợp đồng đang có hiệu lực (active, signed, hoat_dong)
        $contracts = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'expired', 'da_huy'])->get();
        $notifiedCount = 0;

        foreach ($contracts as $contract) {
            $endDateRaw = $contract->end_date ?? null;
            if (!$endDateRaw) {
                // Nếu không có end_date nhưng có start_date và duration_months
                if (!empty($contract->start_date)) {
                    $duration = (int)($contract->duration_months ?? 12);
                    $endDateRaw = Carbon::parse($contract->start_date)->addMonths($duration)->format('Y-m-d');
                } else {
                    continue;
                }
            }

            try {
                $endDate = Carbon::parse($endDateRaw)->startOfDay();
            } catch (\Exception $e) {
                continue;
            }

            // Tính số ngày còn lại đến khi hết hạn
            $daysRemaining = (int) $now->diffInDays($endDate, false);

            // Điều kiện: Sắp hết hạn trong khoảng từ 0 đến 10 ngày (hoặc vừa chạm mốc hết hạn)
            if ($daysRemaining >= 0 && $daysRemaining <= 10) {
                $contractId = (string)($contract->_id ?? $contract->id);
                $endDateFormatted = $endDate->format('d/m/Y');
                
                // Lấy thông tin bài đăng / phòng
                $post = $contract->post;
                if (!$post && !empty($contract->post_id)) {
                    $post = Post::find($contract->post_id);
                }
                $roomTitle = $post ? ($post->title ?? 'Phòng cho thuê') : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng cho thuê');
                $roomCode = $contract->contract_code ?: ('HD-' . strtoupper(substr($contractId, -5)));

                // Xác định người đăng bài / nhân sự quản lý (author/staff/owner)
                $staffUserId = null;
                if ($post && !empty($post->user_id)) {
                    $staffUserId = (string)$post->user_id;
                } elseif (!empty($contract->staff_id)) {
                    $staffUserId = (string)$contract->staff_id;
                } elseif (!empty($contract->created_by)) {
                    $staffUserId = (string)$contract->created_by;
                } elseif (!empty($contract->owner_id)) {
                    $staffUserId = (string)$contract->owner_id;
                }

                // Xác định khách thuê (tenant)
                $tenantUserId = null;
                if (!empty($contract->tenant_id)) {
                    $tenantUserId = (string)$contract->tenant_id;
                } elseif (!empty($contract->tenant_email)) {
                    $tenantUser = User::where('email', $contract->tenant_email)->first();
                    if ($tenantUser) $tenantUserId = (string)($tenantUser->_id ?? $tenantUser->id);
                }

                $timeText = ($daysRemaining == 0) ? "hôm nay ({$endDateFormatted})" : "còn {$daysRemaining} ngày nữa ({$endDateFormatted})";

                // 1. GỬI THÔNG BÁO CHO NGƯỜI ĐĂNG BÀI / NHÂN SỰ PHỤ TRÁCH
                if (!empty($staffUserId)) {
                    // Kiểm tra tránh gửi lặp thông báo trong ngày cho cùng hợp đồng
                    $alreadyNotifiedStaff = Notification::where('user_id', $staffUserId)
                        ->where('category', 'contract_expiring')
                        ->where('contract_id', $contractId)
                        ->where('created_at', '>=', $now)
                        ->exists();

                    if (!$alreadyNotifiedStaff) {
                        Notification::create([
                            'user_id'     => $staffUserId,
                            'contract_id' => $contractId,
                            'post_id'     => (string)($contract->post_id ?? ''),
                            'title'       => "Hợp đồng {$roomCode} sắp hết hạn ({$timeText})",
                            'message'     => "Hợp đồng thuê phòng [{$roomCode}] ({$roomTitle}) của khách {$contract->tenant_name} sẽ hết hạn vào ngày {$endDateFormatted} ({$timeText}). Vui lòng liên hệ khách để thỏa thuận gia hạn hoặc chuẩn bị thủ tục thanh lý phòng.",
                            'type'        => 'warning',
                            'is_read'     => false,
                            'category'    => 'contract_expiring',
                            'action_url'  => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . $contractId),
                        ]);
                        $notifiedCount++;
                        $this->info(" -> Đã gửi thông báo sắp hết hạn đến Nhân sự/Người đăng bài ID: {$staffUserId}");
                    }
                }

                // 2. GỬI THÔNG BÁO CHO KHÁCH THUÊ
                if (!empty($tenantUserId)) {
                    $alreadyNotifiedTenant = Notification::where('user_id', $tenantUserId)
                        ->where('category', 'contract_expiring')
                        ->where('contract_id', $contractId)
                        ->where('created_at', '>=', $now)
                        ->exists();

                    if (!$alreadyNotifiedTenant) {
                        Notification::create([
                            'user_id'     => $tenantUserId,
                            'contract_id' => $contractId,
                            'post_id'     => (string)($contract->post_id ?? ''),
                            'title'       => "Thông báo: Hợp đồng thuê phòng sắp hết hạn ({$timeText})",
                            'message'     => "Hợp đồng thuê phòng ({$roomTitle}) của bạn sẽ kết thúc vào ngày {$endDateFormatted} ({$timeText}). Nếu bạn có nhu cầu tiếp tục gia hạn hoặc trả phòng, vui lòng liên hệ quản lý tòa nhà để được hỗ trợ kịp thời.",
                            'type'        => 'warning',
                            'is_read'     => false,
                            'category'    => 'contract_expiring',
                            'action_url'  => url('/nha-dang-thue'),
                        ]);
                        $notifiedCount++;
                        $this->info(" -> Đã gửi thông báo sắp hết hạn đến Khách thuê ID: {$tenantUserId}");
                    }
                }
            }
        }

        $this->info("Hoàn tất quét! Tổng số thông báo đã gửi: {$notifiedCount}");
        return Command::SUCCESS;
    }
}
