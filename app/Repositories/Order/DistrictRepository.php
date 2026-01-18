<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\Order\District;
use App\Exceptions\CustomException;

class DistrictRepository
{
    public function __construct(protected District $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $slug         = $request->input("slug", null);

        $districts = $this->model->with(["createdBy:id,username"])
        ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
        ->when($slug, fn($query) => $query->where("slug", $slug))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $districts;
    }

    public function list($request)
    {
        $data = $this->model
        ->select('id', 'name')
        ->where('status', StatusEnum::ACTIVE)
        ->withCount([
            'orders' => fn($query) => $request->status_id ? $query->where('current_status_id', $request->status_id) : $query
        ])
        ->withSum([
            'orders as total_amount' => fn($query) =>
            $request->status_id ? $query->where('current_status_id', $request->status_id) : $query
        ], 'payable_price')
        ->when($request->status_id, fn($query) =>
            $query->whereHas('orders', fn($q) =>
                $q->where('current_status_id', $request->status_id)
            )
        )
        ->having('orders_count', '>', 0)
        ->orderBy('name')
        ->get();

        return $data;
    }

    public function store($request)
    {
        $district = new $this->model();

        $district->name = $request->name;
        $district->slug = Str::slug($request->name);
        $district->save();

        return $district;
    }

    public function show($id)
    {
        $district = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$district) {
            throw new CustomException("District not found");
        }

        return $district;
    }

    public function update($request, $id)
    {
        $district = $this->model->find($id);

        if (!$district) {
            throw new CustomException("District not found");
        }

        $district->name   = $request->name;
        $district->slug   = Str::slug($request->name);
        $district->save();

        return $district;
    }

    public function delete($id)
    {
        $district = $this->model->find($id);

        if (!$district) {
            throw new CustomException("District not found");
        }

        return $district->forceDelete();
    }
}
