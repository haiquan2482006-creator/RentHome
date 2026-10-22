<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Notification;
use Carbon\Carbon;

class GenerateMonthlyInvoicesCommand extends Command
{
    /**
     * Tên và chữ ký của console command.
     * Hỗ trợ tham số --date để giả lập ngày chạy (rất tiện khi test cronjob).
     *
     * @var string
     */
    protected $signature = 'billing:generate-monthly 
                            {--date= : Ngày giả lập thực thi (định dạng YYYY-MM-DD)} 
                            {--force : Bỏ qua một số điều kiện cảnh báo để tạo ép buộc}';

    /**
     * Mô tả console command.
     *
     * @var string
     */
    protected $description = 'Tự động chốt hóa đơn tiền phòng định kỳ hàng tháng cho các hợp đồng đến hạn (Tự động bỏ qua kỳ chốt đầu tiên của tháng dọn vào)';

    /**
     * Thực thi command.
     */
    public function handle()
    {
        // 1. Xác định thời điểm chạy (mặc định là hôm nay, hoặc ngày truyền vào qua --date để test)
        $now = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $todayDay = (int) $now->day;
        $currentBillingMonth = $now->format('m/Y'); // vd: '11/2026'

        $this->info("=====================================================================");
        $this->info("⚡ BẮT ĐẦU CRONJOB CHỐT HÓA ĐƠN ĐỊNH KỲ: Ngày {$todayDay} | Kỳ {$currentBillingMonth}");
        $this->info("=====================================================================");

        // 2. Truy vấn MongoDB: Lấy tất cả Hợp đồng đang hiệu lực (active)
        $contracts = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly'])->get();

        if ($contracts->isEmpty()) {
            $this->warn("Không có hợp đồng nào đang hoạt động trong hệ thống.");
            return Command::SUCCESS;
        }

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($contracts as $contract) {
            $contractId = (string) ($contract->_id ?? $contract->id);
            $contractCode = $contract->contract_code ?? ('HD-' . substr($contractId, -4));
            $roomName = $contract->post ? $contract->post->title : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng trọ');

            // 3. Lấy ngày chốt tiền định kỳ (billing_day) của hợp đồng này
            $rawBillDate = $contract->bill_date ?? $contract->billing_date ?? 5;
            $billDay = (int) preg_replace('/[^0-9]/', '', (string) $rawBillDate);
            if ($billDay <= 0 || $billDay > 31) {
                $billDay = 5;
            }

            // Nếu hôm nay KHÔNG PHẢI ngày chốt của hợp đồng này -> Bỏ qua
            if ($todayDay !== $billDay && !$this->option('force')) {
                continue;
            }

            $this->line("---------------------------------------------------------------------");
            $this->line("🔍 Đang kiểm tra hợp đồng: [{$contractCode}] - {$roomName} (Ngày chốt: Mùng {$billDay})");

            // 4. KIỂM TRA LOGIC THỜI GIAN BẮT ĐẦU HỢP ĐỒNG (start_date)
            if (empty($contract->start_date)) {
                $this->warn("⚠️  Hợp đồng [{$contractCode}] chưa có start_date, bỏ qua.");
                $skippedCount++;
                continue;
            }

            $startDate = Carbon::parse($contract->start_date);

            // Nếu ngày bắt đầu hợp đồng trong tương lai so với ngày chốt hôm nay -> Bỏ qua
            if ($startDate->gt($now)) {
                $this->line("⏭️  Bỏ qua [{$contractCode}]: Hợp đồng bắt đầu ở tương lai ({$startDate->format('d/m/Y')}).");
                $skippedCount++;
                continue;
            }

            /*
             * =========================================================================
             * 5. BUSINESS LOGIC CỐT LÕI: BỎ QUA KỲ CHỐT TIỀN ĐẦU TIÊN
             * =========================================================================
             * Quy tắc:
             * - Lúc ký hợp đồng, khách ĐÃ THANH TOÁN tiền cọc + tiền phòng tháng đầu tiên (Thu trước).
             * - Ví dụ: Ký hợp đồng ngày 01/10/2026, ngày chốt định kỳ là mùng 10 hàng tháng.
             *   + Vào ngày 10/10/2026: Cùng tháng/năm 10/2026 với ngày ký -> ĐÃ ĐÓNG TIỀN THÁNG 10 RỒI.
             *     -> Hệ thống BẮT BUỘC BỎ QUA, không sinh hóa đơn mới.
             *   + Vào ngày 10/11/2026: Sang tháng 11/2026 (khác tháng ký) -> Đúng 1 chu kỳ sử dụng.
             *     -> Hệ thống SINH HÓA ĐƠN MỚI: Thu tiền phòng tháng 11 + chốt điện nước đã dùng từ 01/10 đến 10/11.
             */
            if ($startDate->format('Y-m') === $now->format('Y-m') && !$this->option('force')) {
                $this->info("⏭️  [BỎ QUA KỲ ĐẦU TIÊN]: Hợp đồng [{$contractCode}] vừa bắt đầu trong tháng {$now->format('m/Y')} ({$startDate->format('d/m/Y')}). Khách đã đóng tiền tháng đầu khi ký, không tạo hóa đơn mới.");
                $skippedCount++;
                continue;
            }

            // 6. CHỐNG TRÙNG LẶP HÓA ĐƠN (IDEMPOTENCY)
            // Kiểm tra xem kỳ này ($currentBillingMonth) đã từng tạo hóa đơn cho hợp đồng này chưa
            $alreadyExists = Invoice::where('contract_id', $contractId)
                ->where(function ($q) use ($now, $currentBillingMonth) {
                    $q->where('billing_month', $currentBillingMonth)
                      ->orWhere(function ($sub) use ($now) {
                          $sub->where('month', (int) $now->month)
                              ->where('year', (int) $now->year);
                      });
                })
                ->exists();

            if ($alreadyExists && !$this->option('force')) {
                $this->line("ℹ️  Hóa đơn kỳ [{$currentBillingMonth}] của hợp đồng [{$contractCode}] đã được tạo trước đó, bỏ qua.");
                $skippedCount++;
                continue;
            }

            // 7. LẤY CHỈ SỐ ĐIỆN / NƯỚC CŨ ĐỂ LÀM MỐC TÍNH TIỀN CHO KỲ NÀY
            // Lấy từ hóa đơn phát hành gần nhất, nếu là kỳ thứ 2 thì lấy chính chỉ số đầu (start) lúc ký hợp đồng
            $lastInvoice = Invoice::where('contract_id', $contractId)->latest()->first();

            $electricityOld = ($lastInvoice && !empty($lastInvoice->electricity_new))
                ? (int) $lastInvoice->electricity_new
                : (int) ($contract->electricity_start ?? 0);

            $waterOld = ($lastInvoice && !empty($lastInvoice->water_new))
                ? (int) $lastInvoice->water_new
                : (int) ($contract->water_start ?? 0);

            // 8. TÍNH TOÁN CÁC KHOẢN PHÍ ĐỊNH KỲ
            $rentPrice = (float) ($contract->rent_price ?? 0); // Tiền phòng thu trước cho tháng này
            $serviceAmount = (float) ($contract->internet_price ?? 0) + (float) ($contract->garbage_price ?? 0); // Phí dịch vụ cố định
            
            // Điện và Nước để tạm tính = 0, chờ chủ nhà nhập chỉ số mới (new meter reading)
            $electricityAmount = 0;
            $waterAmount = 0;
            $totalAmount = $rentPrice + $serviceAmount; // Tạm tính ban đầu

            $newInvoiceCode = 'INV-' . $now->format('Ym') . '-' . strtoupper(substr(uniqid(), -4));

            // 9. TỰ ĐỘNG TẠO HÓA ĐƠN MỚI VÀO COLLECTION 'invoices' (MongoDB)
            $invoice = Invoice::create([
                'contract_id'        => $contractId,
                'room_id'            => (string) ($contract->room_id ?? $contract->post_id),
                'post_id'            => (string) $contract->post_id,
                'owner_id'           => (string) $contract->owner_id,
                'tenant_id'          => (string) $contract->tenant_id,
                'tenant_name'        => $contract->tenant_name ?? 'Khách thuê',
                'invoice_code'       => $newInvoiceCode,
                'billing_month'      => $currentBillingMonth,
                'billing_period'     => 'Kỳ tháng ' . $currentBillingMonth,
                'month'              => (int) $now->month,
                'year'               => (int) $now->year,
                'deposit'            => 0, // Cọc chỉ thu 1 lần ở tháng đầu, tháng định kỳ không thu
                'rent_price'         => $rentPrice,
                'electricity_old'    => $electricityOld,
                'electricity_new'    => null, // Chờ chủ nhà chốt
                'electricity_amount' => $electricityAmount,
                'water_old'          => $waterOld,
                'water_new'          => null, // Chờ chủ nhà chốt
                'water_amount'       => $waterAmount,
                'service_amount'     => $serviceAmount,
                'total_amount'       => $totalAmount,
                'status'             => 'draft', // Trạng thái 'draft' (Bản nháp - chờ chủ nhà điền số điện nước mới)
                'payment_status'     => 'unpaid',
                'note'               => "Hóa đơn tự động kỳ {$currentBillingMonth} (Chờ chủ nhà cập nhật chỉ số điện/nước đã dùng)",
            ]);

            $this->info("✅ ĐÃ TẠO HÓA ĐƠN NHÁP: [{$newInvoiceCode}] cho {$roomName} - Tiền phòng: " . number_format($rentPrice) . "đ (Số điện cũ: {$electricityOld}, Số nước cũ: {$waterOld})");
            $createdCount++;

            // 10. GỬI THÔNG BÁO CHO CHỦ NHÀ ĐỂ VÀO NHẬP CHỈ SỐ ĐIỆN NƯỚC
            if (!empty($contract->owner_id)) {
                Notification::create([
                    'user_id'  => (string) $contract->owner_id,
                    'post_id'  => (string) $contract->post_id,
                    'title'    => "Đến kỳ chốt hóa đơn {$currentBillingMonth} - {$roomName}",
                    'message'  => "Hệ thống đã tự động tạo hóa đơn nháp [{$newInvoiceCode}] cho {$roomName}. Vui lòng nhập chỉ số điện nước mới để hoàn tất phiếu tính tiền gửi khách thuê.",
                    'type'     => 'info',
                    'is_read'  => false,
                    'category' => 'invoice',
                ]);
            }
        }

        $this->info("=====================================================================");
        $this->info("🎉 HOÀN TẤT CRONJOB: Đã tạo {$createdCount} hóa đơn mới | Bỏ qua {$skippedCount} hợp đồng.");
        $this->info("=====================================================================");

        return Command::SUCCESS;
    }
}
