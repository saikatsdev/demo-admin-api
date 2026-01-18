<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\CancelReasonRepository;
use App\Http\Requests\Backend\Order\CancelReasonRequest;
use App\Http\Resources\Backend\Order\CancelReasonResource;
use App\Http\Resources\Backend\Order\CancelReasonCollection;

class CancelReasonController extends BaseController
{
    public function __construct(protected CancelReasonRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("cancel-reasons-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $cancelReasons = $this->repository->index($request);

            $cancelReasons = new CancelReasonCollection($cancelReasons);

            return $this->sendResponse($cancelReasons, "Cancel reason list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $cancelReasons = $this->repository->list();

            return $this->sendResponse($cancelReasons, "Cancel reason list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CancelReasonRequest $request)
    {
        if (!$request->user()->hasPermission("cancel-reasons-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $cancelReason = $this->repository->store($request);

            $cancelReason = new CancelReasonResource($cancelReason);

            return $this->sendResponse($cancelReason, "Cancel reason created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("cancel-reasons-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $cancelReason = $this->repository->show($id);

            $cancelReason = new CancelReasonResource($cancelReason);

            return $this->sendResponse($cancelReason, "Cancel reason single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(CancelReasonRequest $request, $id)
    {
        if (!$request->user()->hasPermission("cancel-reasons-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $cancelReason = $this->repository->update($request, $id);

            $cancelReason = new CancelReasonResource($cancelReason);

            return $this->sendResponse($cancelReason, "Cancel reason updated successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("cancel-reasons-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $cancelReason = $this->repository->delete($id);

            return $this->sendResponse($cancelReason, "Cancel reason deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
