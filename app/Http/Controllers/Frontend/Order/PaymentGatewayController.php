<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\PaymentGatewayRepository;
use App\Http\Resources\Frontend\Order\PaymentGatewayResource;

class PaymentGatewayController extends BaseController
{
    protected $repository;

    public function __construct(PaymentGatewayRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $paymentGateways = $this->repository->list();

            $paymentGateways = PaymentGatewayResource::collection($paymentGateways);

            return $this->sendResponse($paymentGateways, 'Payment gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($id)
    {
        try {
            $paymentGateway = $this->repository->show($id);

            $paymentGateway = new PaymentGatewayResource($paymentGateway);

            return $this->sendResponse($paymentGateway, "PaymentGateway single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
