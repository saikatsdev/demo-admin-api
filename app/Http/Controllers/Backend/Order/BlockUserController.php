<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\BlockUserRepository;
use App\Http\Requests\Backend\Order\BlockUserRequest;
use App\Http\Resources\Backend\Order\BlockUserResource;
use App\Http\Resources\Backend\Order\BlockUserCollection;

class BlockUserController extends BaseController
{
    public function __construct(protected BlockUserRepository $repository) {}

    public function index(Request $request)
    {
        // if (!$request->user()->hasPermission('block-users-read')) {
        //     return $this->sendError(__("common.unauthorized"), 401);
        // }

        try {
            $blockUsers = $this->repository->index($request);

            $blockUsers = new BlockUserCollection($blockUsers);

            return $this->sendResponse($blockUsers, 'Block user list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(Request $request)
    {
        $blockUser = $this->repository->store($request);

        return $this->sendResponse($blockUser, "Block User Successfully", 200);
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('block-users-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blockUser = $this->repository->show($id);

            $blockUser = new BlockUserResource($blockUser);

            return $this->sendResponse($blockUser, 'Block user show', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(BlockUserRequest $request, $id)
    {
        if (!$request->user()->hasPermission('block-users-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $blockUser = $this->repository->update($request, $id);

            $blockUser = new BlockUserResource($blockUser);

            return $this->sendResponse($blockUser, 'Block user updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
    
    public function userBlock(Request $request)
    {
        try {
            $blockUser = $this->repository->userBlock($request);

            return $this->sendResponse($blockUser, "User is Permanent block");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
