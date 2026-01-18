<?php

namespace App\Http\Resources\Frontend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreeDeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"       => $this->id,
            "quantity" => $this->quantity,
            "price"    => $this->price
        ];
    }
}
