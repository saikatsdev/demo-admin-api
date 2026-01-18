<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UpSell extends Model
{
    protected $guarded = ["id"];

    protected $casts = [
        "trigger_category_ids" => "array"
    ];

    public function upSellDetails(): HasMany
    {
        return $this->hasMany(UpSellDetail::class);
    }

    public function triggerProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'up_sell_details',
            'up_sell_id',
            'trigger_product_id'
        )->distinct();
    }

    public function offerProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'up_sell_details', 'up_sell_id', 'up_sell_product_id')
        ->withPivot(['custom_name', 'discount_type', 'discount_amount', 'calculated_amount']);
    }
}
