<?php

namespace App\Repositories\Order;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\DownSell;
use App\Enums\DiscountTypeEnum;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class DownSellRepository
{
    public function __construct(protected DownSell $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input('status', null);

        $productWiseCoupons = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, fn ($query) => $query->where("title", "like", "%$searchKey%"))
        ->when($status, fn ($query) => $query->where("status", $status))
        ->latest()
        ->paginate($paginateSize);

        return $productWiseCoupons;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $downSell = new $this->model();

            $downSell->title       = $request->title;
            $downSell->type        = $request->type ?? DiscountTypeEnum::FIXED;
            $downSell->amount      = $request->amount;
            $downSell->duration    = $request->duration;
            $downSell->started_at  = Helper::dateTimeFormat($request->started_at);
            $downSell->ended_at    = Helper::dateTimeFormat($request->ended_at);
            $downSell->description = $request->description;
            $downSell->status      = $request->status;

            // Upload image
            if ($request->image) {
                $downSell->img_path = Helper::uploadFile($request->image, $downSell->uploadPath, $request->height, $request->width);
            }

            $downSell->save();

            if (!empty($request->product_ids)) {
                $productIds = $request->product_ids;
            } elseif (!empty($request->category_ids)) {
                $productIds = Product::active()->whereIn('category_id', $request->category_ids)->pluck('id')->toArray();
            } elseif ($request->is_all) {
                $productIds = Product::active()->pluck('id')->toArray();
            } else {
                $productIds = [];
            }

            $downSell->products()->attach($productIds);

            return $downSell;
        });
    }

    public function show($id)
    {
        $downSell = $this->model->with([
            "products",
            "products.variations",
            "products.variations.attributeValue1:id,value,attribute_id",
            "products.variations.attributeValue2:id,value,attribute_id",
            "products.variations.attributeValue3:id,value,attribute_id",
            "products.variations.attributeValue1.attribute:id,name",
            "products.variations.attributeValue2.attribute:id,name",
            "products.variations.attributeValue3.attribute:id,name",
            "createdBy:id,username",
            "updatedBy:id,username"
        ])->find($id);

        if (!$downSell) {
            throw new CustomException("Down sell not found");
        }

        return $downSell;
    }

    public function update($request, $id)
    {
        $downSell = $this->model->find($id);

        if (!$downSell) {
            throw new CustomException("Down sell not found");
        }

        $downSell->title       = $request->title;
        $downSell->type        = $request->type ?? DiscountTypeEnum::FIXED;
        $downSell->amount      = $request->amount;
        $downSell->duration    = $request->duration;
        $downSell->started_at  = Helper::dateTimeFormat($request->started_at);
        $downSell->ended_at    = Helper::dateTimeFormat($request->ended_at);
        $downSell->description = $request->description;
        $downSell->status      = $request->status;

        // Upload image
        if ($request->image) {
            $downSell->img_path = Helper::uploadFile($request->image, $downSell->uploadPath, $request->height, $request->width);
        }

        $downSell->save();

        if (!empty($request->product_ids)) {
            $productIds = $request->product_ids;
        } elseif (!empty($request->category_ids)) {
            $productIds = Product::active()->whereIn('category_id', $request->category_ids)->pluck('id')->toArray();
        } elseif ($request->is_all) {
            $productIds = Product::active()->pluck('id')->toArray();
        } else {
            $productIds = [];
        }

        $downSell->products()->sync($productIds);

        return $downSell;
    }

    public function delete($id)
    {
        $downSell = $this->model->find($id);

        if (!$downSell) {
            throw new CustomException('Down sell not found');
        }

        //  Delete old image
        if ($downSell->img_path) {
            Helper::deleteFile($downSell->img_path);
        }

        $downSell->products()->detach();

        return $downSell->forceDelete();
    }

    public function get()
    {
        $now = Carbon::now();

        $downSell = $this->model
        ->where("status", StatusEnum::ACTIVE)
        ->where("started_at", "<=", $now)
        ->where("ended_at", ">=", $now)
        ->first();

        if (!$downSell) {
            throw new CustomException("Offer not available");
        }

        return $downSell;
    }
}
