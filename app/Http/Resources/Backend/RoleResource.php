<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "display_name" => $this->display_name,
            "name"         => $this->name,
            "description"  => $this->description,
            "created_at"   => $this->created_at,
            "permissions"  => $this->whenLoaded('permissions', function() {
                return $this->permissions->map(function ($permission) {
                    return [
                        "id"           => $permission->id,
                        "name"         => $permission->name,
                        "display_name" => $permission->display_name
                    ];
                });
            }),
            "created_by"   => $this->whenLoaded('createdBy')
        ];
    }
}
