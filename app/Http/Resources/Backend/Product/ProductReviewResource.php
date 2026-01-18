<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "product_id" => $this->product_id,
            "name"       => $this->name,
            "email"      => $this->email ?? "N/A",
            "title"      => $this->title,
            "rating"     => $this->rating,
            "review"     => $this->review,
            "status"     => $this->status,
            "img_path"   => Helper::getFilePath($this->image),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "review_reply" => $this->whenLoaded('reply'),
            "product"    => $this->whenLoaded('product', function () {
                return [
                    "id"       => $this->product->id,
                    "name"     => $this->product->name,
                    "slug"     => $this->product->slug,
                    "img_path" => Helper::getFilePath($this->product->img_path),
                ];
            }),

        ];
    }
}
