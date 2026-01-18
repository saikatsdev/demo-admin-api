<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use App\Models\Order\BlockUserDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockUser extends BaseModel
{
    protected $casts = [
        "is_block"             => "boolean",
        "is_permanent_block"   => "boolean",
        "is_permanent_unblock" => "boolean"
    ];

    public function details(): HasMany
    {
        return $this->hasMany(BlockUserDetail::class, "block_user_id", "id");
    }

    public function  orders(): HasMany
    {
        return $this->hasMany(Order::class, "block_user_id", "id");
    }
}
