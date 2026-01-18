<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Product\Attribute;
use App\Exceptions\CustomException;

class AttributeRepository
{
    public function __construct(protected Attribute $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input('status', null);

        try {
            $attributes = $this->model->with([
                "createdBy:id,username",
                "updatedBy:id,username",
                "attributeValues" => function ($query) {
                    $query
                        ->select("id", "attribute_id", "value", "slug")
                        ->withCount([
                            'productVariations1 as product_count_1' => function ($q) {
                                $q->whereNotNull('attribute_value_id_1');
                            },
                            'productVariations2 as product_count_2' => function ($q) {
                                $q->whereNotNull('attribute_value_id_2');
                            },
                            'productVariations3 as product_count_3' => function ($q) {
                                $q->whereNotNull('attribute_value_id_3');
                            }
                        ]);
                }
            ])
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                    ->orWhere("status", $searchKey);
            })
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy('created_at', 'asc')
            ->paginate($paginateSize);

            return $attributes;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function list()
    {
        try {
            return $this->model
            ->select("id", "name")
            ->where("status", StatusEnum::ACTIVE)
            ->orderBy("name", "ASC")
            ->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            $attribute = new $this->model();

            $attribute->name   = $request->name;
            $attribute->slug   = $request->name;
            $attribute->status = $request->status;
            $attribute->save();

            return $attribute;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $attribute = $this->model->with([
                "createdBy:id,username",
                "updatedBy:id,username",
                "attributeValues:id,attribute_id,value,slug"
            ])->find($id);

            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            return $attribute;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            $attribute = $this->model->find($id);
            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            $attribute->name   = $request->name;
            $attribute->slug   = $request->name;
            $attribute->status = $request->status;
            $attribute->save();

            return $attribute;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $attribute = $this->model->find($id);
            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            return $attribute->delete();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input("search_key", null);

        try {
            $attributes = $this->model->with(["createdBy:id,username", "updatedBy:id,username", "attributeValues"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
            })
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $attributes;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $attribute = $this->model->onlyTrashed()->find($id);
            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            $attribute->restore();

            return $attribute;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $attribute = $this->model->onlyTrashed()->find($id);

            if (!$attribute) {
                throw new CustomException("Attribute not found");
            }

            $attribute->forceDelete();

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
