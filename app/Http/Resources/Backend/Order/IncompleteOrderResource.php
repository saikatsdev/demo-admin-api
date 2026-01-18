<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncompleteOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "phone_number" => $this->phone_number,
            "ip_address"   => $this->ip_address,
            "address"      => $this->address,
            "created_at"   => $this->created_at,
            "status"       => $this->whenLoaded("status"),
            "items"        => IncompleteOrderDetailResource::collection($this->whenLoaded("details"))
        ];
    }
}
