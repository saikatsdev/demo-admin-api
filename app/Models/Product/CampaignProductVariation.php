<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProductVariation extends Model
{
    protected $guarded = ["id"];

    public function campaignProduct(): BelongsTo
    {
        return $this->belongsTo(CampaignProduct::class, "campaign_product_id", "id");
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
