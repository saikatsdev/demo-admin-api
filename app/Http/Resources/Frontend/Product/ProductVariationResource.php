<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            "current_stock"     => $this->current_stock,
            "is_default"        => $this->is_default,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "discount"          => $this->discount,
            "sell_price"        => $this->sell_price,
            "offer_percent"     => $this->offer_percent,
            "image"             => Helper::getFilePath($this->img_path),
            "attribute_value_1" => $this->whenLoaded("attributeValue1"),
            "attribute_value_2" => $this->whenLoaded("attributeValue2"),
            "attribute_value_3" => $this->whenLoaded("attributeValue3"),
        ];
    }
}
