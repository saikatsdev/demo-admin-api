<?php

namespace App\Models\Order;

use App\Models\Product\Product;
use App\Models\Product\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $guarded = ["id"];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, "order_id", "id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function attributeValue1(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_1", "id");
    }

    public function attributeValue2(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_2", "id");
    }

    public function attributeValue3(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_3", "id");
    }
}
