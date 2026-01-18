<?php

namespace App\Http\Resources\Frontend\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "slug"       => $this->slug,
            "bg_color"   => $this->bg_color,
            "text_color" => $this->text_color,
        ];
    }
}
