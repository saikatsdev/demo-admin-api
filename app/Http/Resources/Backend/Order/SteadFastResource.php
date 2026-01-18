<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class SteadFastResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "endpoint"   => $this->endpoint,
            "api_key"    => $this->api_key,
            "secret_key" => $this->secret_key,
            "created_by" => $this->whenLoaded("createdBy"),
            "updated_by" => $this->whenLoaded("updatedBy"),
        ];
    }
}
