<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use App\Http\Resources\Backend\Product\DownSellProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownSellResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "type"        => $this->type,
            "amount"      => $this->amount,
            "duration"    => $this->duration,
            "started_at"  => $this->started_at,
            "ended_at"    => $this->ended_at,
            "status"      => $this->status,
            "image"       => Helper::getFilePath($this->img_path),
            "description" => $this->description,
            "created_at"  => $this->created_at,
            "products"    => DownSellProductResource::collection($this->whenLoaded("products")),
            "created_by"  => $this->whenLoaded('createdBy'),
            "updated_by"  => $this->whenLoaded('updatedBy')
        ];
    }
}
