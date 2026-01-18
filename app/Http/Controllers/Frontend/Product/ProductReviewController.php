<?php

namespace App\Http\Controllers\Frontend\Product;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Frontend\StoreProductReviewRequest;
use App\Http\Resources\Frontend\Product\ProductReviewResource;
use App\Repositories\Product\ProductReviewRepository;
use Illuminate\Http\Request;

class ProductReviewController extends BaseController
{
    public function __construct(protected ProductReviewRepository $repository){}

    public function store(StoreProductReviewRequest $request)
    {
        $review = $this->repository->store($request);

        $review = new ProductReviewResource($review);

        return $this->sendResponse($review, "Product Review Create Successfully", 200);
    }
}
