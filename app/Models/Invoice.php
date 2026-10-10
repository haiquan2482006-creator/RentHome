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
        'tenant_id',
        'tenant_name',
        'invoice_code',
        'billing_month', // vd: '10/2026' hoặc '2026-10'
        'billing_period',
        'month',
        'year',
        'rent_price',
        'electricity_amount',
        'water_amount',
        'service_amount',
        'total_amount',
        'status', // 'paid', 'unpaid'
        'payment_status', // 'paid', 'unpaid'
        'payment_date',
        'note',
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
}
