<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends BaseModel
{
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, "attribute_id", "id");
    }
}
