<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\AttributeRepository;
use App\Http\Resources\Frontend\Product\AttributeCollection;

class AttributeController extends BaseController
{
    protected $repository;

    public function __construct(AttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $attributes = $this->repository->index($request);

            $attributes = new AttributeCollection($attributes);

            return $this->sendResponse($attributes, "Attribute list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
