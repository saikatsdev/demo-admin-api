<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            "quantity"          => $this->quantity,
            "buy_price"         => $this->buy_price,
            "mrp"               => $this->mrp,
            "discount"          => $this->discount,
            "sell_price"        => $this->sell_price,
            "attribute_value_1" => $this->whenLoaded("attributeValue1"),
            "attribute_value_2" => $this->whenLoaded("attributeValue2"),
            "attribute_value_3" => $this->whenLoaded("attributeValue3"),
            "product"           => $this->whenLoaded("product", function () {
                return [
                    "id"            => $this->product->id,
                    "name"          => $this->product->name,
                    "sku"           => $this->product->sku,
                    "current_stock" => $this->product->current_stock,
                    "img_path"      => Helper::getFilePath($this->product->img_path),
                    "variations"    => $this->product->variations
                        ->filter(function ($variation) {
                            return $variation->attribute_value_id_1 === $this->attribute_value_id_1
                            &&     $variation->attribute_value_id_2 === $this->attribute_value_id_2
                            &&     $variation->attribute_value_id_3 === $this->attribute_value_id_3;
                        })
                        ->map(function ($variation) {
                            return [
                                "id"            => $variation->id,
                                "current_stock" => $variation->current_stock,
                                "img_path"      => Helper::getFilePath($variation->img_path),
                            ];
                        }),
                ];
            }),
        ];
    }
}
