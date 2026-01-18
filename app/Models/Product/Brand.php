<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends BaseModel
{
    public $uploadPath = 'uploads/brands';

    public function products() : HasMany
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }
}
