<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "slug"       => $this->slug,
            "status"     => $this->status,
            "image"      => Helper::getFilePath($this->img_path),
            "is_default" => $this->is_default,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => $this->whenLoaded('createdBy'),
            "updated_by" => $this->whenLoaded('updatedBy')
        ];
    }
}
