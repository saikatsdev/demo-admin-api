<?php

namespace App\Http\Controllers\Backend\BlogPost;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\BlogPost\BlogPostCategoryRepository;
use App\Http\Requests\Backend\BlogPost\BlogPostCategoryRequest;
use App\Http\Resources\Backend\BlogPost\BlogPostCategoryResource;
use App\Http\Resources\Backend\BlogPost\BlogPostCategoryCollection;

class BlogPostCategoryController extends BaseController
{
    public function __construct(protected BlogPostCategoryRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('blog-post-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPostCategories = $this->repository->index($request);

            $blogPostCategories = new BlogPostCategoryCollection($blogPostCategories);

            return $this->sendResponse($blogPostCategories, 'Blog post category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(BlogPostCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('blog-post-categories-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPostCategory = $this->repository->store($request);

            $blogPostCategory = new BlogPostCategoryResource($blogPostCategory);

            return $this->sendResponse($blogPostCategory, 'Blog post category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('blog-post-categories-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $blogPostCategory = $this->repository->show($id);

            $blogPostCategory = new BlogPostCategoryResource($blogPostCategory);

            return $this->sendResponse($blogPostCategory, "Blog post category single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function update(BlogPostCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('blog-post-categories-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPostCategory = $this->repository->update($request, $id);

            $blogPostCategory = new BlogPostCategoryResource($blogPostCategory);

            return $this->sendResponse($blogPostCategory, 'Blog post category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('blog-post-categories-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blogPostCategory = $this->repository->delete($id);

            return $this->sendResponse($blogPostCategory, 'Blog post category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateCategorySlug(Request $request, $id)
    {
        $data = $this->repository->updateCategorySlug($request, $id);

        return $this->sendResponse($data,"Permalink update successfully",200);
    }

    public function checkCategorySlug(Request $request)
    {
        $data = $this->repository->checkCategorySlug($request);

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
