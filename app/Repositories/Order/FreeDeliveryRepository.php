<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\FreeDelivery;
use App\Exceptions\CustomException;

class FreeDeliveryRepository
{
    public function __construct(protected FreeDelivery $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $freeDeliveries = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $freeDeliveries;
    }

    public function store($request)
    {
        $freeDelivery = new $this->model();

        $freeDelivery->quantity = $request->quantity;
        $freeDelivery->price    = $request->price;
        $freeDelivery->status   = $request->status;
        $freeDelivery->save();

        return $freeDelivery;
    }

    public function show($id, $status = null)
    {
        $freeDelivery = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])
        ->when($status, fn($query) => $query->where("status", $status))
        ->find($id);

        if (!$freeDelivery) {
            throw new CustomException("Free delivery not found");
        }

        return $freeDelivery;
    }

    public function update($request, $id)
    {
        $freeDelivery = $this->model->find($id);

        if (!$freeDelivery) {
            throw new CustomException("Free delivery not found");
        }

        $freeDelivery->quantity = $request->quantity;
        $freeDelivery->price    = $request->price;
        $freeDelivery->status   = $request->status;
        $freeDelivery->save();

        return $freeDelivery;
    }

    public function delete($id)
    {
        $freeDelivery = $this->model->find($id);

        if (!$freeDelivery) {
            throw new CustomException("Free delivery not found");
        }

        return $freeDelivery->delete();
    }

    public function getFreeDelivery()
    {
        $freeDelivery = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])
        ->where("status", StatusEnum::ACTIVE)
        ->first();

        return $freeDelivery;
    }
}
