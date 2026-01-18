<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $variations = $this->whenLoaded("variations");

        return [
            "id"                    => $this->id,
            "name"                  => $this->name,
            "sku"                   => $this->sku,
            "slug"                  => $this->slug,
            "total_purchase_qty"    => $this->total_purchase_qty,
            "total_sell_qty"        => $this->total_sell_qty,
            "current_stock"         => $this->current_stock,
            "buy_price"             => $this->buy_price,
            "mrp"                   => $this->mrp,
            "offer_price"           => $this->offer_price,
            "discount"              => $this->discount,
            "offer_percent"         => $this->offer_percent,
            "sell_price"            => $this->sell_price,
            "status"                => $this->status,
            "minimum_qty"           => $this->minimum_qty,
            "alert_qty"             => $this->alert_qty,
            "free_shipping"         => $this->free_shipping,
            "description"           => $this->description,
            "short_description"     => $this->short_description,
            "video_url"             => $this->video_url,
            "meta_title"            => $this->meta_title,
            "meta_keywords"         => $this->meta_keywords,
            "meta_description"      => $this->meta_description,
            "image"                 => Helper::getFilePath($this->img_path),
            "variation_price_range" => [
                "min_price" => $variations->min("sell_price"),
                "max_price" => $variations->max("sell_price"),
            ],
            "category"            => $this->whenLoaded("category"),
            "brand"               => $this->whenLoaded("brand"),
            "sub_category"        => $this->whenLoaded("subCategory"),
            "sub_sub_category"    => $this->whenLoaded("subSubCategory"),
            "product_type"        => $this->whenLoaded("productType"),
            "images"              => ImageResource::collection($this->whenLoaded("images")),
            "review_images"       => ReviewImageResource::collection($this->whenLoaded("reviewImages")),
            "reviews"             => ReviewResource::collection($this->whenLoaded("reviews")),
            "variations"          => ProductVariationResource::collection($variations),
            "current_stock_range" => [
                "total_purchase_qty"  => $variations->sum("total_purchase_qty"),
                "total_sell_qty"      => $variations->sum("total_sell_qty"),
                "total_current_stock" => $variations->sum("current_stock"),
            ],
            "up_sell_products" => ProductResource::collection($this->whenLoaded("upSellProducts")),
        ];
    }
}
