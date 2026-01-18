<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use App\Models\Order\DeliveryGateway;

class DeliveryGatewayRepository
{
    public function __construct(protected DeliveryGateway $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input('status', null);

        $deliveryGateways = $this->model->with(["createdBy:id,username"])
            ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

        return $deliveryGateways;
    }

    public function list()
    {
        return $this->model
            ->select("id", "name", "delivery_fee")
            ->where("status", StatusEnum::ACTIVE)
            ->orderBy("name", "ASC")
            ->get();
    }

    public function store($request)
    {
        $deliveryGateway = new $this->model();

        $deliveryGateway->name         = $request->name;
        $deliveryGateway->min_time     = $request->min_time;
        $deliveryGateway->max_time     = $request->max_time;
        $deliveryGateway->time_unit    = $request->time_unit;
        $deliveryGateway->delivery_fee = $request->delivery_fee;
        $deliveryGateway->slug         = Str::slug($request->name);
        $deliveryGateway->status       = $request->status ?? StatusEnum::ACTIVE;
        $deliveryGateway->save();

        return $deliveryGateway;
    }

    public function show($id)
    {
        $deliveryGateway = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$deliveryGateway) {
            throw new CustomException("DeliveryGateway not found");
        }

        return $deliveryGateway;
    }

    public function update($request, $id)
    {
        $deliveryGateway = $this->model->find($id);

        if (!$deliveryGateway) {
            throw new CustomException("Delivery Gateway Not found");
        }

        $deliveryGateway->name         = $request->name;
        $deliveryGateway->min_time     = $request->min_time;
        $deliveryGateway->max_time     = $request->max_time;
        $deliveryGateway->time_unit    = $request->time_unit;
        $deliveryGateway->delivery_fee = $request->delivery_fee;
        $deliveryGateway->slug         = Str::slug($request->name);
        $deliveryGateway->status       = $request->status ?? StatusEnum::ACTIVE;
        $deliveryGateway->save();

        return $deliveryGateway;
    }

    public function delete($id)
    {
        $deliveryGateway = $this->model->find($id);

        if (!$deliveryGateway) {
            throw new CustomException('DeliveryGateway not found');
        }

        return $deliveryGateway->forceDelete();
    }

    public function deliveryPrice($id)
    {
        $deliveryGateway = DeliveryGateway::find($id);

        if (!$deliveryGateway) {
            throw new CustomException("Delivery gateway not found");
        }

        return $deliveryGateway;
    }
}
