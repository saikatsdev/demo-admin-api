<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends BaseModel
{
    protected $casts = [
        "attribute_id" => "integer",
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, "attribute_id", "id");
    }

    public function productVariations1(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'attribute_value_id_1', 'id');
    }

    public function productVariations2(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'attribute_value_id_2', 'id');
    }

    public function productVariations3(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'attribute_value_id_3', 'id');
    }
}
