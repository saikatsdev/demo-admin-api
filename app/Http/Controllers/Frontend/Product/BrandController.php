<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\BrandRepository;
use App\Http\Resources\Backend\Product\BrandResource;
use App\Http\Resources\Backend\Product\BrandCollection;

class BrandController extends BaseController
{
    protected $repository;

    public function __construct(BrandRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $brands = $this->repository->index($request);

            $brands = new BrandCollection($brands);

            return $this->sendResponse($brands, 'Brand list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $brand = $this->repository->show($id);

            $brand = new BrandResource($brand);

            return $this->sendResponse($brand, "Brand single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
