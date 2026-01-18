<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "image"       => Helper::getFilePath($this->img_path),
            "name"        => $this->name,
            "slug"        => $this->slug,
            "category"    => CategoryResource::make($this->whenLoaded("category")),
            "created_at"  => $this->created_at,
            "updated_at"  => $this->updated_at,
            "created_by"  => $this->whenLoaded('createdBy'),
            "updated_by"  => $this->whenLoaded('updatedBy'),
            "status"      => $this->status,
        ];
    }
}
