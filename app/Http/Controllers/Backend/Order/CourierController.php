<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\CourierRepository;
use App\Http\Requests\Backend\Order\CourierRequest;
use App\Http\Resources\Backend\Order\CourierResource;
use App\Http\Resources\Backend\Order\CourierCollection;

class CourierController extends BaseController
{
    public function __construct(protected CourierRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('couriers-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $couriers = $this->repository->index($request);

            $couriers = new CourierCollection($couriers);

            return $this->sendResponse($couriers, 'Courier list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list(Request $request)
    {
        try {
            $couriers = $this->repository->list($request);

            return $this->sendResponse($couriers, 'Courier list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CourierRequest $request)
    {
        if (!$request->user()->hasPermission('couriers-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $courier = $this->repository->store($request);

            $courier = new CourierResource($courier);

            return $this->sendResponse($courier, 'Courier created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('couriers-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $courier = $this->repository->show($id);

            $courier = new CourierResource($courier);

            return $this->sendResponse($courier, 'Courier single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(CourierRequest $request, $id)
    {
        if (!$request->user()->hasPermission('couriers-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $courier = $this->repository->update($request, $id);

            $courier = new CourierResource($courier);

            return $this->sendResponse($courier, 'Courier updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('couriers-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $courier = $this->repository->delete($id);

            return $this->sendResponse($courier, 'Courier deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('couriers-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $couriers = $this->repository->trashList($request);

            $couriers = new CourierCollection($couriers);

            return $this->sendResponse($couriers, 'Courier trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('couriers-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $courier = $this->repository->restore($id);

            $courier = new CourierResource($courier);

            return $this->sendResponse($courier, 'Courier restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('couriers-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $courier = $this->repository->permanentDelete($id);

            return $this->sendResponse($courier, 'Courier permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
    
    public function courierSettings(Request $request)
    {
        try {
            $settings = $this->repository->courierSettings($request);

            return $this->sendResponse($settings, "Courier Settings Update Successfully");
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
