<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategorySection extends BaseModel
{
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, "category_section_items", "category_section_id", "category_id")->withPivot("id", "img_path", "link");
    }
}
