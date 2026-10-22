<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Room extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'rooms';

    protected $fillable = [
        'building_id',
        'post_id',
        'room_number',
        'floor',
        'area',
        'rent_price',
        'deposit',
        'status', // 'available' (trống), 'rented' (đã thuê), 'maintenance' (bảo trì)
        'current_contract_id',
        'current_tenant_id',
        'electricity_index',
        'water_index',
        'amenities',
        'notes',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', '_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', '_id');
    }

    public function currentContract()
    {
        return $this->belongsTo(Contract::class, 'current_contract_id', '_id');
    }

    public function currentTenant()
    {
        return $this->belongsTo(User::class, 'current_tenant_id', '_id');
    }
}
