<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Tính toán tiền phòng (Room Rent) dựa trên chu kỳ thanh toán và ngày chốt sổ
     *
     * @param Contract $contract
     * @param Carbon|string|null $currentBillingDate
     * @return float
     */
    public static function calculateRoomRentFee(Contract $contract, $currentBillingDate = null): float
    {
        $current_billing_date = $currentBillingDate ? Carbon::parse($currentBillingDate) : Carbon::now();

        // Khởi tạo next_rent_date nếu hợp đồng chưa có
        if (empty($contract->next_rent_date)) {
            $startDate = !empty($contract->start_date) ? Carbon::parse($contract->start_date) : Carbon::now();
            $paymentCycle = (int)($contract->payment_cycle ?: 1);
            $contract->next_rent_date = $startDate->copy()->addMonths($paymentCycle)->toDateString();
            $contract->saveQuietly();
        }

        $nextRentDate = Carbon::parse($contract->next_rent_date);
        $paymentCycle = (int)($contract->payment_cycle ?: 1);
        $price = (float)($contract->price ?: ($contract->rent_price ?? 0));

        $roomRentFee = 0;
        
        // So sánh theo Kỳ hóa đơn / Tháng (Y-m):
        // 1. Nếu tháng chốt hóa đơn (billing_month dạng Y-m) >= tháng của next_rent_date
        // 2. Hoặc nếu ngày chốt sổ thực tế >= ngày next_rent_date
        // 3. Hoặc nếu nhân sự chốt số điện nước sớm vào ngày 25-31 cuối tháng để xuất hóa đơn cho đầu tháng sau
        $billingYearMonth = $current_billing_date->format('Y-m');
        $nextRentYearMonth = $nextRentDate->format('Y-m');
        $nextMonthYearMonth = $current_billing_date->copy()->addDays(7)->format('Y-m');

        $isDue = false;
        if ($billingYearMonth >= $nextRentYearMonth) {
            $isDue = true;
        } elseif ($current_billing_date->startOfDay()->greaterThanOrEqualTo($nextRentDate->startOfDay())) {
            $isDue = true;
        } elseif ($current_billing_date->day >= 25 && $nextMonthYearMonth >= $nextRentYearMonth) {
            $isDue = true;
        }

        if ($isDue) {
            // Đã đến hạn thu tiền nhà theo chu kỳ
            $roomRentFee = $price * $paymentCycle;
        }

        return (float)$roomRentFee;
    }

    /**
     * Xử lý khi Hóa đơn chuyển status = 'paid':
     * Tự động gia hạn next_rent_date tăng thêm payment_cycle tháng
     *
     * @param Invoice $invoice
     * @return void
     */
    public static function handleInvoicePaid(Invoice $invoice): void
    {
        // Chỉ xử lý nếu hóa đơn có phát sinh thu tiền phòng
        if (empty($invoice->contract_id) || (float)($invoice->rent_price ?? 0) <= 0) {
            return;
        }

        // Chặn cộng dồn lặp nếu hóa đơn đã được xử lý chu kỳ trước đó
        if (!empty($invoice->rent_cycle_applied)) {
            return;
        }

        $contract = Contract::where('_id', $invoice->contract_id)
            ->orWhere('id', $invoice->contract_id)
            ->orWhere('contract_code', $invoice->contract_id)
            ->first();

        if (!$contract) {
            return;
        }

        $paymentCycle = (int)($contract->payment_cycle ?: 1);
        $currentNextDate = !empty($contract->next_rent_date)
            ? Carbon::parse($contract->next_rent_date)
            : Carbon::parse($contract->start_date ?? now());

        // Update lại: $contract->next_rent_date->addMonths($contract->payment_cycle);
        $newNextRentDate = $currentNextDate->copy()->addMonths($paymentCycle)->toDateString();
        $contract->next_rent_date = $newNextRentDate;
        $contract->last_rent_paid_at = now()->toDateTimeString();
        $contract->saveQuietly();

        // Gắn cờ hóa đơn đã kích hoạt cộng chu kỳ
        $invoice->rent_cycle_applied = true;
        $invoice->saveQuietly();

        Log::info("InvoiceService: Hóa đơn {$invoice->invoice_code} thanh toán thành công. Đã dời next_rent_date của hợp đồng {$contract->contract_code} sang ngày {$newNextRentDate}");
    }
}
