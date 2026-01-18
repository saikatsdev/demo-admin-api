<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\DistrictRepository;
use App\Http\Requests\Backend\Order\DistrictRequest;
use App\Http\Resources\Backend\Order\DistrictResource;
use App\Http\Resources\Backend\Order\DistrictCollection;

class DistrictController extends BaseController
{
    public function __construct(protected DistrictRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('districts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $districts = $this->repository->index($request);

            $districts = new DistrictCollection($districts);

            return $this->sendResponse($districts, 'District list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list(Request $request)
    {
        try {
            $districts = $this->repository->list($request);

            return $this->sendResponse($districts, 'District list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(DistrictRequest $request)
    {
        if (!$request->user()->hasPermission('districts-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->store($request);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, 'District created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('districts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->show($id);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, "District single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(DistrictRequest $request, $id)
    {
        if (!$request->user()->hasPermission('districts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->update($request, $id);

            $district = new DistrictResource($district);

            return $this->sendResponse($district, 'District updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('districts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $district = $this->repository->delete($id);

            return $this->sendResponse($district, 'District deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
