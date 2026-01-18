<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DownSell extends BaseModel
{
    public function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'down_sell_product', 'down_sell_id', 'product_id');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'active')->where('started_at', '<=', now())->where('ended_at', '>=', now());
    }
}
