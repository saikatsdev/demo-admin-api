<?php

namespace App\Http\Resources\Frontend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"     => $this->id,
            "title"  => $this->title,
            "type"   => $this->type,
            "image"  => Helper::getFilePath($this->img_path),
        ];
    }
}
