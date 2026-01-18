<?php

namespace App\Http\Resources\Frontend\BlogPost;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "slug"         => $this->slug,
            "image"        => Helper::getFilePath($this->img_path),
        ];
    }
}
