<?php

namespace App\Http\Resources\Frontend\Product;

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
            "email"      => $this->email,
            "title"      => $this->title,
            "rating"     => $this->rating,
            "review"     => $this->review,
            "img_path"   => Helper::getFilePath($this->image),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "reply"      => ProductReviewReplyResource::make($this->whenLoaded('reply')),
        ];
    }
}
