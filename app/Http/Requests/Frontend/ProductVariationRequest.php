<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariationRequest extends FormRequest
{
    public function rules()
    {
        return [
            "product_id" => ["required", "integer"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
