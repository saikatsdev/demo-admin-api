<?php

namespace App\Http\Resources\Backend\CMS;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'campaign_id'   => $this->campaign_id,
            'product_id'    => $this->product_id,
            'mrp'           => $this->mrp,
            'offer_price'   => $this->offer_price,
            'discount'      => $this->discount,
            'discount_type' => $this->discount_type,
            'offer_percent' => $this->offer_percent,

            'product' => [
                'id'            => $this->product->id,
                'name'          => $this->product->name,
                'slug'          => $this->product->slug,
                'current_stock' => $this->product->current_stock,
                'brand'         => $this->product->brand,
                'category'      => $this->product->category,

                'img_path' => Helper::getFilePath($this->product->img_path),
                'images'   => $this->product->images,
            ],
        ];
    }
}
