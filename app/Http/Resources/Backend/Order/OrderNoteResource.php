<?php

namespace App\Http\Resources\Backend\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderNoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "note"       => $this->note,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => $this->whenLoaded("createdBy"),
            "updated_by" => $this->whenLoaded("updatedBy"),
        ];
    }
}
