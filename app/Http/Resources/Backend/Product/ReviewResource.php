<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"      => $this->id,
            "rate"    => $this->rate,
            "comment" => $this->comment,
            "status"  => $this->status,
            "user"    => $this->whenLoaded("user"),
            "product" => $this->whenLoaded("product", function() {
                return [
                    "id"    => $this->product->id,
                    "name"  => $this->product->name,
                    "slug"  => $this->product->slug,
                    "image" => Helper::getFilePath($this->product->img_path),
                ];
            }),
            "crated_by"  => $this->whenLoaded("createdBy"),
            "update_by"  => $this->whenLoaded("updatedBy"),
        ];
    }
}
