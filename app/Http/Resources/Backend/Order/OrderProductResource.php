<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'sell_price' => $this->sell_price,
            'img'        => $this->img_path ? Helper::getFilePath($this->img_path) : null,
        ];
    }
}
