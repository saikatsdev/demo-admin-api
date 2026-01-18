<?php

namespace App\Http\Controllers\Frontend\Product;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\CategoryRepository;
use App\Http\Resources\Frontend\Product\CategoryResource;
use App\Http\Resources\Frontend\Product\CategoryCollection;

class CategoryController extends BaseController
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $categories = $this->repository->index($request);

            $categories = new CategoryCollection($categories);

            return $this->sendResponse($categories, 'Category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
        try {
            $category = $this->repository->show($id);

            $category = new CategoryResource($category);

            return $this->sendResponse($category, "Category single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
