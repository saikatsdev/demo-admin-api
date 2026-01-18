<?php

namespace App\Http\Requests\Backend;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "items"                       => ["required", "array"],
            "items.*.key"                 => ["required", "string"],
            "items.*.type"                => ["required"],
            "items.*.value"               => ["nullable"],
            "items.*.setting_category_id" => ["nullable", Rule::exists("setting_categories", "id")],
        ];
    }
}
