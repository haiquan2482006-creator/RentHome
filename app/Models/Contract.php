<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Contract extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contracts';

    protected $fillable = [
        'contract_code',
        'contact_id',
        'post_id',
        'room_id',
        'owner_id',
        'created_by',
        'staff_id',
        'owner_phone',
        'tenant_id',
        'tenant_name',
        'tenant_phone',
        'tenant_email',
        'tenant_id_card',
        'tenant_account',
        'tenant_message',
        'rent_price',
        'deposit',
        'electricity_price',
        'electricity_start',
        'water_calculation_type',
        'water_price_block',
        'water_start',
        'water_price_person',
        'water_people_count',
        'internet_price',
        'garbage_price',
        'service_fee',
        'parking_fee',
        'license_plates',
        'cccd_images',
        'contract_documents',
        'payment_cycle',
        'next_rent_date',
        'deposit_amount',
        'bill_date',
        'billing_date',
        'payment_limit_days',
        'late_penalty_fee',
        'start_date',
        'duration_months',
        'end_date',
        'status',
        // Trường nghiệp vụ Thanh lý & Chốt công nợ
        'checkout_request',
        'liquidation_details',
        'final_invoice_id',
        'refund_request_id',
        'terminated_at',
        'terminated_by',
        'liquidation_scenario',
    ];

    protected $casts = [
        'payment_cycle' => 'integer',
    ];

    /**
     * Accessor đồng bộ trường price với rent_price
     */
    public function getPriceAttribute()
    {
        return (float)($this->rent_price ?? 0);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', '_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', '_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', '_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', '_id');
    }

    public function finalInvoice()
    {
        return $this->belongsTo(Invoice::class, 'final_invoice_id', '_id');
    }

    public function refundRequest()
    {
        return $this->belongsTo(RefundRequest::class, 'refund_request_id', '_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', '_id');
    }

    /**
     * Lấy số tiền cọc an toàn từ deposit hoặc deposit_amount
     */
    public function getDepositSafeAttribute(): float
    {
        $dep = $this->deposit ?? ($this->deposit_amount ?? 0);
        if (is_numeric($dep)) return (float)$dep;
        $clean = preg_replace('/[^\d]/', '', (string)$dep);
        return $clean ? (float)$clean : 0.0;
    }
}
