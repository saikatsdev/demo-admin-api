<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Product\Brand;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Str;

class BrandRepository
{
    public function __construct(protected Brand $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $isPaginate   = $request->input("is_paginate", true);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        try {
            $brands = $this->model
            ->with([
                "createdBy:id,username",
                "updatedBy:id,username"
            ])
            ->withCount("products")
            ->when($status, fn ($query) => $query->where("status", $status))
            ->when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"));

            if ($isPaginate) {
                $brands = $brands->orderBy("created_at", "desc")->paginate($paginateSize);
            } else {
                $brands = $brands->orderBy("name", "asc")->get();
            }

            return $brands;
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
            DB::beginTransaction();

            $brand = new $this->model();

            $brand->name   = Str::title($request->name);
            $brand->slug   = $request->name;
            $brand->status = $request->status;

            if ($request->image) {
                $brand->img_path = Helper::uploadFile($request->image, $brand->uploadPath, $request->height, $request->width);
            }

            $brand->save();

            DB::commit();

            return $brand;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $brand = $this->model
            ->with(["createdBy:id,username", "updatedBy:id,username"])
            ->withCount("products")
            ->find($id);

            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            return $brand;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $brand = $this->model->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            $brand->name   = Str::title($request->name);
            $brand->slug   = $request->name;
            $brand->status = $request->status ?? StatusEnum::ACTIVE;

            if ($request->image) {
                $brand->img_path = Helper::uploadFile($request->image, $brand->uploadPath, $request->height, $request->width);
            }

            $brand->save();

            DB::commit();

            return $brand;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $brand = $this->model->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            return $brand->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input("search_key", null);

        try {
            $brands = $this->model->with(["createdBy:id,username"])
            ->onlyTrashed()
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%");
            })
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $brands;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $brand = $this->model->onlyTrashed()->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }
            $brand->restore();

            return $brand;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $brand = $this->model->onlyTrashed()->find($id);
            if (!$brand) {
                throw new CustomException("Brand not found");
            }

            return $brand->forceDelete();

        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
