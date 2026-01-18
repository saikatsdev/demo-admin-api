<?php

namespace App\Http\Resources\Frontend\BlogPost;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "slug"        => $this->slug,
            "description" => $this->description,
            "image"       => Helper::getFilePath($this->img_path),
            "created_at"  => $this->created_at,
            "tags"        => $this->whenLoaded('tags', function() {
                return $this->tags->map(function($tag) {
                    return [
                        "id"   => $tag->id,
                        "name" => $tag->name
                    ];
                });
            }),
        ];
    }
}
