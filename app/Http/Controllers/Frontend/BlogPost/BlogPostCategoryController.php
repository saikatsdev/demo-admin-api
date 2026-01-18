<?php

namespace App\Http\Controllers\Frontend\BlogPost;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\BlogPost\BlogPostCategoryRepository;
use App\Http\Resources\Frontend\BlogPost\BlogPostCategoryResource;
use App\Http\Resources\Frontend\BlogPost\BlogPostCategoryCollection;

class BlogPostCategoryController extends BaseController
{
    protected $repository;

    public function __construct(BlogPostCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $blogPostCategories = $this->repository->index($request);

            $blogPostCategories = new BlogPostCategoryCollection($blogPostCategories);

            return $this->sendResponse($blogPostCategories, 'Blog post category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

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
}
