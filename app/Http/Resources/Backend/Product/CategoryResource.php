<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "image"        => Helper::getFilePath($this->img_path),
            "banner_image" => Helper::getFilePath($this->banner_img),
            "name"         => $this->name,
            "slug"         => $this->slug,
            "status"       => $this->status,
            "created_at"   => $this->created_at,
            "created_by"   => $this->whenLoaded('createdBy'),
            "updated_by"   => $this->whenLoaded('updatedBy'),
        ];
    }
}
