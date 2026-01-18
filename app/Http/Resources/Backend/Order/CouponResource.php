<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"              => $this->id,
            "name"            => $this->name,
            "code"            => $this->code,
            "discount_type"   => $this->discount_type,
            "discount_amount" => $this->discount_amount,
            "min_cart_amount" => $this->min_cart_amount,
            "started_at"      => $this->started_at,
            "ended_at"        => $this->ended_at,
            "description"     => $this->description,
            "status"          => $this->status,
            "created_at"      => $this->created_at,
            "updated_at"      => $this->updated_at,
            "created_by"      => $this->whenLoaded("createdBy"),
            "updated_by"      => $this->whenLoaded("updatedBy"),
        ];
    }
}
