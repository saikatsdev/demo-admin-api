<?php

namespace App\Http\Resources\Frontend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "device_type" => $this->device_type,
            "type"        => $this->type,
            "link"        => $this->link,
            "image"       => Helper::getFilePath($this->img_path)
        ];
    }
}
