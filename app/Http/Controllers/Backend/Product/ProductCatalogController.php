<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\ProductCatalogRepository;
use App\Http\Requests\Backend\Product\ProductCatalogRequest;
use App\Http\Resources\Backend\Product\ProductCatalogResource;
use App\Http\Resources\Backend\Product\ProductCatalogCollection;

class ProductCatalogController extends BaseController
{
    public function __construct(protected ProductCatalogRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('product-catalogs-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productCatalogs = $this->repository->index($request);

            $productCatalogs = new ProductCatalogCollection($productCatalogs);

            return $this->sendResponse($productCatalogs, 'Product catalog list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function generateFbXmlFeed(ProductCatalogRequest $request)
    {
        if (!$request->user()->hasPermission('product-catalogs-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $fbXmlFeed = $this->repository->generateFbXmlFeed($request);

            $fbXmlFeed = new ProductCatalogResource($fbXmlFeed);

            return $this->sendResponse($fbXmlFeed, "Facebook feed generated successfully", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('product-catalogs-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productCatalog = $this->repository->show($id);

            $productCatalog = new ProductCatalogResource($productCatalog);

            return $this->sendResponse($productCatalog, "Product catalog single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateFbXmlFeed(ProductCatalogRequest $request, $id)
    {
        if (!$request->user()->hasPermission('product-catalogs-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $fbXmlFeed = $this->repository->updateFbXmlFeed($request, $id);

            $fbXmlFeed = new ProductCatalogResource($fbXmlFeed);

            return $this->sendResponse($fbXmlFeed, "Facebook feed update successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError(__($exception->getMessage()));
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
            $productCatalog = $this->repository->delete($id);

            return $this->sendResponse($productCatalog, 'Product catalog deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('product-catalogs-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productCatalogs = $this->repository->trashList($request);

            $productCatalogs = new ProductCatalogCollection($productCatalogs);

            return $this->sendResponse($productCatalogs, 'Product catalog trash list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('product-catalogs-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productCatalog = $this->repository->restore($id);

            $productCatalog = new ProductCatalogResource($productCatalog);

            return $this->sendResponse($productCatalog, "Product catalog restore successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('product-catalogs-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $productCatalog = $this->repository->permanentDelete($id);

            return $this->sendResponse($productCatalog, "Product catalog permanently deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
