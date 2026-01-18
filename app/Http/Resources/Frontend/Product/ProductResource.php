<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $variations = $this->whenLoaded('variations');

        // Group attributes by name
        $groupedAttributes = $this->groupAttributes($variations);

        $variationMinPrice = $variations->min('sell_price');
        $variationMaxPrice = $variations->max('sell_price');

        $reviews = $this->relationLoaded('productReviews') ? $this->productReviews : collect();

        $ratingCount = $reviews->count();

        $avgRating = $ratingCount ? round($reviews->avg('rating'), 1) : 0;

        $distribution = collect([5, 4, 3, 2, 1])->mapWithKeys(function ($star) use ($reviews) {
            return [
                (string) $star => $reviews->where('rating', $star)->count()
            ];
        });

        return [
            "id"                => $this->id,
            "name"              => $this->name,
            "slug"              => $this->slug,
            "offer_price"       => $this->offer_price,
            "mrp"               => $this->mrp,
            "discount"          => $this->discount,
            "offer_percent"     => $this->offer_percent,
            "sell_price"        => $this->sell_price,
            "current_stock"     => $this->current_stock,
            "minimum_qty"       => $this->minimum_qty,
            "alert_qty"         => $this->alert_qty,
            "sku"               => $this->sku,
            "free_shipping"     => $this->free_shipping,
            "image"             => Helper::getFilePath($this->img_path),
            "description"       => $this->description,
            "short_description" => $this->short_description,
            "video_url"         => $this->video_url,
            "brand"             => $this->whenLoaded("brand"),
            "category"          => $this->whenLoaded("category"),
            "sub_category"      => $this->whenLoaded("subCategory"),
            "sub_sub_category"  => $this->whenLoaded("subSubCategory"),
            "product_type"      => $this->whenLoaded("productType"),
            "total_sell"        => $this->total_sell_qty,
            "rating"            => [
                "avgRating"    => $avgRating,
                "ratingCount"  => $ratingCount,
                "distribution" => $distribution,
                "reviews"      => ProductReviewResource::collection($this->whenLoaded('productReviews')),
            ],
            'variation_price_range'   => [
                "min_price" => $variationMinPrice,
                "max_price" => $variationMaxPrice,
            ],
            "down_sell"     => DownSellResource::make($this->whenLoaded("downSells", fn() => $this->downSells->first())),
            "images"        => ImageResource::collection($this->whenLoaded("images")),
            "review_images" => ReviewImageResource::collection($this->whenLoaded('reviewImages')),
            "variations"    => [
                "data"       => ProductVariationResource::collection($this->whenLoaded("variations")),
                "attributes" => $groupedAttributes,
            ],
            "up_sell_products" => UpSellProductResource::collection($this->whenLoaded("upSellProducts")),
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
                        'attribute_value_id' => $attributeValue->id,
                        'attribute_value'    => $attributeValue->value,
                        'attribute_id'       => $attributeValue->attribute_id
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
