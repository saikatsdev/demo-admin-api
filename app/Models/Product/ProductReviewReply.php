<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductReviewReply extends Model
{
    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }
}
