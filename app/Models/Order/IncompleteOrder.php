<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncompleteOrder extends BaseModel
{
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, "status_id", "id");
    }

    public function details(): HasMany
    {
        return $this->hasMany(IncompleteOrderDetail::class, "incomplete_order_id", "id");
    }
}
