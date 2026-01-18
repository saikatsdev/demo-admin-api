<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpSellDetail extends Model
{
    public function upSell() : BelongsTo
    {
        return $this->belongsTo(UpSell::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'up_sell_product_id');
    }
}
