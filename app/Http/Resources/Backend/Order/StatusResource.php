<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                      => $this->id,
            "name"                    => $this->name,
            "slug"                    => $this->slug,
            "bg_color"                => $this->bg_color,
            "text_color"              => $this->text_color,
            "status"                  => $this->status,
            "courier_pending_count"   => $this->courier_pending_count,
            "courier_received_count"  => $this->courier_received_count,
            "orders_count"            => $this->orders_count,
            "total_amount"            => $this->total_amount ?? 0,
            "courier_pending_amount"  => $this->courier_pending_amount ?? 0,
            "courier_received_amount" => $this->courier_received_amount ?? 0,
            "position"                => $this->position,
            "created_at"              => $this->created_at,
            "created_by"              => $this->whenLoaded('createdBy')
        ];
    }
}
