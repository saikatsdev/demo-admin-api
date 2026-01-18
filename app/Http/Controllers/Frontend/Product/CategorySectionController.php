<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\CategorySectionRepository;
use App\Http\Resources\Frontend\Product\CategorySectionCollection;

class CategorySectionController extends BaseController
{
    public function __construct(protected CategorySectionRepository $repository) {}

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $categorySections = $this->repository->index($request);

            $categorySections = new CategorySectionCollection($categorySections);

            return $this->sendResponse($categorySections, "Category section list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
