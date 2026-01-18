<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\Product\Warranty;
use App\Exceptions\CustomException;

class WarrantyRepository
{
    public function __construct(protected Warranty $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $warranties = $this->model
        ->with(["createdBy:id,username"])
        ->when($searchKey, fn($query) => $query->where('name', 'like', "%$searchKey%"))
        ->orderBy('name', 'desc')
        ->paginate($paginateSize);

        return $warranties;
    }

    public function store($request)
    {
        $warranty = new $this->model();

        $warranty->name   = $request->name;
        $warranty->slug   = Str::slug($request->name);
        $warranty->status = $request->status;
        $warranty->days   = $request->days;
        $warranty->save();

        return $warranty;
    }

    public function show($id)
    {
        $warranty = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$warranty) {
            throw new CustomException("Warranty not found");
        }

        return $warranty;
    }

    public function update($request, $id)
    {
        $warranty = $this->model->find($id);

        if (!$warranty) {
            throw new CustomException("Warranty not found");
        }

        $warranty->name   = $request->name;
        $warranty->slug   = Str::slug($request->name);
        $warranty->status = $request->status;
        $warranty->days   = $request->days;
        $warranty->save();

        return $warranty;
    }

    public function delete($id)
    {
        $warranty = $this->model->find($id);

        if (!$warranty) {
            throw new CustomException('Warranty not found');
        }

        return $warranty->forceDelete();
    }
}
