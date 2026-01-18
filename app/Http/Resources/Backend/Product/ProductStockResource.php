<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                 => $this->id,
            "name"               => $this->name,
            "buy_price"          => $this->buy_price,
            "mrp"                => $this->mrp,
            "offer_price"        => $this->offer_price,
            "discount"           => $this->discount,
            "sell_price"         => $this->sell_price,
            "offer_percent"      => $this->offer_percent,
            "total_purchase_qty" => $this->total_purchase_qty,
            "total_sell_qty"     => $this->total_sell_qty,
            "current_stock"      => $this->current_stock,
            "minimum_order_qty"  => $this->minimum_qty,
            "alert_qty"          => $this->alert_qty,
            "status"             => $this->status,
            "type"               => $this->type,
            "image"              => Helper::getFilePath($this->img_path),
        ];
    }
}
