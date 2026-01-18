<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\RoleRepository;
use App\Http\Requests\Backend\RoleRequest;
use App\Http\Resources\Backend\RoleResource;
use App\Http\Resources\Backend\RoleCollection;

class RoleController extends BaseController
{
    public function __construct(protected RoleRepository $repository){}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('roles-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $roles = $this->repository->index($request);

            $roles = new RoleCollection($roles);

            return $this->sendResponse($roles, 'Role list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(RoleRequest $request)
    {
        if (!$request->user()->hasPermission('roles-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {

            $role = $this->repository->store($request);

            $role = new RoleResource($role);

            return $this->sendResponse($role, 'Role created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', 'Something went wrong');
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('roles-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $role = $this->repository->show($id);

            $role = new RoleResource($role);

            return $this->sendResponse($role, 'Role single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(RoleRequest $request, $id)
    {
        if (!$request->user()->hasPermission('roles-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $role = $this->repository->update($request, $id);

            $role = new RoleResource($role);

            return $this->sendResponse($role, 'Role updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', 'Something went wrong');
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('roles-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $role = $this->repository->delete($id);

            return $this->sendResponse($role, 'Role deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
