<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Contract;
use App\Http\Controllers\ContractLiquidationController;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    /**
     * Lắng nghe sự kiện khi Hóa đơn được cập nhật (updated)
     */
    public function updated(Invoice $invoice): void
    {
        $this->checkAndTerminateContract($invoice);
        $this->handleRentCycleAdvancement($invoice);
    }

    /**
     * Lắng nghe sự kiện khi Hóa đơn được lưu (saved)
     */
    public function saved(Invoice $invoice): void
    {
        $this->checkAndTerminateContract($invoice);
        $this->handleRentCycleAdvancement($invoice);
    }

    /**
     * Cập nhật next_rent_date khi hóa đơn tiền phòng được thanh toán thành công
     */
    private function handleRentCycleAdvancement(Invoice $invoice): void
    {
        $isPaid = ($invoice->status === 'paid' || $invoice->payment_status === 'paid');
        if ($isPaid) {
            \App\Services\InvoiceService::handleInvoicePaid($invoice);
        }
    }

    /**
     * Kiểm tra nếu Hóa đơn thanh lý cuối cùng chuyển sang trạng thái PAID
     * Tự động hoàn tất thanh lý hợp đồng và đưa phòng về trạng thái available
     */
    private function checkAndTerminateContract(Invoice $invoice): void
    {
        $isPaid = ($invoice->status === 'paid' || $invoice->payment_status === 'paid');
        if (!$isPaid) {
            return;
        }

        $isLiquidationInvoice = (
            $invoice->type === 'final_settlement' || 
            $invoice->invoice_type === 'liquidation' || 
            str_starts_with((string)$invoice->invoice_code, 'INV-LIQ-')
        );

        if (!$isLiquidationInvoice && empty($invoice->contract_id)) {
            return;
        }

        $contract = Contract::where('_id', $invoice->contract_id)
            ->orWhere('id', $invoice->contract_id)
            ->orWhere('final_invoice_id', (string)($invoice->_id ?? $invoice->id))
            ->first();

        if ($contract && ($contract->status === 'pending_liquidation' || $isLiquidationInvoice)) {
            if ($contract->status !== 'terminated') {
                try {
                    $controller = new ContractLiquidationController();
                    $controller->executeTermination(
                        $contract, 
                        null, 
                        "Tự động hoàn tất thanh lý (Trigger Observer): Hóa đơn cuối cùng {$invoice->invoice_code} đã thanh toán thành công."
                    );
                } catch (\Throwable $e) {
                    Log::error('InvoiceObserver error terminating contract: ' . $e->getMessage());
                }
            }
        }
    }
}
