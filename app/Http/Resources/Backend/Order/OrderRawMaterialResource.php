<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRawMaterialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "slug"       => $this->slug,
            "quantity"   => $this->pivot->quantity,
            "unit_cost"  => $this->pivot->unit_cost,
            "total"      => $this->pivot->total,
            "created_at" => $this->pivot->created_at,
        ];
    }
}
