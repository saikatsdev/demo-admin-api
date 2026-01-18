<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Backend\Order\StatusResource;

class LatestOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'customer_name'   => $this->customer_name,
            'phone_number'    => $this->phone_number,
            'net_order_price' => $this->net_order_price,
            'created_at'      => $this->created_at->format('Y-m-d H:i:s'),
            "current_status"     => $this->whenLoaded("currentStatus"),
            "statuses"           => StatusResource::collection($this->whenLoaded("statuses")),
            "assign_user"        => $this->whenLoaded("assignUser"),
            'details' => $this->details->map(function ($detail) {
                return [
                    'id'         => $detail->id,
                    'order_id'   => $detail->order_id,
                    'product_id' => $detail->product_id,
                    'product'    => $detail->product ? [
                        'id'       => $detail->product->id,
                        'name'     => $detail->product->name,
                        'slug'     => $detail->product->slug,
                        'img_path' => Helper::getFilePath($detail->product->img_path),
                    ] : null,
                ];
            }),
        ];
    }
}
