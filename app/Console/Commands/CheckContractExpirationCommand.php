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
    protected $description = 'Kiểm tra các hợp đồng thuê sắp hết hạn (trong vòng 30 ngày) và gửi thông báo cho khách thuê và nhân viên quản lý nhà';

    /**
     * Parse ngày thông minh hỗ trợ các định dạng d/m/Y, Y-m-d, d-m-Y
     */
    public static function parseDateSmart($dateStr)
    {
        if (empty($dateStr)) return null;
        $dateStr = trim((string)$dateStr);
        try {
            if (str_contains($dateStr, '/')) {
                return Carbon::createFromFormat('d/m/Y', $dateStr)->startOfDay();
            }
            return Carbon::parse($dateStr)->startOfDay();
        } catch (\Exception $e) {
            try {
                return Carbon::parse(str_replace('/', '-', $dateStr))->startOfDay();
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Bắt đầu quét kiểm tra hợp đồng sắp hết hạn...");
        $count = self::runCheck(function ($msg) {
            $this->info($msg);
        });
        $this->info("Hoàn tất quét! Tổng số thông báo đã gửi: {$count}");
        return Command::SUCCESS;
    }

    /**
     * Hàm dùng chung để quét và gửi thông báo, có thể gọi từ Artisan CLI hoặc từ Web Controller
     */
    public static function runCheck($logger = null)
    {
        $log = function ($msg) use ($logger) {
            if (is_callable($logger)) {
                $logger($msg);
            }
        };

        $now = Carbon::now()->startOfDay();
        $contracts = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'expired', 'da_huy'])->get();
        $notifiedCount = 0;

        foreach ($contracts as $contract) {
            $contractId = (string)($contract->_id ?? $contract->id);
            $endDateRaw = $contract->end_date ?? null;

            // 1. Phân tích ngày kết thúc hợp đồng
            $endDate = self::parseDateSmart($endDateRaw);
            if (!$endDate && !empty($contract->start_date)) {
                $startDate = self::parseDateSmart($contract->start_date);
                if ($startDate) {
                    $duration = (int)($contract->duration_months ?? 12);
                    $endDate = $startDate->copy()->addMonths($duration)->startOfDay();
                }
            }

            if (!$endDate) {
                continue;
            }

            // 2. Tính số ngày còn lại đến khi kết thúc hợp đồng
            $daysRemaining = (int) $now->diffInDays($endDate, false);

            // Điều kiện: Sắp hết hạn trong vòng 30 ngày (từ 0 đến 30 ngày)
            if ($daysRemaining >= 0 && $daysRemaining <= 30) {
                $endDateFormatted = $endDate->format('d/m/Y');

                // Lấy thông tin bài đăng / phòng
                $post = $contract->post;
                if (!$post && !empty($contract->post_id)) {
                    $post = Post::find($contract->post_id);
                }
                $roomTitle = $post ? ($post->title ?? 'Phòng cho thuê') : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng cho thuê');
                $roomCode = $contract->contract_code ?: ('HD-' . strtoupper(substr($contractId, -5)));

                // Xác định nhân sự quản lý nhà (Staff / Property Manager)
                $staffUserIds = [];
                if (!empty($contract->staff_id)) {
                    $staffUserIds[] = (string)$contract->staff_id;
                }
                if ($post && !empty($post->staff_id)) {
                    $staffUserIds[] = (string)$post->staff_id;
                }
                if ($post && !empty($post->user_id)) {
                    $staffUserIds[] = (string)$post->user_id;
                }
                if (!empty($contract->created_by)) {
                    $staffUserIds[] = (string)$contract->created_by;
                }
                if (empty($staffUserIds) && !empty($contract->owner_id)) {
                    $staffUserIds[] = (string)$contract->owner_id;
                }
                $staffUserIds = array_values(array_unique(array_filter($staffUserIds)));

                // Xác định khách thuê (Tenant)
                $tenantUserId = null;
                if (!empty($contract->tenant_id)) {
                    $tenantUserId = (string)$contract->tenant_id;
                } elseif (!empty($contract->tenant_email)) {
                    $tenantUser = User::where('email', $contract->tenant_email)->first();
                    if ($tenantUser) $tenantUserId = (string)($tenantUser->_id ?? $tenantUser->id);
                } elseif (!empty($contract->tenant_phone)) {
                    $tenantUser = User::where('phone', $contract->tenant_phone)->first();
                    if ($tenantUser) $tenantUserId = (string)($tenantUser->_id ?? $tenantUser->id);
                }

                $timeText = ($daysRemaining == 0) 
                    ? "hôm nay ({$endDateFormatted})" 
                    : "còn {$daysRemaining} ngày nữa ({$endDateFormatted})";

                // Khoảng thời gian giãn cách tránh spam:
                // - Dưới 3 ngày: gửi nhắc mỗi ngày
                // - Từ 4 - 7 ngày: gửi nhắc mỗi 3 ngày
                // - Từ 8 - 30 ngày: gửi nhắc mỗi 7 ngày
                $cooldownDays = ($daysRemaining <= 3) ? 1 : (($daysRemaining <= 7) ? 3 : 7);
                $cooldownThreshold = Carbon::now()->subDays($cooldownDays);

                // A. GỬI THÔNG BÁO CHO NHÂN VIÊN QUẢN LÝ NHÀ
                foreach ($staffUserIds as $staffId) {
                    $alreadyNotifiedStaff = Notification::where('user_id', $staffId)
                        ->where('category', 'contract_expiring')
                        ->where('contract_id', $contractId)
                        ->where('created_at', '>=', $cooldownThreshold)
                        ->exists();

                    if (!$alreadyNotifiedStaff) {
                        Notification::create([
                            'user_id'     => $staffId,
                            'contract_id' => $contractId,
                            'post_id'     => (string)($contract->post_id ?? ''),
                            'title'       => "⚠️ Hợp đồng [{$roomCode}] sắp hết hạn ({$timeText})",
                            'message'     => "Hợp đồng thuê phòng [{$roomCode}] ({$roomTitle}) của khách thuê {$contract->tenant_name} sẽ hết hạn vào ngày {$endDateFormatted} ({$timeText}). Vui lòng chủ động liên hệ khách thuê để thỏa thuận tái ký gia hạn hợp đồng hoặc hướng dẫn thủ tục bàn giao/thanh lý phòng.",
                            'type'        => 'warning',
                            'is_read'     => false,
                            'category'    => 'contract_expiring',
                            'action_url'  => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . $contractId),
                        ]);
                        $notifiedCount++;
                        $log(" -> Đã gửi thông báo sắp hết hạn đến Nhân viên quản lý ID: {$staffId} cho HĐ: {$roomCode}");
                    }
                }

                // B. GỬI THÔNG BÁO CHO KHÁCH THUÊ
                if (!empty($tenantUserId)) {
                    $alreadyNotifiedTenant = Notification::where('user_id', $tenantUserId)
                        ->where('category', 'contract_expiring')
                        ->where('contract_id', $contractId)
                        ->where('created_at', '>=', $cooldownThreshold)
                        ->exists();

                    if (!$alreadyNotifiedTenant) {
                        Notification::create([
                            'user_id'     => $tenantUserId,
                            'contract_id' => $contractId,
                            'post_id'     => (string)($contract->post_id ?? ''),
                            'title'       => "⏳ Thông báo: Hợp đồng thuê phòng sắp hết hạn ({$timeText})",
                            'message'     => "Kính gửi quý khách, hợp đồng thuê phòng ({$roomTitle}) của bạn sẽ kết thúc vào ngày {$endDateFormatted} ({$timeText}). Nếu bạn có nhu cầu tiếp tục gia hạn hoặc có kế hoạch trả phòng, vui lòng liên hệ nhân viên quản lý nhà để được tư vấn và hỗ trợ kịp thời.",
                            'type'        => 'warning',
                            'is_read'     => false,
                            'category'    => 'contract_expiring',
                            'action_url'  => url('/nha-dang-thue'),
                        ]);
                        $notifiedCount++;
                        $log(" -> Đã gửi thông báo sắp hết hạn đến Khách thuê ID: {$tenantUserId} cho HĐ: {$roomCode}");
                    }
                }
            }
        }

        return $notifiedCount;
    }
}
