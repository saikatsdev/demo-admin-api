<?php

namespace App\Repositories\BlogPost;

use Exception;
use App\Helpers\Helper;
use App\Models\BlogPost\BlogPost;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class BlogPostRepository
{
    public function __construct(protected BlogPost $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $categoryId   = $request->input("category_id", null);

        $blogPosts = $this->model
        ->with(["category:id,name", "tags:id,name", "createdBy:id,username"])
        ->orderBy('created_at', 'desc')
        ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
        ->when($categoryId, fn($query) => $query->where("category_id", $categoryId))
        ->paginate($paginateSize);

        return $blogPosts;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {

            $blogPost = new $this->model();

            $blogPost->title                 = $request->title;
            $blogPost->slug                  = $request->title;
            $blogPost->blog_post_category_id = $request->category_id;
            $blogPost->meta_title            = $request->meta_title;
            $blogPost->meta_tag              = $request->meta_tag;
            $blogPost->description           = $request->description;
            $blogPost->meta_description      = $request->meta_description;
            $blogPost->status                = $request->status;

            //update image
            if ($request->image) {
                $blogPost->img_path = Helper::uploadFile($request->image, $blogPost->uploadPath, $request->height, $request->width, null);
            }

            $blogPost->save();

            // Sync with tags
            $blogPost->tags()->sync($request->tag_ids);

            DB::commit();

            return $blogPost;
        } catch (Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    public function show($id)
    {
        $blogPost = $this->model->with([
            "category:id,name",
            "tags:id,name",
            "createdBy:id,username",
            "updatedBy:id,username"
        ])
        ->where(fn($q) => $q->where("slug", $id)->orWhere("id", $id))
        ->first();

        if (!$blogPost) {
            throw new CustomException("Blog post not found");
        }

        return $blogPost;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $blogPost = $this->model->find($id);
            if (!$blogPost) {
                throw new CustomException("Blog post Not found");
            }

            $blogPost->blog_post_category_id = $request->category_id;
            $blogPost->title                 = $request->title;
            $blogPost->slug                  = $request->title;
            $blogPost->meta_title            = $request->meta_title;
            $blogPost->meta_tag              = $request->meta_tag;
            $blogPost->description           = $request->description;
            $blogPost->meta_description      = $request->meta_description;
            $blogPost->status                = $request->status;

            //update image
            if ($request->image) {
                $blogPost->img_path = Helper::uploadFile($request->image, $blogPost->uploadPath, $request->height, $request->width);
            }

            $blogPost->save();

            // Sync with tags
            $blogPost->tags()->detach();
            $blogPost->tags()->sync($request->tag_ids);

            DB::commit();

            return $blogPost;
        } catch (Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    public function delete($id)
    {
        $blogPost = $this->model->find($id);

        if (!$blogPost) {
            throw new CustomException("Blog post not found");
        }

        //  Delete old image
        if ($blogPost->img_path) {
            Helper::deleteFile($blogPost->img_path);
        }

        return $blogPost->forceDelete();
    }

    public function updateBlogSlug($request, $id)
    {
        $blogPost = $this->model->find($id);

        if (!$blogPost) {
            throw new CustomException("Blog Post not found");
        }

        $blogPost->slug = $request->slug;

        return $blogPost->save();
    }

    public function checkBlogSlug($request)
    {
        $data = $this->model->where("slug", $request->slug)->first();

        return $data ? true : false;
    }
}
