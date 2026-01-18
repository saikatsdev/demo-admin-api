<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlinePaymentDiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                      => $this->id,
            "payment_gateway_id"      => $this->payment_gateway_id,
            "discount_type"           => $this->discount_type,
            "discount_amount"         => $this->discount_amount,
            "minimum_cart_amount"     => $this->minimum_cart_amount,
            "maximum_discount_amount" => $this->maximum_discount_amount,
            "status"                  => $this->status,
            "created_at"              => $this->created_at,
            "created_by"              => $this->whenLoaded("createdBy"),
            "updated_by"              => $this->whenLoaded("updatedBy"),
            "payment_gateway"         => new PaymentGatewayResource($this->whenLoaded("paymentGateway")),
        ];
    }
}
