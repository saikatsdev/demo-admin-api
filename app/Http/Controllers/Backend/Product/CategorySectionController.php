<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\CategorySectionRepository;
use App\Http\Requests\Backend\Product\CategorySectionRequest;
use App\Http\Resources\Backend\Product\CategorySectionResource;
use App\Http\Resources\Backend\Product\CategorySectionCollection;

class CategorySectionController extends BaseController
{
    public function __construct(protected CategorySectionRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("category-sections-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categorySections = $this->repository->index($request);

            $categorySections = new CategorySectionCollection($categorySections);

            return $this->sendResponse($categorySections, "Category section list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CategorySectionRequest $request)
    {
        if (!$request->user()->hasPermission("category-sections-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categorySection = $this->repository->store($request);

            $categorySection = new CategorySectionResource($categorySection);

            return $this->sendResponse($categorySection, "Category section created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("category-sections-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categorySection = $this->repository->show($id);

            $categorySection = new CategorySectionResource($categorySection);

            return $this->sendResponse($categorySection, "Category section single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(CategorySectionRequest $request, $id)
    {
        if (!$request->user()->hasPermission("category-sections-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categorySection = $this->repository->update($request, $id);

            $categorySection = new CategorySectionResource($categorySection);

            return $this->sendResponse($categorySection, "Category section updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("category-sections-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $categorySection = $this->repository->delete($id);

            return $this->sendResponse($categorySection, "Category section deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
