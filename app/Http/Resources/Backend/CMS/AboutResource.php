<?php

namespace App\Http\Resources\Backend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "image"       => Helper::getFilePath($this->img_path),
            "title"       => $this->title,
            "description" => $this->description,
            "created_by"  => $this->whenLoaded('createdBy'),
            "updated_by"  => $this->whenLoaded('updatedBy')
        ];
    }
}
