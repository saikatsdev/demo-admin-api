<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\ProductTypeRepository;
use App\Http\Requests\Backend\Product\ProductTypeRequest;
use App\Http\Resources\Backend\Product\ProductTypeResource;
use App\Http\Resources\Backend\Product\ProductTypeCollection;

class ProductTypeController extends BaseController
{
    public function __construct(protected ProductTypeRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("product-types-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productTypes = $this->repository->index($request);

            $productTypes = new ProductTypeCollection($productTypes);

            return $this->sendResponse($productTypes, "Product type list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $productTypes = $this->repository->list();

            return $this->sendResponse($productTypes, 'Product type list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ProductTypeRequest $request)
    {
        if (!$request->user()->hasPermission("product-types-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productType = $this->repository->store($request);

            $productType = new ProductTypeResource($productType);

            return $this->sendResponse($productType, "Product type created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("product-types-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productType = $this->repository->show($id);

            $productType = new ProductTypeResource($productType);

            return $this->sendResponse($productType, "Product type single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ProductTypeRequest $request, $id)
    {
        if (!$request->user()->hasPermission("product-types-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productType = $this->repository->update($request, $id);

            $productType = new ProductTypeResource($productType);

            return $this->sendResponse($productType, "Product type updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("product-types-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productType = $this->repository->delete($id);

            return $this->sendResponse($productType, "Product type deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
