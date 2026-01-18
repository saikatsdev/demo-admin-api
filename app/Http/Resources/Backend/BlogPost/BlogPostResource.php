<?php

namespace App\Http\Resources\Backend\BlogPost;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "image"            => Helper::getFilePath($this->img_path),
            "title"            => $this->title,
            "slug"             => $this->slug,
            "category"         => $this->whenLoaded("category"),
            "category_id"      => $this->category_id,
            "status"           => $this->status,
            "description"      => $this->description,
            "meta_title"       => $this->meta_title,
            "meta_tag"         => $this->meta_tag,
            "meta_description" => $this->meta_description,
            "created_at"       => $this->created_at,
            "tags"             => $this->whenLoaded("tags", function () {
                return $this->tags->map(function($tag) {
                    return [
                        "id"   => $tag->id,
                        "name" => $tag->name
                    ];
                });
            }),
            "created_by"       => $this->whenLoaded("createdBy"),
            "created_by"       => $this->whenLoaded("updatedBy"),
        ];
    }
}
