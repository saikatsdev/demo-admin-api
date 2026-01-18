<?php

namespace App\Http\Controllers\Backend\Product;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Product\UpSellRepository;
use App\Http\Requests\Backend\Product\UpSellRequest;
use App\Http\Resources\Backend\Product\UpSellResource;
use App\Http\Resources\Backend\Product\UpSellCollection;

class UpSellController extends BaseController
{
    public function __construct(protected UpSellRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('up-sells-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $upSells = $this->repository->index($request);

            $upSells = new UpSellCollection($upSells);

            return $this->sendResponse($upSells, 'Up sell list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(UpSellRequest $request)
    {
        if (!$request->user()->hasPermission('up-sells-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $upSell = $this->repository->store($request);

            $upSell = new UpSellResource($upSell);

            return $this->sendResponse($upSell, 'Up sell created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('up-sells-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $upSell = $this->repository->show($id);

            $upSell = new UpSellResource($upSell);

            return $this->sendResponse($upSell, "Up sell single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpSellRequest $request, $id)
    {
        if (!$request->user()->hasPermission('up-sells-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $upSell = $this->repository->update($request, $id);

            $upSell = new UpSellResource($upSell);

            return $this->sendResponse($upSell, 'Up sell updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('up-sells-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $upSell = $this->repository->delete($id);

            return $this->sendResponse($upSell, 'Deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
