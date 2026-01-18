<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCatalogCategory extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
