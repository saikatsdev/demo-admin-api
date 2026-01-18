<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubSubCategory extends BaseModel
{
    public $uploadPath = 'uploads/subSubCategories';

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, "sub_category_id", "id");
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'sub_sub_category_id', 'id');
    }
}
