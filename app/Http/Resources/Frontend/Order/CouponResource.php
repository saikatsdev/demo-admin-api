<?php

namespace App\Http\Resources\Frontend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"              => $this["coupon_id"],
            "name"            => $this["name"],
            "status"          => $this["status"],
            "code"            => $this["code"],
            "discount_type"   => $this["discount_type"],
            "discount_amount" => $this["discount_amount"]
        ];
    }
}
