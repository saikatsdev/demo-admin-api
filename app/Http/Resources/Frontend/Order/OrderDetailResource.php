<?php

namespace App\Http\Resources\Frontend\Order;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                   => $this->id,
            "quantity"             => $this->quantity,
            "mrp"                  => $this->mrp,
            "sell_price"           => $this->sell_price,
            "discount"             => $this->discount,
            "attribute_value_id_1" => $this->whenLoaded("attributeValue1"),
            "attribute_value_id_2" => $this->whenLoaded("attributeValue2"),
            "attribute_value_id_3" => $this->whenLoaded("attributeValue3"),
            "product"              => $this->whenLoaded("product", function () {
                return [
                    "id"    => $this->product->id,
                    "name"  => $this->product->name,
                    "image" => Helper::getFilePath($this->product->img_path)
                ];
            })
        ];
    }
}
