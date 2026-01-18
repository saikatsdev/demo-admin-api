<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $value = $this->type === "image" ? Helper::getFilePath($this->value) : $this->value;

        return [
            "id"          => $this->id,
            "type"        => $this->type,
            "key"         => $this->key,
            "value"       => $value,
            "instruction" => $this->instruction,
            "width"       => $this->width ?? 200,
            "height"      => $this->height ?? 200,
            "created_at"  => $this->created_at,
            "category"    => $this->whenLoaded("settingCategory"),
            "crated_by"   => $this->whenLoaded("cratedBy"),
        ];
    }
}


