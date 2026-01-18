<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "name"        => $this->name,
            "slug"        => $this->slug,
            "order_range" => $this->order_range,
            "status"      => $this->status,
            "created_at"  => $this->created_at,
            "updated_at"  => $this->updated_at,
            "created_by"  => $this->whenLoaded('createdBy'),
            "updated_by"  => $this->whenLoaded('updatedBy')
        ];
    }
}
