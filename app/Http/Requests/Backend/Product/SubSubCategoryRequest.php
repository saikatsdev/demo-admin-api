<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubSubCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'            => ['required', "unique:sub_sub_categories,name,$id"],
            'image'           => ['nullable'],
            "sub_category_id" => ["required", Rule::exists('sub_categories', 'id')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
