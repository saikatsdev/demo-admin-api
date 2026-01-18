<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\ProductCatalogRepository;

class ProductCatalogController extends BaseController
{
    public function __construct(protected ProductCatalogRepository $repository) {}

    public function getFbXmlProductCatalog($slug)
    {
        try {
            $productCatalog = $this->repository->getFbXmlProductCatalog($slug);

            return $this->sendResponse($productCatalog, "Product catalog", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
