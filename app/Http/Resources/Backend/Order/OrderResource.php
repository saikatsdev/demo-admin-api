<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                 => $this->id,
            "invoice_number"     => $this->invoice_number,
            "coupon_value"       => $this->coupon_value,
            "delivery_charge"    => $this->delivery_charge,
            "buy_price"          => $this->buy_price,
            "mrp"                => $this->mrp,
            "sell_price"         => $this->sell_price,
            "discount"           => $this->discount,
            "special_discount"   => $this->special_discount,
            "net_order_price"    => $this->net_order_price,
            "advance_payment"    => $this->advance_payment,
            "payable_price"      => $this->payable_price,
            "courier_payable"    => $this->courier_payable,
            "paid_status"        => $this->paid_status,
            "is_duplicate"       => $this->is_duplicate,
            "is_invoice_printed" => $this->is_invoice_printed,
            "is_incomplete"      => $this->is_incomplete,
            "phone_number"       => $this->phone_number,
            "customer_name"      => $this->customer_name,
            "address_details"    => $this->address_details,
            "pickup_store_id"    => $this->pickup_store_id,
            "delivery_type"      => $this->delivery_type,
            "courier_area_id"    => $this->courier_area_id,
            "delivery_area"      => $this->delivery_area,
            "item_weight"        => $this->item_weight,
            "consignment_id"     => $this->consignment_id,
            "tracking_code"      => $this->tracking_code,
            "note"               => $this->note,
            "courier_status"     => $this->courier_status,
            "created_at"         => $this->created_at,
            "deleted_at"         => $this->deleted_at,
            "prepared_at"        => $this->prepared_at,
            "locked_by_id"       => $this->locked_by_id,
            "callback_response"  => collect($this->callback_response)->sortByDesc('updated_at')->values()->all(),
            "prepared_by"        => $this->whenLoaded("preparedBy"),
            "created_by"         => $this->whenLoaded("createdBy"),
            "updated_by"         => $this->whenLoaded("updatedBy"),
            "delivery_gateway"   => $this->whenLoaded("deliveryGateway"),
            "payment_gateway"    => $this->whenLoaded("paymentGateway"),
            "coupon"             => $this->whenLoaded("coupon"),
            "order_from"         => $this->whenLoaded("orderFrom"),
            "customer_type"      => $this->whenLoaded("customerType"),
            "courier"            => $this->whenLoaded("courier"),
            "district"           => $this->whenLoaded("district"),
            "assign_user"        => $this->whenLoaded("assignUser"),
            "cancel_reason"      => $this->whenLoaded("cancelReason"),
            "current_status"     => $this->whenLoaded("currentStatus"),
            "statuses"           => StatusResource::collection($this->whenLoaded("statuses")),
            "details"            => OrderDetailResource::collection($this->whenLoaded("details")),
            "raw_materials"      => OrderRawMaterialResource::collection($this->whenLoaded("rawMaterials")),
            "notes"              => OrderNoteResource::collection($this->whenLoaded("notes")),
            "transaction"        => TransactionResource::make($this->whenLoaded("transaction"))
        ];
    }
}
