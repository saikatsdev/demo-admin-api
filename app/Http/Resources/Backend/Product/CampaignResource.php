<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Backend\CMS\CampaignProductResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "title"             => $this->title,
            "slug"              => $this->slug,
            "start_date"        => $this->start_date,
            "end_date"          => $this->end_date,
            "status"            => $this->status,
            "image"             => Helper::getFilePath($this->img_path),
            "width"             => $this->width ?? 4360,
            "height"            => $this->height ?? 1826,
            "created_by"        => $this->whenLoaded('createdBy'),
            "updated_by"        => $this->whenLoaded('updated_by'),
            "deleted_by"        => $this->whenLoaded('deleted_by'),
            "campaign_products" => CampaignProductResource::collection($this->whenLoaded('campaignProducts'))
        ];
    }
}
