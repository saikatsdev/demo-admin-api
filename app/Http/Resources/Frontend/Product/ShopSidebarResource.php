<?php

namespace App\Http\Resources\Frontend\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopSidebarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "max_sell_price" => $this["max_sell_price"],
            "min_sell_price" => $this["min_sell_price"],
            "categories"     => CategoryResource::collection($this["categories"]),
            "brands"         => BrandResource::collection($this["brands"]),
            "attributes"     => AttributeResource::collection($this["attributes"]),
        ];
    }
}
