<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'order_id'   => $this->order_id,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'note'       => $this->note,
            'status'     => $this->status,
            'order'      => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
