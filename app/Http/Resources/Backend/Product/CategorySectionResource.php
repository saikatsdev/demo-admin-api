<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategorySectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "title"      => $this->title,
            "status"     => $this->status,
            "position"   => $this->position,
            "created_at" => $this->created_at,
            "categories" => $this->whenLoaded("categories", function () {
                return $this->categories->map(function ($category) {
                    return [
                        "id"          => $category->pivot->id,
                        "category_id" => $category->id,
                        "name"        => $category->name,
                        "slug"        => $category->slug,
                        "link"        => $category->pivot->link,
                        "image"       => Helper::getFilePath($category->pivot->img_path)
                    ];
                });
            }),
            "created_by" => $this->whenLoaded("createdBy"),
            "updated_by" => $this->whenLoaded("updatedBy"),
        ];
    }
}
