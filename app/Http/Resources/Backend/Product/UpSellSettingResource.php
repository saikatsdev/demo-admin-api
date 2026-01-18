<?php

namespace App\Http\Resources\Backend\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpSellSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "greetings"         => $this->greetings,
            "title"             => $this->title,
            "sub_title"         => $this->sub_title,
            "button_text"       => $this->button_text,
            "button_text_color" => $this->button_text_color,
            "button_bg_color"   => $this->button_bg_color,
        ];
    }
}
