<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Product\Category;
use App\Exceptions\CustomException;

class CategoryRepository
{
    public function __construct(protected Category $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        try {
            $categories = $this->model->with([
                "createdBy:id,username",
                "updatedBy:id,username",
                "subCategories:id,name,category_id,img_path",
                "subCategories.subSubCategories:id,name,sub_category_id,img_path"
            ])
            ->withCount("products")
            ->when($status, fn($query) => $query->where("status", $status))
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $categories;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function list()
    {
        try {
            return $this->model
            ->select("id", "name")
            ->where("status", StatusEnum::ACTIVE)
            ->orderBy("name", "ASC")
            ->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            $category = new $this->model();

            $category->name   = $request->name;
            $category->slug   = $request->name;
            $category->status = $request->status ?? StatusEnum::ACTIVE;

            // Upload image
            if ($request->image) {
                $category->img_path = Helper::uploadFile($request->image, $category->uploadPath, $request->height, $request->width);
            }

            $category->save();

            return $category;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $category = $this->model->with([
                "createdBy:id,username",
                "updatedBy:id,username",
                "subCategories:id,name,category_id",
                "subCategories.subSubCategories:id,name,sub_category_id"
            ])
            ->withCount("products")
            ->find($id);

            if (!$category) {
                throw new CustomException("Category not found");
            }

            return $category;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                throw new CustomException("Category Not found");
            }

            $category->name   = $request->name;
            $category->slug   = $request->name;
            $category->status = $request->status ?? StatusEnum::ACTIVE;

            // Update image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
            
                $category->img_path = Helper::uploadImage($file,$category->uploadPath,$request->height,$request->width,$category->img_path);
            } elseif (is_string($request->image)) {
                $category->img_path = $request->image;
            }

            $category->save();

            return $category;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $category = $this->model->find($id);

            if (!$category) {
                throw new CustomException("Category not found");
            }

            $category->sectionItems()->delete();
            $category->delete();

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input("search_key", null);

        try {
            $categories = $this->model->with(["createdBy:id,username", "subCategories"])
                ->onlyTrashed()
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where("name", "like", "%$searchKey%")
                        ->orWhere("status", $searchKey);
                })
                ->orderBy("created_at", "desc")
                ->paginate($paginateSize);

            return $categories;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $category = $this->model->onlyTrashed()->find($id);
            if (!$category) {
                throw new CustomException("Category not found");
            }

            $category->restore();

            return $category;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $category = $this->model->onlyTrashed()->find($id);

            if (!$category) {
                throw new CustomException("Category not found");
            }

            $category->sectionItems()->delete();
            $category->forceDelete();

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function updateCategorySlug($request, $id)
    {
        $category = $this->model->find($id);

        if (!$category) {
            throw new CustomException("Category not found");
        }

        $category->slug = $request->slug;

        return $category->save();
    }

    public function checkCategorySlug($request)
    {
        $data = $this->model->where("slug", $request->slug)->first();

        return $data ? true : false;
    }
}
