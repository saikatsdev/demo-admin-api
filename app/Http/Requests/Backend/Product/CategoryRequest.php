<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"         => ["required", "unique:categories,name,{$id}"],
            "image"        => ["nullable"],
            "banner_image" => ["nullable"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
