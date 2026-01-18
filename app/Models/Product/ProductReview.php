<?php

namespace App\Models\Product;

use App\Models\BaseModel;

class ProductReview extends BaseModel
{
    public $uploadPath = "uploads/productReview";

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reply()
    {
        return $this->hasOne(ProductReviewReply::class);
    }
}
