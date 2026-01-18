<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use App\Models\Product\SubCategory;
use Illuminate\Support\Facades\Log;
use App\Repositories\Product\SubCategoryRepository;
use App\Http\Requests\Backend\Product\SubCategoryRequest;
use App\Http\Resources\Backend\Product\SubCategoryResource;
use App\Http\Resources\Backend\Product\SubCategoryCollection;

class SubCategoryController extends BaseController
{
    public function __construct(protected SubCategoryRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategories = $this->repository->index($request);

            $subCategories = new SubCategoryCollection($subCategories);

            return $this->sendResponse($subCategories, 'Sub category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $subCategories = $this->repository->list();

            return $this->sendResponse($subCategories, 'Sub category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SubCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('sub-categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->store($request);

            $sub_category = new SubCategoryResource($sub_category);

            return $this->sendResponse($sub_category, 'Sub category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategory = $this->repository->show($id);

            $subCategory = new SubCategoryResource($subCategory);

            return $this->sendResponse($subCategory, 'Sub category single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(SubCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->update($request, $id);

            $sub_category = new SubCategoryResource($sub_category);

            return $this->sendResponse($sub_category, 'Sub category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategory = $this->repository->delete($id);

            return $this->sendResponse($subCategory, 'Sub category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getSubCategoryIdByCategoryId($id)
    {
        $sub_category = SubCategory::select('name', 'slug', 'status', 'id')->where('category_id', $id)->get();

        return $this->sendResponse($sub_category, 'Sub category list successfully', 200);
    }

    public function trashList(Request $request)
    {
        if (!$request->user()->hasPermission('sub-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $subCategories = $this->repository->trashList($request);

            $subCategories = new SubCategoryCollection($subCategories);

            return $this->sendResponse($subCategories, 'Sub category trust list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function restore(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->restore($id);

            $sub_category = new SubCategoryResource($sub_category);

            return $this->sendResponse($sub_category, 'Sub category restore successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function permanentDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission('sub-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $sub_category = $this->repository->permanentDelete($id);

            return $this->sendResponse($sub_category, 'Sub category permanently delete successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateSubCategorySlug(Request $request, $id)
    {
        $data = $this->repository->updateSubCategorySlug($request, $id);

        return $this->sendResponse($data,"Permalink update successfully",200);
    }

    public function checkSubCategorySlug(Request $request)
    {
        $data = $this->repository->checkSubCategorySlug($request);

        if ($data) {
            return response()->json([
                "success"   => true,
                "available" => false,
                "message"   => "Slug already taken"
            ]);
        }

        return response()->json([
            "success"   => true,
            "available" => true,
            "message"   => "Slug available"
        ]);
    }
}
