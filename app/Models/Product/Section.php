<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use App\Models\CMS\Banner;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends BaseModel
{
    public $uploadPath = 'uploads/sections';

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'section_products', 'section_id', 'product_id');
    }

    public function banners()
    {
        return $this->hasMany(Banner::class, 'section_id', 'id');
    }
}
