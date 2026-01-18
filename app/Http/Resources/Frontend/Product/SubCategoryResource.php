<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"                 => $this->id,
            "name"               => $this->name,
            "slug"               => $this->slug,
            "products_count"     => $this->products_count,
            "image"              => Helper::getFilePath($this->img_path),
            "category"           => CategoryResource::make($this->whenLoaded("category")),
            "sub_sub_categories" => SubSubCategoryResource::collection($this->whenLoaded("subSubCategories"))
        ];
    }
}
