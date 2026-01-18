<?php

namespace App\Http\Resources\Backend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "title"      => $this->title,
            "type"       => $this->type,
            "status"     => $this->status,
            "height"     => $this->height,
            "status"     => $this->status,
            "image"      => Helper::getFilePath($this->img_path),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => $this->whenLoaded('createdBy'),
            "updated_by" => $this->whenLoaded('updatedBy'),
        ];
    }
}
