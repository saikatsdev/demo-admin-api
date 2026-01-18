<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Exceptions\CustomException;
use App\Models\Product\ProductType;

class ProductTypeRepository
{
    public function __construct(protected ProductType $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);

        $productTypes = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $productTypes;
    }

    public function list()
    {
        return $this->model
        ->select("id", "name")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("name", "ASC")
        ->get();
    }

    public function store($request)
    {
        $productType = new $this->model();

        $productType->name   = $request->name;
        $productType->slug   = $request->name;
        $productType->status = $request->status;
        $productType->save();

        return $productType;
    }

    public function show($id)
    {
        $productType = $this->model->with([
            "createdBy:id,username",
            "updatedBy:id,username",
            "products",
            "products.variations",
            "products.variations.attributeValue1:id,value",
            "products.variations.attributeValue2:id,value",
            "products.variations.attributeValue3:id,value"
        ])->find($id);

        if (!$productType) {
            throw new CustomException("Product type not found");
        }

        return $productType;
    }

    public function update($request, $id)
    {
        $productType = $this->model->find($id);
        if (!$productType) {
            throw new CustomException("Product type Not found");
        }

        $productType->name    = $request->name;
        $productType->slug    = $request->name;
        $productType->status  = $request->status;
        $productType->save();

        return $productType;
    }

    public function delete($id)
    {
        $productType = $this->model->find($id);

        if (!$productType) {
            throw new CustomException("Product type not found");
        }

        return $productType->delete();
    }
}
