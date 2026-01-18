<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class SeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "img_path"         => ["nullable", "mimes:png,jpg,jpeg,webp,svg", "max:2024"],
            "page"             => ["required", "string", "max:255"],
            "meta_title"       => ["required", "string", "max:255"],
            "meta_description" => ["nullable", "string"],
            "meta_keywords"    => ["nullable", "string"],
            "status"           => ["nullable", "in:published,draft"],
        ];
    }
}