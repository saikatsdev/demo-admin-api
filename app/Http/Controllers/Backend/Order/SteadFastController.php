<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\SteadFastRepository;
use App\Http\Requests\Backend\Order\SteadFastRequest;
use App\Http\Resources\Backend\Order\SteadFastResource;
use App\Http\Requests\Backend\Order\SteadFastCreateOrderRequest;
use App\Http\Requests\Backend\Order\SteadFastBulkCreateOrderRequest;

class SteadFastController extends BaseController
{
    public function __construct(protected SteadFastRepository $repository) {}

    public function createOrder(SteadFastCreateOrderRequest $request)
    {
        try {
            $result = $this->repository->createOrder($request);

            return $this->sendResponse($result, $result["message"]);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function bulkCreate(SteadFastBulkCreateOrderRequest $request)
    {
        try {
            $result = $this->repository->bulkCreate($request);

            return $this->sendResponse($result, $result["message"]);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getDeliveryStatus($id)
    {
        try {
            $result = $this->repository->getDeliveryStatus($id);

            return $this->sendResponse($result, "Order delivery status");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function getCurrentBalance()
    {
        try {
            $result = $this->repository->getCurrentBalance();

            return $this->sendResponse($result, "Balance");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request)
    {
        if (!$request->user()->hasPermission("stead-fast-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $res = $this->repository->show();

            $res = new SteadFastResource($res);

            return $this->sendResponse($res, "Stead fast credentials");
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateEnvCredential(SteadFastRequest $request)
    {
        if (!$request->user()->hasPermission("stead-fast-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $res = $this->repository->updateEnvCredential($request);

            $res = new SteadFastResource($res);

            return $this->sendResponse($res, "Credential updated successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function callback(Request $request)
    {
        try {
            $result = $this->repository->callback($request);

            return $this->sendResponse($result, "Callback response");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }
}
