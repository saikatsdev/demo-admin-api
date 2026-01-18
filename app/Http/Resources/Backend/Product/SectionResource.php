<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "title"         => $this->title,
            "status"        => $this->status,
            "link"          => $this->link,
            "position"      => $this->position,
            "is_slider"     => $this->is_slider,
            "banner_status" => $this->banner_status,
            "image"         => Helper::getFilePath($this->img_path),
            "products"      => ProductResource::collection($this->whenLoaded("products")),
            "crated_by"     => $this->whenLoaded("cratedBy"),
            "updatedBy"     => $this->whenLoaded("updatedBy")
        ];
    }
}
