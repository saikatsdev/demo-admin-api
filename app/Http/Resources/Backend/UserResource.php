<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"              => $this->id,
            "username"        => $this->username,
            "phone_number"    => $this->phone_number,
            "email"           => $this->email,
            "status"          => $this->status,
            "user_type"       => $this->user_type,
            "active"          => $this->active,
            "salary"          => $this->salary,
            "staff_login_otp" => $this->staff_login_otp,
            "is_verified"     => $this->is_verified,
            "login_at"        => Helper::diffForHuman($this->login_at),
            "logout_at"       => Helper::diffForHuman($this->logout_at),
            "image"           => Helper::getFilePath($this->img_path),
            "category"        => $this->whenLoaded("userCategory"),
            "manager"         => $this->whenLoaded("manager"),
            "roles"           => RoleResource::collection($this->whenLoaded("roles")),
        ];
    }
}
