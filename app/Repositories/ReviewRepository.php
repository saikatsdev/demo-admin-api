<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Product\Review;
use App\Exceptions\CustomException;

class ReviewRepository
{
    public function __construct(protected Review $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $productId    = $request->input('product_id', null);
        $userId       = $request->input('user_id', null);

        $reviews = $this->model->with([
            'product:id,name,slug,img_path',
            'user:id,username'
        ])
        ->when($productId, fn($query) => $query->where("product_id", $productId))
        ->when($userId, fn($query) => $query->where("user_id", $userId))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $reviews;
    }

    public function store($request)
    {
        $review = new $this->model();

        $review->user_id    = $request->user_id;
        $review->product_id = $request->product_id;
        $review->rate       = $request->rate;
        $review->comment    = $request->comment;
        $review->status     = $request->status;
        $review->save();

        return $review;
    }

    public function show($id)
    {
        $review = $this->model->with([
            'user:id,username',
            "product:id,name,slug,img_path",
            "createdBy:id,username",
            "updatedBy:id,username"
        ])->find($id);

        if (!$review) {
            throw new CustomException("Review not found");
        }

        return $review;
    }

    public function update($request, $id)
    {
        $review = $this->model->find($id);

        if (!$review) {
            throw new CustomException("Review not found");
        }

        $review->user_id    = $request->user_id;
        $review->product_id = $request->product_id;
        $review->rate       = $request->rate;
        $review->comment    = $request->comment;
        $review->status     = $request->status;
        $review->save();

        return $review;
    }

    public function delete($id)
    {
        $review = $this->model->find($id);
        if (!$review) {
            throw new CustomException('Review not found');
        }

        return $review->forceDelete();
    }
}
