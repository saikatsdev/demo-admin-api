<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PermissionRepository;
use App\Http\Requests\Backend\PermissionRequest;
use App\Http\Resources\Backend\PermissionResource;
use App\Http\Resources\Backend\PermissionCollection;

class PermissionController extends BaseController
{
    public function __construct(protected PermissionRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('permissions-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $permissions = $this->repository->index($request);

            $permissions = new PermissionCollection($permissions);

            return $this->sendResponse($permissions, 'Permission list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $permissions = $this->repository->list();

            // $permissions = new PermissionCollection($permissions);

            return $this->sendResponse($permissions, 'Permission list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(PermissionRequest $request)
    {
        if (!$request->user()->hasPermission('permissions-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $permission = $this->repository->store($request);

            $permission = new PermissionResource($permission);

            return $this->sendResponse($permission, 'Permission created successfully', 200);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('permissions-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $permission = $this->repository->show($id);

            $permission = new PermissionResource($permission);

            return $this->sendResponse($permission, 'Permission single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(PermissionRequest $request, $id)
    {
        if (!$request->user()->hasPermission('permissions-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $permission = $this->repository->update($request, $id);

            $permission = new PermissionResource($permission);

            return $this->sendResponse($permission, 'Permission updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('permissions-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $permission = $this->repository->delete($id);

            return $this->sendResponse($permission, 'permission deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));

        }
    }
}
