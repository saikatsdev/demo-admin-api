<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class UpSellProductResource extends JsonResource
{
    public function toArray($request)
    {
        $variations = $this->whenLoaded('variations');

        // get minimum and maximum variation prices
        $variationMinPrice = $variations->min('sell_price');
        $variationMaxPrice = $variations->max('sell_price');

        return [

            "id"                => $this->id,
            "name"              => $this->name,
            "slug"              => $this->slug,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "discount"          => $this->discount,
            "offer_percent"     => $this->offer_percent,
            "sell_price"        => $this->sell_price,
            "current_stock"     => $this->current_stock,
            "minimum_qty"       => $this->minimum_qty,
            "alert_qty"         => $this->alert_qty,
            "type"              => $this->type,
            "sku"               => $this->sku,
            "free_shipping"     => $this->free_shipping,
            "image"             => Helper::getFilePath($this->img_path),
            "video_url"         => $this->video_url,
            "brand"             => $this->brand ?? null,
            "category"          => $this->category ?? null,
            "sub_category"      => $this->subCategory ?? null,
            'variation_price_range'   => [
                "min_price" => $variationMinPrice,
                "max_price" => $variationMaxPrice,
            ],
            "images"           => ImageResource::collection($this->whenLoaded("images")),
            "review_images"    => ReviewImageResource::collection($this->whenLoaded('reviewImages')),
            "variations"       => ProductVariationResource::collection($this->whenLoaded("variations")),
            "up_sell_products" => UpSellProductResource::collection($this->whenLoaded("upSellProducts")),
            "coupon"           => ProductWiseCouponResource::make($this->whenLoaded("coupon"))
        ];
    }
}
