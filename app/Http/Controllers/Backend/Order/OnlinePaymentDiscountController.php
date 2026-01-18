<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OnlinePaymentDiscountRepository;
use App\Http\Requests\Backend\Order\OnlinePaymentDiscountRequest;
use App\Http\Resources\Backend\Order\OnlinePaymentDiscountResource;
use App\Http\Resources\Backend\Order\OnlinePaymentDiscountCollection;

class OnlinePaymentDiscountController extends BaseController
{
    public function __construct(protected OnlinePaymentDiscountRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('online-payment-discounts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $onlinePaymentDiscounts = $this->repository->index($request);

            $onlinePaymentDiscounts = new OnlinePaymentDiscountCollection($onlinePaymentDiscounts);

            return $this->sendResponse($onlinePaymentDiscounts, 'Online payment discount list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(OnlinePaymentDiscountRequest $request)
    {
        if (!$request->user()->hasPermission('online-payment-discounts-create')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $onlinePaymentDiscount = $this->repository->store($request);

            $onlinePaymentDiscount = new OnlinePaymentDiscountResource($onlinePaymentDiscount);

            return $this->sendResponse($onlinePaymentDiscount, 'Online payment discount created successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('online-payment-discounts-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $onlinePaymentDiscount = $this->repository->show($id);

            $onlinePaymentDiscount = new OnlinePaymentDiscountResource($onlinePaymentDiscount);

            return $this->sendResponse($onlinePaymentDiscount, 'Online payment discount single view', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(OnlinePaymentDiscountRequest $request, $id)
    {
        if (!$request->user()->hasPermission('online-payment-discounts-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $onlinePaymentDiscount = $this->repository->update($request, $id);

            $onlinePaymentDiscount = new OnlinePaymentDiscountResource($onlinePaymentDiscount);

            return $this->sendResponse($onlinePaymentDiscount, 'Online payment discount updated successfully', 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('online-payment-discounts-delete')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $onlinePaymentDiscount = $this->repository->delete($id);

            return $this->sendResponse($onlinePaymentDiscount, 'Online payment discount deleted successfully', 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
