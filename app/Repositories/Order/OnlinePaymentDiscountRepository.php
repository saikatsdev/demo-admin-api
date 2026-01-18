<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Exceptions\CustomException;
use App\Models\Order\OnlinePaymentDiscount;

class OnlinePaymentDiscountRepository
{
    public function __construct(protected OnlinePaymentDiscount $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input('status', null);

        $onlinePaymentDiscounts = $this->model->with(["createdBy:id,username", "paymentGateway"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("discount_type", "like", "%$searchKey%");
        })
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $onlinePaymentDiscounts;
    }

    public function store($request)
    {
        $onlinePaymentDiscount = $this->model
        ->where("payment_gateway_id", $request->payment_gateway_id)
        ->first();

        if (!$onlinePaymentDiscount) {
            $onlinePaymentDiscount = new $this->model();
        }

        $onlinePaymentDiscount->payment_gateway_id      = $request->payment_gateway_id;
        $onlinePaymentDiscount->discount_type           = $request->discount_type;
        $onlinePaymentDiscount->discount_amount         = $request->discount_amount;
        $onlinePaymentDiscount->minimum_cart_amount     = $request->minimum_cart_amount;
        $onlinePaymentDiscount->maximum_discount_amount = $request->maximum_discount_amount;
        $onlinePaymentDiscount->status                  = $request->status;
        $onlinePaymentDiscount->save();

        return $onlinePaymentDiscount;
    }

    public function show($id)
    {
        $onlinePaymentDiscount = $this->model
        ->with(["paymentGateway", "createdBy:id,username", "updatedBy:id,username"])
        ->find($id);

        if (!$onlinePaymentDiscount) {
            throw new CustomException("Online payment discount not found");
        }

        return $onlinePaymentDiscount;
    }

    public function update($request, $id)
    {
        $onlinePaymentDiscount = $this->model->find($id);

        if (!$onlinePaymentDiscount) {
            throw new CustomException("Online payment discount not found");
        }

        $onlinePaymentDiscount->payment_gateway_id      = $request->payment_gateway_id;
        $onlinePaymentDiscount->discount_type           = $request->discount_type;
        $onlinePaymentDiscount->discount_amount         = $request->discount_amount;
        $onlinePaymentDiscount->minimum_cart_amount     = $request->minimum_cart_amount;
        $onlinePaymentDiscount->maximum_discount_amount = $request->maximum_discount_amount;
        $onlinePaymentDiscount->status                  = $request->status ?? StatusEnum::ACTIVE;
        $onlinePaymentDiscount->save();

        return $onlinePaymentDiscount;
    }

    public function delete($id)
    {
        $onlinePaymentDiscount = $this->model->find($id);

        if (!$onlinePaymentDiscount) {
            throw new CustomException("Online payment discount not found");
        }

        return $onlinePaymentDiscount->forceDelete();
    }
}
