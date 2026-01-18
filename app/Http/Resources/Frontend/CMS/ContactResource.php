<?php

namespace App\Http\Resources\Frontend\CMS;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"          => $this->id,
            "name"        => $this->name,
            "phone"       => $this->phone,
            "email"       => $this->email,
            "description" => $this->description
        ];
    }
}
