<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Product\ProductRepository;
use App\Http\Requests\Frontend\ProductVariationRequest;
use App\Http\Resources\Frontend\Product\ProductResource;
use App\Http\Resources\Frontend\Product\ProductCollection;
use App\Http\Resources\Frontend\Product\ShopSidebarResource;

class ProductController extends BaseController
{
    protected $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(['request_from' => 'frontend']);
            
            $request->merge(["request_from" => "frontend", "status" => StatusEnum::ACTIVE]);

            $products = $this->repository->index($request);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Product list", 200);
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

    public function show($slug)
    {
        try {
            $status  = StatusEnum::ACTIVE;
            $product = $this->repository->show($slug, $status);

            $product = new ProductResource($product);

            return $this->sendResponse($product, "Product single data");
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function productVariation(ProductVariationRequest $request)
    {
        try {
            $variations = $this->repository->productVariation($request);

            return $variations;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function categoryWiseProduct(Request $request, $slug)
    {
        try {
            $products = $this->repository->categoryWiseProduct($request, $slug);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Category wise product", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function subCategoryWiseProduct(Request $request, $slug)
    {
        try {
            $products = $this->repository->subCategoryWiseProduct($request, $slug);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Sub Category wise product", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError("Something went wrong");
        }
    }

    public function shopSidebarData()
    {
        try {
            $data = $this->repository->shopSidebarData();

            $data = new ShopSidebarResource($data);

            return $this->sendResponse($data, "Shop page sidebar data", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
