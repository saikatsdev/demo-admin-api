<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Courier;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class CourierRepository
{
    public function __construct(protected Courier $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $couriers = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $couriers;
    }

    public function list($request)
    {
        $data = $this->model
        ->select('id', 'name','is_default')
        ->where('status', StatusEnum::ACTIVE)
        ->withCount([
        'orders' => fn($query) =>
            $request->status_id ? $query->where('current_status_id', $request->status_id) : $query
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
        ->orderBy('name')
        ->get();

        return $data;
    }


    public function store($request)
    {
        $courier = new $this->model();

        $courier->name   = $request->name;
        $courier->slug   = $request->name;
        $courier->status = $request->status;

        // Upload image
        if ($request->image) {
            $courier->img_path = Helper::uploadFile($request->image, $courier->uploadPath, $request->height, $request->width);
        }

        $courier->save();

        return $courier;
    }

    public function show($id)
    {
        $courier = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$courier) {
            throw new CustomException("Courier not found");
        }

        return $courier;
    }

    public function update($request, $id)
    {
        $courier = $this->model->find($id);

        if (!$courier) {
            throw new CustomException("Courier not found");
        }


        $courier->name   = $request->name;
        $courier->slug   = $request->name;
        $courier->status = $request->status ?? StatusEnum::ACTIVE;

        // Upload image
        if ($request->image) {
            $courier->img_path = Helper::uploadFile($request->image, $courier->uploadPath, $request->height, $request->width, $courier->img_path);
        }

        $courier->save();

        return $courier;
    }

    public function delete($id)
    {
        $courier = $this->model->find($id);

        if (!$courier) {
            throw new CustomException("Courier not found");
        }

        return $courier->delete();
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        $couriers = $this->model->with(["createdBy:id,username"])
        ->onlyTrashed()
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $couriers;
    }

    public function restore($id)
    {
        $courier = $this->model->onlyTrashed()->find($id);

        if (!$courier) {
            throw new CustomException("Courier not found");
        }
        $courier->restore();

        return $courier;
    }

    public function permanentDelete($id)
    {
        $courier = $this->model->onlyTrashed()->find($id);

        if (!$courier) {
            throw new CustomException("Courier not found");
        }

        return $courier->forceDelete();
    }
    
    public function courierSettings($request)
    {
        return DB::transaction(function () use ($request) {

            $courier = $this->model->find($request->courier_id);

            if (!$courier) {
                throw new CustomException("Courier not found");
            }

            $this->model->where('is_default', 1)->update([
                'is_default' => 0
            ]);

            $courier->is_default = 1;
            $courier->save();

            return true;
        });
    }
}
