<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OrderRepository;
use App\Http\Requests\Frontend\OrderRequest;
use App\Http\Requests\Frontend\UpSellOrderRequest;
use App\Http\Resources\Frontend\Order\OrderResource;
use App\Http\Resources\Frontend\Order\OrderCollection;

class OrderController extends BaseController
{
    protected $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->repository->customerOrderList($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(OrderRequest $request)
    {
        try {
            $order = $this->repository->customerOrder($request);

            $order = new OrderResource($order);

            return $this->sendResponse($order, 'Order submitted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function upSellOrder(UpSellOrderRequest $request)
    {
        try {
            $order = $this->repository->upSellOrder($request);

            $order = new OrderResource($order);

            if ($request->payment_gateway_id == 1) {
                return $this->sendResponse($order, 'Order submitted successfully', 200);
            } else if ($request->payment_gateway_id == 2) {
                return $this->sendResponse($order, 'Order submitted successfully', 200);
            } else {
                return $this->sendResponse($order, 'Order submitted successfully', 200);
            }
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function thankYouPageOrder(ThankYouPageOrderRequest $request)
    {
        try {
            $order = $this->repository->thankYouPageOrder($request);

            $order = new OrderResource($order);

            if ($request->payment_gateway_id == 1) {
                return $this->sendResponse($order, 'Order submitted successfully', 200);
            } else if ($request->payment_gateway_id == 2) {
                return $this->sendResponse($order, 'Order submitted successfully', 200);
            } else {
                return $this->sendResponse($order, 'Order submitted successfully', 200);
            }
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
        try {
            $order = $this->repository->customerShow($id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
