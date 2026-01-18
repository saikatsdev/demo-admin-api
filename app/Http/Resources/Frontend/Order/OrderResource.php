<?php

namespace App\Http\Resources\Frontend\Order;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Models\Product\UpSellDetail;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Frontend\Product\ProductVariationResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "invoice_number"   => $this->invoice_number,
            "coupon_value"     => $this->coupon_value,
            "delivery_charge"  => $this->delivery_charge,
            "mrp"              => number_format($this->mrp, 2),
            "sell_price"       => number_format($this->sell_price, 2),
            "discount"         => number_format($this->discount, 2),
            "special_discount" => number_format($this->special_discount, 2),
            "net_order_price"  => number_format($this->net_order_price, 2),
            "advance_payment"  => number_format($this->advance_payment, 2),
            "payable_price"    => number_format($this->payable_price, 2),
            "courier_payable"  => number_format($this->courier_payable, 2),
            "paid_status"      => $this->paid_status,
            "phone_number"     => $this->phone_number,
            "customer_name"    => $this->customer_name,
            "district"         => $this->district,
            "address_details"  => $this->address_details,
            "consignment_id"   => $this->consignment_id,
            "tracking_code"    => $this->tracking_code,
            "note"             => $this->note,
            "created_at"       => $this->created_at,
            "delivery_gateway" => $this->whenLoaded('deliveryGateway'),
            "payment_gateway"  => $this->whenLoaded('paymentGateway'),
            "coupon"           => $this->whenLoaded('coupon'),
            "courier"          => $this->whenLoaded('courier'),
            "current_status"   => StatusResource::make($this->whenLoaded('currentStatus')),
            "statuses"         => StatusResource::collection($this->whenLoaded('statuses')),
            "details"          => OrderDetailResource::collection($this->whenLoaded('details')),
            "up_sell_details"  => $this->getUpSellDetail(),
        ];
    }

    private function getUpSellDetail()
    {
        $orderedProductIds = $this->details
        ->pluck('product_id')
        ->unique()
        ->values();

        if ($orderedProductIds->isEmpty()) {
            return [];
        }

        $upSellDetails = UpSellDetail::query()
        ->whereIn('trigger_product_id', $orderedProductIds)
        ->whereHas('upSell', function ($q) {
            $q->where('status', StatusEnum::ACTIVE)
                ->where(function ($q) {
                    $today = Carbon::today();
                    $q->whereNull('started_at')->orWhere('started_at', '<=', $today);
                })
                ->where(function ($q) {
                    $today = Carbon::today();
                    $q->whereNull('ended_at')->orWhere('ended_at', '>=', $today);
                });
        })
        ->with([
            "product",
            "product.variations",
            "product.variations.attributeValue1:id,value,attribute_id",
            "product.variations.attributeValue2:id,value,attribute_id",
            "product.variations.attributeValue3:id,value,attribute_id",
            "product.variations.attributeValue1.attribute:id,name",
            "product.variations.attributeValue2.attribute:id,name",
            "product.variations.attributeValue3.attribute:id,name"
        ])
        ->get()
        ->unique('up_sell_product_id')
        ->values();

        return $upSellDetails->map(function ($upSellDetail) {
            return [
                'up_sell_detail_id' => $upSellDetail->id,
                'custom_name'       => $upSellDetail->custom_name,
                'discount_type'     => $upSellDetail->discount_type,
                'discount_amount'   => $upSellDetail->discount_amount,
                'calculated_amount' => $upSellDetail->calculated_amount,
                'product'           => [
                    'id'            => $upSellDetail->product->id,
                    'name'          => $upSellDetail->product->name,
                    'slug'          => $upSellDetail->product->slug,
                    'mrp'           => $upSellDetail->product->mrp,
                    'offer_price'   => $upSellDetail->product->offer_price,
                    'discount'      => $upSellDetail->product->discount,
                    'sell_price'    => $upSellDetail->product->sell_price,
                    'offer_percent' => $upSellDetail->product->offer_percent,
                    'current_stock' => $upSellDetail->product->current_stock,
                    'minimum_qty'   => $upSellDetail->product->minimum_qty,
                    'sku'           => $upSellDetail->product->sku,
                    'free_shipping' => $upSellDetail->product->free_shipping,
                    'image'         => Helper::getFilePath($upSellDetail->product->img_path),
                    'variations'    => ProductVariationResource::collection($upSellDetail->product->variations)
                ],
            ];
        });
    }
}
