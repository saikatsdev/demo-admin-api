<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "value" => ["required", "string"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
