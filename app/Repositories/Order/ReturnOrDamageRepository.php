<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Order\ReturnOrDamage;
use App\Models\Product\ProductVariation;
use App\Models\Order\ReturnOrDamageDetail;

class ReturnOrDamageRepository
{
    public function __construct(protected ReturnOrDamage $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $statusId     = $request->input("status_id", null);
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);

        if ($startDate && $endDate) {
            $startDate = Helper::startOfDate($startDate);
            $endDate   = Helper::endOfDate($endDate);
        }

        $returnOrDamages = $this->model
        ->with(["status"])
        ->when($statusId, fn($query) => $query->where("status_id", $statusId))
        ->when(($startDate && $endDate), fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
        ->when($searchKey, fn($query) => $query->where("reason", "like", "%$searchKey%"))
        ->orderBy("created_at", "desc")
        ->paginate($paginateSize);

        return $returnOrDamages;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $returnOrDamage = new $this->model();

            $returnOrDamage->order_id  = $request->order_id;
            $returnOrDamage->status_id = $request->status_id;
            $returnOrDamage->type      = $request->type;
            $returnOrDamage->reason    = $request->reason;
            $returnOrDamage->save();

            // Get order
            $order = Order::find($request->order_id);

            if (!$order) {
                throw new CustomException("Order not fond");
            }

            $order->current_status_id = $request->status_id;
            $order->save();

            $details = [];
            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"];
                $attributeValueId2 = $item["attribute_value_id_2"];
                $attributeValueId3 = $item["attribute_value_id_3"];
                $quantity          = $item["quantity"];
                $buyPrice          = $item["buy_price"];
                $mrp               = $item["mrp"];
                $discount          = $item["discount"];
                $sellPrice         = $item["sell_price"];

                // Check is stock maintain and status return
                if (Helper::setting("is_stock_maintain") && $request->status_id === 9) {
                    $product = Product::select("id", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock")
                        ->with("variations")
                        ->where("status", StatusEnum::ACTIVE)
                        ->find($productId);

                    if (!$product) {
                        throw new CustomException("Product not found");
                    }

                    // Check product have variation
                    if ($product->variations && count($product->variations) > 0) {
                        $productVariation = ProductVariation::where("product_id", $productId)
                            ->where("attribute_value_id_1", $attributeValueId1)
                            ->where("attribute_value_id_2", $attributeValueId2)
                            ->where("attribute_value_id_3", $attributeValueId3)
                            ->first();

                        // Check product current stock
                        if (!$productVariation) {
                            throw new CustomException("Variation $product->name not found");
                        }

                        // Update variation product current stock
                        $productVariation->current_stock += $quantity;
                        $productVariation->save();
                    } else {
                        // Update product current stock
                        $product->current_stock += $quantity;
                        $product->save();
                    }
                }

                $details[] = [
                    "return_or_damage_id"  => $returnOrDamage->id,
                    "product_id"           => $productId,
                    "attribute_value_id_1" => $attributeValueId1,
                    "attribute_value_id_2" => $attributeValueId2,
                    "attribute_value_id_3" => $attributeValueId3,
                    "quantity"             => $quantity,
                    "buy_price"            => $buyPrice,
                    "mrp"                  => $mrp,
                    "discount"             => $discount,
                    "sell_price"           => $sellPrice,
                    "created_at"           => now()
                ];
            }

            ReturnOrDamageDetail::insert($details);

            // Update buy_price, mrp, discount, sell_price
            $returnOrDamage->updateOrderValue($returnOrDamage);

            return $returnOrDamage;
        });
    }

    public function show($id)
    {
        $returnOrDamage = $this->model->with(
            [
                "createdBy:id,username",
                "updatedBy:id,username",
                "status:id,name,bg_color,text_color",
                "details",
                "details.attributeValue1:id,value,attribute_id",
                "details.attributeValue2:id,value,attribute_id",
                "details.attributeValue3:id,value,attribute_id",
                "details.attributeValue1.attribute:id,name",
                "details.attributeValue2.attribute:id,name",
                "details.attributeValue3.attribute:id,name",
                "details.product:id,name,img_path",
                "order",
                "order.details",
                "order.details.attributeValue1:id,value,attribute_id",
                "order.details.attributeValue2:id,value,attribute_id",
                "order.details.attributeValue3:id,value,attribute_id",
                "order.details.attributeValue1.attribute:id,name",
                "order.details.attributeValue2.attribute:id,name",
                "order.details.attributeValue3.attribute:id,name",
                "order.details.product:id,name,img_path",
            ]
        )->find($id);

        if (!$returnOrDamage) {
            throw new CustomException("Not found");
        }

        return $returnOrDamage;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $returnOrDamage = $this->model->find($id);

            if (!$returnOrDamage) {
                throw new CustomException("Not found");
            }

            // Check is stock maintain
            if (Helper::setting("is_stock_maintain") && $request->status_id === 9) {
                // Add previous quantity with product current stock
                foreach ($returnOrDamage->details as $item) {
                    $previousProduct = Product::with(["variations"])->find($item->product_id);

                    if ($previousProduct) {
                        if (count($previousProduct->variations) > 0) {
                            $previousVariation = ProductVariation::where("product_id", $item->product_id)
                                ->where("attribute_value_id_1", $item->attribute_value_id_1)
                                ->where("attribute_value_id_2", $item->attribute_value_id_2)
                                ->where("attribute_value_id_3", $item->attribute_value_id_3)
                                ->first();

                            if (!$previousVariation) {
                                throw new CustomException("Variation product not found");
                            }

                            $previousVariation->current_stock -= $item->quantity;
                            $previousVariation->save();
                        } else {
                            $previousProduct->current_stock -= $item->quantity;
                            $previousProduct->save();
                        }
                    }
                }
            }

            $returnOrDamage = $this->model->find($id);

            $returnOrDamage->status_id = $request->status_id;
            $returnOrDamage->type      = $request->type;
            $returnOrDamage->reason    = $request->reason;
            $returnOrDamage->save();

            // Get order
            $order = Order::find($request->order_id);
            if (!$order) {
                throw new CustomException("Order not fond");
            }

            $order->current_status_id = $request->status_id;
            $order->save();

            $details = [];

            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"];
                $attributeValueId2 = $item["attribute_value_id_2"];
                $attributeValueId3 = $item["attribute_value_id_3"];
                $quantity          = $item["quantity"];
                $buyPrice          = $item["buy_price"];
                $mrp               = $item["mrp"];
                $discount          = $item["discount"];
                $sellPrice         = $item["sell_price"];

                if (Helper::setting("is_stock_maintain") && $request->status_id === 9) {
                    $product = Product::select("id", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock")
                        ->with("variations")
                        ->where("status", StatusEnum::ACTIVE)
                        ->find($productId);

                    // Check product exist
                    if (!$product) {
                        throw new CustomException("Product not fount");
                    }

                    // Check product have variation
                    if (count($product->variations) > 0) {
                        $variation = ProductVariation::where("product_id", $productId)
                            ->where("attribute_value_id_1", $attributeValueId1)
                            ->where("attribute_value_id_2", $attributeValueId2)
                            ->where("attribute_value_id_3", $attributeValueId3)
                            ->first();

                        if (!$variation) {
                            throw new CustomException("Variation product not fount");
                        }

                        // Update variation product stock
                        $variation->current_stock += $quantity;
                        $variation->save();
                    } else {
                        // Update product current stock
                        $product->current_stock += $quantity;
                        $product->save();
                    }
                }

                // Prepare order details payload
                $details[] = [
                    "return_or_damage_id"  => $returnOrDamage->id,
                    "product_id"           => $productId,
                    "attribute_value_id_1" => $attributeValueId1,
                    "attribute_value_id_2" => $attributeValueId2,
                    "attribute_value_id_3" => $attributeValueId3,
                    "quantity"             => $quantity,
                    "buy_price"            => $buyPrice,
                    "mrp"                  => $mrp,
                    "discount"             => $discount,
                    "sell_price"           => $sellPrice,
                    "created_at"           => now()
                ];
            }

            // Delete previous order details
            $returnOrDamage->details()->delete();

            ReturnOrDamageDetail::insert($details);

            // Update buy_price, mrp, discount, sell_price
            $returnOrDamage->updateOrderValue($returnOrDamage);

            return $returnOrDamage;
        });
    }
}
