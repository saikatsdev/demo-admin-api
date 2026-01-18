<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWiseCouponVariationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            "is_default"        => $this->is_default,
            "buy_price"         => $this->buy_price,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "discount"          => $this->discount,
            "offer_percent"     => $this->offer_percent,
            "image"             => Helper::getFilePath($this->img_path),
            "attribute_value_1" => $this->attributeValue1,
            "attribute_value_2" => $this->attributeValue2,
            "attribute_value_3" => $this->attributeValue3,
        ];
    }
}
