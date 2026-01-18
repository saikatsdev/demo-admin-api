<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Backend\Product\StoreProductReviewRequest;
use App\Http\Resources\Backend\Product\ProductReviewCollection;
use App\Http\Resources\Backend\Product\ProductReviewResource;
use App\Repositories\Product\ProductReviewRepository;
use Illuminate\Http\Request;

class ProductReviewController extends BaseController
{
    public function __construct(protected ProductReviewRepository $repository){}

    public function index(Request $request)
    {
        $reviews = $this->repository->index($request);

        $reviews = new ProductReviewCollection($reviews);

        return $this->sendResponse($reviews, "Product Reviews List", 200);
    }

    public function store(StoreProductReviewRequest $request)
    {
        $review = $this->repository->store($request);

        $review = new ProductReviewResource($review);

        return $this->sendResponse($review, 'Review Created Successfully');
    }

    public function show($id)
    {
        $review = $this->repository->show($id);

        $review = new ProductReviewResource($review);

        return $this->sendResponse($review, 'Review Show', 200);
    }

    public function update(Request $request, $id)
    {
        $review = $this->repository->update($request, $id);

        $review = new ProductReviewResource($review);

        return $this->sendResponse($review, 'Review Update Successfully', 200);
    }

    public function statusUpdate(Request $request)
    {
        $review = $this->repository->statusUpdate($request);

        $review = new ProductReviewResource($review);

        return $this->sendResponse($review, "Status update successfully", 200);
    }

    public function destroy($id)
    {
        $review = $this->repository->destroy($id);

        return $this->sendResponse($review, "Review Deleted Successfully", 200);
    }
}
