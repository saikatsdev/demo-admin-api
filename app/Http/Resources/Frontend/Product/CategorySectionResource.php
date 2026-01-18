<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class CategorySectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "title"      => $this->title,
            "position"   => $this->position,
            "categories" => $this->whenLoaded("categories", function () {
                return $this->categories->map(function ($category) {
                    return [
                        "id"    => $category->pivot->id,
                        "name"  => $category->name,
                        "slug"  => $category->slug,
                        "link"  => $category->pivot->link,
                        "image" => Helper::getFilePath($category->pivot->img_path)
                    ];
                });
            })
        ];
    }
}
