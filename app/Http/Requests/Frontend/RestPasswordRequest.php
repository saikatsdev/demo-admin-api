<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class RestPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "phone_number"     => ["required", "regex:/^[0-9]+$/", "digits:11"],
            "password"         => ["required", "confirmed"],
            "verification_otp" => ["required"]
        ];
    }
}
