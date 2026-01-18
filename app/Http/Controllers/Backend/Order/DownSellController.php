<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\DownSellRepository;
use App\Http\Requests\Backend\Order\DownSellRequest;
use App\Http\Resources\Backend\Order\DownSellResource;
use App\Http\Resources\Backend\Order\DownSellCollection;

class DownSellController extends BaseController
{
    public function __construct(protected DownSellRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('down-sells-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $downSells = $this->repository->index($request);

            $downSells = new DownSellCollection($downSells);

            return $this->sendResponse($downSells, 'Down sell list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(DownSellRequest $request)
    {
        if (!$request->user()->hasPermission('down-sells-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $downSell = $this->repository->store($request);

            $downSell = new DownSellResource($downSell);

            return $this->sendResponse($downSell, 'Down sell created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('down-sells-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $downSell = $this->repository->show($id);

            $downSell = new DownSellResource($downSell);

            return $this->sendResponse($downSell, "Down sell single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(DownSellRequest $request, $id)
    {
        if (!$request->user()->hasPermission('down-sells-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $downSell = $this->repository->update($request, $id);

            $downSell = new DownSellResource($downSell);

            return $this->sendResponse($downSell, 'Down sell updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('down-sells-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $downSell = $this->repository->delete($id);

            return $this->sendResponse($downSell, 'Down sell deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
