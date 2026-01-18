<?php

namespace App\Http\Resources\Frontend\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "reply"      => $this->reply,
            "created_at" => $this->created_at,
        ];
    }
}
