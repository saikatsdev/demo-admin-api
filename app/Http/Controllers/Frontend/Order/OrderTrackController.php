<?php

namespace App\Http\Controllers\Frontend\Order;

use App\Http\Controllers\BaseController;
use App\Repositories\Order\OrderTrackRepository;
use App\Http\Requests\Frontend\Order\OrderTrackRequest;

class OrderTrackController extends BaseController
{
    public function __construct(protected OrderTrackRepository $repository){}

    public function index(OrderTrackRequest $request)
    {
        $order = $this->repository->index($request);

        return $this->sendResponse($order, 'Order found', 200);
    }
}
