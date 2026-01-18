<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SettingCategoryRepository;
use App\Http\Requests\Backend\SettingCategoryRequest;
use App\Http\Resources\Backend\SettingCategoryResource;
use App\Http\Resources\Backend\SettingCategoryCollection;

class SettingCategoryController extends BaseController
{
    public function __construct(protected SettingCategoryRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('setting-category-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategories = $this->repository->index($request);

            $settingCategories = new SettingCategoryCollection($settingCategories);

            return $this->sendResponse($settingCategories, 'Setting category list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SettingCategoryRequest $request)
    {
        if (!$request->user()->hasPermission('setting-category-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->store($request);

            $settingCategory = new SettingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, 'Setting category created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->show($id);

            $settingCategory = new settingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, "Setting category single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(settingCategoryRequest $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->update($request, $id);

            $settingCategory = new settingCategoryResource($settingCategory);

            return $this->sendResponse($settingCategory, 'Setting category updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('setting-category-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settingCategory = $this->repository->delete($id);

            return $this->sendResponse($settingCategory, 'Setting category deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
