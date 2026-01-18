<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    public function toArray($request)
    {
        $variations = $this->whenLoaded("campaignProductVariations");

        // Group attributes by name
        $groupedAttributes = $this->groupAttributes($variations);

        // get minimum and maximum variation prices
        $variationMinPrice = $variations->min("offer_price");
        $variationMaxPrice = $variations->max("offer_price");

        return [
            "campaign_product_id"   => $this->id,
            "discount_type"         => $this->discount_type,
            "mrp"                   => $this->mrp,
            "offer_price"           => $this->offer_price,
            "sell_price"            => $this->offer_price,
            "offer_percent"         => $this->offer_percent,
            "discount"              => $this->discount,
            "discount_value"        => $this->discount_value,
            "id"                    => @$this->product->id,                              // this product is campaign product Id
            "name"                  => @$this->product->name,
            "slug"                  => @$this->product->slug,
            "free_shipping"         => @$this->product->free_shipping,
            "current_stock"         => @$this->product->current_stock,
            "sku"                   => @$this->product->sku,
            "short_description"     => @$this->product->short_description,
            "description"           => @$this->product->description,
            "image"                 => Helper::getFilePath(@$this->product->img_path),
            "variation_price_range" => [
                "min_price" => $variationMinPrice,
                "max_price" => $variationMaxPrice,
            ],
            "category" => $this->when($this->product && $this->product->relationLoaded("category"), function () {
                return [
                    "id"   => $this->product->category->id,
                    "name" => $this->product->category->name,
                    "slug" => $this->product->category->slug,
                ];
            }),
            "brand" => $this->when($this->product && $this->product->relationLoaded("brand"), function () {
                return [
                    "id"   => $this->product->brand->id,
                    "name" => $this->product->brand->name,
                    "slug" => $this->product->brand->slug,
                ];
            }),
            "variations" => [
                "data"       => CampaignProductVariationResource::collection($this->whenLoaded("campaignProductVariations")),
                "attributes" => $groupedAttributes,
            ]
        ];
    }

    private function groupAttributes($variations)
    {
        $attributes = [];

        foreach ($variations as $variation) {
            // Loop through possible attribute values
            for ($i = 1; $i <= 3; $i++) {
                $attributeValue = $variation->{"attributeValue$i"} ?? null;
                if ($attributeValue) {
                    $attributeName = @$attributeValue->attribute->name;

                    // Check if the attribute name exists in the array
                    if (!isset($attributes[$attributeName])) {
                        $attributes[$attributeName] = [];
                    }

                    // Add unique attributes to the array
                    $attributes[$attributeName][$attributeValue->id] = [
                        "attribute_value_id" => $attributeValue->id,
                        "attribute_value"    => $attributeValue->value,
                        "attribute_id"       => $attributeValue->attribute_id
                    ];
                }
            }
        }

        // Flatten the arrays to ensure that each attribute type is properly formatted
        foreach ($attributes as &$attributeGroup) {
            $attributeGroup = array_values($attributeGroup);
        }

        return $attributes;
    }
}
