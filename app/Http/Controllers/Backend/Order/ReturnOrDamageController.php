<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\ReturnOrDamageRepository;
use App\Http\Requests\Backend\Order\ReturnOrDamageRequest;
use App\Http\Resources\Backend\Order\ReturnOrDamageResource;
use App\Http\Resources\Backend\Order\ReturnOrDamageCollection;

class ReturnOrDamageController extends BaseController
{
    public function __construct(protected ReturnOrDamageRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("return-and-damages-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->index($request);

            $orders = new ReturnOrDamageCollection($orders);

            return $this->sendResponse($orders, "List data", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ReturnOrDamageRequest $request)
    {
        if (!$request->user()->hasPermission("return-and-damages-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->store($request);

            $order = new ReturnOrDamageResource($order);

            return $this->sendResponse($order, "Created successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("return-and-damages-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->show($id);

            $order = new ReturnOrDamageResource($order);

            return $this->sendResponse($order, "Details view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->hasPermission("return-and-damages-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->update($request, $id);

            $order = new ReturnOrDamageResource($order);

            return $this->sendResponse($order, "Updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("return-and-damages-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $order = $this->repository->destroy($id);

            return $this->sendResponse($order, "Deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
