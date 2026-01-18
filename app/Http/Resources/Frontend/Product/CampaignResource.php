<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "title"      => $this->title,
            "slug"       => $this->slug,
            "start_date" => $this->start_date,
            "end_date"   => $this->end_date,
            "status"     => $this->status,
            "image"      => Helper::getFilePath($this->img_path),
            "products"   => CampaignProductResource::collection($this->whenLoaded("campaignProducts"))
        ];
    }
}
