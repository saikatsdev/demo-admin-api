<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\DeliveryGatewayRepository;
use App\Http\Resources\Frontend\Order\DeliveryGatewayResource;

class DeliveryGatewayController extends BaseController
{
    protected $repository;

    public function __construct(DeliveryGatewayRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $deliveryGateways = $this->repository->list();

            return $this->sendResponse($deliveryGateways, 'Delivery gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
        try {
            $deliveryGateway = $this->repository->show($id);

            $deliveryGateway = new DeliveryGatewayResource($deliveryGateway);

            return $this->sendResponse($deliveryGateway, 'Delivery gateway', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function deliveryPrice($id)
    {
        try {
            $deliveryGateway = $this->repository->deliveryPrice($id);

            $deliveryGateway = new DeliveryGatewayResource($deliveryGateway);

            return $this->sendResponse($deliveryGateway, 'Delivery gateway', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
