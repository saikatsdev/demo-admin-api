<?php

namespace App\Repositories\BlogPost;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Exceptions\CustomException;
use App\Models\BlogPost\BlogPostCategory;

class BlogPostCategoryRepository
{
    public function __construct(protected BlogPostCategory $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $status       = $request->input("status", null);

        $blogPostCategories = $this->model->with(["createdBy:id,username"])
        ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $blogPostCategories;
    }

    public function store($request)
    {
        $blogPostCategory = new $this->model();

        $blogPostCategory->name   = $request->name;
        $blogPostCategory->slug   = $request->name;
        $blogPostCategory->status = $request->status ?? StatusEnum::ACTIVE;

        // Upload image
        if ($request->image) {
            $blogPostCategory->img_path = Helper::uploadFile($request->image, $blogPostCategory->uploadPath, $request->height, $request->width);
        }

        $blogPostCategory->save();

        return $blogPostCategory;
    }

    public function show($id)
    {
        $blogPostCategory = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$blogPostCategory) {
            throw new CustomException("Blog post category not found");
        }

        return $blogPostCategory;
    }

    public function update($request, $id)
    {
        $blogPostCategory = BlogPostCategory::find($id);
        if (!$blogPostCategory) {
            throw new CustomException("Blog post category Not found");
        }

        $blogPostCategory->name   = $request->name;
        $blogPostCategory->slug   = $request->name;
        $blogPostCategory->status = $request->status ?? StatusEnum::ACTIVE;

        // Update image
        if ($request->image) {
            $blogPostCategory->img_path = Helper::uploadFile($request->image, $blogPostCategory->uploadPath, $request->height, $request->width);
        }

        $blogPostCategory->save();

        return $blogPostCategory;
    }

    public function delete($id)
    {
        $blogPostCategory = $this->model->find($id);
        if (!$blogPostCategory) {
            throw new CustomException("Blog post category not found");
        }

        // Delete image
        if ($blogPostCategory->img_path) {
            Helper::deleteFile($blogPostCategory->img_path);
        }

        return $blogPostCategory->forceDelete();
    }

    public function updateCategorySlug($request, $id)
    {
        $blogPostCategory = $this->model->find($id);

        if (!$blogPostCategory) {
            throw new CustomException("Blog post category not found");
        }

        $blogPostCategory->slug = $request->slug;

        return $blogPostCategory->save();
    }

    public function checkCategorySlug($request)
    {
        $data = $this->model->where("slug", $request->slug)->first();

        return $data ? true : false;
    }
}
