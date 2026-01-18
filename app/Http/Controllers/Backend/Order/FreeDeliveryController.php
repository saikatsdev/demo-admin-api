<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\FreeDeliveryRepository;
use App\Http\Requests\Backend\Order\FreeDeliveryRequest;
use App\Http\Resources\Backend\Order\FreeDeliveryResource;
use App\Http\Resources\Backend\Order\FreeDeliveryCollection;

class FreeDeliveryController extends BaseController
{
    public function __construct(protected FreeDeliveryRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('free-delivery-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDeliveries = $this->repository->index($request);

            $freeDeliveries = new FreeDeliveryCollection($freeDeliveries);

            return $this->sendResponse($freeDeliveries, 'Free delivery list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(FreeDeliveryRequest $request)
    {
        if (!$request->user()->hasPermission('free-delivery-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->store($request);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->show($id);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(FreeDeliveryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->update($request, $id);

            $freeDelivery = new FreeDeliveryResource($freeDelivery);

            return $this->sendResponse($freeDelivery, 'Free delivery updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('free-delivery-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $freeDelivery = $this->repository->delete($id);

            return $this->sendResponse($freeDelivery, 'Free Delivery deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
