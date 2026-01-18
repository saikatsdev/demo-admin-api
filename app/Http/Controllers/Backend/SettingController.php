<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SettingRepository;
use App\Http\Requests\Backend\SettingRequest;
use App\Http\Resources\Backend\SettingResource;
use App\Http\Resources\Backend\SettingCollection;

class SettingController extends BaseController
{
    public function __construct(protected SettingRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('settings-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settings = $this->repository->index($request);

            $settings = new SettingCollection($settings);

            return $this->sendResponse($settings, 'Setting list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list(Request $request)
    {
        if (!$request->user()->hasPermission('settings-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $settings = $this->repository->list($request);

            return $this->sendResponse($settings, 'Setting list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(SettingRequest $request)
    {
        if (!$request->user()->hasPermission('settings-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $this->repository->store($request);

            return $this->sendResponse(null, 'Setting data store successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('settings-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->show($id);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, 'Setting single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('settings-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $setting = $this->repository->delete($id);

            return $this->sendResponse($setting, 'Setting deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getModuleStatus(Request $request)
    {
        try {
            if (!$request->user()->hasPermission('settings-read')) {
                return $this->sendError(__("common.unauthorized"), 401);
            }

            $modulesStatus = $this->repository->getModuleStatus();

            return $this->sendResponse($modulesStatus, 'Module status list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateModuleStatus(Request $request)
    {
        try {
            if (!$request->user()->hasPermission('settings-create')) {
                return $this->sendError(__("common.unauthorized"), 401);
            }

            $modulesStatus = $this->repository->updateModuleStatus($request);

            return $this->sendResponse($modulesStatus, 'Module status updated successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
