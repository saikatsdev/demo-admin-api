<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends BaseModel
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'current_status_id', 'id');
    }

    public function courierPending(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_status_id', 'id')
        ->where('current_status_id',  OrderStatusEnum::ON_THE_WAY)
        ->where('courier_status_id',  OrderStatusEnum::COURIER_PENDING);
    }

    public function courierReceived(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_status_id', 'id')
        ->where('current_status_id',  OrderStatusEnum::ON_THE_WAY)
        ->where('courier_status_id',  OrderStatusEnum::COURIER_RECEIVED);
    }
}
