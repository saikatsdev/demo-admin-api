<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Exceptions\CustomException;
use App\Models\Product\SubSubCategory;

class SubSubCategoryRepository
{
    public function __construct(protected SubSubCategory $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $subCategoryId    = $request->input('sub_category_id', null);

        $subSubCategories = $this->model
        ->with([
            "subCategory:id,name,category_id",
            "subCategory.category:id,name",
            "createdBy:id,username",
            "updatedBy:id,username"
        ])
        ->when($subCategoryId, function ($query) use ($subCategoryId) {
            $query->where("sub_category_id", $subCategoryId);
        })
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", "like", "$searchKey");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $subSubCategories;
    }

    public function list($request)
    {
        $query = $this->model
            ->select('id', 'name')
            ->where('status', StatusEnum::ACTIVE)
            ->orderBy('name', 'ASC');

        if ($request->filled('subcategory_id')) {
            $query->where('sub_category_id', $request->subcategory_id);
        }

        return $query->get();
    }

    public function store($request)
    {
        $subSubCategory = new $this->model();

        $subSubCategory->name            = $request->name;
        $subSubCategory->slug            = $request->name;
        $subSubCategory->sub_category_id = $request->sub_category_id;
        $subSubCategory->status          = $request->status;

        // Upload image
        if ($request->image) {
            $subSubCategory->img_path = Helper::uploadFile($request->image, $subSubCategory->uploadPath, $request->height, $request->width);
        }

        $subSubCategory->save();

        return $subSubCategory;
    }

    public function show($id)
    {
        $subSubCategories = $this->model->with([
            "subCategory:id,name,category_id",
            "subCategory.category:id,name",
            "createdBy:id,username",
            "updatedBy:id,username"
        ])
            ->find($id);

        if (!$subSubCategories) {
            throw new CustomException("Sub Sub Category not found");
        }

        return $subSubCategories;
    }

    function update($request, $id)
    {
        $subSubCategory = $this->model->find($id);

        if (!$subSubCategory) {
            throw new CustomException("Sub Sub Category not found");
        }

        $subSubCategory->name            = $request->name;
        $subSubCategory->slug            = $request->name;
        $subSubCategory->sub_category_id = $request->sub_category_id;
        $subSubCategory->status          = $request->status;

        // Upload image
        if ($request->image) {
            $subSubCategory->img_path = Helper::uploadFile($request->image, $subSubCategory->uploadPath, $request->height, $request->width);
        }

        $subSubCategory->save();

        return $subSubCategory;
    }

    public function delete($id)
    {
        $subSubCategory = $this->model->find($id);

        if (!$subSubCategory) {
            throw new CustomException("Sub Sub Category not found");
        }

        return $subSubCategory->delete();
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $subCategories = $this->model->with([
        "subCategory:id,name,category_id",
        "subCategory.category:id,name",
        "createdBy:id,username",
        "updatedBy:id,username"
        ])
        ->onlyTrashed()
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", "like", "$searchKey");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $subCategories;
    }

    public function restore($id)
    {
        $subSubCategory = $this->model->onlyTrashed()->find($id);

        if (!$subSubCategory) {
            throw new CustomException("Sub Sub Category not found");
        }

        $subSubCategory->restore();

        return $subSubCategory;
    }

    public function permanentDelete($id)
    {
        $subSubCategory = $this->model->onlyTrashed()->find($id);

        if (!$subSubCategory) {
            throw new CustomException("Sub Sub Category not found");
        }

        return $subSubCategory->forceDelete();
    }
}
