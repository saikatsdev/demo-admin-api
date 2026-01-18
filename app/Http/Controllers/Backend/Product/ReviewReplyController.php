<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Product\StoreReviewReplyRequest;
use App\Repositories\Product\ReviewReplyRepository;
use Illuminate\Http\Request;

class ReviewReplyController extends BaseController
{
    public function __construct(protected ReviewReplyRepository $repository){}

    public function store(StoreReviewReplyRequest $request)
    {
        $reply = $this->repository->store($request);

        return $this->sendResponse($reply, "Review Replied Successfully", 200);
    }
}
