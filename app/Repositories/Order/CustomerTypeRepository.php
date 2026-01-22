<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\CustomerType;
use App\Exceptions\CustomException;

class CustomerTypeRepository
{
    public function __construct(protected CustomerType $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $customerTypes = $this->model
        ->with(["createdBy:id,username", "updatedBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $customerTypes;
    }

    public function list()
    {
        return $this->model
        ->select("id", "name","order_range")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("name", "ASC")
        ->get();
    }

    public function store($request)
    {
        $customerType = new $this->model();

        $customerType->name        = $request->name;
        $customerType->slug        = $request->name;
        $customerType->order_range = $request->order_range;
        $customerType->status      = $request->status;
        $customerType->save();

        return $customerType;
    }

    public function show($id)
    {
        $customerType = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$customerType) {
            throw new CustomException("customer type not found");
        }

        return $customerType;
    }

    public function update($request, $id)
    {
        $customerType = $this->model->find($id);

        if (!$customerType) {
            throw new CustomException("customerType not found");
        }

        $customerType->name        = $request->name;
        $customerType->slug        = $request->name;
        $customerType->order_range = $request->order_range;
        $customerType->status      = $request->status ?? StatusEnum::ACTIVE;
        $customerType->save();

        return $customerType;
    }

    public function delete($id)
    {
        $customerType = $this->model->find($id);

        if (!$customerType) {
            throw new CustomException("Courier not found");
        }

        return $customerType->forceDelete();
    }
}
