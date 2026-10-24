<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'invoices';

    protected $fillable = [
        'contract_id',
        'room_id',
        'post_id',
        'owner_id',
        'created_by',
        'staff_id',
        'tenant_id',
        'tenant_name',
        'invoice_code',
        'billing_month', // vd: '10/2026' hoặc '2026-10'
        'billing_period',
        'month',
        'year',
        'deposit',
        'rent_price',
        'electricity_amount',
        'electricity_old',
        'electricity_new',
        'water_amount',
        'water_old',
        'water_new',
        'service_amount',
        'damage_fee',
        'deposit_deducted',
        'type', // 'monthly', 'first_month', 'final_settlement', 'liquidation'
        'invoice_type',
        'total_amount',
        'status', // 'paid', 'unpaid'
        'payment_status', // 'paid', 'unpaid'
        'payment_date',
        'note',
        'payment_method',        // 'bank_transfer', 'cash'
        'proof_image',           // URL hoặc path ảnh bill khách chuyển khoản
        'collected_by',          // staff_id (user_id của nhân sự thu tiền mặt)
        'is_remitted_to_admin',  // boolean: đã nộp tiền mặt cho admin/công ty chưa
        'transfer_reported_at',  // thời gian khách báo chuyển khoản
        'remitted_at',           // thời gian nhân viên nộp tiền
        'admin_approved_at',     // thời gian admin duyệt chuyển khoản
        'admin_approved_by',     // admin duyệt
        'rejection_reason',      // lý do từ chối nếu bill không hợp lệ
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', '_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', '_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', '_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', '_id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by', '_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'admin_approved_by', '_id');
    }

    /**
     * Tên phòng hoặc tiêu đề phòng liên quan đến hóa đơn
     */
    public function getRoomDisplayNameAttribute()
    {
        if ($this->post && !empty($this->post->title)) {
            return $this->post->title;
        }
        if ($this->contract && !empty($this->contract->tenant_name)) {
            return 'Phòng của ' . $this->contract->tenant_name;
        }
        return 'Phòng ' . ($this->room_id ?? $this->post_id ?? 'RentHome');
    }

    /**
     * Tên nhân sự thu tiền
     */
    public function getCollectorDisplayNameAttribute()
    {
        if ($this->collector) {
            return $this->collector->account_name ?? ($this->collector->name ?? ($this->collector->username ?? 'Nhân viên'));
        }
        if (!empty($this->collected_by)) {
            $user = User::find($this->collected_by);
            if ($user) {
                return $user->account_name ?? ($user->name ?? ($user->username ?? 'Nhân viên'));
            }
        }
        return 'Chưa xác định';
    }
}
