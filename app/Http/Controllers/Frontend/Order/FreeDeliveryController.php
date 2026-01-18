<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\FreeDeliveryRepository;
use App\Http\Resources\Frontend\Order\FreeDeliveryResource;

class FreeDeliveryController extends BaseController
{
    public function __construct(protected FreeDeliveryRepository $repository) {}

    public function getFreeDelivery()
    {
        try {
            $freeDelivery = $this->repository->getFreeDelivery();

            if ($freeDelivery) {
                $freeDelivery = new FreeDeliveryResource($freeDelivery);
            }

            return $this->sendResponse($freeDelivery, 'Free delivery single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
