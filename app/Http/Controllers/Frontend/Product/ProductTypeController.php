<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\ProductTypeRepository;
use App\Http\Resources\Frontend\Product\ProductTypeResource;
use App\Http\Resources\Frontend\Product\ProductTypeCollection;

class ProductTypeController extends BaseController
{
    public function __construct(protected ProductTypeRepository $repository) {}

    public function index(Request $request)
    {
        try {
            $productTypes = $this->repository->index($request);

            $productTypes = new ProductTypeCollection($productTypes);

            return $this->sendResponse($productTypes, "Product type list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
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
}
