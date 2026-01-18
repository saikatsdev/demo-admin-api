<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class IncompleteOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "phone_number" => ["required", "string"],
            "items"        => ["nullable", "array"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
