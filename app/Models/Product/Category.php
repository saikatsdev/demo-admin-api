<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    public $uploadPath = 'uploads/categories';

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sectionItems(): HasMany
    {
        return $this->hasMany(CategorySectionItem::class, "category_id", "id");
    }
}
