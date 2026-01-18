<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\OrderFrom;
use App\Exceptions\CustomException;

class OrderFromRepository
{
    public function __construct(protected OrderFrom $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $orderFroms = $this->model
        ->with(["createdBy:id,username"])
        ->when($searchKey, fn($query) => $query->where("name", "like", "%$searchKey%"))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $orderFroms;
    }

    public function list()
    {
        return $this->model
        ->select("id", "name", "slug")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("name", "ASC")
        ->get();
    }

    public function store($request)
    {
        $orderFrom = new $this->model();

        $orderFrom->name   = $request->name;
        $orderFrom->slug   = $request->name;
        $orderFrom->status = $request->status;
        $orderFrom->save();

        return $orderFrom;
    }

    public function show($id)
    {
        $orderFrom = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$orderFrom) {
            throw new CustomException("Order From not found");
        }

        return $orderFrom;
    }

    public function update($request, $id)
    {
        $orderFrom = $this->model->find($id);

        if (!$orderFrom) {
            throw new CustomException("Order From Not found");
        }

        $orderFrom->name   = $request->name;
        $orderFrom->slug   = $request->name;
        $orderFrom->status = $request->status;
        $orderFrom->save();

        return $orderFrom;
    }

    public function delete($id)
    {
        $orderFrom = $this->model->find($id);

        if (!$orderFrom) {
            throw new CustomException('Order Froms not found');
        }

        return $orderFrom->delete();
    }
}
