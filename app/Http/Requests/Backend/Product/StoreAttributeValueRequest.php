<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "attribute_id" => ["required", Rule::exists("attributes", "id")],
            "value"        => ["required", "string"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
