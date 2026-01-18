<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use App\Models\Product\AttributeValue;

class AttributeValueRepository
{
    public function __construct(protected AttributeValue $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input("is_paginate", null);
        $attributeId  = $request->input("attribute_id", null);
        $value        = $request->input("value", null);

        try {
            $attributeValues = $this->model->with(["attribute:id,name", "createdBy:id,username"])
            ->when($value, fn($query) => $query->where("value", "like", "%$value%"))
            ->when($attributeId, fn($query) => $query->where("attribute_id", $attributeId))
            ->orderBy('created_at', 'desc');

            return $isPaginate ? $attributeValues->paginate($paginateSize) : $attributeValues->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function list()
    {
        try {
            return $this->model
            ->select("id", "value")
            ->orderBy("value", "ASC")
            ->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            $attributeValue = $this->model->firstOrCreate(
                [
                    'attribute_id' => $request->attribute_id,
                    'slug'         => Str::slug($request->value, "-"),
                ],
                [
                    'value' => $request->value,
                ]
            );

            $attributeValue->load('attribute:id,name');

            return $attributeValue;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $attributeValue = $this->model->with(["attribute:id,name", "createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            return $attributeValue;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            $attributeValue = $this->model->find($id);
            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            $attributeValue->value = $request->value;
            $attributeValue->slug = $request->value;
            $attributeValue->save();

            $attributeValue->load('attribute:id,name');

            return $attributeValue;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $attributeValue = $this->model->find($id);
            if (!$attributeValue) {
                throw new CustomException("Attribute value not found");
            }

            return $attributeValue->delete();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input("search_key", null);

        try {
            $attributes = $this->model->with(["attribute:id,name", "createdBy:id,username"])
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
                throw new CustomException("Attribute value not found");
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
                throw new CustomException("Attribute value not found");
            }

            $attribute->forceDelete();

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
