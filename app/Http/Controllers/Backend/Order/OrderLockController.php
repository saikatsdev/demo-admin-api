<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OrderLockRepository;

class OrderLockController extends BaseController
{
    public function __construct(protected OrderLockRepository $repository) {}

    public function orderLockedStatus($id)
    {
        try {
            $lockStatus = $this->repository->orderLockedStatus($id);

            return $this->sendResponse($lockStatus, "Order locked by", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderLocked($id)
    {
        try {
            $lockOrder = $this->repository->orderLocked($id);

            return $this->sendResponse($lockOrder, "Order locked", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderUnlocked($id)
    {
        try {
            $unlockOrder = $this->repository->orderUnlocked($id);

            return $this->sendResponse($unlockOrder, "Order unlocked", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
