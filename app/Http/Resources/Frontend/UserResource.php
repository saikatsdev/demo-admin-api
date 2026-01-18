<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"             => $this->id,
            "username"       => $this->username,
            "email"          => $this->email,
            "phone_number"   => $this->phone_number,
            "home_address"   => $this->home_address,
            "office_address" => $this->office_address,
            "dob"            => $this->dob,
            // "verification_otp" => $this->verification_otp,
            // "is_verified"      => $this->is_verified,
            "img_path"     => Helper::getFilePath($this->img_path),
            "bonus_points" => $this->bonus_points,
        ];
    }
}
