<?php

namespace App\Http\Resources\Backend\CMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnPolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "description" => $this->description,
            "created_at"  => $this->created_at,
            "updated_at"  => $this->updated_at,
        ];
    }
}
