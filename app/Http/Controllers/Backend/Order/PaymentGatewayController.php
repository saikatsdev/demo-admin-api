<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\PaymentGatewayRepository;
use App\Http\Requests\Backend\Order\PaymentGatewayRequest;
use App\Http\Resources\Backend\Order\PaymentGatewayResource;
use App\Http\Resources\Backend\Order\PaymentGatewayCollection;

class PaymentGatewayController extends BaseController
{
    public function __construct(protected PaymentGatewayRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('payment-gateways-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $paymentGateways = $this->repository->index($request);

            $paymentGateways = new PaymentGatewayCollection($paymentGateways);

            return $this->sendResponse($paymentGateways, 'Payment gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $paymentGateways = $this->repository->list();

            return $this->sendResponse($paymentGateways, 'Payment gateway list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('payment-gateways-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }
        try {
            $paymentGateway = $this->repository->show($id);

            $paymentGateway = new PaymentGatewayResource($paymentGateway);

            return $this->sendResponse($paymentGateway, 'Payment gateway single view', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(PaymentGatewayRequest $request)
    {
        if (!$request->user()->hasPermission('payment-gateways-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $paymentGateway = $this->repository->store($request);

            $paymentGateway = new PaymentGatewayResource($paymentGateway);


            return $this->sendResponse($paymentGateway, 'Payment gateway created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(PaymentGatewayRequest $request, $id)
    {
        if (!$request->user()->hasPermission('payment-gateways-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $paymentGateway = $this->repository->update($request, $id);

            $paymentGateway = new PaymentGatewayResource($paymentGateway);

            return $this->sendResponse($paymentGateway, 'Payment gateway updated successfully', 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('payment-gateways-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $paymentGateway = $this->repository->delete($id);

            return $this->sendResponse($paymentGateway, 'Payment gateway deleted successfully', 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}

