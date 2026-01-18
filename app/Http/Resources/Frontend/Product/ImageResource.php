<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"    => $this->id,
            "image" => Helper::getFilePath($this->img_path)
        ];
    }
}
