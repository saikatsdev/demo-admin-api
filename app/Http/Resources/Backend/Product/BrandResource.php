<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "image"      => Helper::getFilePath($this->img_path),
            "name"       => $this->name,
            "slug"       => $this->slug,
            "created_at" => $this->created_at,
            "status"     => $this->status,
            "created_by" => $this->whenLoaded('createdBy'),
            "updated_by" => $this->whenLoaded('updatedBy')
        ];
    }
}
