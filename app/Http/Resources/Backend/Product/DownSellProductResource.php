<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownSellProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "name"          => $this->name,
            "slug"          => $this->slug,
            "mrp"           => $this->mrp,
            "offer_price"   => $this->offer_price,
            "discount"      => $this->discount,
            "sell_price"    => $this->sell_price,
            "offer_percent" => $this->offer_percent,
            "current_stock" => $this->current_stock,
            "img_path"      => Helper::getFilePath($this->img_path),

            "variations" => $this->variations->map(function ($variation) {
                return [
                    "id"                => $variation->id,
                    "product_id"        => $variation->product_id,
                    "current_stock"     => $variation->current_stock,
                    "is_default"        => $variation->is_default,
                    "mrp"               => $variation->mrp,
                    "offer_price"       => $variation->offer_price,
                    "discount"          => $variation->discount,
                    "sell_price"        => $variation->sell_price,
                    "offer_percent"     => $variation->offer_percent,
                    "img_path"          => Helper::getFilePath($variation->img_path),
                    "attribute_value_1" => $variation->attributeValue1,
                    "attribute_value_2" => $variation->attributeValue2,
                    "attribute_value_3" => $variation->attributeValue3
                ];
            }),
        ];
    }
}
