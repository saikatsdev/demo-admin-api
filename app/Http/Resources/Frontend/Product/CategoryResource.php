<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"             => $this->id,
            "name"           => $this->name,
            "slug"           => $this->slug,
            "products_count" => $this->products_count,
            "image"          => Helper::getFilePath($this->img_path),
            "sub_categories" => SubCategoryResource::collection($this->whenLoaded('subCategories')),
        ];
    }
}
