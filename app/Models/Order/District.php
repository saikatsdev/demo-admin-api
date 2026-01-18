<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends BaseModel
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'district_id', 'id');
    }
}
