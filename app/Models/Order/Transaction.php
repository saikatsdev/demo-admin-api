<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
