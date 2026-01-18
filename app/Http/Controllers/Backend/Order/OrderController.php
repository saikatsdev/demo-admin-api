<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OrderRepository;
use App\Http\Resources\Backend\Order\OrderResource;
use App\Http\Resources\Backend\Order\OrderCollection;
use App\Http\Requests\Backend\Order\StoreOrderRequest;
use App\Http\Requests\Backend\Order\UpdateOrderRequest;
use App\Http\Resources\Backend\Order\OrderDetailResource;
use App\Http\Resources\Backend\Order\OrderListCollection;
use App\Http\Resources\Backend\Product\LatestOrderResource;
use App\Http\Requests\Backend\Order\UpdateOrderStatusRequest;
use App\Http\Requests\Backend\Order\StoreAdditionalCostRequest;
use App\Http\Requests\Backend\Order\UpdateOrderPaidStatusRequest;

class OrderController extends BaseController
{
    public function __construct(protected OrderRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->index($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function latestOrders()
    {
        $orders = $this->repository->latestOrders();

        $orders = LatestOrderResource::collection($orders);

        return $this->sendResponse($orders, "Latest Order", 200);
    }
    
    public function list(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $response = $this->repository->list($request);

            $result = [
                'orders'            => new OrderListCollection($response["orders"]),
                "total_orders"      => $response["total_orders"],
                "total_order_price" => $response["total_order_price"],
                "status_report"     => $response["status_report"]
            ];

            return $this->sendResponse($result, "Order list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreOrderRequest $request)
    {
        if (!$request->user()->hasPermission("orders-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->store($request);

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order submitted successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->show($id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->update($request, $id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("orders-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->destroy($id);

            return $this->sendResponse($order, "Order Deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->updateStatus($request);

            return $this->sendResponse($order, "Status updated successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function sendCourier(Request $request)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->sendCourier($request);

            return $this->sendResponse($order, "Courier send successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updatePaidStatus(UpdateOrderPaidStatusRequest $request)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->updatePaidStatus($request);

            return $this->sendResponse($order, "Paid status updated successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function addAdditionCost(StoreAdditionalCostRequest $request)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->addAdditionCost($request);

            return $this->sendResponse($order, "Cost added updated successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function multipleInvoice(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->multipleInvoice($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Multiple invoice", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderHistory(Request $request, $id)
    {
        try {
            $orderHistories = $this->repository->orderHistory($request, $id);

            return $this->sendResponse($orderHistories, "Order history", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->trashList($request);

            $orders = new OrderCollection($orders);

            return $this->sendResponse($orders, "Order trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->restore($id);

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order restore successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission("orders-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->permanentDelete($id);

            return $this->sendResponse($order, "Order permanent delete successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function districtWiseOrderCount(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $districtWiseOrderCount = $this->repository->districtWiseOrderCount();

            return $this->sendResponse($districtWiseOrderCount, "District wise order count", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function searchByPhoneNumber(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->searchByPhoneNumber($request);

            // $orders = OrderDetailResource::collection($orders);

            return $this->sendResponse($orders, "Order list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function pikingList(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $pikingList = $this->repository->pikingList();

            $pikingList = OrderDetailResource::collection($pikingList);

            return $this->sendResponse($pikingList, "Order piking list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function itemList(Request $request)
    {
        if (!$request->user()->hasPermission("orders-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderItemList = $this->repository->itemList($request);

            $orderItemList = OrderDetailResource::collection($orderItemList);

            return $this->sendResponse($orderItemList, "Order item list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateInvoice($id)
    {
        $order = $this->repository->updateInvoice($id);

        $order = new OrderResource($order);

        return $this->sendResponse($order, "Order list", 200);
    }
}
