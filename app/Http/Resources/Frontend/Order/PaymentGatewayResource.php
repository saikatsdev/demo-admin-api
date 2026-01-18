<?php

namespace App\Http\Resources\Frontend\Order;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "slug"         => $this->slug,
            "phone_number" => $this->phone_number,
            "image"        => Helper::getFilePath($this->img_path)
        ];
    }
}
