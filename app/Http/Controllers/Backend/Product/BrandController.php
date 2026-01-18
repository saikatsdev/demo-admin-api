<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;;
use App\Repositories\Product\BrandRepository;
use App\Http\Requests\Backend\Product\BrandRequest;
use App\Http\Resources\Backend\Product\BrandResource;
use App\Http\Resources\Backend\Product\BrandCollection;

class BrandController extends BaseController
{
    public function __construct(protected BrandRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('brands-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brands = $this->repository->index($request);

            $brands = new BrandCollection($brands);

            return $this->sendResponse($brands, 'Brand list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $brands = $this->repository->list();

            return $this->sendResponse($brands, 'Brand list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(BrandRequest $request)
    {
        if (!$request->user()->hasPermission('brands-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brand = $this->repository->store($request);

            $brand = new BrandResource($brand);

            return $this->sendResponse($brand, 'Brand created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('brands-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brand = $this->repository->show($id);

            $brand = new BrandResource($brand);

            return $this->sendResponse($brand, 'Brand single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->hasPermission('brands-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brand = $this->repository->update($request, $id);

            $brand = new BrandResource($brand);

            return $this->sendResponse($brand, 'Brand updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('brands-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brand = $this->repository->delete($id);

            return $this->sendResponse($brand, 'Brand deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('brands-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brands = $this->repository->trashList($request);

            $brands = new BrandCollection($brands);

            return $this->sendResponse($brands, 'Brand trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('brands-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brand = $this->repository->restore($id);

            $brand = new BrandResource($brand);

            return $this->sendResponse($brand, 'Brand restore successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('brands-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $brand = $this->repository->permanentDelete($id);

            return $this->sendResponse($brand, 'Brand permanently deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
