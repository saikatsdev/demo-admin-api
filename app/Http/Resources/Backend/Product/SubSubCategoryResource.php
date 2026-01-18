<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubSubCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"              => $this->id,
            "image"           => Helper::getFilePath($this->img_path),
            "name"            => $this->name,
            "slug"            => $this->slug,
            "sub_category"    => $this->whenLoaded("subCategory"),
            "sub_category_id" => $this->sub_category_id,
            "category"        => $this->whenLoaded("subCategory.category"),
            "created_at"      => $this->created_at,
            "updated_at"      => $this->updated_at,
            "created_by"      => $this->whenLoaded('createdBy'),
            "updated_by"      => $this->whenLoaded('updatedBy'),
            "status"          => $this->status,
        ];
    }
}
