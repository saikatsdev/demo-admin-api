<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWiseCouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"            => $this->id,
            "name"          => $this->name,
            "coupon_type"   => $this->coupon_type,
            "coupon_amount" => $this->coupon_amount,
            "mrp"           => $this->mrp,
            "offer_price"   => $this->offer_price,
            "discount"      => $this->discount,
            "offer_percent" => $this->offer_percent,
            "started_at"    => $this->started_at,
            "ended_at"      => $this->ended_at,
            "image"         => Helper::getFilePath($this->img_path),
            "variations"    => ProductWiseCouponVariationResource::collection($this->whenLoaded("variations")),
        ];
    }
}
