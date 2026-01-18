<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpSellResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                   => $this->id,
            "title"                => $this->title,
            "started_at"           => $this->started_at,
            "ended_at"             => $this->ended_at,
            "status"               => $this->status,
            "is_all"               => $this->is_all,
            "trigger_category_ids" => $this->trigger_category_ids,
            "created_at"           => $this->created_at,
            "trigger_products" => $this->whenLoaded('triggerProducts', function () {
                return $this->triggerProducts->map(function ($product) {
                    return [
                        "id"             => $product->id,
                        "name"           => $product->name,
                        "slug"           => $product->slug,
                        "mrp"            => $product->mrp,
                        "offer_price"    => $product->offer_price,
                        "sell_price"     => $product->sell_price,
                        "discount"       => $product->discount,
                        "offer_percent"  => $product->offer_percent,
                        "current_stock"  => $product->current_stock,
                        "status"         => $product->status,
                        "sku"            => $product->sku,
                        "img_path"       => Helper::getFilePath($product->img_path),
                        "variations"     => ProductVariationResource::collection($product->variations),
                    ];
                });
            }),
            "offer_products" => $this->whenLoaded('offerProducts', function () {
                return $this->offerProducts
                    ->unique('id')
                    ->values()
                    ->map(function ($product) {
                        return [
                            "id"             => $product->id,
                            "name"           => $product->name,
                            "slug"           => $product->slug,
                            "mrp"            => $product->mrp,
                            "offer_price"    => $product->offer_price,
                            "sell_price"     => $product->sell_price,
                            "discount"       => $product->discount,
                            "offer_percent"  => $product->offer_percent,
                            "current_stock"  => $product->current_stock,
                            "status"         => $product->status,
                            "sku"            => $product->sku,
                            "img_path"       => Helper::getFilePath($product->img_path),
                            "variations"     => ProductVariationResource::collection($product->variations),
                            "pivot"          => $product->pivot
                        ];
                    });
            }),
        ];
    }
}
