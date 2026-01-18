<?php

namespace App\Http\Resources\Backend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "section_id"  => $this->section_id,
            "title"       => $this->title,
            "device_type" => $this->device_type,
            "type"        => $this->type,
            "link"        => $this->link,
            "description" => $this->description,
            "status"      => $this->status,
            "width"       => $this->width,
            "height"      => $this->height,
            "image"       => Helper::getFilePath($this->img_path),
            "created_at"  => $this->created_at,
            "updated_at"  => $this->updated_at,
            "created_by"  => $this->whenLoaded('createdBy'),
            "updated_by"  => $this->whenLoaded('updatedBy')
        ];
    }
}
