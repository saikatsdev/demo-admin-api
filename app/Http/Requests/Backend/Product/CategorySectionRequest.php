<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategorySectionRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->id) {
            $image = ["nullable"];
        } else {
            $image = ["required"];
        }

        return [
            "title"               => ["required", "string"],
            "status"              => ["required", Rule::in(StatusEnum::activeStatus())],
            "position"            => ["required", "integer"],
            "items"               => ["required", "array"],
            "items.*.category_id" => ["required", Rule::exists("categories", 'id')],
            "items.*.image"       => $image,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
