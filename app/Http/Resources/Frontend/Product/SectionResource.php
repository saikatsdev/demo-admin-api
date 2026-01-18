<?php

namespace App\Http\Resources\Frontend\Product;

use App\Http\Resources\Frontend\CMS\BannerCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"        => $this->id,
            "title"     => $this->title,
            "link"      => $this->link,
            "position"  => $this->position,
            "is_slider" => $this->is_slider,
            "products"  => new ProductCollection($this->whenLoaded("products")),
            "banners"   => new BannerCollection($this->whenLoaded("banners"))
        ];
    }
}
