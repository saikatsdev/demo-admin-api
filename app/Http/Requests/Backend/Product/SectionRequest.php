<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title"         => ["required"],
            "product_ids"   => ["nullable", "array", "required_without:category_id"],
            "category_id"   => ["nullable", "required_without:product_ids"],
            "status"        => ["required", Rule::in(StatusEnum::activeStatus())],
            "position"      => ["required", "integer"],
            "img_path"      => ["nullable", "image", "mimes:png,jpg,webp,jpeg"],
            "banner_status" => ["nullable", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
