<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\WarrantyRepository;
use App\Http\Requests\Backend\Product\WarrantyRequest;
use App\Http\Resources\Backend\Product\WarrantyResource;
use App\Http\Resources\Backend\Product\WarrantyCollection;

class WarrantyController extends BaseController
{
    public function __construct(protected WarrantyRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('warranties-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $warranties = $this->repository->index($request);

            $warranties = new WarrantyCollection($warranties);

            return $this->sendResponse($warranties, "Warranty list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(WarrantyRequest $request)
    {
        if (!$request->user()->hasPermission("warranties-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $warranty = $this->repository->store($request);

            $warranty = new WarrantyResource($warranty);

            return $this->sendResponse($warranty, "Warranty created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('warranties-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $warranty = $this->repository->show($id);

            $warranty = new WarrantyResource($warranty);

            return $this->sendResponse($warranty, "Warranty single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(WarrantyRequest $request, $id)
    {
        if (!$request->user()->hasPermission('warranties-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $warranty = $this->repository->update($request, $id);

            $warranty = new WarrantyResource($warranty);

            return $this->sendResponse($warranty, "Warranty update successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('warranties-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $warranty = $this->repository->delete($id);

            return $this->sendResponse($warranty, "Warranty deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
