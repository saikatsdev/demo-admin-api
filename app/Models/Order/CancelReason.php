<?php

namespace  App\Models\Order;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CancelReason extends BaseModel
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, "cancel_reason_id", "id");
    }
}
