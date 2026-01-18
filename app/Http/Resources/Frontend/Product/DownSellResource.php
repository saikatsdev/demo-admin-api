<?php

namespace App\Http\Resources\Frontend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownSellResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'type'        => $this->type,
            'amount'      => $this->amount,
            'duration'    => $this->duration,
            'started_at'  => $this->started_at,
            'ended_at'    => $this->ended_at,
            'description' => $this->description,
            'img_path'    => Helper::getFilePath($this->img_path),
            'is_all'      => $this->is_all,
        ];
    }
}
