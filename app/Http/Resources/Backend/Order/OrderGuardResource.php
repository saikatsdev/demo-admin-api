<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderGuardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                      => $this->id,
            "quantity"                => $this->quantity,
            "duration"                => $this->duration,
            "block_message"           => $this->block_message,
            "permanent_block_message" => $this->permanent_block_message,
            "courier_block_message"   => $this->courier_block_message,
            "allow_percentage"        => $this->allow_percentage,
            "duration_type"           => $this->duration_type,
            "status"                  => $this->status,
            "created_at"              => $this->created_at,
            "created_by"              => $this->whenLoaded('createdBy')
        ];
    }
}
