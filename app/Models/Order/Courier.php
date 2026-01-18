<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courier extends BaseModel
{
    public $uploadPath = 'uploads/couriers';

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class, "courier_id", "id");
    }
}
