<?php

namespace App\Repositories\Product;

use App\Exceptions\CustomException;
use App\Models\Product\ProductReview;
use App\Models\Product\ProductReviewReply;

class ReviewReplyRepository
{
    public function __construct(protected ProductReviewReply $model){}

    public function store($request)
    {
        $review = ProductReview::find($request->review_id);

        if(!$review){
            throw new CustomException('Review not found');
        }

        $reviewReply = $this->model->where('product_review_id', $request->review_id);

        if(!$reviewReply){
            throw new CustomException("Review not Found");
        }else{
            $reviewReply = new $this->model;
        }

        $reviewReply->product_review_id = $request->review_id;
        $reviewReply->reply             = $request->reply;

        $reviewReply->save();

        return $reviewReply;
    }
}
