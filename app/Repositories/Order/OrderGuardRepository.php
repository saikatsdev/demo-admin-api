<?php

namespace App\Repositories\Order;

use App\Enums\StatusEnum;
use App\Helpers\Helper;
use App\Models\Order\OrderGuard;
use App\Exceptions\CustomException;

class OrderGuardRepository
{
    public function __construct(protected OrderGuard $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input("status", null);

        $guards = $this->model->with(["createdBy:id,username"])
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'desc')
            ->paginate($paginateSize);

        return $guards;
    }

    public function store($request)
    {
        $guard = new $this->model();

        $guard->quantity              = $request->quantity;
        $guard->duration              = $request->duration;
        $guard->allow_percentage      = $request->allow_percentage;
        $guard->block_message         = $request->block_message;
        $guard->courier_block_message = $request->courier_block_message;
        $guard->permanent_block_message = $request->permanent_block_message;
        $guard->duration_type         = $request->duration_type;
        $guard->status                = $request->status;
        $guard->save();

        return $guard;
    }

    public function show($id, $status = null)
    {
        $guard = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])
            ->when($status, fn($query) => $query->where("status", $status))
            ->find($id);

        if (!$guard) {
            throw new CustomException("Order guard not found");
        }

        return $guard;
    }

    public function update($request, $id)
    {
        $guard = $this->model->find($id);

        if (!$guard) {
            throw new CustomException("Order guard Not found");
        }

        $guard->quantity              = $request->quantity;
        $guard->duration              = $request->duration;
        $guard->duration_type         = $request->duration_type;
        $guard->allow_percentage      = $request->allow_percentage;
        $guard->block_message         = $request->block_message;
        $guard->permanent_block_message = $request->permanent_block_message;
        $guard->courier_block_message = $request->courier_block_message;
        $guard->status                = $request->status;
        $guard->save();

        return $guard;
    }

    public function delete($id)
    {
        $guard = $this->model->find($id);

        if (!$guard) {
            throw new CustomException("Order guard not found");
        }

        return $guard->forceDelete();
    }

    public function get()
    {
        $orderGuard = $this->model->select("id", "quantity","duration","block_message","permanent_block_message","courier_block_message","duration_type","allow_percentage")
        ->where("status", StatusEnum::ACTIVE)
        ->first();

        if (!$orderGuard) {
            throw new CustomException("Order guard not found");
        }

        return $orderGuard;
    }
}
