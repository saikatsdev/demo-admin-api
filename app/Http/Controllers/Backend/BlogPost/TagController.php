<?php

namespace App\Http\Controllers\Backend\BlogPost;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\BlogPost\TagRepository;
use App\Http\Requests\Backend\BlogPost\TagRequest;
use App\Http\Resources\Backend\BlogPost\TagResource;
use App\Http\Resources\Backend\BlogPost\TagCollection;

class TagController extends BaseController
{
    public function __construct(protected TagRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("tags-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $tags = $this->repository->index($request);

            $tags = new TagCollection($tags);

            return $this->sendResponse($tags, "Tag list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $tags = $this->repository->list();

            return $this->sendResponse($tags, 'Tag list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(TagRequest $request)
    {
        if (!$request->user()->hasPermission("tags-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $tag = $this->repository->store($request);

            $tag = new TagResource($tag);

            return $this->sendResponse($tag, "Tag created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("tags-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $tag = $this->repository->show($id);

            $tag = new TagResource($tag);

            return $this->sendResponse($tag, "Tag single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(TagRequest $request, $id)
    {
        if (!$request->user()->hasPermission("tags-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $tag = $this->repository->update($request, $id);

            $tag = new TagResource($tag);

            return $this->sendResponse($tag, "Tag updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("tags-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $tag = $this->repository->delete($id);

            return $this->sendResponse($tag, "Tag deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
