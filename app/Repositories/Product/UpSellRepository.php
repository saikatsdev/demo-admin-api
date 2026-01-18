<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use App\Models\Product\UpSell;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Product\UpSellDetail;

class UpSellRepository
{
    public function __construct(protected UpSell $model) {}

    public function index($request)
    {
        $paginateSize = $request->input("paginate_size", config('app.paginate_size'));
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        return $this->model
        ->when($status, fn($query) => $query->where("status", $status))
        ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
        ->orderBy("created_at", "asc")
        ->paginate($paginateSize);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $upSell = $this->model::create([
                "title"                => $request->title,
                "started_at"           => $request->started_at,
                "ended_at"             => $request->ended_at,
                "status"               => $request->status,
                "is_all"               => $request->is_all,
                "trigger_category_ids" => $request->trigger_category_ids ?? []
            ]);

            if (!empty($request->trigger_product_ids)) {
                $triggerProductIds = Product::active()->whereIn("id", $request->trigger_product_ids)->pluck("id");
            } elseif (!empty($request->trigger_category_ids)) {
                $triggerProductIds = Product::active()->whereIn("category_id", $request->trigger_category_ids)->pluck("id");
            } elseif ($request->is_all) {
                $triggerProductIds = Product::active()->pluck("id");
            } else {
                $triggerProductIds = collect();
            }

            // Preload up-sell products
            $offerProductIds = collect($request->up_sell_offers)->pluck("product_id")->unique();

            $products = Product::whereIn("id", $offerProductIds)->get()->keyBy("id");

            $insertData = [];

            foreach ($triggerProductIds as $triggerProductId) {
                foreach ($request->up_sell_offers as $offer) {
                    $product = $products[$offer["product_id"]] ?? null;

                    if (!$product) {
                        throw new CustomException("Product not found for up sell offer");
                    }

                    $calculatedAmount = 0;

                    if ($offer["discount_amount"] > 0) {
                        $calculatedAmount = $offer["discount_type"] === "fixed" ? $offer["discount_amount"] : ($product->mrp * $offer["discount_amount"]) / 100;
                    }

                    $insertData[] = [
                        "up_sell_id"         => $upSell->id,
                        "trigger_product_id" => $triggerProductId,
                        "up_sell_product_id" => $offer["product_id"],
                        "custom_name"        => $offer["custom_name"] ?? null,
                        "discount_type"      => $offer["discount_type"],
                        "discount_amount"    => $offer["discount_amount"],
                        "calculated_amount"  => round($calculatedAmount),
                        "created_at"         => now(),
                        "updated_at"         => now(),
                    ];
                }
            }

            UpSellDetail::insert($insertData);

            return $upSell;
        });
    }

    public function show($id)
    {
        $upSell = $this->model->with([
            "triggerProducts:id,name,slug,brand_id,category_id,sub_category_id,mrp,offer_price,sell_price,discount,offer_percent,current_stock,status,sku,img_path",
            "triggerProducts.variations",
            "triggerProducts.variations.attributeValue1:id,value,attribute_id",
            "triggerProducts.variations.attributeValue2:id,value,attribute_id",
            "triggerProducts.variations.attributeValue3:id,value,attribute_id",
            "triggerProducts.variations.attributeValue1.attribute:id,name",
            "triggerProducts.variations.attributeValue2.attribute:id,name",
            "triggerProducts.variations.attributeValue3.attribute:id,name",

            "offerProducts:id,name,slug,brand_id,category_id,sub_category_id,mrp,offer_price,sell_price,discount,offer_percent,current_stock,status,sku,img_path",
            "offerProducts.variations",
            "offerProducts.variations.attributeValue1:id,value,attribute_id",
            "offerProducts.variations.attributeValue2:id,value,attribute_id",
            "offerProducts.variations.attributeValue3:id,value,attribute_id",
            "offerProducts.variations.attributeValue1.attribute:id,name",
            "offerProducts.variations.attributeValue2.attribute:id,name",
            "offerProducts.variations.attributeValue3.attribute:id,name",
        ])->find($id);

        if (!$upSell) {
            throw new CustomException("Not found");
        }

        return $upSell;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $upSell = $this->model->findOrFail($id);

            $upSell->update([
                "title"                => $request->title,
                "started_at"           => $request->started_at,
                "ended_at"             => $request->ended_at,
                "status"               => $request->status,
                "is_all"               => $request->is_all,
                "trigger_category_ids" => $request->trigger_category_ids ?? []
            ]);

            if (!empty($request->trigger_product_ids)) {
                $triggerProductIds = Product::active()->whereIn("id", $request->trigger_product_ids)->pluck("id");
            } elseif (!empty($request->trigger_category_ids)) {
                $triggerProductIds = Product::active()->whereIn("category_id", $request->trigger_category_ids)->pluck("id");
            } elseif ($request->is_all) {
                $triggerProductIds = Product::active()->pluck("id");
            } else {
                $triggerProductIds = collect();
            }

            $offerProductIds = collect($request->up_sell_offers)->pluck("product_id")->unique();
            $products = Product::whereIn("id", $offerProductIds)->get()->keyBy("id");

            UpSellDetail::where("up_sell_id", $upSell->id)->delete();

            $insertData = [];

            foreach ($triggerProductIds as $triggerProductId) {
                foreach ($request->up_sell_offers as $offer) {
                    $product = $products[$offer["product_id"]] ?? null;

                    if (!$product) {
                        throw new CustomException("Product not found for up sell offer");
                    }

                    $calculatedAmount = 0;

                    if ($offer["discount_amount"] > 0) {
                        $calculatedAmount = $offer["discount_type"] === "fixed"
                            ? $offer["discount_amount"]
                            : ($product->mrp * $offer["discount_amount"]) / 100;
                    }

                    $insertData[] = [
                        "up_sell_id"         => $upSell->id,
                        "trigger_product_id" => $triggerProductId,
                        "up_sell_product_id" => $offer["product_id"],
                        "custom_name"        => $offer["custom_name"] ?? null,
                        "discount_type"      => $offer["discount_type"],
                        "discount_amount"    => $offer["discount_amount"],
                        "calculated_amount"  => round($calculatedAmount),
                        "created_at"         => now(),
                        "updated_at"         => now(),
                    ];
                }
            }

            UpSellDetail::insert($insertData);

            return $upSell;
        });
    }

    public function delete($id)
    {
        $upSell = $this->model->find($id);

        if (!$upSell) {
            throw new CustomException("Not found");
        }

        $upSell->upSellDetails()->delete();

        return $upSell->delete();
    }
}
