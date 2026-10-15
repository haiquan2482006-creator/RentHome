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
        'payment_cycle',
        'bill_date',
        'billing_date',
        'payment_limit_days',
        'late_penalty_fee',
        'start_date',
        'duration_months',
        'end_date',
        'status',
    ];

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
}
