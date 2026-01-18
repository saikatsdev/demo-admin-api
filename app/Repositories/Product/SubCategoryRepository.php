<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use App\Models\Product\SubCategory;

class SubCategoryRepository
{
    public function __construct(protected SubCategory $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $categoryId    = $request->input('category_id', null);

        $subCategories = $this->model->with([
            "category:id,name",
            "createdBy:id,username",
            "updatedBy:id,username",
            "subSubCategories:id,name"
        ])
        ->when($categoryId, function ($query) use ($categoryId) {
            $query->where("category_id",  $categoryId);
        })
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", "like", "$searchKey");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $subCategories;
    }

    public function list()
    {
        return $this->model
        ->select("id", "name")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("name", "ASC")
        ->get();
    }

    public function store($request)
    {
        $subCategory = new $this->model();

        $subCategory->name        = Str::title($request->name);
        $subCategory->slug        = $request->name;
        $subCategory->category_id = $request->category_id;
        $subCategory->status      = $request->status;

        if ($request->image) {
            $subCategory->img_path = Helper::uploadFile($request->image, $subCategory->uploadPath, $request->height, $request->width);
        }

        $subCategory->save();

        return $subCategory;
    }

    public function show($id)
    {
        $subCategories = $this->model->with([
            "category:id,name",
            "createdBy:id,username",
            "updatedBy:id,username",
            "subSubCategories:id,name"
        ])->find($id);

        if (!$subCategories) {
            throw new CustomException("Sub Category not found");
        }

        return $subCategories;
    }

    public function update($request,  $id)
    {
        $subCategory = $this->model->find($id);

        if (!$subCategory) {
            throw new CustomException("Sub Category not found");
        }

        $subCategory->name        = $request->name;
        $subCategory->slug        = $request->name;
        $subCategory->category_id = $request->category_id;
        $subCategory->status      = $request->status;

        if ($request->image) {
            $subCategory->img_path = Helper::uploadFile($request->image, $subCategory->uploadPath, $request->height, $request->width);
        }

        $subCategory->save();

        return $subCategory;
    }

    public function delete($id)
    {
        $subCategory = $this->model->find($id);

        if (!$subCategory) {
            throw new CustomException("Sub Category not found");
        }

        $subCategory->subSubCategories()->delete();

        return $subCategory->delete();
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $subCategories = $this->model->with([
            "category:id,name",
            "createdBy:id,username"
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
        $subCategory = $this->model->onlyTrashed()->find($id);

        if (!$subCategory) {
            throw new CustomException("Sub Category not found");
        }
        $subCategory->restore();

        return $subCategory;
    }

    public function permanentDelete($id)
    {
        $subCategory = $this->model->onlyTrashed()->find($id);

        if (!$subCategory) {
            throw new CustomException("Sub Category not found");
        }

        return $subCategory->forceDelete();
    }

    public function updateSubCategorySlug($request, $id)
    {
        $subCategory = $this->model->find($id);

        if (!$subCategory) {
            throw new CustomException("Sub Category not found");
        }

        $subCategory->slug = $request->slug;

        return $subCategory->save();
    }

    public function checkSubCategorySlug($request)
    {
        $data = $this->model->where("slug", $request->slug)->first();

        return $data ? true : false;
    }
}
