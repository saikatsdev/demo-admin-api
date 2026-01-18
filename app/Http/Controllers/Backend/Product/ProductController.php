<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\ProductRepository;
use App\Http\Requests\Backend\Product\ProductRequest;
use App\Http\Resources\Backend\Product\ProductResource;
use App\Http\Resources\Backend\Product\ProductCollection;
use App\Http\Resources\Backend\Product\ProductStockCollection;
use App\Http\Requests\Backend\Product\ProductBulkActionRequest;
use App\Http\Resources\Backend\Product\ProductVariationResource;
use App\Http\Requests\Backend\Product\UpdateProductStatusRequest;

class ProductController extends BaseController
{
    public function __construct(protected ProductRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $request->merge(['request_from' => 'backend']);
            
            $products = $this->repository->index($request);

            $productCollection = new ProductCollection($products);

            $totalActiveCount   = $this->repository->countByStatus('active');
            $totalInactiveCount = $this->repository->countByStatus('inactive');

            return $this->sendResponse(
                $productCollection->additional([
                    'totalActiveCount' => $totalActiveCount,
                    'totalInactiveCount' => $totalInactiveCount,
                ]),
                "Product list",
                200
            );
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list(Request $request)
    {
        try {
            $products = $this->repository->list($request);

            return $this->sendResponse($products, "Product list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
    
    public function search(Request $request)
    {
        try {
            $products = $this->repository->search($request);

            return $this->sendResponse($products, "Product list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ProductRequest $request)
    {
        if (!$request->user()->hasPermission("products-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->store($request);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->show($id);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ProductRequest $request, $id)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->update($request, $id);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->destroy($id);

            return $this->sendResponse($product, "Product deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $products = $this->repository->trashList($request);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Product trash list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->restore($id);

            return $this->sendResponse($product, "Product restore successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->permanentDelete($id);

            return $this->sendResponse($product, "Product permanent deleted successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function copy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->copy($id);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product copy successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function bulkUpdateStatus(UpdateProductStatusRequest $request)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $product = $this->repository->updateStatus($request);

            return $this->sendResponse($product, "Product status updated successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function bulkDelete(ProductBulkActionRequest $request)
    {
        if (!$request->user()->hasPermission("products-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->bulkDelete($request);

            return $this->sendResponse($product, "Product deleted successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function bulkRestore(ProductBulkActionRequest $request)
    {
        if (!$request->user()->hasPermission("products-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->bulkRestore($request);

            return $this->sendResponse($product, "Product restore successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function bulkPermanentDelete(ProductBulkActionRequest $request)
    {
        if (!$request->user()->hasPermission("products-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $product = $this->repository->bulkPermanentDelete($request);

            return $this->sendResponse($product, "Product permanent deleted successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function productHistory(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $audits = $this->repository->productHistory($request, $id);

            return $this->sendResponse($audits, "Product history", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function stockReport(Request $request)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $stockReports = $this->repository->stockReport($request);

            $stockReports = new ProductStockCollection($stockReports);

            return $this->sendResponse($stockReports, "Product stock reports", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function variationCurrentStock(Request $request, $id)
    {
        if (!$request->user()->hasPermission("products-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $variationCurrentStock = $this->repository->variationCurrentStock($id);

            $variationCurrentStock = ProductVariationResource::collection($variationCurrentStock);

            return $this->sendResponse($variationCurrentStock, "Product variation current stock", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateProductSlug(Request $request, $id)
    {
        $data = $this->repository->updateProductSlug($request, $id);

        return $this->sendResponse($data,"Permalink update successfully",200);
    }

    public function checkProductSlug(Request $request)
    {
        $data = $this->repository->checkProductSlug($request);

        if ($data) {
            return response()->json([
                "success"   => true,
                "available" => false,
                "message"   => "Slug already taken"
            ]);
        }

        return response()->json([
            "success"   => true,
            "available" => true,
            "message"   => "Slug available"
        ]);
    }
}
