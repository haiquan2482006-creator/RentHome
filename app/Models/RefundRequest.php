<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class RefundRequest extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'refund_requests';

    protected $fillable = [
        'contract_id',
        'contract_code',
        'room_id',
        'post_id',
        'owner_id',
        'tenant_id',
        'tenant_name',
        'tenant_phone',
        'tenant_bank_account',
        'tenant_bank_name',
        'tenant_bank_holder',
        'total_debt',
        'deposit',
        'unused_rent_refund',
        'refund_amount',
        'damage_fee',
        'damage_reason',
        'liquidation_note',
        'status', // 'pending' (chờ kế toán duyệt), 'approved' (đã duyệt), 'refunded' (đã chuyển khoản hoàn tất), 'rejected'
        'created_by',
        'approved_by',
        'refunded_by',
        'refunded_at',
        'proof_image',
        'payment_reference',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', '_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', '_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', '_id');
    }

    public function refundedByUser()
    {
        return $this->belongsTo(User::class, 'refunded_by', '_id');
    }
}
