<?php

namespace App\Http\Resources\Backend\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "slug"       => $this->slug,
            "created_at" => $this->created_at,
            "status"     => $this->status,
            "values"     => $this->whenLoaded('attributeValues'),
            "created_by" => $this->whenLoaded('createdBy'),
            "updated_by" => $this->whenLoaded('updatedBy'),
        ];
    }
}
