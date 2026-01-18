<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Models\Product\SubSubCategory;
use App\Repositories\Product\SubSubCategoryRepository;
use App\Http\Requests\Backend\Product\SubSubCategoryRequest;
use App\Http\Resources\Backend\Product\SubSubCategoryResource;
use App\Http\Resources\Backend\Product\SubSubCategoryCollection;

class SubSubCategoryController extends BaseController
{
    public function __construct(protected SubSubCategoryRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategories = $this->repository->index($request);

            $subSubCategories = new SubSubCategoryCollection($subSubCategories);

            return $this->sendResponse($subSubCategories, 'Sub sub category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list(Request $request)
    {
        try {
            $subSubCategories = $this->repository->list();

            return $this->sendResponse($subSubCategories, 'Sub sub category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SubSubCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategory = $this->repository->store($request);

            $subSubCategory = new SubSubCategoryResource($subSubCategory);

            return $this->sendResponse($subSubCategory, 'Sub Sub category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $subSubCategory = $this->repository->show($id);

            $subSubCategory = new SubSubCategoryResource($subSubCategory);

            return $this->sendResponse($subSubCategory, 'Sub Sub category single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(SubSubCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategory = $this->repository->update($request, $id);

            $subSubCategory = new SubSubCategoryResource($subSubCategory);

            return $this->sendResponse($subSubCategory, 'Sub Sub category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategory = $this->repository->delete($id);

            return $this->sendResponse($subSubCategory, 'Sub Sub Category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getSubSubCategoryIdBySubCategoryId($id)
    {
        $subSubCategory = SubSubCategory::select('name', 'slug', 'status', 'id')->where('sub_category_id', $id)->get();

        return $this->sendResponse($subSubCategory, 'Sub Sub Category list successfully', 200);
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategories = $this->repository->trashList($request);

            $subSubCategories = new SubSubCategoryCollection($subSubCategories);

            return $this->sendResponse($subSubCategories, 'Sub Sub category trust list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }


    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategory = $this->repository->restore($id);

            $subSubCategory = new SubSubCategoryResource($subSubCategory);

            return $this->sendResponse($subSubCategory, 'Sub Sub category restore successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-sub-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subSubCategory = $this->repository->permanentDelete($id);

            return $this->sendResponse($subSubCategory, 'Sub Sub category permanently delete successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
