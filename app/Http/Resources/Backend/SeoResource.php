<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "page"             => $this->page,
            "meta_title"       => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords"    => $this->meta_keywords,
            "status"           => $this->status,
            "img_path"         => Helper::getFilePath($this->img_path),
            "created_at"       => $this->created_at,
        ];
    }
}