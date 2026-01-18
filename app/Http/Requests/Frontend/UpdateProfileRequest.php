<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = Auth::id();

        return [
            "username"       => ["required", "string", "max:100"],
            "email"          => ["nullable", "email", "unique:users,email,$id"],
            "home_address"   => ["nullable", "string"],
            "office_address" => ["nullable", "string"],
            "dob"            => ["nullable", "date"]
        ];
    }
}
