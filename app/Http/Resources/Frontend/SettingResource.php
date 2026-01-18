<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $value = $this->type === "image" ? Helper::getFilePath($this->value) : $this->value;

        return [
            "id"    => $this->id,
            "key"   => $this->key,
            "type"  => $this->type,
            "value" => $value
        ];
    }
}
