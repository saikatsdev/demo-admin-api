<?php

namespace App\Http\Resources\Frontend\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"       => $this->id,
            "name"     => $this->name,
            "slug"     => $this->slug,
            "products" => ProductResource::collection($this->whenLoaded("products"))
        ];
    }
}
