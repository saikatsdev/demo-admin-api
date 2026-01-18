<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                      => $this->id,
            "payment_id"              => $this->payment_id,
            "payment_gateway_trx_id"  => $this->payment_gateway_trx_id,
            "payment_send_number"     => $this->payment_send_number,
            "payment_received_number" => $this->payment_received_number,
            "order"                   => $this->whenLoaded("order")
        ];
    }
}
