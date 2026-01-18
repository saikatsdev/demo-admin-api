<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends BaseModel
{
    public function products() : HasMany
    {
        return $this->hasMany(Product::class, "product_type_id", "id");
    }
}
