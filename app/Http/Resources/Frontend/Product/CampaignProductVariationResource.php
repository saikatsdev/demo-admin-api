<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use App\Models\Product\ProductVariation;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductVariationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                => $this->id,
            "is_default"        => $this->is_default,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "sell_price"        => $this->offer_price,
            "discount"          => $this->discount,
            "offer_percent"     => $this->offer_percent,
            "current_stock"     => $this->getCurrentStock(),
            "image"             => Helper::getFilePath($this->img_path),
            "attribute_value_1" => $this->whenLoaded("attributeValue1"),
            "attribute_value_2" => $this->whenLoaded("attributeValue2"),
            "attribute_value_3" => $this->whenLoaded("attributeValue3"),
        ];
    }

    private function getCurrentStock()
    {
        return ProductVariation::where("product_id", $this->campaignProduct->product_id)
        ->where("attribute_value_id_1", $this->attribute_value_id_1)
        ->where("attribute_value_id_2", $this->attribute_value_id_2)
        ->where("attribute_value_id_3", $this->attribute_value_id_3)
        ->first()
        ?->current_stock;
    }
}
