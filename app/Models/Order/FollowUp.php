<?php

namespace App\Models\Order;

use App\Models\BaseModel;

class FollowUp extends BaseModel
{
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
