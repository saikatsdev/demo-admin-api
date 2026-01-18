<?php

namespace App\Http\Resources\Frontend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "description" => $this->description,
            "image"       => Helper::getFilePath($this->img_path)
        ];
    }
}
