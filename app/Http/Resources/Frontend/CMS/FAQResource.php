<?php

namespace App\Http\Resources\Frontend\CMS;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"       => $this->id,
            "question" => $this->question,
            "answer"   => $this->answer,
        ];
    }
}
