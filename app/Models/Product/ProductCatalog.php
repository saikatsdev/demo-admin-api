<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCatalog extends BaseModel
{
    public function catalogType(): BelongsTo
    {
        return $this->belongsTo(ProductCatalogType::class, "product_catalog_type_id", "id");
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProductCatalogCategory::class, "product_catalog_id", "id");
    }
}
