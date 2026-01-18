<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategory extends BaseModel
{
    public $uploadPath = 'uploads/subCategories';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subSubCategories(): HasMany
    {
        return $this->hasMany(SubSubCategory::class, 'sub_category_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'sub_category_id', 'id');
    }
}
