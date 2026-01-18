<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\IncompleteOrderRepository;
use App\Http\Requests\Backend\Order\IncompleteOrderRequest;
use App\Http\Resources\Backend\Order\IncompleteOrderResource;
use App\Http\Resources\Backend\Order\IncompleteOrderCollection;

class IncompleteOrderController extends BaseController
{
    public function __construct(protected IncompleteOrderRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('incomplete-orders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
    
        try {
            $result = $this->repository->index($request);
    
            $ordersCollection = new IncompleteOrderCollection($result['orders']);
    
            $data = [
                'summary' => [
                    'total_orders'    => (int) $result['summary']->total_orders,
                    'total_pending'   => (int) $result['summary']->total_pending,
                    'total_approved'  => (int) $result['summary']->total_approved,
                    'total_cancelled' => (int) $result['summary']->total_cancelled,
                ],
                'orders' => $ordersCollection,
            ];
    
            return $this->sendResponse($data, 'Incomplete order list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('incomplete-orders-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $incompleteOrder = $this->repository->show($id);

            $incompleteOrder = new IncompleteOrderResource($incompleteOrder);

            return $this->sendResponse($incompleteOrder, "Guest single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(IncompleteOrderRequest $request, $id)
    {
        if (!$request->user()->hasPermission('incomplete-orders-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $incompleteOrder = $this->repository->update($request, $id);

            $incompleteOrder = new IncompleteOrderResource($incompleteOrder);

            return $this->sendResponse($incompleteOrder, 'Updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('incomplete-orders-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $incompleteOrder = $this->repository->delete($id);

            return $this->sendResponse($incompleteOrder, 'Deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
    
    public function trashed(Request $request)
    {
        $trashed = $this->repository->trashed($request);

        return $this->sendResponse($trashed, "Trash List", 200);
    }
    
    public function restore($id)
    {
        $restoreData = $this->repository->restore($id);

        return $this->sendResponse($restoreData, "Data Restore Successfully", 200);
    }
}
