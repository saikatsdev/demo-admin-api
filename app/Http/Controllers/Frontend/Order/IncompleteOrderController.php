<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\IncompleteOrderRepository;
use App\Http\Requests\Frontend\IncompleteOrderRequest;

class IncompleteOrderController extends BaseController
{
    protected $repository;

    public function __construct(IncompleteOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(IncompleteOrderRequest $request)
    {
        try {
            $this->repository->store($request);

            return $this->sendResponse([], "Data store successfully", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
