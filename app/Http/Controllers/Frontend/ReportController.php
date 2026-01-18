<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\Product\ProductCollection;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends BaseController
{
    public function __construct(protected ReportRepository $repository) {}

    public function topSelling(Request $request)
    {
        try {
            $products = $this->repository->orderReportBySellingFrontend($request);

            $products = new ProductCollection($products);

            return $this->sendResponse($products, "Top selling products", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
