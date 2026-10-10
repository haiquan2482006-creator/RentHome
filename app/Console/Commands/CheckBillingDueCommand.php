<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Notification;
use Carbon\Carbon;

class CheckBillingDueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra các hợp đồng đến hạn chốt hóa đơn / đóng tiền phòng và gửi thông báo tự động thời gian thực';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $today = $now->day;
        $currentMonthStr = $now->format('m/Y'); // vd: '10/2026'

        $this->info("Bắt đầu kiểm tra hạn đóng tiền phòng ngày {$today} - Tháng {$currentMonthStr}...");

        $contracts = Contract::where('status', '!=', 'cancelled')->get();
        $notifiedCount = 0;

        foreach ($contracts as $contract) {
            $rawBillDate = $contract->bill_date ?? $contract->billing_date ?? 5;
            $billDay = (int) preg_replace('/[^0-9]/', '', (string)$rawBillDate);
            if ($billDay <= 0 || $billDay > 31) {
                $billDay = 5;
            }

            // Chỉ xử lý khi ngày hiện tại đã đến hạn hoặc quá hạn chốt hóa đơn
            if ($today < $billDay) {
                continue;
            }

            $contractId = (string)($contract->_id ?? $contract->id);

            // Kiểm tra xem tháng này đã có hóa đơn được thanh toán chưa
            $isPaid = Invoice::where('contract_id', $contractId)
                ->where(function ($q) use ($now, $currentMonthStr) {
                    $q->where('billing_month', $currentMonthStr)
                      ->orWhere('month', $now->month)
                      ->orWhere('billing_period', 'like', "%{$currentMonthStr}%")
                      ->orWhereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                })
                ->where(function ($q) {
                    $q->where('status', 'paid')
                      ->orWhere('payment_status', 'paid');
                })
                ->exists();

            if (!$isPaid && $contract->payment_status === 'paid' && $contract->last_paid_period === $currentMonthStr) {
                $isPaid = true;
            }

            // Nếu CHƯA thanh toán và ĐẾN HẠN
            if (!$isPaid) {
                $roomName = $contract->post ? $contract->post->title : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng trọ');
                $roomCode = $contract->contract_code ?: ('PH-' . substr($contractId, -4));
                $amount = number_format($contract->rent_price ?? 0);

                // 1. Gửi thông báo cho Chủ nhà (Owner) nếu chưa gửi trong tháng này
                if (!empty($contract->owner_id)) {
                    $alreadyNotifiedOwner = Notification::where('user_id', (string)$contract->owner_id)
                        ->where('category', 'billing_due')
                        ->where('message', 'like', "%kỳ {$currentMonthStr}%")
                        ->where('message', 'like', "%{$roomCode}%")
                        ->exists();

                    if (!$alreadyNotifiedOwner) {
                        Notification::create([
                            'user_id'  => (string)$contract->owner_id,
                            'post_id'  => (string)($contract->post_id ?? ''),
                            'title'    => "Đến hạn thu tiền phòng {$roomCode} (Kỳ {$currentMonthStr})",
                            'message'  => "Phòng {$roomCode} ({$roomName}) có ngày chốt mùng {$billDay} hàng tháng đã đến hạn thanh toán kỳ {$currentMonthStr}. Số tiền cần thu: {$amount}đ. Khách thuê: {$contract->tenant_name} ({$contract->tenant_phone}).",
                            'type'     => 'warning',
                            'is_read'  => false,
                            'category' => 'billing_due',
                        ]);
                        $notifiedCount++;
                    }
                }

                // 2. Gửi thông báo cho Khách thuê (Tenant) nếu có tài khoản
                $tenantUserId = $contract->tenant_id ?? null;
                if (!empty($tenantUserId)) {
                    $alreadyNotifiedTenant = Notification::where('user_id', (string)$tenantUserId)
                        ->where('category', 'billing_due')
                        ->where('message', 'like', "%kỳ {$currentMonthStr}%")
                        ->exists();

                    if (!$alreadyNotifiedTenant) {
                        Notification::create([
                            'user_id'  => (string)$tenantUserId,
                            'post_id'  => (string)($contract->post_id ?? ''),
                            'title'    => "Nhắc nhở thanh toán tiền phòng {$roomCode} (Kỳ {$currentMonthStr})",
                            'message'  => "Tiền thuê phòng {$roomName} kỳ {$currentMonthStr} đã đến hạn thanh toán vào ngày {$billDay}. Số tiền: {$amount}đ. Vui lòng liên hệ chủ nhà để thanh toán đúng hạn.",
                            'type'     => 'warning',
                            'is_read'  => false,
                            'category' => 'billing_due',
                        ]);
                        $notifiedCount++;
                    }
                }
            }
        }

        $this->info("Hoàn tất! Đã tạo {$notifiedCount} thông báo đến hạn đóng tiền.");
        return 0;
    }
}
