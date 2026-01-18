<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Exceptions\CustomException;
use App\Models\Order\PaymentGateway;

class PaymentGatewayRepository
{
    public function __construct(protected PaymentGateway $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        $paymentGateways = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $paymentGateways;
    }

    public function list()
    {
        return $this->model
        ->select("id", "name", "slug","phone_number", "img_path")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("name", "ASC")
        ->get();
    }

    public function store($request)
    {
        $paymentGateway = new $this->model();

        $paymentGateway->name         = $request->name;
        $paymentGateway->slug         = $request->name;
        $paymentGateway->status       = $request->status;
        $paymentGateway->phone_number = $request->phone_number;

        //Upload image
        if ($request->image) {
            $paymentGateway->img_path = Helper::uploadFile($request->image, $paymentGateway->uploadPath, $request->height, $request->width);
        }

        $paymentGateway->save();

        return $paymentGateway;
    }

    public function show($id)
    {
        $paymentGateway = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$paymentGateway) {
            throw new CustomException('PaymentGateway not found');
        }

        return $paymentGateway;
    }

    public function update($request, $id)
    {
        $paymentGateway = $this->model->find($id);

        if (!$paymentGateway) {
            throw new CustomException("Payment Gateway Not found");
        }

        $paymentGateway->name         = $request->name;
        $paymentGateway->slug         = $request->name;
        $paymentGateway->status       = $request->status;
        $paymentGateway->phone_number = $request->phone_number;

        // Update image
        if ($request->image) {
            $paymentGateway->img_path = Helper::uploadFile($request->image, $paymentGateway->uploadPath, $request->height, $request->width);
        }

        $paymentGateway->save();

        return $paymentGateway;
    }

    public function delete($id)
    {
        $paymentGateway = $this->model->find($id);

        if (!$paymentGateway) {
            throw new CustomException("Payment Gateway not found");
        }

        return $paymentGateway->forceDelete();
    }
}
