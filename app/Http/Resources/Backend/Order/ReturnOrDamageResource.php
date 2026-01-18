<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrDamageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "buy_price"  => $this->buy_price,
            "mrp"        => $this->mrp,
            "discount"   => $this->discount,
            "sell_price" => $this->sell_price,
            "type"       => $this->type,
            "reason"     => $this->reason,
            "created_at" => $this->created_at,
            "details"    => OrderDetailResource::collection($this->whenLoaded('details')),
            "status"     => StatusResource::make($this->whenLoaded("status")),
            "order"      => OrderResource::make($this->whenLoaded("order")),
            "created_by" => $this->whenLoaded("createdBy"),
            "updated_by" => $this->whenLoaded("updatedBy")
        ];
    }
}
