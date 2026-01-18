<?php

namespace App\Http\Controllers\Frontend\Order;

use App\Exceptions\CustomException;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OrderGuardRepository;

class OrderGuardController extends BaseController
{
    public function __construct(protected OrderGuardRepository $repository) {}

    public function get()
    {
        try {
            $orderGuard = $this->repository->get();

            return $this->sendResponse($orderGuard, 'Order guard list', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }
}
