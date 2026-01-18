<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'        => ['required', "unique:sub_categories,name,$id"],
            'image'       => ['nullable'],
            "category_id" => ["required", "string", Rule::exists('categories', 'id')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
