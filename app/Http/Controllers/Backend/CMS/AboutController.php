<?php

namespace App\Http\Controllers\Backend\CMS;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CMS\AboutRepository;
use App\Http\Requests\Backend\CMS\AboutRequest;
use App\Http\Resources\Backend\CMS\AboutResource;
use App\Http\Resources\Backend\CMS\AboutCollection;

class AboutController extends BaseController
{
    public function __construct(protected AboutRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('abouts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $abouts = $this->repository->index($request);

            $abouts = new AboutCollection($abouts);

            return $this->sendResponse($abouts, 'About list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(AboutRequest $request)
    {
        if (!$request->user()->hasPermission('abouts-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $about = $this->repository->store($request);

            $about = new AboutResource($about);

            return $this->sendResponse($about, 'About created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('abouts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $about = $this->repository->show($id);

            $about = new AboutResource($about);

            return $this->sendResponse($about, "About single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(AboutRequest $request, $id)
    {
        if (!$request->user()->hasPermission('abouts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $about = $this->repository->update($request, $id);

            $about = new AboutResource($about);

            return $this->sendResponse($about, 'About updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('abouts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $about = $this->repository->delete($id);

            return $this->sendResponse($about, 'About deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
