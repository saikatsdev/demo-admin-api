<?php

namespace App\Http\Resources\Frontend\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helper;

class OrderTrackResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "orderId"     => $this->invoice_number,
            "orderDate"   => $this->created_at?->format('D M d Y'),
            "finalStatus" => strtoupper(optional($this->currentStatus)->slug),
            "currency"    => "BDT",

            "customer" => [
                "name"    => $this->customer_name,
                "mobile"  => $this->phone_number,
                "address" => $this->address_details,
            ],

            "items" => $this->details->map(function ($detail) {
                return [
                    "sku"   => optional($detail->product)->sku,
                    "name"  => $detail->product_name ?? optional($detail->product)->name,
                    "qty"   => (int) $detail->quantity,
                    "price" => (float) $detail->sell_price,
                ];
            }),

            "timeline" => $this->statuses->map(function ($status) {
                return [
                    "status" => strtoupper($status->slug),
                    "at"     => optional($status->pivot->created_at)?->toIso8601String(),
                    "note"   => $status->name,
                ];
            }),

            "cancelReason" => optional($this->cancelReason)->name,
        ];
    }
}
