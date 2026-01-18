<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class IncompleteOrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            "product"           => $this->whenLoaded("product", function(){
                return [
                    "id"            => $this->product->id,
                    "name"          => $this->product->name,
                    "slug"          => $this->product->slug,
                    "image"         => Helper::getFilePath($this->product->img_path),
                    "buy_price"     => $this->product->buy_price,
                    "mrp"           => $this->product->mrp,
                    "offer_price"   => $this->product->offer_price,
                    "discount"      => $this->product->discount,
                    "sell_price"    => $this->product->sell_price,
                    "offer_percent" => $this->product->offer_percent,
                    "current_stock" => $this->product->current_stock
                ];
            }),
            "attribute_value_1" => $this->whenLoaded("attributeValue1"),
            "attribute_value_2" => $this->whenLoaded("attributeValue2"),
            "attribute_value_3" => $this->whenLoaded("attributeValue3"),
            "note"              => $this->note,
        ];
    }
}
